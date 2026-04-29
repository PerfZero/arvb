<?php
if (!defined("ABSPATH")) {
    exit();
}

get_header();
?>

<?php if (have_posts()):
    while (have_posts()):

        the_post();
        $author_name = trim((string) get_field("article_author_name"));
        if ($author_name === "") {
            $author_name = get_the_author();
        }

        $author_photo = get_field("article_author_photo");
        $author_photo_url = "";
        $author_photo_alt = $author_name;
        if (is_array($author_photo) && !empty($author_photo["url"])) {
            $author_photo_url = (string) $author_photo["url"];
            $author_photo_alt = (string) ($author_photo["alt"] ?? $author_name);
        } elseif (is_numeric($author_photo)) {
            $author_photo_url = (string) wp_get_attachment_url((int) $author_photo);
        } elseif (is_string($author_photo) && $author_photo !== "") {
            $author_photo_url = $author_photo;
        }

        $author_text = wp_strip_all_tags(
            $author_name !== "" ? $author_name : get_the_title(),
        );
        $author_letter = function_exists("mb_substr")
            ? mb_substr($author_text, 0, 1)
            : substr($author_text, 0, 1);
        $published_date = get_the_date("d.m.Y");
        $author_letter_upper = function_exists("mb_strtoupper")
            ? mb_strtoupper($author_letter)
            : strtoupper($author_letter);
        $read_time = spbau_reading_time(get_the_content());
        $theme_uri = get_template_directory_uri();
        $icon_calendar = $theme_uri . "/images/calendary.svg";
        $icon_clock = $theme_uri . "/images/clock.svg";
        $icon_eye = $theme_uri . "/images/eye.svg";

        $views = function_exists("spbau_get_post_views")
            ? spbau_get_post_views((int) get_the_ID())
            : 0;
        ?>

<main class="single-article">
    <div class="single-article__inner">
        <?php get_template_part("template-parts/breadcrumb"); ?>

        <div class="single-article__layout">
            <div class="single-article__main">
                <div class="single-article__meta-card">
                    <div class="single-article__meta-author">
                        <?php if ($author_photo_url !== ""): ?>
                        <div class="single-article__meta-photo">
                            <img src="<?php echo esc_url($author_photo_url); ?>" alt="<?php echo esc_attr(
    $author_photo_alt,
); ?>">
                        </div>
                        <?php else: ?>
                        <div class="single-article__meta-photo single-article__meta-photo--fallback"><?php echo esc_html(
                            $author_letter_upper,
                        ); ?></div>
                        <?php endif; ?>

                        <div class="single-article__meta-item single-article__meta-item--author">
                            <span class="single-article__meta-label">Автор статьи</span>
                            <span class="single-article__meta-value single-article__meta-value--author"><?php echo esc_html(
                                $author_name,
                            ); ?></span>
                        </div>
                    </div>

                    <div class="single-article__meta-stats">
                        <div class="single-article__meta-item">
                            <span class="single-article__meta-label">Опубликовано</span>
                            <span class="single-article__meta-info">
                                <img src="<?php echo esc_url(
                                    $icon_calendar,
                                ); ?>" alt="" aria-hidden="true">
                                <span class="single-article__meta-value"><?php echo esc_html(
                                    $published_date,
                                ); ?></span>
                            </span>
                        </div>
                        <div class="single-article__meta-item">
                            <span class="single-article__meta-label">Время прочтения</span>
                            <span class="single-article__meta-info">
                                <img src="<?php echo esc_url(
                                    $icon_clock,
                                ); ?>" alt="" aria-hidden="true">
                                <span class="single-article__meta-value"><?php echo esc_html(
                                    $read_time,
                                ); ?> минут</span>
                            </span>
                        </div>
                        <div class="single-article__meta-item">
                            <span class="single-article__meta-label">Просмотров</span>
                            <span class="single-article__meta-info">
                                <img src="<?php echo esc_url(
                                    $icon_eye,
                                ); ?>" alt="" aria-hidden="true">
                                <span class="single-article__meta-value"><?php echo esc_html(
                                    (string) $views,
                                ); ?></span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="single-article__heading">
                    <h1 class="single-article__title"><?php the_title(); ?></h1>

                </div>

                <div class="single-article__content">
                    <?php the_content(); ?>
                </div>
            </div>

            <aside class="single-article__sidebar">
                <?php get_template_part("template-parts/calculator-widget"); ?>
            </aside>
        </div>
    </div>

    <?php
    $related = new WP_Query([
        "post_type" => "post",
        "posts_per_page" => 3,
        "post_status" => "publish",
        "post__not_in" => [get_the_ID()],
        "orderby" => "date",
        "order" => "ASC",
        "category__in" => wp_get_post_categories(get_the_ID()),
    ]);

    if ($related->have_posts()): ?>
    <section class="related-articles">
        <h2 class="related-articles__title">Другие новости</h2>
        <div class="articles-grid">
            <?php
            while ($related->have_posts()):
                $related->the_post();
                get_template_part("template-parts/article-card");
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
    </section>
    <?php endif;
    ?>

    <?php get_template_part("template-parts/marathon"); ?>
</main>

<?php
    endwhile;
endif; ?>

<?php get_footer(); ?>
