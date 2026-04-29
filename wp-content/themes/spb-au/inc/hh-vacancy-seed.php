<?php
if (!defined("ABSPATH")) {
    exit();
}

/**
 * One-time HH vacancies seed.
 *
 * Run once (as admin): /?spbau_hh_seed=run
 * Force rerun:          /?spbau_hh_seed=force
 */
add_action("init", static function (): void {
    if (!isset($_GET["spbau_hh_seed"])) {
        return;
    }

    if (!is_user_logged_in() || !current_user_can("manage_options")) {
        status_header(403);
        wp_die("Forbidden");
    }

    $mode = sanitize_text_field((string) $_GET["spbau_hh_seed"]);
    if ($mode !== "run" && $mode !== "force") {
        wp_die("Usage: ?spbau_hh_seed=run or ?spbau_hh_seed=force");
    }

    $option_key = "spbau_hh_seed_2457792_done";
    $already_done = (bool) get_option($option_key, false);
    if ($already_done && $mode !== "force") {
        wp_die("HH seed already completed. Use ?spbau_hh_seed=force to rerun.");
    }

    @set_time_limit(180);

    $source_url = "https://spb.hh.ru/search/vacancy?employer_id=2457792";
    $search_html = spbau_hh_seed_fetch_html($source_url);
    if ($search_html === "") {
        wp_die("Failed to load HH vacancies list.");
    }

    $vacancies = spbau_hh_seed_parse_search_html($search_html);
    if (empty($vacancies)) {
        wp_die("No vacancies found on HH search page.");
    }

    $created = 0;
    $updated = 0;

    foreach ($vacancies as &$vacancy) {
        if ($vacancy["salary"] === "") {
            $detail_html = spbau_hh_seed_fetch_html($vacancy["url"]);
            if ($detail_html !== "") {
                $vacancy["salary"] = spbau_hh_seed_parse_salary_from_detail($detail_html);
            }
        }

        $result = spbau_hh_seed_upsert_vacancy($vacancy);
        if ($result === "created") {
            $created++;
        } elseif ($result === "updated") {
            $updated++;
        }
    }
    unset($vacancy);

    update_option($option_key, [
        "done_at" => current_time("mysql"),
        "count" => count($vacancies),
        "created" => $created,
        "updated" => $updated,
    ], false);

    $lines = [];
    $lines[] = "HH seed completed";
    $lines[] = "Total: " . count($vacancies);
    $lines[] = "Created: " . $created;
    $lines[] = "Updated: " . $updated;
    $lines[] = "";
    $lines[] = "Vacancies:";
    foreach ($vacancies as $vacancy) {
        $lines[] = "- " . $vacancy["title"] . " | " . ($vacancy["salary"] ?: "salary not specified");
    }

    wp_die(
        nl2br(esc_html(implode("\n", $lines))),
        "HH Seed Result",
        ["response" => 200]
    );
});

function spbau_hh_seed_fetch_html(string $url): string
{
    $response = wp_remote_get($url, [
        "timeout" => 30,
        "redirection" => 5,
        "headers" => [
            "User-Agent" => "Mozilla/5.0 (compatible; SPBAU-HH-Seed/1.0)",
            "Accept-Language" => "ru-RU,ru;q=0.9,en;q=0.8",
        ],
    ]);

    if (is_wp_error($response)) {
        return "";
    }

    $code = (int) wp_remote_retrieve_response_code($response);
    if ($code < 200 || $code >= 300) {
        return "";
    }

    $body = (string) wp_remote_retrieve_body($response);
    return trim($body);
}

function spbau_hh_seed_parse_search_html(string $html): array
{
    $rows = [];
    $seen = [];

    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    if (!$dom->loadHTML(mb_convert_encoding($html, "HTML-ENTITIES", "UTF-8"))) {
        return [];
    }

    $xpath = new DOMXPath($dom);
    $anchors = $xpath->query("//a[@data-qa='serp-item__title']");
    if (!$anchors instanceof DOMNodeList) {
        return [];
    }

    foreach ($anchors as $anchor) {
        if (!$anchor instanceof DOMElement) {
            continue;
        }

        $href = trim((string) $anchor->getAttribute("href"));
        if ($href === "") {
            continue;
        }
        if (strpos($href, "http") !== 0) {
            $href = "https://spb.hh.ru" . $href;
        }
        $href = preg_replace("/\\?.*$/", "", $href) ?: $href;

        if (!preg_match("~/vacancy/(\\d+)~", $href, $m)) {
            continue;
        }
        $vacancy_id = $m[1];
        if (isset($seen[$vacancy_id])) {
            continue;
        }
        $seen[$vacancy_id] = true;

        $title = trim(preg_replace("/\\s+/u", " ", (string) $anchor->textContent));
        if ($title === "") {
            continue;
        }

        $salary = "";
        $serp_item = $anchor;
        while ($serp_item && (!$serp_item->hasAttribute("data-qa") || $serp_item->getAttribute("data-qa") !== "serp-item")) {
            $serp_item = $serp_item->parentNode instanceof DOMElement ? $serp_item->parentNode : null;
        }
        if ($serp_item instanceof DOMElement) {
            $salary_node = $xpath->query(
                ".//*[@data-qa='vacancy-serp__vacancy-compensation' or @data-qa='vacancy-serp__vacancy_salary']",
                $serp_item
            );
            if ($salary_node instanceof DOMNodeList && $salary_node->length > 0) {
                $salary = trim(preg_replace("/\\s+/u", " ", (string) $salary_node->item(0)->textContent));
            }
        }

        $rows[] = [
            "id" => $vacancy_id,
            "title" => $title,
            "salary" => $salary,
            "url" => $href,
        ];
    }

    return $rows;
}

function spbau_hh_seed_parse_salary_from_detail(string $html): string
{
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    if (!$dom->loadHTML(mb_convert_encoding($html, "HTML-ENTITIES", "UTF-8"))) {
        return "";
    }

    $xpath = new DOMXPath($dom);
    $salary_node = $xpath->query(
        "//*[@data-qa='vacancy-salary' or @data-qa='vacancy-salary-compensation-type-net' or @data-qa='vacancy-salary-compensation-type-gross']"
    );
    if ($salary_node instanceof DOMNodeList && $salary_node->length > 0) {
        return trim(preg_replace("/\\s+/u", " ", (string) $salary_node->item(0)->textContent));
    }

    return "";
}

function spbau_hh_seed_upsert_vacancy(array $vacancy): string
{
    $hh_id = (string) ($vacancy["id"] ?? "");
    $title = trim((string) ($vacancy["title"] ?? ""));
    $salary = trim((string) ($vacancy["salary"] ?? ""));
    $url = trim((string) ($vacancy["url"] ?? ""));

    if ($hh_id === "" || $title === "" || $url === "") {
        return "skip";
    }

    $post_id = 0;
    $found_by_hh_id = get_posts([
        "post_type" => "vacancy",
        "post_status" => "any",
        "posts_per_page" => 1,
        "fields" => "ids",
        "meta_key" => "hh_vacancy_id",
        "meta_value" => $hh_id,
    ]);
    if (!empty($found_by_hh_id)) {
        $post_id = (int) $found_by_hh_id[0];
    }

    if ($post_id === 0) {
        $found_by_url = get_posts([
            "post_type" => "vacancy",
            "post_status" => "any",
            "posts_per_page" => 1,
            "fields" => "ids",
            "meta_key" => "vacancy_url",
            "meta_value" => $url,
        ]);
        if (!empty($found_by_url)) {
            $post_id = (int) $found_by_url[0];
        }
    }

    $result = "updated";
    if ($post_id > 0) {
        wp_update_post([
            "ID" => $post_id,
            "post_title" => $title,
            "post_status" => "publish",
        ]);
    } else {
        $insert_id = wp_insert_post([
            "post_type" => "vacancy",
            "post_title" => $title,
            "post_status" => "publish",
        ], true);
        if (is_wp_error($insert_id) || !$insert_id) {
            return "skip";
        }
        $post_id = (int) $insert_id;
        $result = "created";
    }

    update_post_meta($post_id, "vacancy_salary", $salary);
    update_post_meta($post_id, "vacancy_url", $url);
    update_post_meta($post_id, "vacancy_btn_text", "Подробнее");
    update_post_meta($post_id, "hh_source", "hh_employer_2457792");
    update_post_meta($post_id, "hh_vacancy_id", $hh_id);

    return $result;
}
