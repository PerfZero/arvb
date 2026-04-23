<?php
if (!defined('ABSPATH')) exit;

$title            = get_field('social_title',            'option');
$subscribers      = get_field('social_subscribers',      'option');
$networks         = get_field('social_networks',         'option');
$messengers_title = get_field('social_messengers_title', 'option');
$messengers       = get_field('social_messengers',       'option');
$disclaimer       = get_field('social_disclaimer',       'option');
?>
<?php if ($title): ?>
<h2 class="social-block__title"><?php echo wp_kses_post($title); ?></h2>
<?php endif; ?>

<?php if ($subscribers): ?>
<p class="social-block__subscribers"><?php echo esc_html($subscribers); ?></p>
<?php endif; ?>

<?php if ($networks): ?>
<div class="social-block__icons">
    <?php foreach ($networks as $net): ?>
    <a href="<?php echo esc_url($net['url']); ?>" class="social-block__icon-link" target="_blank" rel="noopener noreferrer">
        <?php if ($net['icon']): ?>
        <img src="<?php echo esc_url($net['icon']['url']); ?>" alt="<?php echo esc_attr($net['icon']['alt']); ?>" class="social-block__icon-img">
        <?php endif; ?>
        <?php if ($net['label']): ?>
        <span class="social-block__icon-label"><?php echo esc_html($net['label']); ?></span>
        <?php endif; ?>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if ($messengers_title): ?>
<p class="social-block__msg-title"><?php echo esc_html($messengers_title); ?></p>
<?php endif; ?>

<?php if ($messengers): ?>
<div class="social-block__messengers">
    <?php foreach ($messengers as $msg): ?>
    <a href="<?php echo esc_url($msg['url']); ?>" class="social-block__icon-link" target="_blank" rel="noopener noreferrer">
        <?php if ($msg['icon']): ?>
        <img src="<?php echo esc_url($msg['icon']['url']); ?>" alt="<?php echo esc_attr($msg['icon']['alt']); ?>" class="social-block__icon-img">
        <?php endif; ?>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if ($disclaimer): ?>
<p class="social-block__disclaimer"><?php echo esc_html($disclaimer); ?></p>
<?php endif; ?>
