<?php
if (!defined("ABSPATH")) {
    exit();
}

get_header();
?>

<main class="archive-reviews">
    <div class="cases-inner">
        <?php get_template_part("template-parts/breadcrumb"); ?>
    </div>
    <?php get_template_part("template-parts/reviews-section"); ?>
</main>

<?php get_footer(); ?>
