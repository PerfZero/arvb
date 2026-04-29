<?php
if (!defined("ABSPATH")) {
    exit();
}

require_once get_template_directory() . "/inc/hh-vacancy-seed.php";

// ACF Options Page
if (function_exists("acf_add_options_page")) {
    acf_add_options_page([
        "page_title" => "Настройки сайта",
        "menu_title" => "Настройки сайта",
        "menu_slug" => "site-settings",
        "capability" => "edit_posts",
        "redirect" => false,
    ]);
}

// Таксономия для СМИ (отдельная от рубрик статей)
add_action("init", static function (): void {
    register_taxonomy("smi_category", "smi", [
        "labels" => [
            "name" => "Категории СМИ",
            "singular_name" => "Категория СМИ",
            "add_new_item" => "Добавить категорию",
        ],
        "hierarchical" => true,
        "show_ui" => true,
        "show_in_menu" => true,
        "rewrite" => ["slug" => "smi-category"],
    ]);
});

// Кастомный тип записей: Публикации в СМИ
add_action("init", static function (): void {
    register_post_type("smi", [
        "labels" => [
            "name" => "Публикации в СМИ",
            "singular_name" => "Публикация в СМИ",
            "add_new_item" => "Добавить публикацию",
            "edit_item" => "Редактировать публикацию",
        ],
        "public" => true,
        "show_in_menu" => true,
        "supports" => ["title", "excerpt", "thumbnail", "custom-fields"],
        "menu_icon" => "dashicons-media-text",
        "has_archive" => false,
        "rewrite" => ["slug" => "smi"],
    ]);
});

add_action("after_setup_theme", static function (): void {
    add_theme_support("title-tag");
    add_theme_support("post-thumbnails");
    add_theme_support("custom-logo");

    register_nav_menus([
        "primary"          => __("Основное меню", "spb-au"),
        "secondary"        => __("Дополнительное меню", "spb-au"),
        "footer_primary"   => __("Подвал: Колонка 1", "spb-au"),
        "footer_secondary" => __("Подвал: Колонка 2", "spb-au"),
        "footer_legal"     => __("Подвал: Юридические ссылки", "spb-au"),
    ]);
});

// Отключаем глобальные стили и SVG-фильтры WordPress (убирают "острова")
add_action(
    "wp_enqueue_scripts",
    static function (): void {
        wp_dequeue_style("global-styles");
        wp_dequeue_style("core-block-supports");
    },
    100,
);

add_filter(
    "wp_enqueue_scripts",
    static function (): void {
        remove_action("wp_print_styles", "print_emoji_styles");
    },
    11,
);

remove_action("wp_head", "wp_global_styles_render_svg_filters");

function spbau_render_toc_block(array $attributes = [], string $content = ""): string
{
    $title = isset($attributes["title"]) && is_string($attributes["title"])
        ? trim($attributes["title"])
        : "";
    if ($title === "") {
        $title = "Содержание статьи";
    }

    $include_h3 = !empty($attributes["includeH3"]);
    $target_selector =
        isset($attributes["targetSelector"]) &&
        is_string($attributes["targetSelector"]) &&
        trim($attributes["targetSelector"]) !== ""
            ? trim($attributes["targetSelector"])
            : ".single-article__content";

    $headings_selector = $include_h3 ? "h2,h3" : "h2";

    $icon_url = get_template_directory_uri() . "/images/book.svg";
    $wrapper_attrs = get_block_wrapper_attributes([
        "class" => "single-article__toc spbau-toc-block",
        "data-target-selector" => $target_selector,
        "data-headings-selector" => $headings_selector,
    ]);

    ob_start();
    ?>
    <nav <?php echo $wrapper_attrs; ?>>
        <h2 class="single-article__toc-title">
            <img src="<?php echo esc_url($icon_url); ?>" width="16" height="16" alt="">
            <?php echo esc_html($title); ?>
        </h2>
        <ul class="single-article__toc-list" data-spbau-toc-list></ul>
    </nav>
    <?php

    return (string) ob_get_clean();
}

add_action("init", static function (): void {
    if (!function_exists("register_block_type")) {
        return;
    }

    $version = wp_get_theme()->get("Version");
    $dir_uri = get_template_directory_uri();
    wp_register_script(
        "spbau-toc-block-editor",
        $dir_uri . "/js/toc-block-editor.js",
        ["wp-blocks", "wp-element", "wp-components", "wp-block-editor", "wp-server-side-render"],
        $version,
        true,
    );

    wp_register_script(
        "spbau-toc-block-frontend",
        $dir_uri . "/js/toc-block-frontend.js",
        [],
        $version,
        true,
    );

    register_block_type("spbau/toc", [
        "api_version" => 2,
        "editor_script" => "spbau-toc-block-editor",
        "script" => "spbau-toc-block-frontend",
        "render_callback" => "spbau_render_toc_block",
        "attributes" => [
            "title" => [
                "type" => "string",
                "default" => "Содержание статьи",
            ],
            "includeH3" => [
                "type" => "boolean",
                "default" => true,
            ],
            "targetSelector" => [
                "type" => "string",
                "default" => ".single-article__content",
            ],
        ],
    ]);
});

// ACF JSON — сохранять/загружать из папки темы
add_filter("acf/settings/save_json", function () {
    return get_stylesheet_directory() . "/acf-json";
});
add_filter("acf/settings/load_json", function ($paths) {
    $paths[] = get_stylesheet_directory() . "/acf-json";
    return $paths;
});

function spbau_get_image_data($image, string $fallback_alt = ""): array
{
    $url = "";
    $alt = "";

    if (is_array($image) && !empty($image["url"])) {
        $url = (string) $image["url"];
        $alt = (string) ($image["alt"] ?? "");
    } elseif (is_numeric($image)) {
        $attachment_id = (int) $image;
        $url = (string) wp_get_attachment_url($attachment_id);
        $alt = (string) get_post_meta($attachment_id, "_wp_attachment_image_alt", true);
    } elseif (is_string($image) && $image !== "") {
        $url = $image;
    }

    if ($alt === "") {
        $alt = $fallback_alt;
    }

    return [
        "url" => $url,
        "alt" => $alt,
    ];
}

function spbau_get_case_fallback_image(string $fallback_alt = ""): array
{
    $fallback = function_exists("get_field")
        ? get_field("cases_fallback_image", "option")
        : null;
    $image = spbau_get_image_data($fallback, $fallback_alt);

    if ($image["url"] === "") {
        $image["url"] = get_template_directory_uri() . "/images/case-placeholder.svg";
    }
    if ($image["alt"] === "") {
        $image["alt"] = $fallback_alt;
    }

    return $image;
}

function spbau_get_case_image($image = null, int $post_id = 0, string $fallback_alt = ""): array
{
    $image_data = spbau_get_image_data($image, $fallback_alt);
    if ($image_data["url"] !== "") {
        return $image_data;
    }

    $post_id = $post_id > 0 ? $post_id : get_the_ID();
    if ($post_id && has_post_thumbnail($post_id)) {
        $thumbnail_id = (int) get_post_thumbnail_id($post_id);
        $thumbnail_src = wp_get_attachment_image_src($thumbnail_id, "full");
        if (is_array($thumbnail_src) && !empty($thumbnail_src[0])) {
            $image_data["url"] = (string) $thumbnail_src[0];
            $image_data["alt"] = (string) get_post_meta($thumbnail_id, "_wp_attachment_image_alt", true);
            if ($image_data["alt"] === "") {
                $image_data["alt"] = $fallback_alt;
            }
            return $image_data;
        }
    }

    return spbau_get_case_fallback_image($fallback_alt);
}

add_filter("acf/load_field_group", static function (array $group): array {
    if (($group["key"] ?? "") === "group_quiz_assignment") {
        $group["active"] = false;
    }
    return $group;
});

function spbau_sync_acf_order_to_menu_order($post_id): void
{
    static $is_syncing = false;
    if ($is_syncing || !is_numeric($post_id)) {
        return;
    }

    $post_id = (int) $post_id;
    $post_type = get_post_type($post_id);
    if (!in_array($post_type, ["faq_video", "team_member"], true)) {
        return;
    }

    $field_name = $post_type === "faq_video" ? "fv_order" : "tm_order";
    $raw_value = function_exists("get_field")
        ? get_field($field_name, $post_id)
        : get_post_meta($post_id, $field_name, true);

    if ($raw_value === null || $raw_value === "") {
        return;
    }

    $new_order = (int) $raw_value;
    $current_order = (int) get_post_field("menu_order", $post_id);
    if ($new_order === $current_order) {
        return;
    }

    $is_syncing = true;
    wp_update_post([
        "ID" => $post_id,
        "menu_order" => $new_order,
    ]);
    $is_syncing = false;
}
add_action("acf/save_post", "spbau_sync_acf_order_to_menu_order");

// Кастомный тип записей: Услуги
add_action("init", static function (): void {
    register_post_type("service", [
        "labels" => [
            "name" => "Услуги",
            "singular_name" => "Услуга",
            "add_new_item" => "Добавить услугу",
            "edit_item" => "Редактировать услугу",
        ],
        "public" => true,
        "has_archive" => false,
        "show_in_rest" => true,
        "supports" => ["title", "thumbnail"],
        "menu_icon" => "dashicons-hammer",
        "rewrite" => ["slug" => "uslugi"],
    ]);
});

// Кастомный тип записей: Завершенные дела
add_action("init", static function (): void {
    register_post_type("case", [
        "labels" => [
            "name" => "Завершённые дела",
            "singular_name" => "Дело",
            "add_new_item" => "Добавить дело",
            "edit_item" => "Редактировать дело",
        ],
        "public" => true,
        "has_archive" => "cases",
        "show_in_rest" => true,
        "supports" => ["title", "thumbnail"],
        "menu_icon" => "dashicons-portfolio",
        "rewrite" => ["slug" => "cases"],
    ]);

    register_taxonomy("case_status", "case", [
        "label" => "Статус",
        "hierarchical" => false,
        "show_in_rest" => true,
        "rewrite" => ["slug" => "case-status"],
    ]);

    register_taxonomy("case_debt_type", "case", [
        "label" => "Вид долгов",
        "hierarchical" => false,
        "show_in_rest" => true,
        "rewrite" => ["slug" => "case-debt-type"],
    ]);

    register_taxonomy("case_creditor_type", "case", [
        "label" => "Типы кредиторов",
        "hierarchical" => false,
        "show_in_rest" => true,
        "rewrite" => ["slug" => "case-creditor-type"],
    ]);

    register_post_type("review", [
        "labels" => [
            "name" => "Отзывы",
            "singular_name" => "Отзыв",
            "add_new_item" => "Добавить отзыв",
            "edit_item" => "Редактировать отзыв",
        ],
        "public" => true,
        "has_archive" => "review",
        "show_in_rest" => true,
        "supports" => ["title", "thumbnail"],
        "menu_icon" => "dashicons-testimonial",
        "rewrite" => ["slug" => "review"],
    ]);

    register_post_type("vacancy", [
        "labels" => [
            "name" => "Вакансии",
            "singular_name" => "Вакансия",
            "add_new_item" => "Добавить вакансию",
            "edit_item" => "Редактировать вакансию",
        ],
        "public" => false,
        "show_ui" => true,
        "show_in_rest" => true,
        "supports" => ["title", "page-attributes"],
        "menu_icon" => "dashicons-id-alt",
        "menu_position" => 9,
    ]);

    register_post_type("quiz", [
        "labels" => [
            "name" => "Квизы",
            "singular_name" => "Квиз",
            "add_new_item" => "Добавить квиз",
            "edit_item" => "Редактировать квиз",
        ],
        "public" => false,
        "show_ui" => true,
        "show_in_rest" => false,
        "supports" => ["title"],
        "menu_icon" => "dashicons-forms",
        "menu_position" => 8,
    ]);

    register_taxonomy("review_debt_type", "review", [
        "label" => "Вид долгов",
        "hierarchical" => false,
        "show_in_rest" => true,
        "rewrite" => ["slug" => "review-debt-type"],
    ]);

    register_taxonomy("review_creditor_type", "review", [
        "label" => "Типы кредиторов",
        "hierarchical" => false,
        "show_in_rest" => true,
        "rewrite" => ["slug" => "review-creditor-type"],
    ]);

    register_post_type("faq_video", [
        "labels" => [
            "name" => "FAQ видео",
            "singular_name" => "FAQ видео",
            "add_new_item" => "Добавить видео",
            "edit_item" => "Редактировать видео",
        ],
        "public" => false,
        "show_ui" => true,
        "show_in_rest" => false,
        "supports" => ["title", "thumbnail"],
        "menu_icon" => "dashicons-video-alt3",
        "menu_position" => 6,
    ]);

    register_post_type("team_member", [
        "labels" => [
            "name" => "Команда",
            "singular_name" => "Сотрудник",
            "add_new_item" => "Добавить сотрудника",
            "edit_item" => "Редактировать сотрудника",
        ],
        "public" => false,
        "show_ui" => true,
        "show_in_rest" => false,
        "supports" => ["title", "excerpt", "thumbnail", "page-attributes"],
        "menu_icon" => "dashicons-groups",
        "menu_position" => 7,
    ]);
});

function spbau_detect_quiz_context(string $mode = "main"): string
{
    if ($mode === "widget") {
        if (is_singular("case")) {
            return "single_case";
        }
        if (is_singular("review")) {
            return "single_review";
        }
        return "";
    }

    if (is_front_page()) {
        return "front_page";
    }
    if (is_page_template("page-zavod.php")) {
        return "page_zavod";
    }
    if (is_singular("service")) {
        return "single_service";
    }
    if (is_singular("post")) {
        return "single_post";
    }
    if (is_singular("case")) {
        return "single_case";
    }
    if (is_singular("review")) {
        return "single_review";
    }

    return "";
}

function spbau_get_active_quiz_id(string $mode = "main"): int
{
    static $cache = [];

    $context = spbau_detect_quiz_context($mode);
    if ($context === "") {
        return 0;
    }
    if (isset($cache[$context])) {
        return (int) $cache[$context];
    }

    $posts = get_posts([
        "post_type" => "quiz",
        "post_status" => "publish",
        "posts_per_page" => -1,
        "orderby" => "date",
        "order" => "DESC",
        "fields" => "ids",
        "no_found_rows" => true,
        "suppress_filters" => true,
    ]);

    $best_id = 0;
    $best_priority = PHP_INT_MAX;
    $best_date = 0;

    foreach ($posts as $quiz_id) {
        $locations = function_exists("get_field")
            ? get_field("quiz_display_locations", $quiz_id)
            : get_post_meta($quiz_id, "quiz_display_locations", true);

        if (is_string($locations) && $locations !== "") {
            $locations = [$locations];
        }
        if (!is_array($locations) || empty($locations)) {
            continue;
        }

        if (
            !in_array("all", $locations, true) &&
            !in_array($context, $locations, true)
        ) {
            continue;
        }

        $priority_raw = function_exists("get_field")
            ? get_field("quiz_display_priority", $quiz_id)
            : get_post_meta($quiz_id, "quiz_display_priority", true);
        $priority = (int) $priority_raw;
        if ($priority <= 0) {
            $priority = 100;
        }

        $quiz_post = get_post($quiz_id);
        $date = $quiz_post ? strtotime((string) $quiz_post->post_date_gmt) : 0;

        if (
            $best_id === 0 ||
            $priority < $best_priority ||
            ($priority === $best_priority && $date > $best_date)
        ) {
            $best_id = (int) $quiz_id;
            $best_priority = $priority;
            $best_date = $date;
        }
    }

    $cache[$context] = $best_id;
    return $best_id;
}

add_action("init", static function (): void {
    $rewrite_version = "spbau_rewrite_v20260423_review_archive";
    if (get_option("spbau_rewrite_version") !== $rewrite_version) {
        flush_rewrite_rules(false);
        update_option("spbau_rewrite_version", $rewrite_version, false);
    }
}, 99);

// Время чтения статьи
/**
 * Конвертирует ссылку VK (clip/video) в embed URL для iframe.
 * Поддерживает:
 *   https://vk.com/clip-12345_67890
 *   https://vk.com/video-12345_67890
 *   https://vk.com/video?z=video-12345_67890
 */
function spbau_vk_embed_url(string $url): string
{
    // vk.com/clip-OID_VID или vk.com/video-OID_VID
    if (preg_match("#vk\.com/(?:clip|video)(-?\d+)_(\d+)#", $url, $m)) {
        return "https://vk.com/video_ext.php?oid=" .
            $m[1] .
            "&id=" .
            $m[2] .
            "&hd=2&autoplay=1";
    }
    // vk.com/video?z=video-OID_VID или vk.com/video?z=clip-OID_VID
    if (preg_match("#[?&]z=(?:video|clip)(-?\d+)_(\d+)#", $url, $m)) {
        return "https://vk.com/video_ext.php?oid=" .
            $m[1] .
            "&id=" .
            $m[2] .
            "&hd=2&autoplay=1";
    }
    // YouTube
    if (
        preg_match(
            "#(?:youtube\.com/watch\?v=|youtu\.be/)([a-zA-Z0-9_-]{11})#",
            $url,
            $m,
        )
    ) {
        return "https://www.youtube.com/embed/" . $m[1] . "?autoplay=1";
    }
    return $url;
}

function spbau_reading_time(string $content = ""): int
{
    $text = wp_strip_all_tags(strip_shortcodes($content));
    preg_match_all("/[\\p{L}\\p{N}]+/u", $text, $matches);
    $words = isset($matches[0]) ? count($matches[0]) : 0;
    return max(1, (int) ceil($words / 200));
}

function spbau_get_post_views(int $post_id): int
{
    if ($post_id <= 0) {
        return 0;
    }

    $keys = ["post_views_count", "views", "page_views"];
    foreach ($keys as $key) {
        $value = (int) get_post_meta($post_id, $key, true);
        if ($value > 0) {
            return $value;
        }
    }

    return 0;
}

function spbau_set_post_views(int $post_id, int $views): void
{
    if ($post_id <= 0) {
        return;
    }

    $views = max(0, $views);
    update_post_meta($post_id, "post_views_count", $views);
    update_post_meta($post_id, "views", $views);
    update_post_meta($post_id, "page_views", $views);
}

function spbau_track_post_view(): void
{
    if (
        is_admin() ||
        wp_doing_ajax() ||
        is_feed() ||
        is_preview() ||
        is_robots() ||
        is_trackback()
    ) {
        return;
    }

    if (!is_singular("post")) {
        return;
    }

    $post_id = (int) get_queried_object_id();
    if ($post_id <= 0) {
        return;
    }

    $cookie_name = "spbau_post_viewed_" . $post_id;
    if (isset($_COOKIE[$cookie_name])) {
        return;
    }

    $current = spbau_get_post_views($post_id);
    spbau_set_post_views($post_id, $current + 1);

    $expire = time() + 6 * HOUR_IN_SECONDS;
    $path = defined("COOKIEPATH") && COOKIEPATH ? COOKIEPATH : "/";
    $domain = defined("COOKIE_DOMAIN") ? COOKIE_DOMAIN : "";
    setcookie($cookie_name, "1", $expire, $path, $domain, is_ssl(), true);
    $_COOKIE[$cookie_name] = "1";
}
add_action("template_redirect", "spbau_track_post_view", 20);

add_action("wp_enqueue_scripts", static function (): void {
    $swiper_templates = ['page-loyalty.php', 'page-reviews.php'];
    $needs_swiper = is_singular('post') || is_front_page()
        || is_singular('case') || is_singular('service')
        || (is_page() && in_array(get_page_template_slug(), $swiper_templates, true));
    if ($needs_swiper) {
        wp_enqueue_style(
            "swiper",
            get_template_directory_uri() . "/libs/swiper/swiper-bundle.min.css",
            [],
            "11",
        );
        wp_enqueue_script(
            "swiper",
            get_template_directory_uri() . "/libs/swiper/swiper-bundle.min.js",
            [],
            "11",
            true,
        );
        wp_enqueue_style(
            "glightbox",
            "https://cdn.jsdelivr.net/npm/glightbox@3/dist/css/glightbox.min.css",
            [],
            null,
        );
        wp_enqueue_script(
            "glightbox",
            "https://cdn.jsdelivr.net/npm/glightbox@3/dist/js/glightbox.min.js",
            [],
            null,
            true,
        );
        wp_enqueue_script(
            "spb-au-gallery",
            get_template_directory_uri() . "/js/gallery.js",
            ["swiper", "glightbox"],
            wp_get_theme()->get("Version"),
            true,
        );
    }
});

add_action("wp_enqueue_scripts", static function (): void {
    wp_enqueue_style(
        "spb-au-fonts",
        get_template_directory_uri() . "/fonts/fonts.css",
        [],
        wp_get_theme()->get("Version"),
    );
    wp_enqueue_style(
        "spb-au-style",
        get_stylesheet_uri(),
        ["spb-au-fonts"],
        wp_get_theme()->get("Version"),
    );

    wp_enqueue_script(
        "spb-au-scale",
        get_template_directory_uri() . "/js/scale.js",
        [],
        wp_get_theme()->get("Version"),
        true,
    );

    wp_enqueue_script(
        "spb-au-menu",
        get_template_directory_uri() . "/js/menu.js",
        [],
        wp_get_theme()->get("Version"),
        true,
    );

    wp_enqueue_script(
        "spb-cases-filter",
        get_template_directory_uri() . "/js/cases-filter.js",
        [],
        wp_get_theme()->get("Version"),
        true,
    );

    wp_enqueue_style(
        "intl-tel-input",
        "https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/css/intlTelInput.css",
        [],
        "25.3.1",
    );

    wp_enqueue_script(
        "intl-tel-input",
        "https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/intlTelInput.min.js",
        [],
        "25.3.1",
        true,
    );

    wp_enqueue_script(
        "spb-au-phone",
        get_template_directory_uri() . "/js/phone.js",
        ["intl-tel-input"],
        wp_get_theme()->get("Version"),
        true,
    );

    wp_enqueue_script(
        "spb-au-consult-modal",
        get_template_directory_uri() . "/js/consult-modal.js",
        [],
        wp_get_theme()->get("Version"),
        true,
    );

    wp_enqueue_script(
        "spb-au-booking",
        get_template_directory_uri() . "/js/booking.js",
        [],
        wp_get_theme()->get("Version"),
        true,
    );
    wp_localize_script("spb-au-booking", "spbauBookingAjax", [
        "url" => admin_url("admin-ajax.php"),
        "action" => "spbau_booking_slots",
    ]);
});

function spbau_bitrix_webhook_url(): string
{
    if (defined("SPBAU_BITRIX_WEBHOOK_URL") && SPBAU_BITRIX_WEBHOOK_URL) {
        return trim((string) SPBAU_BITRIX_WEBHOOK_URL);
    }

    $env_url = getenv("SPBAU_BITRIX_WEBHOOK_URL");
    if (is_string($env_url) && $env_url !== "") {
        return trim($env_url);
    }

    if (function_exists("get_field")) {
        $acf_url = get_field("bitrix_webhook_url", "option");
        if (is_string($acf_url) && $acf_url !== "") {
            return trim($acf_url);
        }
    }

    return "";
}

function spbau_send_smi_lead_to_bitrix(
    string $phone,
    string $referer = "",
    string $title = 'Заявка с формы "Мы в СМИ"',
    string $source_description = "Сайт spb-au / Мы в СМИ",
    string $client_name = "Клиент сайта",
    string $extra_comments = "",
    string $email = "",
): array {
    $webhook = spbau_bitrix_webhook_url();
    if ($webhook === "") {
        return [
            "ok" => false,
            "message" =>
                "Не настроен webhook Битрикс. Добавьте SPBAU_BITRIX_WEBHOOK_URL.",
        ];
    }

    $endpoint = $webhook;
    if (stripos($endpoint, "crm.lead.add") === false) {
        $endpoint = rtrim($endpoint, "/") . "/crm.lead.add.json";
    }

    $payload = [
        "fields" => [
            "TITLE" => $title,
            "NAME" => $client_name,
            "PHONE" => [["VALUE" => $phone, "VALUE_TYPE" => "WORK"]],
            "STATUS_ID" => "UC_3XRA33",
            "SOURCE_ID" => "WEB",
            "COMMENTS" =>
                "Источник: " .
                $source_description .
                "\n" .
                "URL: " .
                ($referer ?: home_url("/")) .
                "\n" .
                "Дата: " .
                wp_date("Y-m-d H:i:s") .
                ($extra_comments !== "" ? "\n" . $extra_comments : ""),
            "SOURCE_DESCRIPTION" => $source_description,
            "OPENED" => "Y",
        ],
        "params" => ["REGISTER_SONET_EVENT" => "Y"],
    ];

    $email = sanitize_email($email);
    if ($email !== "" && is_email($email)) {
        $payload["fields"]["EMAIL"] = [[
            "VALUE" => $email,
            "VALUE_TYPE" => "WORK",
        ]];
    }

    $timeout = 30;
    $env_timeout = getenv("SPBAU_BITRIX_TIMEOUT");
    if (is_string($env_timeout) && $env_timeout !== "") {
        $timeout = max(10, (int) $env_timeout);
    } elseif (
        defined("SPBAU_BITRIX_TIMEOUT") &&
        is_numeric((string) SPBAU_BITRIX_TIMEOUT)
    ) {
        $timeout = max(10, (int) SPBAU_BITRIX_TIMEOUT);
    }

    $attempts = 2;
    $response = null;
    $last_error = "";

    for ($i = 1; $i <= $attempts; $i++) {
        $response = wp_remote_post($endpoint, [
            "timeout" => $timeout,
            "headers" => ["Content-Type" => "application/json; charset=utf-8"],
            "body" => wp_json_encode($payload),
        ]);

        if (!is_wp_error($response)) {
            break;
        }

        $last_error = (string) $response->get_error_message();
        $is_timeout =
            stripos($last_error, "cURL error 28") !== false ||
            stripos($last_error, "timed out") !== false;
        if (!$is_timeout || $i >= $attempts) {
            break;
        }

        // Небольшая пауза и повтор, если сеть/Bitrix ответили с задержкой.
        usleep(350000);
    }

    if (is_wp_error($response)) {
        $host = (string) wp_parse_url($endpoint, PHP_URL_HOST);
        $is_timeout =
            stripos($last_error, "cURL error 28") !== false ||
            stripos($last_error, "timed out") !== false;

        if ($is_timeout) {
            return [
                "ok" => false,
                "message" =>
                    "Ошибка отправки в Битрикс: превышено время ожидания ответа от " .
                    $host .
                    ". Проверьте доступ сервера к Bitrix24 и корректность webhook.",
            ];
        }

        return [
            "ok" => false,
            "message" => "Ошибка отправки в Битрикс: " . $last_error,
        ];
    }

    $code = (int) wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    $decoded = json_decode($body, true);

    if (isset($decoded["error_description"]) && $decoded["error_description"] !== "") {
        return [
            "ok" => false,
            "message" => "Ошибка Битрикс: " . (string) $decoded["error_description"],
        ];
    }

    if (isset($decoded["error"]) && $decoded["error"] !== "") {
        return [
            "ok" => false,
            "message" => "Ошибка Битрикс: " . (string) $decoded["error"],
        ];
    }

    if ($code < 200 || $code >= 300) {
        return [
            "ok" => false,
            "message" => "Битрикс вернул HTTP " . $code,
        ];
    }

    if (!isset($decoded["result"])) {
        return [
            "ok" => false,
            "message" => "Неожиданный ответ Битрикс.",
        ];
    }

    return ["ok" => true, "message" => "Заявка отправлена. Скоро свяжемся с вами."];
}

function spbau_handle_smi_collab_submit(): void
{
    $nonce_ok = isset($_POST["spbau_smi_collab_nonce"]) &&
        wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST["spbau_smi_collab_nonce"])),
            "spbau_smi_collab_submit",
        );

    $redirect = wp_get_referer();
    if (!$redirect) {
        $redirect = home_url("/");
    }

    if (!$nonce_ok) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "smi_form_status" => "error",
                    "smi_form_message" => "Ошибка безопасности формы.",
                ],
                $redirect,
            ),
        );
        exit();
    }

    $phone_raw = isset($_POST["smi_phone"])
        ? sanitize_text_field(wp_unslash($_POST["smi_phone"]))
        : "";
    $agree = !empty($_POST["smi_agree"]);
    $digits = preg_replace("/\D+/", "", $phone_raw);
    $phone = $digits !== "" ? "+" . $digits : "";

    if (strlen($digits) < 10) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "smi_form_status" => "error",
                    "smi_form_message" =>
                        "Укажите корректный номер телефона.",
                ],
                $redirect,
            ),
        );
        exit();
    }

    if (!$agree) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "smi_form_status" => "error",
                    "smi_form_message" =>
                        "Нужно согласие на обработку данных.",
                ],
                $redirect,
            ),
        );
        exit();
    }

    $result = spbau_send_smi_lead_to_bitrix($phone, $redirect);

    wp_safe_redirect(
        add_query_arg(
            [
                "smi_form_status" => $result["ok"] ? "success" : "error",
                "smi_form_message" => $result["message"],
            ],
            $redirect,
        ),
    );
    exit();
}

add_action("admin_post_spbau_smi_collab_submit", "spbau_handle_smi_collab_submit");
add_action(
    "admin_post_nopriv_spbau_smi_collab_submit",
    "spbau_handle_smi_collab_submit",
);

function spbau_normalize_telegram_url(string $url): string
{
    $normalized = esc_url_raw(trim($url));
    if ($normalized === "") {
        return "";
    }

    $parts = wp_parse_url($normalized);
    if (!$parts || empty($parts["scheme"]) || empty($parts["host"])) {
        return "";
    }

    $scheme = strtolower((string) $parts["scheme"]);
    if (!in_array($scheme, ["http", "https"], true)) {
        return "";
    }

    $host = strtolower((string) $parts["host"]);
    if (str_starts_with($host, "www.")) {
        $host = substr($host, 4);
    }
    if (!in_array($host, ["t.me", "telegram.me"], true)) {
        return "";
    }

    return $normalized;
}

function spbau_handle_expertise_tg_submit(): void
{
    $append_fragment = static function (string $url, string $fragment): string {
        $parts = wp_parse_url($url);
        if (!$parts || empty($parts["scheme"]) || empty($parts["host"])) {
            return $url . "#" . ltrim($fragment, "#");
        }

        $result = $parts["scheme"] . "://" . $parts["host"];
        if (!empty($parts["port"])) {
            $result .= ":" . $parts["port"];
        }
        if (!empty($parts["path"])) {
            $result .= $parts["path"];
        }
        if (!empty($parts["query"])) {
            $result .= "?" . $parts["query"];
        }

        return $result . "#" . ltrim($fragment, "#");
    };

    $nonce_ok = isset($_POST["spbau_expertise_tg_nonce"]) &&
        wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST["spbau_expertise_tg_nonce"])),
            "spbau_expertise_tg_submit",
        );

    $redirect = wp_get_referer();
    if (!$redirect) {
        $redirect = home_url("/");
    }
    $redirect = $append_fragment($redirect, "expertise");

    if (!$nonce_ok) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "expertise_form_status" => "error",
                    "expertise_form_message" => "Ошибка безопасности формы.",
                ],
                $redirect,
            ),
        );
        exit();
    }

    $phone_raw = isset($_POST["expertise_tg_phone"])
        ? sanitize_text_field(wp_unslash($_POST["expertise_tg_phone"]))
        : "";
    $target_raw = isset($_POST["expertise_tg_target"])
        ? sanitize_text_field(wp_unslash($_POST["expertise_tg_target"]))
        : "";
    $digits = preg_replace("/\D+/", "", $phone_raw);
    $phone = $digits !== "" ? "+" . $digits : "";
    $target_url = $target_raw !== "" ? spbau_normalize_telegram_url($target_raw) : "";

    if (strlen($digits) < 10) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "expertise_form_status" => "error",
                    "expertise_form_message" =>
                        "Укажите корректный номер телефона.",
                ],
                $redirect,
            ),
        );
        exit();
    }

    $result = spbau_send_smi_lead_to_bitrix(
        $phone,
        $redirect,
        'Заявка с блока "Экспертиза"',
        "Сайт spb-au / Главная / Экспертиза",
    );

    if (!$result["ok"]) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "expertise_form_status" => "error",
                    "expertise_form_message" => $result["message"],
                ],
                $redirect,
            ),
        );
        exit();
    }

    if ($target_url !== "" && $target_url !== "#") {
        wp_redirect($target_url);
        exit();
    }

    wp_safe_redirect(
        add_query_arg(
            [
                "expertise_form_status" => "success",
                "expertise_form_message" => $result["message"],
            ],
            $redirect,
        ),
    );
    exit();
}

add_action(
    "admin_post_spbau_expertise_tg_submit",
    "spbau_handle_expertise_tg_submit",
);
add_action(
    "admin_post_nopriv_spbau_expertise_tg_submit",
    "spbau_handle_expertise_tg_submit",
);

function spbau_handle_marathon_submit(): void
{
    $append_fragment = static function (string $url, string $fragment): string {
        $parts = wp_parse_url($url);
        if (!$parts || empty($parts["scheme"]) || empty($parts["host"])) {
            return $url . "#" . ltrim($fragment, "#");
        }

        $result = $parts["scheme"] . "://" . $parts["host"];
        if (!empty($parts["port"])) {
            $result .= ":" . $parts["port"];
        }
        if (!empty($parts["path"])) {
            $result .= $parts["path"];
        }
        if (!empty($parts["query"])) {
            $result .= "?" . $parts["query"];
        }

        return $result . "#" . ltrim($fragment, "#");
    };

    $nonce_ok = isset($_POST["spbau_marathon_nonce"]) &&
        wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST["spbau_marathon_nonce"])),
            "spbau_marathon_submit",
        );

    $redirect = wp_get_referer();
    if (!$redirect) {
        $redirect = home_url("/");
    }
    $redirect = $append_fragment($redirect, "marathon");

    if (!$nonce_ok) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "marathon_form_status" => "error",
                    "marathon_form_message" => "Ошибка безопасности формы.",
                ],
                $redirect,
            ),
        );
        exit();
    }

    $phone_raw = isset($_POST["marathon_phone"])
        ? sanitize_text_field(wp_unslash($_POST["marathon_phone"]))
        : "";
    $agree = !empty($_POST["marathon_agree"]);
    $digits = preg_replace("/\D+/", "", $phone_raw);
    $phone = $digits !== "" ? "+" . $digits : "";

    if (!$agree) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "marathon_form_status" => "error",
                    "marathon_form_message" =>
                        "Нужно согласие на обработку данных.",
                ],
                $redirect,
            ),
        );
        exit();
    }

    if (strlen($digits) < 10) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "marathon_form_status" => "error",
                    "marathon_form_message" =>
                        "Укажите корректный номер телефона.",
                ],
                $redirect,
            ),
        );
        exit();
    }

    $result = spbau_send_smi_lead_to_bitrix(
        $phone,
        $redirect,
        'Заявка с формы "Марафон"',
        "Сайт spb-au / Марафон",
    );

    if (!$result["ok"]) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "marathon_form_status" => "error",
                    "marathon_form_message" => $result["message"],
                ],
                $redirect,
            ),
        );
        exit();
    }

    $target_url = "";
    if (function_exists("get_field")) {
        $target = get_field("marathon_button_url", "option");
        if (is_string($target)) {
            $target_url = trim($target);
        }
    }

    if ($target_url !== "" && $target_url !== "#") {
        $target_url = esc_url_raw($target_url);
        if ($target_url !== "") {
            wp_redirect($target_url);
            exit();
        }
    }

    wp_safe_redirect(
        add_query_arg(
            [
                "marathon_form_status" => "success",
                "marathon_form_message" => $result["message"],
            ],
            $redirect,
        ),
    );
    exit();
}

add_action("admin_post_spbau_marathon_submit", "spbau_handle_marathon_submit");
add_action(
    "admin_post_nopriv_spbau_marathon_submit",
    "spbau_handle_marathon_submit",
);

function spbau_bitrix_webhook_user_id(): int
{
    $webhook = spbau_bitrix_webhook_url();
    if ($webhook === "") {
        return 0;
    }

    $path = (string) wp_parse_url($webhook, PHP_URL_PATH);
    if (preg_match("#/rest/(\d+)/#", $path, $m)) {
        return (int) $m[1];
    }

    return 0;
}

function spbau_bitrix_calendar_get_events(string $from_iso, string $to_iso): array
{
    $webhook = spbau_bitrix_webhook_url();
    $user_id = spbau_bitrix_webhook_user_id();
    if ($webhook === "" || $user_id <= 0) {
        return [];
    }

    $endpoint = rtrim($webhook, "/");
    if (stripos($endpoint, "calendar.event.get") === false) {
        $endpoint .= "/calendar.event.get.json";
    }

    $url = add_query_arg(
        [
            "type" => "user",
            "ownerId" => $user_id,
            "from" => $from_iso,
            "to" => $to_iso,
        ],
        $endpoint,
    );

    $response = wp_remote_get($url, ["timeout" => 20]);
    if (is_wp_error($response)) {
        return [];
    }

    $decoded = json_decode(wp_remote_retrieve_body($response), true);
    if (!is_array($decoded) || !isset($decoded["result"]) || !is_array($decoded["result"])) {
        return [];
    }

    return $decoded["result"];
}

function spbau_booking_times(): array
{
    return [
        "10:00",
        "11:00",
        "12:00",
        "13:00",
        "14:00",
        "15:00",
        "16:00",
        "17:00",
        "18:00",
        "19:00",
        "20:00",
        "20:45",
    ];
}

function spbau_booking_busy_map(array $days, array $times): array
{
    if (!$days || !$times) {
        return [];
    }

    $first_iso = $days[0]["iso"] ?? "";
    $last_iso = $days[count($days) - 1]["iso"] ?? "";
    if ($first_iso === "" || $last_iso === "") {
        return [];
    }

    $tz = wp_timezone();
    $from = new DateTime($first_iso . " 00:00:00", $tz);
    $to = new DateTime($last_iso . " 23:59:59", $tz);
    $events = spbau_bitrix_calendar_get_events(
        $from->format("Y-m-d\\TH:i:sP"),
        $to->format("Y-m-d\\TH:i:sP"),
    );

    $busy = [];
    foreach ($events as $event) {
        $ev_from_raw = (string) ($event["DATE_FROM"] ?? "");
        $ev_to_raw = (string) ($event["DATE_TO"] ?? "");
        if ($ev_from_raw === "" || $ev_to_raw === "") {
            continue;
        }

        $ev_from = DateTime::createFromFormat("d.m.Y H:i:s", $ev_from_raw, $tz);
        $ev_to = DateTime::createFromFormat("d.m.Y H:i:s", $ev_to_raw, $tz);
        if (!$ev_from || !$ev_to) {
            continue;
        }

        foreach ($days as $day) {
            $dmy = (string) ($day["date"] ?? "");
            $iso = (string) ($day["iso"] ?? "");
            if ($dmy === "" || $iso === "") {
                continue;
            }

            foreach ($times as $time) {
                $slot_from = DateTime::createFromFormat("Y-m-d H:i:s", $iso . " " . $time . ":00", $tz);
                if (!$slot_from) {
                    continue;
                }
                $slot_to = clone $slot_from;
                $slot_to->modify("+1 hour");

                if ($ev_from < $slot_to && $ev_to > $slot_from) {
                    $busy[$dmy . "|" . $time] = true;
                }
            }
        }
    }

    return $busy;
}

function spbau_booking_slot_is_busy(string $date, string $time): bool
{
    $tz = wp_timezone();
    $slot_from = DateTime::createFromFormat("d.m.Y H:i:s", $date . " " . $time . ":00", $tz);
    if (!$slot_from) {
        return true;
    }
    $slot_to = clone $slot_from;
    $slot_to->modify("+1 hour");

    $events = spbau_bitrix_calendar_get_events(
        $slot_from->format("Y-m-d\\TH:i:sP"),
        $slot_to->format("Y-m-d\\TH:i:sP"),
    );
    if (!$events) {
        return false;
    }

    foreach ($events as $event) {
        $ev_from_raw = (string) ($event["DATE_FROM"] ?? "");
        $ev_to_raw = (string) ($event["DATE_TO"] ?? "");
        if ($ev_from_raw === "" || $ev_to_raw === "") {
            continue;
        }
        $ev_from = DateTime::createFromFormat("d.m.Y H:i:s", $ev_from_raw, $tz);
        $ev_to = DateTime::createFromFormat("d.m.Y H:i:s", $ev_to_raw, $tz);
        if (!$ev_from || !$ev_to) {
            continue;
        }

        if ($ev_from < $slot_to && $ev_to > $slot_from) {
            return true;
        }
    }

    return false;
}

function spbau_ajax_booking_slots(): void
{
    $days = [];
    for ($i = 0; $i < 7; $i++) {
        $ts = strtotime("+" . $i . " days");
        $days[] = [
            "date" => date("d.m.Y", $ts),
            "iso" => date("Y-m-d", $ts),
        ];
    }

    $times = spbau_booking_times();
    $busy_map = spbau_booking_busy_map($days, $times);
    $busy_keys = array_keys($busy_map);

    wp_send_json_success([
        "busyKeys" => $busy_keys,
        "serverTs" => current_time("timestamp"),
    ]);
}

add_action("wp_ajax_spbau_booking_slots", "spbau_ajax_booking_slots");
add_action("wp_ajax_nopriv_spbau_booking_slots", "spbau_ajax_booking_slots");

function spbau_add_bitrix_calendar_event(
    string $date,
    string $time,
    string $name,
    string $phone,
    string $contact_method,
): array {
    $webhook = spbau_bitrix_webhook_url();
    if ($webhook === "") {
        return ["ok" => false, "message" => "Webhook Битрикс не настроен."];
    }

    $user_id = spbau_bitrix_webhook_user_id();
    if ($user_id <= 0) {
        return ["ok" => false, "message" => "Не удалось определить userId webhook."];
    }

    $tz = wp_timezone();
    $dt_start = DateTime::createFromFormat("d.m.Y H:i", $date . " " . $time, $tz);
    if (!$dt_start) {
        return ["ok" => false, "message" => "Некорректная дата/время слота."];
    }
    $dt_end = clone $dt_start;
    $dt_end->modify("+1 hour");

    $endpoint = rtrim($webhook, "/");
    if (stripos($endpoint, "calendar.event.add") === false) {
        $endpoint .= "/calendar.event.add.json";
    }

    $from = $dt_start->format("Y-m-d\\TH:i:sP");
    $to = $dt_end->format("Y-m-d\\TH:i:sP");

    $payload = [
        "type" => "user",
        "ownerId" => $user_id,
        "name" => "Консультация: " . $name,
        "from" => $from,
        "to" => $to,
        "description" =>
            "Запись с сайта\n" .
            "Клиент: " .
            $name .
            "\n" .
            "Телефон: " .
            $phone .
            "\n" .
            "Связь: " .
            $contact_method,
    ];

    $response = wp_remote_post($endpoint, [
        "timeout" => 20,
        "headers" => ["Content-Type" => "application/json; charset=utf-8"],
        "body" => wp_json_encode($payload),
    ]);

    if (is_wp_error($response)) {
        return [
            "ok" => false,
            "message" =>
                "Календарь Битрикс недоступен: " . $response->get_error_message(),
        ];
    }

    $decoded = json_decode(wp_remote_retrieve_body($response), true);
    if (isset($decoded["error"]) && $decoded["error"] !== "") {
        return [
            "ok" => false,
            "message" =>
                "Календарь Битрикс: " .
                (string) ($decoded["error_description"] ?? $decoded["error"]),
        ];
    }

    return ["ok" => true, "message" => "Событие календаря создано."];
}

function spbau_handle_booking_submit(): void
{
    $append_fragment = static function (string $url, string $fragment): string {
        $parts = wp_parse_url($url);
        if (!$parts || empty($parts["scheme"]) || empty($parts["host"])) {
            return $url . "#" . ltrim($fragment, "#");
        }

        $result = $parts["scheme"] . "://" . $parts["host"];
        if (!empty($parts["port"])) {
            $result .= ":" . $parts["port"];
        }
        if (!empty($parts["path"])) {
            $result .= $parts["path"];
        }
        if (!empty($parts["query"])) {
            $result .= "?" . $parts["query"];
        }

        return $result . "#" . ltrim($fragment, "#");
    };

    $nonce_ok = isset($_POST["spbau_booking_nonce"]) &&
        wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST["spbau_booking_nonce"])),
            "spbau_booking_submit",
        );

    $redirect = wp_get_referer();
    if (!$redirect) {
        $redirect = home_url("/");
    }
    $redirect = $append_fragment($redirect, "booking");

    if (!$nonce_ok) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "booking_form_status" => "error",
                    "booking_form_message" => "Ошибка безопасности формы.",
                ],
                $redirect,
            ),
        );
        exit();
    }

    $name = isset($_POST["booking_name"])
        ? sanitize_text_field(wp_unslash($_POST["booking_name"]))
        : "";
    $phone_raw = isset($_POST["booking_phone"])
        ? sanitize_text_field(wp_unslash($_POST["booking_phone"]))
        : "";
    $contact = isset($_POST["booking_contact"])
        ? sanitize_key(wp_unslash($_POST["booking_contact"]))
        : "phone";
    $date = isset($_POST["booking_date"])
        ? sanitize_text_field(wp_unslash($_POST["booking_date"]))
        : "";
    $time = isset($_POST["booking_time"])
        ? sanitize_text_field(wp_unslash($_POST["booking_time"]))
        : "";

    $digits = preg_replace("/\D+/", "", $phone_raw);
    $phone = $digits !== "" ? "+" . $digits : "";

    if ($name === "" || strlen($digits) < 10 || $date === "" || $time === "") {
        wp_safe_redirect(
            add_query_arg(
                [
                    "booking_form_status" => "error",
                    "booking_form_message" =>
                        "Заполните имя, телефон и выберите слот.",
                ],
                $redirect,
            ),
        );
        exit();
    }

    if (spbau_booking_slot_is_busy($date, $time)) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "booking_form_status" => "error",
                    "booking_form_message" =>
                        "Этот слот уже занят. Выберите другое время.",
                ],
                $redirect,
            ),
        );
        exit();
    }

    $contact_map = [
        "phone" => "Телефон",
        "whatsapp" => "WhatsApp",
        "telegram" => "Telegram",
        "max" => "MAX",
    ];
    $contact_text = $contact_map[$contact] ?? "Телефон";

    $lead_result = spbau_send_smi_lead_to_bitrix(
        $phone,
        $redirect,
        'Заявка с блока "Booking"',
        "Сайт spb-au / Главная / Booking",
        $name,
        "Желаемая дата: " . $date . "\n" . "Желаемое время: " . $time . "\n" . "Способ связи: " . $contact_text,
    );

    if (!$lead_result["ok"]) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "booking_form_status" => "error",
                    "booking_form_message" => $lead_result["message"],
                ],
                $redirect,
            ),
        );
        exit();
    }

    $calendar_result = spbau_add_bitrix_calendar_event(
        $date,
        $time,
        $name,
        $phone,
        $contact_text,
    );

    $success_message = "Запись принята. Скоро свяжемся с вами.";
    if (!$calendar_result["ok"]) {
        $success_message .= " Лид создан, но календарь не добавлен: " . $calendar_result["message"];
    }

    wp_safe_redirect(
        add_query_arg(
            [
                "booking_form_status" => "success",
                "booking_form_message" => $success_message,
            ],
            $redirect,
        ),
    );
    exit();
}

add_action("admin_post_spbau_booking_submit", "spbau_handle_booking_submit");
add_action(
    "admin_post_nopriv_spbau_booking_submit",
    "spbau_handle_booking_submit",
);

function spbau_handle_faqform_submit(): void
{
    $append_fragment = static function (string $url, string $fragment): string {
        $parts = wp_parse_url($url);
        if (!$parts || empty($parts["scheme"]) || empty($parts["host"])) {
            return $url . "#" . ltrim($fragment, "#");
        }

        $result = $parts["scheme"] . "://" . $parts["host"];
        if (!empty($parts["port"])) {
            $result .= ":" . $parts["port"];
        }
        if (!empty($parts["path"])) {
            $result .= $parts["path"];
        }
        if (!empty($parts["query"])) {
            $result .= "?" . $parts["query"];
        }

        return $result . "#" . ltrim($fragment, "#");
    };

    $nonce_ok = isset($_POST["spbau_faqform_nonce"]) &&
        wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST["spbau_faqform_nonce"])),
            "spbau_faqform_submit",
        );

    $redirect = wp_get_referer();
    if (!$redirect) {
        $redirect = home_url("/");
    }
    $redirect = $append_fragment($redirect, "faq-form");

    if (!$nonce_ok) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "faqform_status" => "error",
                    "faqform_message" => "Ошибка безопасности формы.",
                ],
                $redirect,
            ),
        );
        exit();
    }

    $question = isset($_POST["faqform_question"])
        ? sanitize_text_field(wp_unslash($_POST["faqform_question"]))
        : "";
    $name = isset($_POST["faqform_name"])
        ? sanitize_text_field(wp_unslash($_POST["faqform_name"]))
        : "";
    $phone_raw = isset($_POST["faqform_phone"])
        ? sanitize_text_field(wp_unslash($_POST["faqform_phone"]))
        : "";
    $contact = isset($_POST["faqform_contact"])
        ? sanitize_key(wp_unslash($_POST["faqform_contact"]))
        : "call";
    $agree = !empty($_POST["faqform_agree"]);

    $digits = preg_replace("/\D+/", "", $phone_raw);
    $phone = $digits !== "" ? "+" . $digits : "";

    if (!$agree) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "faqform_status" => "error",
                    "faqform_message" =>
                        "Нужно согласие на обработку данных.",
                ],
                $redirect,
            ),
        );
        exit();
    }

    if ($name === "" || strlen($digits) < 10) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "faqform_status" => "error",
                    "faqform_message" =>
                        "Укажите имя и корректный номер телефона.",
                ],
                $redirect,
            ),
        );
        exit();
    }

    $contact_map = [
        "call" => "Телефон",
        "whatsapp" => "WhatsApp",
        "telegram" => "Telegram",
        "max" => "MAX",
    ];
    $contact_text = $contact_map[$contact] ?? "Телефон";

    $extra = "Форма: FAQ\n" . "Способ связи: " . $contact_text;
    if ($question !== "") {
        $extra .= "\n" . "Вопрос: " . $question;
    }

    $result = spbau_send_smi_lead_to_bitrix(
        $phone,
        $redirect,
        'Заявка с формы "FAQ"',
        "Сайт spb-au / Главная / FAQ Form",
        $name,
        $extra,
    );

    wp_safe_redirect(
        add_query_arg(
            [
                "faqform_status" => $result["ok"] ? "success" : "error",
                "faqform_message" => $result["message"],
            ],
            $redirect,
        ),
    );
    exit();
}

add_action("admin_post_spbau_faqform_submit", "spbau_handle_faqform_submit");
add_action(
    "admin_post_nopriv_spbau_faqform_submit",
    "spbau_handle_faqform_submit",
);

function spbau_handle_lfbanner_submit(): void
{
    $nonce_ok = isset($_POST["spbau_lfbanner_nonce"]) &&
        wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST["spbau_lfbanner_nonce"])),
            "spbau_lfbanner_submit",
        );

    $redirect = wp_get_referer() ?: home_url("/");

    if (!$nonce_ok) {
        wp_safe_redirect(add_query_arg(["lfbanner_status" => "error", "lfbanner_message" => "Ошибка безопасности формы."], $redirect));
        exit();
    }

    $name      = isset($_POST["lf_name"])    ? sanitize_text_field(wp_unslash($_POST["lf_name"]))    : "";
    $phone_raw = isset($_POST["lf_phone"])   ? sanitize_text_field(wp_unslash($_POST["lf_phone"]))   : "";
    $contact   = isset($_POST["lf_contact"]) ? sanitize_key(wp_unslash($_POST["lf_contact"]))        : "call";
    $agree     = !empty($_POST["lf_agree"]);

    $digits = preg_replace("/\D+/", "", $phone_raw);
    $phone  = $digits !== "" ? "+" . $digits : "";

    if (!$agree) {
        wp_safe_redirect(add_query_arg(["lfbanner_status" => "error", "lfbanner_message" => "Нужно согласие на обработку данных."], $redirect));
        exit();
    }

    if ($name === "" || strlen($digits) < 10) {
        wp_safe_redirect(add_query_arg(["lfbanner_status" => "error", "lfbanner_message" => "Укажите имя и корректный номер телефона."], $redirect));
        exit();
    }

    $contact_map  = ["call" => "Телефон", "whatsapp" => "WhatsApp", "telegram" => "Telegram", "max" => "MAX", "email" => "Email"];
    $contact_text = $contact_map[$contact] ?? "Телефон";
    $extra        = "Форма: Программа лояльности\nСпособ связи: " . $contact_text;

    $result = spbau_send_smi_lead_to_bitrix(
        $phone,
        $redirect,
        'Заявка с формы "Программа лояльности"',
        "Сайт spb-au / Программа лояльности",
        $name,
        $extra,
    );

    wp_safe_redirect(add_query_arg(["lfbanner_status" => $result["ok"] ? "success" : "error", "lfbanner_message" => $result["message"]], $redirect));
    exit();
}

add_action("admin_post_spbau_lfbanner_submit",        "spbau_handle_lfbanner_submit");
add_action("admin_post_nopriv_spbau_lfbanner_submit", "spbau_handle_lfbanner_submit");

function spbau_handle_footer_form_submit(): void
{
    $append_fragment = static function (string $url, string $fragment): string {
        $parts = wp_parse_url($url);
        if (!$parts || empty($parts["scheme"]) || empty($parts["host"])) {
            return $url . "#" . ltrim($fragment, "#");
        }

        $result = $parts["scheme"] . "://" . $parts["host"];
        if (!empty($parts["port"])) {
            $result .= ":" . $parts["port"];
        }
        if (!empty($parts["path"])) {
            $result .= $parts["path"];
        }
        if (!empty($parts["query"])) {
            $result .= "?" . $parts["query"];
        }

        return $result . "#" . ltrim($fragment, "#");
    };

    $nonce_ok = isset($_POST["spbau_footer_form_nonce"]) &&
        wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST["spbau_footer_form_nonce"])),
            "spbau_footer_form_submit",
        );

    $redirect = wp_get_referer();
    if (!$redirect) {
        $redirect = home_url("/");
    }
    $redirect = $append_fragment($redirect, "site-footer");

    if (!$nonce_ok) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "footer_form_status" => "error",
                    "footer_form_message" => "Ошибка безопасности формы.",
                ],
                $redirect,
            ),
        );
        exit();
    }

    $phone_raw = isset($_POST["phone"])
        ? sanitize_text_field(wp_unslash($_POST["phone"]))
        : "";
    $agree = !empty($_POST["consent"]);
    $digits = preg_replace("/\D+/", "", $phone_raw);
    $phone = $digits !== "" ? "+" . $digits : "";

    if (!$agree) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "footer_form_status" => "error",
                    "footer_form_message" =>
                        "Нужно согласие на обработку данных.",
                ],
                $redirect,
            ),
        );
        exit();
    }

    if (strlen($digits) < 10) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "footer_form_status" => "error",
                    "footer_form_message" =>
                        "Укажите корректный номер телефона.",
                ],
                $redirect,
            ),
        );
        exit();
    }

    $result = spbau_send_smi_lead_to_bitrix(
        $phone,
        $redirect,
        'Заявка с формы "Footer"',
        "Сайт spb-au / Footer",
    );

    wp_safe_redirect(
        add_query_arg(
            [
                "footer_form_status" => $result["ok"] ? "success" : "error",
                "footer_form_message" => $result["message"],
            ],
            $redirect,
        ),
    );
    exit();
}

add_action(
    "admin_post_spbau_footer_form_submit",
    "spbau_handle_footer_form_submit",
);
add_action(
    "admin_post_nopriv_spbau_footer_form_submit",
    "spbau_handle_footer_form_submit",
);

// ── Consult Modal Form ────────────────────────────────────────
function spbau_handle_consultmodal_submit(): void
{
    $nonce_ok = isset($_POST["spbau_consultmodal_nonce"]) &&
        wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST["spbau_consultmodal_nonce"])),
            "spbau_consultmodal_submit",
        );

    $redirect = isset($_POST["redirect_url"])
        ? esc_url_raw(wp_unslash($_POST["redirect_url"]))
        : "";
    if (!$redirect) {
        $redirect = wp_get_referer() ?: home_url("/");
    }

    if (!$nonce_ok) {
        wp_safe_redirect(add_query_arg(["cm_status" => "error", "cm_message" => "Ошибка безопасности формы."], $redirect));
        exit();
    }

    $name      = isset($_POST["cm_name"])    ? sanitize_text_field(wp_unslash($_POST["cm_name"]))    : "";
    $phone_raw = isset($_POST["cm_phone"])   ? sanitize_text_field(wp_unslash($_POST["cm_phone"]))   : "";
    $contact   = isset($_POST["cm_contact"]) ? sanitize_key(wp_unslash($_POST["cm_contact"]))        : "call";
    $agree     = !empty($_POST["cm_agree"]);

    $digits = preg_replace("/\D+/", "", $phone_raw);
    $phone  = $digits !== "" ? "+" . $digits : "";

    if (!$agree) {
        wp_safe_redirect(add_query_arg(["cm_status" => "error", "cm_message" => "Нужно согласие на обработку данных."], $redirect));
        exit();
    }

    if ($name === "" || strlen($digits) < 10) {
        wp_safe_redirect(add_query_arg(["cm_status" => "error", "cm_message" => "Укажите имя и корректный номер телефона."], $redirect));
        exit();
    }

    $contact_map  = ["call" => "Телефон", "whatsapp" => "WhatsApp", "telegram" => "Telegram", "max" => "MAX"];
    $contact_text = $contact_map[$contact] ?? "Телефон";
    $extra        = "Форма: Бесплатная консультация (попап)\nСпособ связи: " . $contact_text;

    $result = spbau_send_smi_lead_to_bitrix(
        $phone,
        $redirect,
        'Заявка на бесплатную консультацию',
        "Сайт spb-au / Попап «Решить мою проблему»",
        $name,
        $extra,
    );

    wp_safe_redirect(add_query_arg(["cm_status" => $result["ok"] ? "success" : "error", "cm_message" => $result["message"]], $redirect));
    exit();
}

add_action("admin_post_spbau_consultmodal_submit",        "spbau_handle_consultmodal_submit");
add_action("admin_post_nopriv_spbau_consultmodal_submit", "spbau_handle_consultmodal_submit");

function spbau_handle_quiz_submit(): void
{
    $append_fragment = static function (string $url, string $fragment): string {
        $parts = wp_parse_url($url);
        if (!$parts || empty($parts["scheme"]) || empty($parts["host"])) {
            return $url . "#" . ltrim($fragment, "#");
        }

        $result = $parts["scheme"] . "://" . $parts["host"];
        if (!empty($parts["port"])) {
            $result .= ":" . $parts["port"];
        }
        if (!empty($parts["path"])) {
            $result .= $parts["path"];
        }
        if (!empty($parts["query"])) {
            $result .= "?" . $parts["query"];
        }

        return $result . "#" . ltrim($fragment, "#");
    };

    $quiz_context = isset($_POST["quiz_context"])
        ? sanitize_key(wp_unslash($_POST["quiz_context"]))
        : "main";

    $nonce_ok = isset($_POST["spbau_quiz_nonce"]) &&
        wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST["spbau_quiz_nonce"])),
            "spbau_quiz_submit",
        );

    $redirect = wp_get_referer();
    if (!$redirect) {
        $redirect = home_url("/");
    }
    if ($quiz_context === "main") {
        $redirect = $append_fragment($redirect, "calc-quiz");
    }

    if (!$nonce_ok) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "quiz_form_status" => "error",
                    "quiz_form_message" => "Ошибка безопасности формы.",
                ],
                $redirect,
            ),
        );
        exit();
    }

    $name = isset($_POST["quiz_name"])
        ? sanitize_text_field(wp_unslash($_POST["quiz_name"]))
        : "";
    $phone_raw = isset($_POST["quiz_phone"])
        ? sanitize_text_field(wp_unslash($_POST["quiz_phone"]))
        : "";
    $agree = !empty($_POST["quiz_agree"]);
    $quiz_title = isset($_POST["quiz_title"])
        ? sanitize_text_field(wp_unslash($_POST["quiz_title"]))
        : "Квиз";
    $quiz_id = isset($_POST["quiz_id"]) ? (int) $_POST["quiz_id"] : 0;
    $answers = isset($_POST["quiz_answers"])
        ? sanitize_textarea_field(wp_unslash($_POST["quiz_answers"]))
        : "";
    $target_url = isset($_POST["quiz_target_url"])
        ? esc_url_raw(wp_unslash($_POST["quiz_target_url"]))
        : "";

    $digits = preg_replace("/\D+/", "", $phone_raw);
    $phone = $digits !== "" ? "+" . $digits : "";

    if ($name === "" || strlen($digits) < 10) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "quiz_form_status" => "error",
                    "quiz_form_message" => "Укажите имя и корректный телефон.",
                ],
                $redirect,
            ),
        );
        exit();
    }

    if (!$agree) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "quiz_form_status" => "error",
                    "quiz_form_message" => "Нужно согласие на обработку данных.",
                ],
                $redirect,
            ),
        );
        exit();
    }

    if ($quiz_title === "" && $quiz_id > 0) {
        $post_title = get_the_title($quiz_id);
        if (is_string($post_title) && $post_title !== "") {
            $quiz_title = $post_title;
        }
    }
    if ($quiz_title === "") {
        $quiz_title = "Квиз";
    }

    $context_map = [
        "main" => "основной блок",
        "widget" => "виджет в сайдбаре",
    ];
    $context_text = $context_map[$quiz_context] ?? "основной блок";

    $extra = "Форма: Квиз\n" . "Контекст: " . $context_text;
    if ($quiz_id > 0) {
        $extra .= "\n" . "ID квиза: " . $quiz_id;
    }
    if ($answers !== "") {
        $extra .= "\n" . "Ответы:\n" . $answers;
    }

    $result = spbau_send_smi_lead_to_bitrix(
        $phone,
        $redirect,
        'Заявка с квиза "' . $quiz_title . '"',
        "Сайт spb-au / Квиз",
        $name,
        $extra,
    );

    if (!$result["ok"]) {
        wp_safe_redirect(
            add_query_arg(
                [
                    "quiz_form_status" => "error",
                    "quiz_form_message" => $result["message"],
                ],
                $redirect,
            ),
        );
        exit();
    }

    if ($target_url !== "" && $target_url !== "#") {
        wp_redirect($target_url);
        exit();
    }

    wp_safe_redirect(
        add_query_arg(
            [
                "quiz_form_status" => "success",
                "quiz_form_message" => $result["message"],
            ],
            $redirect,
        ),
    );
    exit();
}

add_action("admin_post_spbau_quiz_submit", "spbau_handle_quiz_submit");
add_action("admin_post_nopriv_spbau_quiz_submit", "spbau_handle_quiz_submit");

// ── Import News ──────────────────────────────────────────────
add_action('admin_menu', static function (): void {
    add_management_page(
        'Импорт новостей',
        'Импорт новостей',
        'manage_options',
        'spbau-import-news',
        'spbau_import_news_page',
    );
});

function spbau_import_news_page(): void {
    if (!current_user_can('manage_options')) return;

    $message = '';

    if (isset($_POST['spbau_import_nonce']) && wp_verify_nonce($_POST['spbau_import_nonce'], 'spbau_import')) {
        if (!empty($_FILES['json_file']['tmp_name'])) {
            $json = file_get_contents($_FILES['json_file']['tmp_name']);
            $data = json_decode($json, true);

            if (!$data || empty($data['items'])) {
                $message = '<div class="notice notice-error"><p>Ошибка: неверный формат JSON.</p></div>';
            } else {
                $imported = 0;
                $skipped  = 0;

                foreach ($data['items'] as $item) {
                    $slug = sanitize_title($item['slug'] ?? $item['title']);

                    // Проверяем — уже есть такой пост?
                    $existing = get_page_by_path($slug, OBJECT, 'post');
                    if ($existing) {
                        spbau_assign_post_categories_from_item((int) $existing->ID, $item);
                        $skipped++;
                        continue;
                    }

                    $post_id = wp_insert_post([
                        'post_title'   => wp_strip_all_tags($item['title']),
                        'post_name'    => $slug,
                        'post_content' => $item['content_html'] ?? '',
                        'post_status'  => 'publish',
                        'post_type'    => 'post',
                    ]);

                    if (is_wp_error($post_id)) {
                        continue;
                    }

                    // Загружаем картинки в галерею
                    $all_images = array_filter(array_merge(
                        isset($item['featured_image']) ? [$item['featured_image']] : [],
                        $item['images'] ?? []
                    ));
                    $all_images = array_unique($all_images);

                    $attach_ids = [];
                    foreach ($all_images as $img_url) {
                        $id = spbau_import_sideload_image($post_id, $img_url);
                        if ($id) $attach_ids[] = $id;
                    }
                    if ($attach_ids) {
                        update_field('article_gallery', $attach_ids, $post_id);
                        set_post_thumbnail($post_id, $attach_ids[0]);
                    }
                    spbau_assign_post_categories_from_item((int) $post_id, $item);

                    $imported++;
                }

                $message = "<div class='notice notice-success'><p>Импортировано: <b>{$imported}</b>. Пропущено (уже есть): <b>{$skipped}</b>.</p></div>";
            }
        } else {
            $message = '<div class="notice notice-error"><p>Файл не загружен.</p></div>';
        }
    }

    ?>
    <div class="wrap">
        <h1>Импорт новостей</h1>
        <?php echo $message; ?>
        <p>Загрузите JSON-файл со структурой <code>{"items": [...]}</code>.</p>
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('spbau_import', 'spbau_import_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="json_file">JSON файл</label></th>
                    <td><input type="file" name="json_file" id="json_file" accept=".json" required></td>
                </tr>
            </table>
            <?php submit_button('Импортировать'); ?>
        </form>
    </div>
    <?php
}

function spbau_assign_post_categories_from_item(int $post_id, array $item): void {
    $category_names = [];
    if (!empty($item['categories']) && is_array($item['categories'])) {
        $category_names = $item['categories'];
    } elseif (!empty($item['category']) && is_string($item['category'])) {
        $category_names = [$item['category']];
    }

    if (!$category_names) return;

    $cat_ids = [];
    foreach ($category_names as $cat_name) {
        $cat_name = sanitize_text_field(wp_strip_all_tags((string) $cat_name));
        if ($cat_name === '') continue;

        $term = get_term_by('name', $cat_name, 'category');
        if ($term && !is_wp_error($term) && !empty($term->term_id)) {
            $cat_ids[] = (int) $term->term_id;
            continue;
        }

        $created = wp_insert_term($cat_name, 'category');
        if (!is_wp_error($created) && !empty($created['term_id'])) {
            $cat_ids[] = (int) $created['term_id'];
        }
    }

    if ($cat_ids) {
        wp_set_post_categories($post_id, array_values(array_unique($cat_ids)), false);
    }
}

function spbau_import_sideload_image(int $post_id, string $img_url): int {
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $tmp = download_url($img_url);
    if (is_wp_error($tmp)) return 0;

    $file = [
        'name'     => basename(parse_url($img_url, PHP_URL_PATH)) ?: 'image.jpg',
        'tmp_name' => $tmp,
    ];

    $attach_id = media_handle_sideload($file, $post_id);
    return is_wp_error($attach_id) ? 0 : $attach_id;
}

// ── Import Reviews (CSV) ─────────────────────────────────────
add_action("admin_menu", static function (): void {
    add_submenu_page(
        "edit.php?post_type=review",
        "Импорт отзывов",
        "Импорт отзывов",
        "manage_options",
        "spbau-import-reviews",
        "spbau_import_reviews_page",
    );
});

function spbau_import_reviews_page(): void
{
    if (!current_user_can("manage_options")) {
        return;
    }

    $message = "";

    if (
        isset($_POST["spbau_import_reviews_nonce"]) &&
        wp_verify_nonce(
            sanitize_text_field(
                wp_unslash($_POST["spbau_import_reviews_nonce"]),
            ),
            "spbau_import_reviews",
        )
    ) {
        if (empty($_FILES["csv_file"]["tmp_name"])) {
            $message =
                '<div class="notice notice-error"><p>Файл CSV не загружен.</p></div>';
        } else {
            $rows = spbau_reviews_read_csv_rows(
                (string) $_FILES["csv_file"]["tmp_name"],
            );
            if (empty($rows)) {
                $message =
                    '<div class="notice notice-error"><p>CSV пустой или поврежден.</p></div>';
            } else {
                $imported = 0;
                $updated = 0;
                $errors = 0;
                $fetched_text = 0;

                foreach ($rows as $row) {
                    $person_name = sanitize_text_field(
                        (string) ($row["ФИО"] ?? ""),
                    );
                    if ($person_name === "") {
                        continue;
                    }

                    $source_url = esc_url_raw(
                        (string) ($row["Ссылка на текст отзыва"] ?? ""),
                    );
                    $amount_text = sanitize_text_field(
                        (string) ($row["Сумма долга"] ?? ""),
                    );
                    $debts_count = spbau_reviews_parse_int(
                        (string) ($row["Количество долгов"] ?? ""),
                    );
                    $creditors_text = sanitize_textarea_field(
                        (string) ($row["Кредиторы"] ?? ""),
                    );
                    $debt_text = sanitize_textarea_field(
                        (string) ($row["Вид долгов"] ?? ""),
                    );
                    $review_text = (string) ($row["Текст отзыва"] ?? "");

                    if ($review_text === "" && $source_url !== "") {
                        $review_text = spbau_reviews_fetch_google_doc_text(
                            $source_url,
                        );
                        if ($review_text !== "") {
                            $fetched_text++;
                        }
                    }

                    $amount_data = spbau_reviews_parse_amount($amount_text);
                    $amount_min = $amount_data["min"];
                    $amount_max = $amount_data["max"];
                    $amount_range = $amount_data["range"];

                    $post_id = 0;
                    if ($source_url !== "") {
                        $existing_ids = get_posts([
                            "post_type" => "review",
                            "post_status" => "any",
                            "numberposts" => 1,
                            "fields" => "ids",
                            "meta_key" => "review_source_url",
                            "meta_value" => $source_url,
                        ]);
                        if (!empty($existing_ids)) {
                            $post_id = (int) $existing_ids[0];
                        }
                    }

                    if ($post_id > 0) {
                        $result = wp_update_post([
                            "ID" => $post_id,
                            "post_title" => $person_name,
                            "post_status" => "publish",
                        ], true);
                        if (is_wp_error($result)) {
                            $errors++;
                            continue;
                        }
                        $updated++;
                    } else {
                        $result = wp_insert_post([
                            "post_type" => "review",
                            "post_title" => $person_name,
                            "post_status" => "publish",
                        ], true);
                        if (is_wp_error($result)) {
                            $errors++;
                            continue;
                        }
                        $post_id = (int) $result;
                        $imported++;
                    }

                    spbau_reviews_update_meta($post_id, "review_person_name", $person_name);
                    spbau_reviews_update_meta($post_id, "review_amount_text", $amount_text);
                    spbau_reviews_update_meta($post_id, "review_amount_min", $amount_min);
                    spbau_reviews_update_meta($post_id, "review_amount_max", $amount_max);
                    spbau_reviews_update_meta($post_id, "review_amount_range", $amount_range);
                    spbau_reviews_update_meta($post_id, "review_debts_count", $debts_count);
                    spbau_reviews_update_meta($post_id, "review_creditors_text", $creditors_text);
                    spbau_reviews_update_meta($post_id, "review_text", $review_text);
                    spbau_reviews_update_meta($post_id, "review_source_url", $source_url);

                    $debt_terms = [];
                    foreach (spbau_reviews_split_values($debt_text) as $item) {
                        $normalized_debt = spbau_reviews_normalize_debt_label($item);
                        if ($normalized_debt !== "") {
                            $debt_terms[] = $normalized_debt;
                        }
                    }
                    if (!empty($debt_terms)) {
                        wp_set_object_terms(
                            $post_id,
                            array_values(array_unique($debt_terms)),
                            "review_debt_type",
                            false,
                        );
                    } else {
                        wp_set_object_terms($post_id, [], "review_debt_type", false);
                    }

                    $creditor_types = spbau_reviews_detect_creditor_types(
                        $creditors_text,
                    );
                    if (!empty($creditor_types)) {
                        wp_set_object_terms(
                            $post_id,
                            array_values(array_unique($creditor_types)),
                            "review_creditor_type",
                            false,
                        );
                    } else {
                        wp_set_object_terms(
                            $post_id,
                            ["Прочие"],
                            "review_creditor_type",
                            false,
                        );
                    }
                }

                $message =
                    "<div class='notice notice-success'><p>" .
                    "Создано: <b>{$imported}</b>, обновлено: <b>{$updated}</b>, " .
                    "ошибок: <b>{$errors}</b>, подтянуто текстов из Google Docs: <b>{$fetched_text}</b>." .
                    "</p></div>";
            }
        }
    }
    ?>
    <div class="wrap">
        <h1>Импорт отзывов (CSV)</h1>
        <?php echo $message; ?>
        <p>Ожидаемые колонки: <code>Ссылка на текст отзыва, ФИО, Сумма долга, Количество долгов, Кредиторы, Вид долгов, Текст отзыва</code>.</p>
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field(
                "spbau_import_reviews",
                "spbau_import_reviews_nonce",
            ); ?>
            <table class="form-table">
                <tr>
                    <th><label for="csv_file">CSV файл</label></th>
                    <td><input type="file" name="csv_file" id="csv_file" accept=".csv,text/csv" required></td>
                </tr>
            </table>
            <?php submit_button("Импортировать отзывы"); ?>
        </form>
    </div>
    <?php
}

function spbau_reviews_update_meta(int $post_id, string $field_name, $value): void
{
    if (function_exists("update_field")) {
        update_field($field_name, $value, $post_id);
        return;
    }
    update_post_meta($post_id, $field_name, $value);
}

function spbau_reviews_read_csv_rows(string $path): array
{
    $raw = @file_get_contents($path);
    if ($raw === false || $raw === "") {
        return [];
    }

    if (function_exists("mb_detect_encoding")) {
        $encoding = mb_detect_encoding(
            $raw,
            ["UTF-8", "Windows-1251", "CP1251", "ISO-8859-1"],
            true,
        );
        if ($encoding && strtoupper($encoding) !== "UTF-8") {
            $raw = mb_convert_encoding($raw, "UTF-8", $encoding);
        }
    }
    $raw = preg_replace("/^\xEF\xBB\xBF/", "", $raw);
    if (!is_string($raw)) {
        return [];
    }

    $first_line_end = strcspn($raw, "\r\n");
    $first_line = substr($raw, 0, $first_line_end);
    $candidates = [",", ";", "\t"];
    $delimiter = ",";
    $max_count = -1;
    foreach ($candidates as $candidate) {
        $count = substr_count($first_line, $candidate);
        if ($count > $max_count) {
            $max_count = $count;
            $delimiter = $candidate;
        }
    }

    $stream = fopen("php://temp", "r+");
    if ($stream === false) {
        return [];
    }
    fwrite($stream, $raw);
    rewind($stream);

    $header = fgetcsv($stream, 0, $delimiter);
    if (!is_array($header)) {
        fclose($stream);
        return [];
    }
    $header = array_map(
        static function ($value) {
            return trim((string) $value);
        },
        $header,
    );

    $rows = [];
    while (($row = fgetcsv($stream, 0, $delimiter)) !== false) {
        $has_non_empty = false;
        foreach ($row as $cell) {
            if (trim((string) $cell) !== "") {
                $has_non_empty = true;
                break;
            }
        }
        if (!$has_non_empty) {
            continue;
        }

        $row = array_pad($row, count($header), "");
        $assoc = array_combine($header, array_slice($row, 0, count($header)));
        if (is_array($assoc)) {
            $rows[] = $assoc;
        }
    }
    fclose($stream);

    return $rows;
}

function spbau_reviews_parse_int(string $value): int
{
    if (preg_match("/\d+/", $value, $m)) {
        return (int) $m[0];
    }
    return 0;
}

function spbau_reviews_split_values(string $value): array
{
    $value = trim($value);
    if ($value === "") {
        return [];
    }
    $parts = preg_split(
        "/\s*(?:\r\n|\r|\n|,|;|\+|\/|\\\\|\s+и\s+)\s*/iu",
        $value,
    );
    if (!is_array($parts)) {
        return [];
    }
    $result = [];
    foreach ($parts as $part) {
        $part = trim($part, " \t\n\r\0\x0B\"'()[]{}.");
        if ($part !== "" && !in_array($part, $result, true)) {
            $result[] = $part;
        }
    }
    return $result;
}

function spbau_reviews_normalize_debt_label(string $value): string
{
    $raw = trim($value);
    if ($raw === "") {
        return "";
    }
    $lc = spbau_reviews_lower($raw);
    $lc = str_replace("ё", "е", $lc);

    if (strpos($lc, "ипотек") !== false) {
        return "Ипотека";
    }
    if (strpos($lc, "автокредит") !== false) {
        return "Автокредит";
    }
    if (strpos($lc, "микрозайм") !== false || strpos($lc, "мфо") !== false) {
        return "Микрозаймы";
    }
    if (
        (strpos($lc, "кредит") !== false && strpos($lc, "карт") !== false) ||
        strpos($lc, "кредитная карта") !== false ||
        strpos($lc, "кредитна карта") !== false
    ) {
        return "Кредитные карты";
    }
    if (
        strpos($lc, "потреб") !== false ||
        in_array($lc, ["кредиты", "потребительский"], true)
    ) {
        return "Потребительские кредиты";
    }
    if (strpos($lc, "рассроч") !== false) {
        return "Рассрочка";
    }
    if (strpos($lc, "налог") !== false) {
        return "Налоговая задолженность";
    }
    if (strpos($lc, "жкх") !== false || strpos($lc, "жку") !== false) {
        return "Задолженность по ЖКХ";
    }
    if (strpos($lc, "поручител") !== false) {
        return "Поручительство";
    }
    if (strpos($lc, "мошенн") !== false) {
        return "Мошеннические кредиты";
    }
    if (strpos($lc, "строительств") !== false) {
        return "Кредит на строительство";
    }

    return spbau_reviews_title($raw);
}

function spbau_reviews_detect_creditor_types(string $creditors_text): array
{
    $types = [];
    foreach (spbau_reviews_split_values($creditors_text) as $item) {
        $lc = spbau_reviews_lower($item);
        $lc = str_replace(["ё", "«", "»", '"', "'"], ["е", "", "", "", ""], $lc);

        if (
            strpos($lc, "мфо") !== false ||
            strpos($lc, "микрозайм") !== false ||
            strpos($lc, "корона") !== false ||
            strpos($lc, "быстрозайм") !== false ||
            strpos($lc, "турбозайм") !== false ||
            strpos($lc, "аденьги") !== false
        ) {
            $types[] = "МФО";
            continue;
        }

        if (
            (strpos($lc, "пко") !== false ||
                strpos($lc, "коллект") !== false ||
                strpos($lc, "агентств") !== false ||
                strpos($lc, "авд") !== false ||
                strpos($lc, "цду") !== false ||
                strpos($lc, "траст") !== false) &&
            strpos($lc, "банк") === false
        ) {
            $types[] = "Коллекторы";
            continue;
        }

        if (strpos($lc, "жкх") !== false || strpos($lc, "жку") !== false) {
            $types[] = "ЖКХ";
            continue;
        }

        if (strpos($lc, "фнс") !== false || strpos($lc, "налог") !== false) {
            $types[] = "Налоговая";
            continue;
        }

        if (
            strpos($lc, "сбер") !== false ||
            strpos($lc, "тинь") !== false ||
            strpos($lc, "т-банк") !== false ||
            strpos($lc, "тбанк") !== false ||
            strpos($lc, "альфа") !== false ||
            strpos($lc, "совком") !== false ||
            strpos($lc, "втб") !== false ||
            strpos($lc, "уралсиб") !== false ||
            strpos($lc, "мтс") !== false ||
            strpos($lc, "почта") !== false ||
            strpos($lc, "газпром") !== false ||
            strpos($lc, "райф") !== false ||
            strpos($lc, "ренес") !== false ||
            strpos($lc, "русск") !== false ||
            strpos($lc, "отп") !== false ||
            strpos($lc, "азиат") !== false ||
            strpos($lc, "санкт") !== false ||
            strpos($lc, "озон") !== false ||
            strpos($lc, "яндекс") !== false ||
            strpos($lc, "банк") !== false
        ) {
            $types[] = "Банки";
            continue;
        }

        $types[] = "Прочие";
    }

    $types = array_values(array_unique($types));
    if (empty($types)) {
        return ["Прочие"];
    }
    return $types;
}

function spbau_reviews_parse_amount(string $value): array
{
    $value = trim(spbau_reviews_lower($value));
    if ($value === "") {
        return ["min" => 0, "max" => 0, "range" => "all"];
    }

    $value = str_replace(["–", "—"], "-", $value);

    $min = 0;
    $max = 0;

    if (strpos($value, "-") !== false) {
        $parts = array_values(array_filter(array_map("trim", explode("-", $value))));
        if (count($parts) >= 2) {
            $n1 = spbau_reviews_parse_amount_part($parts[0]);
            $n2 = spbau_reviews_parse_amount_part($parts[1]);
            if ($n1 > 0 && $n2 > 0) {
                $min = min($n1, $n2);
                $max = max($n1, $n2);
            }
        }
    }

    if ($min === 0 || $max === 0) {
        $single = spbau_reviews_parse_amount_part($value);
        if ($single > 0) {
            $min = $single;
            $max = $single;
        }
    }

    $range = "all";
    if ($min > 0 || $max > 0) {
        if ($max >= 350000 && $min <= 500000) {
            $range = "350-500";
        } elseif ($max >= 500000 && $min <= 1000000) {
            $range = "500-1000";
        } elseif ($max > 1000000 || $min > 1000000) {
            $range = "1000plus";
        }
    }

    return ["min" => $min, "max" => $max, "range" => $range];
}

function spbau_reviews_parse_amount_part(string $value): int
{
    $value = spbau_reviews_lower($value);
    $value = str_replace(["–", "—"], "-", $value);

    $multiplier = 1;
    if (
        strpos($value, "млн") !== false ||
        strpos($value, "миллион") !== false
    ) {
        $multiplier = 1000000;
    } elseif (
        strpos($value, "тыс") !== false ||
        strpos($value, "т.р") !== false ||
        strpos($value, "тр") !== false ||
        preg_match("/\d+\s*к/u", $value)
    ) {
        $multiplier = 1000;
    }

    if (!preg_match("/\d[\d\s\.,]*/u", $value, $m)) {
        return 0;
    }
    $token = str_replace(" ", "", (string) $m[0]);
    if (substr_count($token, ".") >= 2 && strpos($token, ",") === false) {
        $token = str_replace(".", "", $token);
    }
    $token = str_replace(",", ".", $token);
    $number = (float) $token;
    if ($number <= 0) {
        return 0;
    }

    if ($multiplier > 1) {
        return (int) round($number * $multiplier);
    }
    return (int) round($number);
}

function spbau_reviews_fetch_google_doc_text(string $url): string
{
    $url = trim($url);
    if ($url === "") {
        return "";
    }
    if (!preg_match("#/document/d/([a-zA-Z0-9_-]+)#", $url, $m)) {
        return "";
    }
    $doc_id = $m[1];
    $export_url = "https://docs.google.com/document/d/" . $doc_id . "/export?format=txt";

    $response = wp_remote_get($export_url, [
        "timeout" => 20,
        "headers" => ["User-Agent" => "Mozilla/5.0"],
    ]);
    if (is_wp_error($response)) {
        return "";
    }
    $code = (int) wp_remote_retrieve_response_code($response);
    if ($code < 200 || $code >= 300) {
        return "";
    }
    $body = (string) wp_remote_retrieve_body($response);
    if ($body === "") {
        return "";
    }
    $probe = ltrim(spbau_reviews_lower(substr($body, 0, 512)));
    if (strpos($probe, "<html") === 0 || strpos($probe, "<!doctype html") === 0) {
        return "";
    }
    $body = preg_replace("/\r\n|\r/u", "\n", $body);
    $body = preg_replace("/\n{3,}/u", "\n\n", (string) $body);
    return trim((string) $body);
}

function spbau_reviews_lower(string $value): string
{
    if (function_exists("mb_strtolower")) {
        return mb_strtolower($value, "UTF-8");
    }
    return strtolower($value);
}

function spbau_reviews_title(string $value): string
{
    if (function_exists("mb_convert_case")) {
        return mb_convert_case($value, MB_CASE_TITLE, "UTF-8");
    }
    return ucwords(strtolower($value));
}
