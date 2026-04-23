<?php
if (!defined('ABSPATH')) exit;

$badge   = get_field('sblock_badge',   'option') ?: 'УСЛУГИ';
$title   = get_field('sblock_title',   'option');
$btn_t   = get_field('sblock_btn_text','option') ?: 'Смотреть больше →';
$btn_url = get_field('sblock_btn_url', 'option');

$services = new WP_Query([
    'post_type'      => 'service',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
]);

if (!$services->have_posts()) return;
?>
<section class="slist">
    <div class="slist__inner">

        <!-- Header -->
        <div class="slist__header">
            <div class="slist__header-left">
                <span class="service-badge"><?php echo esc_html($badge); ?></span>
                <?php if ($title): ?>
                <h2 class="slist__title"><?php echo wp_kses_post($title); ?></h2>
                <?php endif; ?>
            </div>
            <?php if ($btn_url): ?>
            <a href="<?php echo esc_url($btn_url); ?>" class="slist__more-btn">
                <?php echo esc_html($btn_t); ?>
            </a>
            <?php endif; ?>
        </div>

        <!-- Service rows -->
        <div class="slist__rows">
            <?php while ($services->have_posts()): $services->the_post();
                $desc  = get_field('service_desc');
                $image = get_field('service_hero_image');
                $tags  = get_field('service_tags');
                $s_btn = get_field('service_btn_text') ?: 'Перейти к услуге';
            ?>
            <div class="slist__row">
                <div class="slist__row-left">
                    <h3 class="slist__row-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <?php if ($desc): ?>
                    <p class="slist__row-desc"><?php echo nl2br(esc_html($desc)); ?></p>
                    <?php endif; ?>
                    <?php if ($tags): ?>
                    <div class="slist__tags">
                        <?php foreach ($tags as $tag): ?>
                        <span class="slist__tag"><?php echo esc_html($tag['service_tag_text']); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <a href="<?php the_permalink(); ?>" class="slist__row-btn">
                        <?php echo esc_html($s_btn); ?>
                    </a>
                </div>
                <?php if ($image): ?>
                <div class="slist__row-image">
                    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                </div>
                <?php endif; ?>
            </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>

    </div>
</section>
