<?php
/*
 * Template Name: Отзывы о нашей работе
 */
if (!defined('ABSPATH')) exit;

get_header();

$title = get_field('reviews_title') ?: 'Отзывы о нашей работе';
?>

<main class="reviews-page">
    <div class="reviews-page__inner">

        <?php get_template_part('template-parts/breadcrumb'); ?>

        <?php get_template_part('template-parts/faq-vid', null, [
            'title'    => $title,
            'badge'    => null,
            'subtitle' => null,
        ]); ?>

    </div>
</main>

<?php get_footer(); ?>
