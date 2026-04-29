<?php
if (!defined('ABSPATH')) exit;

get_header();

the_post();

$badge    = get_field('service_badge') ?: 'УСЛУГА';
$desc     = get_field('service_desc');
$btn_text = get_field('service_btn_text') ?: 'Оставить заявку';
$image    = get_field('service_hero_image');
?>

<main class="single-service">

    <div class="services-archive">
        <div class="services-inner">
            <?php get_template_part('template-parts/breadcrumb'); ?>
            <div class="service-block">
                <div class="service-block__left">
                    <span class="service-badge"><?php echo esc_html($badge); ?></span>
                    <h1 class="service-block__title"><?php the_title(); ?></h1>
                    <?php if ($desc): ?>
                    <div class="service-block__desc"><?php echo wp_kses_post($desc); ?></div>
                    <?php endif; ?>
                    <a href="#consult" class="service-block__btn"><?php echo esc_html($btn_text); ?></a>
                </div>
                <?php if ($image): ?>
                <div class="service-block__right">
                    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php get_template_part('template-parts/cases-section'); ?>
    <?php get_template_part('template-parts/calculator'); ?>
    <?php get_template_part('template-parts/gift-materials'); ?>

    <section class="blog-preview">
        <div class="blog-preview__inner">
            <h2 class="blog-preview__title">Блог</h2>
            <div class="blog-preview__grid">
                <?php
                $blog = new WP_Query([
                    'post_type'      => 'post',
                    'posts_per_page' => 3,
                    'post_status'    => 'publish',
                    'orderby'        => 'date',
                    'order'          => 'ASC',
                ]);
                while ($blog->have_posts()): $blog->the_post();
                    get_template_part('template-parts/article-card');
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
