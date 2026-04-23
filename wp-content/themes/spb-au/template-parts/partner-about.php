<?php
if (!defined('ABSPATH')) exit;

// Если передан $args['post_id'] — берём поля оттуда,
// иначе используем текущую страницу, если это page-partners.php.
$post_id = $args['post_id'] ?? null;
if (!$post_id && is_page()) {
    $current_id = get_the_ID();
    if (get_page_template_slug($current_id) === 'page-partners.php') {
        $post_id = $current_id;
    }
}
if (!$post_id) {
    $partners_pages = get_posts([
        'post_type'      => 'page',
        'posts_per_page' => 1,
        'meta_key'       => '_wp_page_template',
        'meta_value'     => 'page-partners.php',
        'fields'         => 'ids',
    ]);
    $post_id = $partners_pages[0] ?? get_the_ID();
}

$about_badge  = get_field('partner_about_badge', $post_id) ?: 'О НАС';
$about_stars  = (int)(get_field('partner_about_stars', $post_id) ?? 5);
$about_title  = get_field('partner_about_title', $post_id);
$about_desc   = get_field('partner_about_desc', $post_id);
$about_items  = get_field('partner_about_items', $post_id);
$about_btn    = get_field('partner_about_btn_text', $post_id) ?: 'О компании';
$about_url    = get_field('partner_about_btn_url', $post_id);
$about_image  = get_field('partner_about_image', $post_id);
?>
<section class="partner-about">
    <div class="partner-about__inner">

        <!-- Верхняя строка: заголовок + декор + бейдж -->
        <div class="partner-about__top">
            <h2 class="partner-about__title"><?php echo wp_kses_post($about_title); ?></h2>
            <span class="partner-about__deco"></span>
            <span class="partner-about__badge"><?php echo esc_html($about_badge); ?></span>
        </div>

        <!-- Нижняя строка: контент + фото -->
        <div class="partner-about__body">
            <div class="partner-about__left">
                <?php if ($about_stars > 0): ?>
                <div class="partner-about__stars">
                    <?php for ($s = 0; $s < $about_stars; $s++): ?>
                    <span class="partner-about__star">★</span>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>

                <?php if ($about_desc): ?>
                <div class="partner-about__desc"><?php echo wp_kses_post($about_desc); ?></div>
                <?php endif; ?>

                <?php if ($about_items): ?>
                <ul class="partner-about__checklist">
                    <?php foreach ($about_items as $item): ?>
                    <li><?php echo esc_html($item['item_text']); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>

                <a href="<?php echo esc_url($about_url ?: '#'); ?>" class="partner-hero__btn">
                    <?php echo esc_html($about_btn); ?>
                </a>
            </div>

            <?php if ($about_image): ?>
            <div class="partner-about__right">
                <img src="<?php echo esc_url($about_image['url']); ?>" alt="<?php echo esc_attr($about_image['alt']); ?>" class="partner-about__img">
            </div>
            <?php endif; ?>
        </div>

    </div>
</section>
