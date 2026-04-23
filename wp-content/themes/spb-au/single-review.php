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
$read_source = $review_text !== "" ? $review_text : get_the_content();
$read_time = spbau_reading_time($read_source);
$minute_label = "минут";
if ($read_time % 10 === 1 && $read_time % 100 !== 11) {
    $minute_label = "минута";
} elseif (
    in_array($read_time % 10, [2, 3, 4], true) &&
    !in_array($read_time % 100, [12, 13, 14], true)
) {
    $minute_label = "минуты";
}

$review_photo_url = "";
$review_photo_alt = "";
$placeholder = get_template_directory_uri() . "/images/case-placeholder.svg";
if (has_post_thumbnail()) {
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
                <p class="single-review__read-time">Время прочтения: <?php echo esc_html(
                    (string) $read_time,
                ); ?> <?php echo esc_html($minute_label); ?></p>

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
