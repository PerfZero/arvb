<?php get_header(); ?>
<main class="services-archive">
    <div class="services-inner">
        <?php get_template_part('template-parts/breadcrumb'); ?>
        <?php if (have_posts()): while (have_posts()): the_post();
            $desc = get_field('service_desc');
            $img = get_field('service_hero_image');
            $btn_text = get_field('service_btn_text') ?: 'Оставить заявку';
        ?>
        <article class="service-block">
            <div class="service-block__left">
                <span class="service-badge">УСЛУГИ</span>
                <h2 class="service-block__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <?php if ($desc): ?>
                    <div class="service-block__desc"><?php echo wp_kses_post($desc); ?></div>
                <?php endif; ?>
                <a href="<?php the_permalink(); ?>" class="service-block__btn"><?php echo esc_html($btn_text); ?></a>
            </div>
            <div class="service-block__right">
                <?php if ($img): ?>
                    <img src="<?php echo esc_url($img['url']); ?>" alt="<?php echo esc_attr($img['alt']); ?>">
                <?php endif; ?>
            </div>
        </article>
        <?php endwhile; endif; ?>
    </div>
</main>
<?php get_footer(); ?>
