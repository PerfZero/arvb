<?php
if (!defined('ABSPATH')) exit;

$title    = get_field('pb_title', 'option');
$items    = get_field('pb_items', 'option');
$btn_text = get_field('pb_btn_text', 'option') ?: 'Записаться на консультацию';
$btn_url  = get_field('pb_btn_url', 'option');
$image    = get_field('pb_image', 'option');

if (!$title && !$items) return;
?>
<section class="partner-banner">
    <div class="partner-banner__inner">
        <div class="partner-banner__left">
            <?php if ($title): ?>
            <h2 class="partner-banner__title"><?php echo wp_kses_post($title); ?></h2>
            <?php endif; ?>

            <?php if ($items): ?>
            <ul class="partner-banner__list">
                <?php foreach ($items as $item): ?>
                <li><?php echo esc_html($item['item_text']); ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>

            <a href="<?php echo esc_url($btn_url ?: '#'); ?>" class="partner-banner__btn">
                <?php echo esc_html($btn_text); ?>
            </a>
        </div>

        <?php if ($image): ?>
        <div class="partner-banner__right">
            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" class="partner-banner__img">
        </div>
        <?php endif; ?>
    </div>
</section>
