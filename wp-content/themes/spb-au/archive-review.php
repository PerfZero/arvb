<?php
if (!defined("ABSPATH")) {
    exit();
}

get_header();
?>

<main class="archive-reviews">
    <?php get_template_part("template-parts/breadcrumb"); ?>
    <?php get_template_part("template-parts/reviews-section"); ?>
</main>

<?php get_footer(); ?>
