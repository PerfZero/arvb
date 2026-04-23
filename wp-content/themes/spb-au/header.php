<?php
if (!defined("ABSPATH")) {
    exit();
} ?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo("charset"); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="header-inner">

        <a href="<?php echo esc_url(home_url("/")); ?>" class="site-logo">
            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/logo.svg"
                 alt="СПБ АУ — Санкт-Петербургский Арбитражный Управляющий"
                 width="140" height="52">
        </a>

        <nav class="header-nav" aria-label="Основное меню">
            <?php wp_nav_menu([
                "theme_location" => "primary",
                "menu_class" => "nav-list",
                "container" => false,
                "fallback_cb" => static function () {
                    echo '<ul class="nav-list">
                        <li><a href="#">Услуги по банкротству</a></li>
                        <li><a href="#">Отзывы и кейсы</a></li>
                        <li><a href="#">Завод банкротства</a></li>
                        <li><a href="#">Статьи</a></li>
                        <li><a href="#">Контакты</a></li>
                        <li><a href="#">Мы в СМИ</a></li>
                    </ul>';
                },
            ]); ?>
            <button class="icon-btn nav-toggle" aria-label="Меню">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <!-- Secondary nav dropdown -->
            <div class="nav-dropdown__panel">
                <?php wp_nav_menu([
                    "theme_location" => "secondary",
                    "menu_class"     => "nav-dropdown__list",
                    "container"      => false,
                    "fallback_cb"    => static function () {
                        echo '<ul class="nav-dropdown__list">
                            <li><a href="#">Партнерская программа</a></li>
                            <li><a href="#">Программа лояльности</a></li>
                            <li><a href="#">FAQ</a></li>
                            <li><a href="#">Команда</a></li>
                            <li><a href="#">Работа у нас</a></li>
                        </ul>';
                    },
                ]); ?>
            </div>
        </nav>

        <div class="header-right">
            <button class="icon-btn" aria-label="Написать нам">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/chat.svg">
            </button>
            <div class="header-contact">
                <a href="tel:+79169496622" class="header-phone">+7 916 949-66-22</a>
                <a href="#contact" class="header-callback">Свяжитесь со мной</a>
            </div>
        </div>

        <button class="burger-btn" aria-label="Открыть меню" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>

    </div>
</header>

<!-- Mobile menu -->
<div class="mobile-menu" aria-hidden="true">
    <div class="mobile-menu__overlay"></div>
    <div class="mobile-menu__drawer">
        <button class="mobile-menu__close" aria-label="Закрыть меню">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                <line x1="1" y1="1" x2="17" y2="17" stroke="white" stroke-width="2" stroke-linecap="round"/>
                <line x1="17" y1="1" x2="1" y2="17" stroke="white" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
        <nav class="mobile-menu__nav">
            <?php wp_nav_menu([
                "theme_location" => "primary",
                "menu_class"     => "mobile-nav-list",
                "container"      => false,
                "fallback_cb"    => static function () {
                    echo '<ul class="mobile-nav-list">
                        <li><a href="#">Услуги по банкротству</a></li>
                        <li><a href="#">Отзывы и кейсы</a></li>
                        <li><a href="#">Завод банкротства</a></li>
                        <li><a href="#">Статьи</a></li>
                        <li><a href="#">Контакты</a></li>
                        <li><a href="#">Мы в СМИ</a></li>
                    </ul>';
                },
            ]); ?>
        </nav>
        <div class="mobile-menu__contact">
            <a href="tel:+79169496622" class="mobile-menu__phone">+7 916 949-66-22</a>
            <a href="#contact" class="mobile-menu__callback">Свяжитесь со мной</a>
        </div>
    </div>
</div>

