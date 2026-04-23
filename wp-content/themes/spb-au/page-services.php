<?php
/*
 * Template Name: Услуги
 */
if (!defined('ABSPATH')) exit;

get_header();
?>

<main class="services-page">

    <?php get_template_part('template-parts/proc'); ?>

    <?php get_template_part('template-parts/services-list'); ?>

</main>

<?php get_footer(); ?>
