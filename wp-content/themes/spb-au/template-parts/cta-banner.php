<?php
if (!defined('ABSPATH')) exit;

$title    = get_field('ctabanner_title',    'option');
$btn_text = get_field('ctabanner_btn_text', 'option') ?: 'Консультация с экспертом';
$btn_url  = get_field('ctabanner_btn_url',  'option');
$btn_is_popup = trim((string) $btn_url) === '#';
$image    = get_field('ctabanner_image',    'option');

if (!$title && !$btn_url) return;
?>
<section class="cta-banner">
    <div class="cta-banner__inner">

        <div class="cta-banner__left">
            <?php if ($title): ?>
            <p class="cta-banner__title"><?php echo wp_kses_post($title); ?></p>
            <?php endif; ?>
            <?php if ($btn_url): ?>
            <a href="<?php echo esc_url($btn_url); ?>" class="cta-banner__btn"<?php echo $btn_is_popup ? ' data-consult-open' : ''; ?>>
                <?php echo esc_html($btn_text); ?>
            </a>
            <?php endif; ?>
        </div>

        <div class="cta-banner__deco">
            <span class="cta-banner__shape cta-banner__shape--1"></span>
            <span class="cta-banner__shape cta-banner__shape--2"></span>
            <span class="cta-banner__shape cta-banner__shape--3"></span>
        </div>

        <?php if ($image): ?>
        <div class="cta-banner__right">
            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
        </div>
        <?php endif; ?>

    </div>
</section>
