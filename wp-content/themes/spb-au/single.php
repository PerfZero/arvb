<?php
if (!defined("ABSPATH")) {
    exit();
}

get_header();
?>

<?php if (have_posts()):
    while (have_posts()):

        the_post();
        $cats = get_the_category();
        $cat_name = $cats ? $cats[0]->name : "";
        $time_ago = human_time_diff(
            get_the_time("U"),
            current_time("timestamp"),
        );
        $gallery = get_field("article_gallery");
        $read_time = spbau_reading_time(get_the_content());
        ?>

<main class="single-article">
    <div class="single-article__inner">

        <div class="single-article__left">

            <?php get_template_part("template-parts/breadcrumb"); ?>

            <h1 class="single-article__title"><?php the_title(); ?></h1>

            <div class="single-article__meta">
                <?php if ($cat_name): ?>
                    <span><?php echo esc_html(
                        $cat_name . " (" . $time_ago . " назад)",
                    ); ?></span>
                <?php endif; ?>
                <span>Время прочтения: <?php echo esc_html(
                    $read_time,
                ); ?> минут</span>
            </div>

            <div class="single-article__content">
                <?php if ($gallery): ?>
                <div class="single-article__float">
                    <div class="post-gallery">

                        <div class="swiper post-gallery__main">
                            <div class="swiper-wrapper">
                                <?php foreach ($gallery as $img): ?>
                                <div class="swiper-slide">
                                    <a href="<?php echo esc_url($img["url"]); ?>"
                                       class="glightbox"
                                       data-gallery="article-gallery"
                                       data-description="<?php echo esc_attr(
                                           $img["alt"],
                                       ); ?>">
                                        <img src="<?php echo esc_url(
                                            $img["url"],
                                        ); ?>"
                                             alt="<?php echo esc_attr(
                                                 $img["alt"],
                                             ); ?>">
                                    </a>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <?php if (count($gallery) > 1): ?>
                        <div class="swiper post-gallery__thumbs">
                            <div class="swiper-wrapper">
                                <?php foreach ($gallery as $img): ?>
                                <div class="swiper-slide">
                                    <img src="<?php echo esc_url(
                                        $img["sizes"]["medium"] ?? $img["url"],
                                    ); ?>"
                                         alt="">
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
                <?php endif; ?>

                <?php the_content(); ?>
            </div>

        </div>

    </div>
    <?php
    $related = new WP_Query([
        "post_type" => "post",
        "posts_per_page" => 3,
        "post_status" => "publish",
        "post__not_in" => [get_the_ID()],
        "orderby" => "date",
        "order" => "DESC",
        "category__in" => wp_get_post_categories(get_the_ID()),
    ]);

    if ($related->have_posts()): ?>

    <?php get_template_part("template-parts/calculator"); ?>


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
