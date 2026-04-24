<?php
/*
 * Template Name: Типовая страница
 */
if (!defined("ABSPATH")) {
    exit();
}

get_header();
?>

<main class="typical-page">
    <div class="typical-page__inner">
        <?php get_template_part("template-parts/breadcrumb"); ?>

        <?php if (have_posts()): ?>
            <?php while (have_posts()): the_post(); ?>
                <article class="typical-page__article">
                    <h1 class="typical-page__title"><?php the_title(); ?></h1>

                    <?php if (has_post_thumbnail()): ?>
                        <div class="typical-page__cover">
                            <?php the_post_thumbnail("large", [
                                "class" => "typical-page__cover-img",
                                "alt" => get_the_title(),
                            ]); ?>
                        </div>
                    <?php endif; ?>

                    <div class="typical-page__content">
                        <?php the_content(); ?>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>

