<?php
/*
 * Template Name: Партнёрская программа
 */
if (!defined("ABSPATH")) {
    exit();
}

get_header();

$hero_title = get_field("partner_hero_title");
$hero_items = get_field("partner_hero_items");
$hero_btn_text =
    get_field("partner_hero_btn_text") ?: "Участвовать в программе";
$hero_btn_url = get_field("partner_hero_btn_url");
$hero_image = get_field("partner_hero_image");
?>

<main class="partners-page">

    <?php get_template_part("template-parts/breadcrumb"); ?>

    <?php if ($hero_title || $hero_items): ?>
    <section class="partner-hero">
        <div class="partner-hero__inner">
            <div class="partner-hero__left">
                <?php if ($hero_title): ?>
                <h1 class="partner-hero__title"><?php echo wp_kses_post(
                    $hero_title,
                ); ?></h1>
                <?php endif; ?>

                <?php if ($hero_items): ?>
                <ul class="partner-hero__list">
                    <?php foreach ($hero_items as $item): ?>
                    <li><?php echo esc_html($item["item_text"]); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>

                <?php if ($hero_btn_text): ?>
                <a href="<?php echo esc_url(
                    $hero_btn_url ?: "#",
                ); ?>" class="partner-hero__btn">
                    <?php echo esc_html($hero_btn_text); ?>
                </a>
                <?php endif; ?>
            </div>

            <?php if ($hero_image): ?>
                <img src="<?php echo esc_url(
                    $hero_image["url"],
                ); ?>" alt="<?php echo esc_attr($hero_image["alt"]); ?>">
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php
    $steps_title    = get_field('partner_steps_title') ?: 'Как это работает:';
    $steps_subtitle = get_field('partner_steps_subtitle') ?: '4 шага к вашему бонусу';
    $steps_btn_text = get_field('partner_steps_btn_text') ?: 'Участвовать в программе лояльности';
    $steps_btn_url  = get_field('partner_steps_btn_url');
    $steps_items    = get_field('partner_steps_items');
    $cta_title      = get_field('partner_steps_cta_title') ?: "Хотите зарабатывать больше?\nСтаньте нашим партнером";
    $cta_btn_text   = get_field('partner_steps_cta_btn_text') ?: 'Участвовать в программе лояльности';
    $cta_btn_url    = get_field('partner_steps_cta_btn_url');
    ?>
    <section class="partner-steps">
        <div class="partner-steps__inner">
            <div class="partner-steps__header">
                <div class="partner-steps__heading">
                    <span class="partner-steps__title"><?php echo esc_html($steps_title); ?></span>
                    <span class="partner-steps__subtitle"><?php echo esc_html($steps_subtitle); ?></span>
                </div>
                <a href="<?php echo esc_url($steps_btn_url ?: '#'); ?>" class="partner-steps__btn">
                    <?php echo esc_html($steps_btn_text); ?>
                </a>
            </div>

            <div class="partner-steps__grid">
                <?php if ($steps_items): foreach ($steps_items as $n => $step): ?>
                <div class="partner-step-card">
                    <div class="partner-step-card__head">
                        <span class="partner-step-card__num"><?php echo $n + 1; ?></span>
                        <h3 class="partner-step-card__title"><?php echo esc_html($step['step_title']); ?></h3>
                    </div>
                    <p class="partner-step-card__text"><?php echo wp_kses_post($step['step_text']); ?></p>
                </div>
                <?php endforeach; endif; ?>

                <div class="partner-step-card partner-step-card--cta">
                    <h3 class="partner-step-card__cta-title"><?php echo wp_kses_post($cta_title); ?></h3>
                    <a href="<?php echo esc_url($cta_btn_url ?: '#'); ?>" class="partner-hero__btn">
                        <?php echo esc_html($cta_btn_text); ?>
                    </a>
                    <span class="partner-step-card__deco partner-step-card__deco--1"></span>
                    <span class="partner-step-card__deco partner-step-card__deco--2"></span>
                </div>
            </div>
        </div>
    </section>

    <?php get_template_part('template-parts/partner-about'); ?>

    <?php get_template_part('template-parts/stats-cards'); ?>

    <?php get_template_part('template-parts/partner-banner'); ?>

</main>

<?php get_footer(); ?>
