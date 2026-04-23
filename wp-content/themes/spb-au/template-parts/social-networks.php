<?php
if (!defined('ABSPATH')) exit;

$image = get_field('social_image', 'option');
?>
<section class="social-block">
    <div class="social-block__inner">

        <div class="social-block__left">
            <?php get_template_part('template-parts/social-left'); ?>
        </div>

        <?php if ($image): ?>
        <div class="social-block__right">
            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" class="social-block__image">
        </div>
        <?php endif; ?>

    </div>
</section>
