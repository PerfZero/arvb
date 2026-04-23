<?php
if (!defined("ABSPATH")) {
    exit();
}

get_header();
the_post();

$person_name = get_field("review_person_name");
if (!$person_name) {
    $person_name = get_the_title();
}

$amount_text = (string) get_field("review_amount_text");
$debts_count = (int) get_field("review_debts_count");
$creditors_text = (string) get_field("review_creditors_text");
$review_text = (string) get_field("review_text");
$source_url = (string) get_field("review_source_url");
$media_source = (string) get_field("review_media_source");
$media_video = get_field("review_video");
$media_video_file = get_field("review_video_file");
$media_photo = get_field("review_media_photo");
$review_photo_field = get_field("review_photo");
if ($review_text === "") {
    $review_text = trim((string) get_the_content());
}

$debt_terms = get_the_terms(get_the_ID(), "review_debt_type");
$creditor_terms = get_the_terms(get_the_ID(), "review_creditor_type");

$debt_names =
    $debt_terms && !is_wp_error($debt_terms)
        ? implode(", ", wp_list_pluck($debt_terms, "name"))
        : "";
$creditor_type_names =
    $creditor_terms && !is_wp_error($creditor_terms)
        ? implode(", ", wp_list_pluck($creditor_terms, "name"))
        : "";

$archive_title = "Отзывы";

$review_photo_url = "";
$review_photo_alt = "";
$placeholder = get_template_directory_uri() . "/images/case-placeholder.svg";
if (
    is_array($review_photo_field) &&
    !empty($review_photo_field["url"])
) {
    $review_photo_url = (string) $review_photo_field["url"];
    $review_photo_alt = (string) ($review_photo_field["alt"] ?? "");
} elseif (is_numeric($review_photo_field)) {
    $review_photo_url = (string) wp_get_attachment_url((int) $review_photo_field);
} elseif (has_post_thumbnail()) {
    $thumb_id = (int) get_post_thumbnail_id();
    $thumb_src = wp_get_attachment_image_src($thumb_id, "full");
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
    $review_photo_url = $placeholder;
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
    $media_photo_url = (string) wp_get_attachment_url((int) $media_photo);
}

$video_file_url = "";
$video_file_type = "";
if (is_array($media_video_file) && !empty($media_video_file["url"])) {
    $video_file_url = (string) $media_video_file["url"];
    $video_file_type = (string) ($media_video_file["mime_type"] ?? "");
} elseif (is_numeric($media_video_file)) {
    $video_file_url = (string) wp_get_attachment_url((int) $media_video_file);
    $video_file_type = (string) get_post_mime_type((int) $media_video_file);
} elseif (is_string($media_video_file) && $media_video_file !== "") {
    $video_file_url = $media_video_file;
}
if ($video_file_url !== "" && $video_file_type === "") {
    $video_file_type_data = wp_check_filetype($video_file_url);
    $video_file_type = (string) ($video_file_type_data["type"] ?? "");
}

if (!in_array($media_source, ["photo", "link", "file"], true)) {
    if ($video_file_url !== "") {
        $media_source = "file";
    } elseif (!empty($media_video)) {
        $media_source = "link";
    } else {
        $media_source = "photo";
    }
}
$render_video_file = $media_source === "file" && $video_file_url !== "";
$render_video_link = $media_source === "link" && !empty($media_video);
$render_media_photo = !$render_video_file && !$render_video_link;
$rendered_media_photo_url =
    $media_photo_url !== "" ? $media_photo_url : $review_photo_url;
$rendered_media_photo_alt =
    $media_photo_alt !== "" ? $media_photo_alt : $review_photo_alt;
$has_media = $render_video_file || $render_video_link || $render_media_photo;

$related = new WP_Query([
    "post_type" => "review",
    "posts_per_page" => 4,
    "post_status" => "publish",
    "post__not_in" => [get_the_ID()],
    "orderby" => "date",
    "order" => "DESC",
]);
?>

<main class="single-case single-review">
    <div class="single-case__inner">

        <?php get_template_part("template-parts/breadcrumb"); ?>
        <h1 class="single-case__archive-title"><?php echo esc_html(
            $archive_title,
        ); ?></h1>

        <div class="single-case__layout">

            <aside class="single-case__sidebar">
                <?php get_template_part("template-parts/calculator-widget"); ?>
            </aside>

            <div class="single-case__content">
                <h2 class="single-case__title"><?php echo esc_html(
                    $person_name,
                ); ?></h2>

                <?php if ($has_media): ?>
                <div class="single-case__media">
                    <?php if ($render_video_file): ?>
                    <video controls preload="metadata" playsinline>
                        <source src="<?php echo esc_url(
                            $video_file_url,
                        ); ?>"<?php echo $video_file_type !== ""
    ? ' type="' . esc_attr($video_file_type) . '"'
    : ""; ?>>
                    </video>
                    <?php elseif ($render_video_link): ?>
                        <?php echo $media_video; ?>
                    <?php else: ?>
                    <img src="<?php echo esc_url(
                        $rendered_media_photo_url,
                    ); ?>" alt="<?php echo esc_attr($rendered_media_photo_alt); ?>">
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="case-card__row single-case__details-row">
                    <div class="case-card__body single-case__details-body">
                        <div class="single-case__meta">
                            <?php if ($amount_text !== ""): ?>
                            <div class="case-meta-row">
                                <span class="case-meta-label">Сумма долга:</span>
                                <span class="case-meta-value"><?php echo esc_html(
                                    $amount_text,
                                ); ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if ($debts_count > 0): ?>
                            <div class="case-meta-row">
                                <span class="case-meta-label">Количество долгов:</span>
                                <span class="case-meta-value"><?php echo esc_html(
                                    (string) $debts_count,
                                ); ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if ($creditor_type_names !== ""): ?>
                            <div class="case-meta-row">
                                <span class="case-meta-label">Типы кредиторов:</span>
                                <span class="case-meta-value"><?php echo esc_html(
                                    $creditor_type_names,
                                ); ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if ($debt_names !== ""): ?>
                            <div class="case-meta-row">
                                <span class="case-meta-label">Вид долгов:</span>
                                <span class="case-meta-value"><?php echo esc_html(
                                    $debt_names,
                                ); ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if ($creditors_text !== ""): ?>
                            <div class="case-meta-row">
                                <span class="case-meta-label">Кредиторы:</span>
                                <span class="case-meta-value"><?php echo esc_html(
                                    $creditors_text,
                                ); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($review_text !== ""): ?>
                        <div class="single-case__review">
                            <p class="single-case__review-label">Текст отзыва:</p>
                            <div class="single-case__review-text"><?php echo wp_kses_post(
                                wpautop($review_text),
                            ); ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if ($source_url !== ""): ?>
                        <a href="<?php echo esc_url(
                            $source_url,
                        ); ?>" class="case-card__btn" target="_blank" rel="noopener">Открыть источник</a>
                        <?php endif; ?>
                    </div>

                    <div class="case-card__photo single-case__photo">
                        <img src="<?php echo esc_url(
                            $review_photo_url,
                        ); ?>" alt="<?php echo esc_attr($review_photo_alt); ?>">
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php if ($related->have_posts()): ?>
    <section class="single-review__related">
        <div class="single-case__inner">
            <h3 class="single-review__related-title">Другие отзывы</h3>
            <div class="single-review__related-grid">
                <?php while ($related->have_posts()):
                    $related->the_post();
                    $related_name = get_field("review_person_name");
                    if (!$related_name) {
                        $related_name = get_the_title();
                    }
                    $related_amount = (string) get_field("review_amount_text");
                    ?>
                <article class="single-review__related-card">
                    <h4 class="single-review__related-name"><?php echo esc_html(
                        $related_name,
                    ); ?></h4>
                    <?php if ($related_amount !== ""): ?>
                    <p class="single-review__related-amount"><?php echo esc_html(
                        $related_amount,
                    ); ?></p>
                    <?php endif; ?>
                    <a class="single-review__related-link" href="<?php the_permalink(); ?>">Читать отзыв</a>
                </article>
                <?php endwhile;
                wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
