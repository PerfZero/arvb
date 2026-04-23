<?php
if (!defined("ABSPATH")) {
    exit();
}
get_header();
?>

<main class="page-404">
    <div class="page-404__inner">
        <img src="<?php echo esc_url(
            get_template_directory_uri() . "/images/404.svg",
        ); ?>" alt="404" class="page-404__img">
        <p class="page-404__text">Упс, кажется такой страницы не существует</p>
        <a href="<?php echo esc_url(
            home_url("/"),
        ); ?>" class="page-404__btn">Вернуться на главную</a>
    </div>
</main>

<?php get_footer(); ?>
