<?php
/*
 * Template Name: Контакты
 */
if (!defined('ABSPATH')) exit;

get_header();

$phone   = get_field('contacts_phone',   'option');
$hours   = get_field('contacts_hours',   'option');
$address = get_field('contacts_address', 'option');
$map     = get_field('contacts_map',     'option');
?>

<main class="contacts-page">
    <div class="contacts-inner">

        <?php get_template_part('template-parts/breadcrumb'); ?>
        <h1 class="contacts-title">Контакты</h1>

        <div class="contacts-layout">

            <div class="contacts-left social-block__left">
                <?php get_template_part('template-parts/social-left'); ?>
            </div>

            <div class="contacts-right">
                <?php if ($phone): ?>
                <div class="contacts-info-group">
                    <span class="contacts-info-label">Телефон:</span>
                    <a href="tel:<?php echo esc_attr(preg_replace('/\D/', '', $phone)); ?>" class="contacts-phone"><?php echo esc_html($phone); ?></a>
                </div>
                <?php endif; ?>

                <?php if ($hours): ?>
                <div class="contacts-info-group">
                    <span class="contacts-info-label">Время работы:</span>
                    <span class="contacts-hours"><?php echo esc_html($hours); ?></span>
                </div>
                <?php endif; ?>

                <?php if ($address): ?>
                <div class="contacts-info-group">
                    <span class="contacts-info-label">Юр. адрес:</span>
                    <p class="contacts-address"><?php echo wp_kses_post($address); ?></p>
                </div>
                <?php endif; ?>

                <?php if ($map): ?>
                <div class="contacts-map">
                    <?php echo $map; ?>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</main>

<?php get_footer(); ?>
