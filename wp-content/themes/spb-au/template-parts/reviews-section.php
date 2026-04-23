<?php
if (!defined("ABSPATH")) {
    exit();
}

$title = $args["title"] ?? "Отзывы о нашей работе";

$reviews = new WP_Query([
    "post_type" => "review",
    "posts_per_page" => -1,
    "post_status" => "publish",
    "orderby" => "date",
    "order" => "DESC",
]);

$debt_types = get_terms([
    "taxonomy" => "review_debt_type",
    "hide_empty" => false,
]);
$creditor_types = get_terms([
    "taxonomy" => "review_creditor_type",
    "hide_empty" => false,
]);
/**
 * @return array<int, string>
 */
$parse_creditors = static function (string $value): array {
    $items = preg_split("/[\n\r,;]+/u", $value) ?: [];
    $result = [];
    foreach ($items as $item) {
        $item = trim(preg_replace("/\s+/u", " ", (string) $item) ?: "");
        if ($item === "") {
            continue;
        }
        $result[] = $item;
    }
    return array_values(array_unique($result));
};
/**
 * @return string
 */
$creditor_key = static function (string $value): string {
    $value = trim($value);
    if ($value === "") {
        return "";
    }
    if (function_exists("mb_strtolower")) {
        $value = mb_strtolower($value, "UTF-8");
    } else {
        $value = strtolower($value);
    }
    return substr(md5($value), 0, 12);
};

if (is_wp_error($debt_types) || !is_array($debt_types)) {
    $debt_types = [];
}
if (is_wp_error($creditor_types) || !is_array($creditor_types)) {
    $creditor_types = [];
}

$creditor_options = [];
if (!empty($reviews->posts)) {
    foreach ($reviews->posts as $review_post) {
        $creditors_raw = (string) get_field(
            "review_creditors_text",
            $review_post->ID,
        );
        if ($creditors_raw === "") {
            continue;
        }
        foreach ($parse_creditors($creditors_raw) as $creditor_item) {
            $slug = $creditor_key($creditor_item);
            if ($slug === "" || isset($creditor_options[$slug])) {
                continue;
            }
            $creditor_options[$slug] = $creditor_item;
        }
    }
    asort($creditor_options, SORT_NATURAL | SORT_FLAG_CASE);
}

$per_page = 5;
$i = 0;
?>
<section class="cases-section reviews-section">
    <div class="cases-inner">
    <h2 class="cases-title"><?php echo esc_html($title); ?></h2>
    <div class="cases-layout">

        <div class="cases-filter-toggle-wrap">
            <button class="cases-filter-toggle" type="button">Фильтры</button>
        </div>

        <div class="cases-filter-overlay"></div>

        <aside class="cases-filter">
            <button class="cases-filter-close" type="button">Закрыть</button>

            <div class="filter-group">
                <div class="filter-group__title">Сумма долга</div>
                <div class="filter-group__items">
                    <label class="filter-radio"><input type="radio" name="amount" value="all" checked> Все суммы</label>
                    <label class="filter-radio"><input type="radio" name="amount" value="350-500"> 350 000–500 000 рублей</label>
                    <label class="filter-radio"><input type="radio" name="amount" value="500-1000"> 500 000–1 000 000 рублей</label>
                    <label class="filter-radio"><input type="radio" name="amount" value="1000plus"> Более 1 000 000 рублей</label>
                </div>
            </div>

            <div class="filter-group">
                <div class="filter-group__title">Вид долгов</div>
                <div class="filter-group__items">
                    <label class="filter-radio"><input type="radio" name="debt" value="all" checked> Все</label>
                    <?php foreach ($debt_types as $term): ?>
                    <label class="filter-radio">
                        <input type="radio" name="debt" value="<?php echo esc_attr($term->slug); ?>">
                        <?php echo esc_html($term->name); ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="filter-group">
                <div class="filter-group__title">Типы кредиторов</div>
                <div class="filter-group__items">
                    <label class="filter-radio"><input type="radio" name="creditorType" value="all" checked> Все</label>
                    <?php foreach ($creditor_types as $term): ?>
                    <label class="filter-radio">
                        <input type="radio" name="creditorType" value="<?php echo esc_attr($term->slug); ?>">
                        <?php echo esc_html($term->name); ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (!empty($creditor_options)): ?>
            <div class="filter-group">
                <div class="filter-group__title">Кредиторы</div>
                <div class="filter-group__items">
                    <label class="filter-radio"><input type="radio" name="creditor" value="all" checked> Все</label>
                    <?php foreach ($creditor_options as $creditor_slug => $creditor_label): ?>
                    <label class="filter-radio">
                        <input type="radio" name="creditor" value="<?php echo esc_attr(
                            $creditor_slug,
                        ); ?>">
                        <?php echo esc_html($creditor_label); ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <button class="cases-reset">Сбросить настройки</button>
        </aside>

        <div class="cases-list">
            <?php if ($reviews->have_posts()):
                while ($reviews->have_posts()):
                    $reviews->the_post();
                    $i++;

                    $person_name = get_field("review_person_name");
                    if (!$person_name) {
                        $person_name = get_the_title();
                    }

                    $amount_text = get_field("review_amount_text");
                    $amount_range = get_field("review_amount_range") ?: "all";
                    $debts_count = (int) get_field("review_debts_count");
                    $creditors_text = get_field("review_creditors_text");
                    $media_source = (string) get_field("review_media_source");
                    $media_video = get_field("review_video");
                    $media_video_file = get_field("review_video_file");
                    $media_photo = get_field("review_media_photo");
                    $review_photo = get_field("review_photo");

                    $placeholder_photo =
                        get_template_directory_uri() .
                        "/images/case-placeholder.svg";

                    $review_photo_url = "";
                    $review_photo_alt = "";
                    if (is_array($review_photo) && !empty($review_photo["url"])) {
                        $review_photo_url = (string) $review_photo["url"];
                        $review_photo_alt = (string) ($review_photo["alt"] ?? "");
                    } elseif (is_numeric($review_photo)) {
                        $review_photo_url = (string) wp_get_attachment_url(
                            (int) $review_photo,
                        );
                    } elseif (has_post_thumbnail()) {
                        $thumb_id = (int) get_post_thumbnail_id();
                        $thumb_src = wp_get_attachment_image_src(
                            $thumb_id,
                            "full",
                        );
                        if (is_array($thumb_src) && !empty($thumb_src[0])) {
                            $review_photo_url = (string) $thumb_src[0];
                            $review_photo_alt = (string) get_post_meta(
                                $thumb_id,
                                "_wp_attachment_image_alt",
                                true,
                            );
                        }
                    }
                    if ($review_photo_url === "") {
                        $review_photo_url = $placeholder_photo;
                    }
                    if ($review_photo_alt === "") {
                        $review_photo_alt = $person_name;
                    }

                    $media_photo_url = "";
                    $media_photo_alt = "";
                    if (is_array($media_photo) && !empty($media_photo["url"])) {
                        $media_photo_url = (string) $media_photo["url"];
                        $media_photo_alt = (string) ($media_photo["alt"] ?? "");
                    } elseif (is_numeric($media_photo)) {
                        $media_photo_url = (string) wp_get_attachment_url(
                            (int) $media_photo,
                        );
                    }

                    $video_file_url = "";
                    $video_file_type = "";
                    if (
                        is_array($media_video_file) &&
                        !empty($media_video_file["url"])
                    ) {
                        $video_file_url = (string) $media_video_file["url"];
                        $video_file_type = (string) ($media_video_file["mime_type"] ??
                            "");
                    } elseif (is_numeric($media_video_file)) {
                        $video_file_url = (string) wp_get_attachment_url(
                            (int) $media_video_file,
                        );
                        $video_file_type = (string) get_post_mime_type(
                            (int) $media_video_file,
                        );
                    } elseif (
                        is_string($media_video_file) &&
                        $media_video_file !== ""
                    ) {
                        $video_file_url = $media_video_file;
                    }
                    if ($video_file_url !== "" && $video_file_type === "") {
                        $video_file_type_data = wp_check_filetype($video_file_url);
                        $video_file_type = (string) ($video_file_type_data["type"] ??
                            "");
                    }

                    $debt_terms_post = get_the_terms(get_the_ID(), "review_debt_type");
                    $creditor_terms_post = get_the_terms(
                        get_the_ID(),
                        "review_creditor_type",
                    );

                    $debt_slugs =
                        $debt_terms_post && !is_wp_error($debt_terms_post)
                            ? implode(",", wp_list_pluck($debt_terms_post, "slug"))
                            : "all";
                    $creditor_type_slugs =
                        $creditor_terms_post && !is_wp_error($creditor_terms_post)
                            ? implode(",", wp_list_pluck($creditor_terms_post, "slug"))
                            : "all";

                    $debt_names =
                        $debt_terms_post && !is_wp_error($debt_terms_post)
                            ? implode(", ", wp_list_pluck($debt_terms_post, "name"))
                            : "";
                    $creditor_type_names =
                        $creditor_terms_post && !is_wp_error($creditor_terms_post)
                            ? implode(", ", wp_list_pluck($creditor_terms_post, "name"))
                            : "";
                    $creditor_slugs = "all";
                    if (!empty($creditors_text)) {
                        $creditor_slug_list = [];
                        foreach (
                            $parse_creditors((string) $creditors_text)
                            as $creditor_item
                        ) {
                            $slug = $creditor_key($creditor_item);
                            if ($slug !== "") {
                                $creditor_slug_list[] = $slug;
                            }
                        }
                        $creditor_slug_list = array_values(
                            array_unique($creditor_slug_list),
                        );
                        if (!empty($creditor_slug_list)) {
                            $creditor_slugs = implode(",", $creditor_slug_list);
                        }
                    }

                    if (
                        !in_array($media_source, ["photo", "link", "file"], true)
                    ) {
                        if ($video_file_url !== "") {
                            $media_source = "file";
                        } elseif (!empty($media_video)) {
                            $media_source = "link";
                        } else {
                            $media_source = "photo";
                        }
                    }

                    $render_video_file =
                        $media_source === "file" && $video_file_url !== "";
                    $render_video_link =
                        $media_source === "link" && !empty($media_video);
                    $render_media_photo =
                        !$render_video_file && !$render_video_link;
                    $rendered_media_photo_url =
                        $media_photo_url !== ""
                            ? $media_photo_url
                            : $review_photo_url;
                    $rendered_media_photo_alt =
                        $media_photo_alt !== ""
                            ? $media_photo_alt
                            : $review_photo_alt;
                    $has_media =
                        $render_video_file || $render_video_link || $render_media_photo;
                    $hidden_class = $i > $per_page ? " case-card--hidden" : "";
                    ?>
            <article class="case-card<?php echo esc_attr(
                $hidden_class,
            ); ?><?php echo $has_media ? " case-card--media" : ""; ?>"
                data-amount="<?php echo esc_attr($amount_range); ?>"
                data-debt="<?php echo esc_attr($debt_slugs); ?>"
                data-creditor-type="<?php echo esc_attr(
                    $creditor_type_slugs,
                ); ?>"
                data-creditor="<?php echo esc_attr($creditor_slugs); ?>">

                <h3 class="case-card__title"><?php echo esc_html($person_name); ?></h3>

                <?php if ($has_media): ?>
                <div class="case-card__media">
                    <?php if ($render_video_file): ?>
                    <div class="case-card__video">
                        <video controls preload="metadata" playsinline>
                            <source src="<?php echo esc_url(
                                $video_file_url,
                            ); ?>"<?php echo $video_file_type !== ""
    ? ' type="' . esc_attr($video_file_type) . '"'
    : ""; ?>>
                        </video>
                    </div>
                    <?php elseif ($render_video_link): ?>
                    <div class="case-card__video"><?php echo $media_video; ?></div>
                    <?php else: ?>
                    <img src="<?php echo esc_url(
                        $rendered_media_photo_url,
                    ); ?>" alt="<?php echo esc_attr($rendered_media_photo_alt); ?>">
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="case-card__row">
                    <div class="case-card__body">
                        <div class="case-card__meta">
                            <?php if ($amount_text): ?>
                            <div class="case-meta-row">
                                <span class="case-meta-label">Сумма долга:</span>
                                <span class="case-meta-value"><?php echo esc_html($amount_text); ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if ($debts_count > 0): ?>
                            <div class="case-meta-row">
                                <span class="case-meta-label">Количество долгов:</span>
                                <span class="case-meta-value"><?php echo esc_html((string) $debts_count); ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if ($creditor_type_names): ?>
                            <div class="case-meta-row">
                                <span class="case-meta-label">Типы кредиторов:</span>
                                <span class="case-meta-value"><?php echo esc_html($creditor_type_names); ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if ($debt_names): ?>
                            <div class="case-meta-row">
                                <span class="case-meta-label">Вид долгов:</span>
                                <span class="case-meta-value"><?php echo esc_html($debt_names); ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if ($creditors_text): ?>
                            <div class="case-meta-row">
                                <span class="case-meta-label">Кредиторы:</span>
                                <span class="case-meta-value"><?php echo esc_html($creditors_text); ?></span>
                            </div>
                            <?php endif; ?>

                        </div>

                        <a href="<?php the_permalink(); ?>" class="case-card__btn">Подробнее</a>
                    </div>

                    <div class="case-card__photo">
                        <img src="<?php echo esc_url(
                            $review_photo_url,
                        ); ?>" alt="<?php echo esc_attr($review_photo_alt); ?>">
                    </div>
                </div>
            </article>
            <?php
                endwhile;
                wp_reset_postdata();
            endif; ?>
        </div>
    </div>

    <?php if ($reviews->found_posts > $per_page): ?>
    <div class="cases-more-wrap">
        <button class="cases-more-btn" data-shown="<?php echo esc_attr((string) $per_page); ?>">Смотреть ещё</button>
    </div>
    <?php endif; ?>
    </div>
</section>
