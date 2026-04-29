<?php
/*
 * Template Name: Мы в СМИ
 */
if (!defined("ABSPATH")) {
    exit();
}

get_header();

$search_query = isset($_GET["s"]) ? sanitize_text_field($_GET["s"]) : "";
$form_status = isset($_GET["smi_form_status"])
    ? sanitize_key($_GET["smi_form_status"])
    : "";
$form_message = isset($_GET["smi_form_message"])
    ? sanitize_text_field(wp_unslash($_GET["smi_form_message"]))
    : "";

$args = [
    "post_type" => "smi",
    "posts_per_page" => -1,
    "post_status" => "publish",
    "orderby" => "date",
    "order" => "DESC",
];

if ($search_query) {
    $args["s"] = $search_query;
}

$smi_query = new WP_Query($args);
?>

<main class="smi-page">
    <div class="smi-inner">

        <div class="smi-hero">
            <div class="smi-hero-left">
                <?php get_template_part("template-parts/breadcrumb"); ?>
                <h1 class="smi-title">Более 70 публикаций в СМИ</h1>
            </div>
            <form class="smi-search" action="" method="get">
                <div class="smi-search-input-wrap">
                    <svg class="smi-search-icon" width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <path d="M2 5h14M2 9h10M2 13h7" stroke="#888" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <input type="text" name="s" placeholder="Поиск по сайту"
                           value="<?php echo esc_attr($search_query); ?>">
                </div>
                <button type="submit" class="smi-search-btn">Найти</button>
            </form>
        </div>

        <?php
        $collab_title   = get_field('smi_collab_title',         'option') ?: 'Сотрудничество';
        $collab_text    = get_field('smi_collab_text',          'option');
        $collab_label   = get_field('smi_collab_contact_label', 'option');
        $collab_email   = get_field('smi_collab_email',         'option');
        $collab_consent = get_field('smi_collab_consent',       'option') ?: 'Отправляя форму, вы даёте согласие на обработку персональных данных';
        $collab_btn     = get_field('smi_collab_btn_text',      'option') ?: 'Скачать Медиакит';
        $collab_url     = get_field('smi_collab_btn_url',       'option');
        ?>

        <div class="smi-layout">
        <div class="smi-collab">
            <h2 class="smi-collab__title"><?php echo esc_html($collab_title); ?></h2>
            <?php if ($collab_text): ?>
            <p class="smi-collab__text"><?php echo wp_kses_post($collab_text); ?></p>
            <?php endif; ?>
            <?php if ($collab_label || $collab_email): ?>
            <div class="smi-collab__contact">
                <?php if ($collab_label): ?>
                <span><?php echo esc_html($collab_label); ?></span>
                <?php endif; ?>
                <?php if ($collab_email): ?>
                <a href="mailto:<?php echo esc_attr($collab_email); ?>" class="smi-collab__email"><?php echo esc_html($collab_email); ?></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <form class="smi-collab__form" action="<?php echo esc_url(
                admin_url("admin-post.php"),
            ); ?>" method="post" novalidate data-smi-collab-form data-media-kit-url="<?php echo esc_url($collab_url); ?>">
                <input type="hidden" name="action" value="spbau_smi_collab_submit">
                <?php wp_nonce_field(
                    "spbau_smi_collab_submit",
                    "spbau_smi_collab_nonce",
                ); ?>
                <div class="smi-collab__field">
                    <label class="smi-collab__label">Номер телефона</label>
                    <div class="smi-collab__phone-wrap">
                        <span class="smi-collab__flag">🇷🇺 +7</span>
                        <input type="tel" name="smi_phone" class="smi-collab__input" placeholder="___-___-__-__" required>
                    </div>
                </div>
                <label class="smi-collab__consent">
                    <input type="checkbox" name="smi_agree" class="smi-collab__consent-input" required>
                    <span class="smi-collab__consent-box" aria-hidden="true">
                        <svg width="12" height="10" viewBox="0 0 12 10" fill="none">
                            <path d="M1 5L4.2 8.2L11 1.2" stroke="#2d6bd9" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="smi-collab__consent-text"><?php echo esc_html($collab_consent); ?></span>
                </label>
                <?php if ($form_status && $form_message): ?>
                <div class="smi-collab__notice smi-collab__notice--<?php echo esc_attr(
                    $form_status,
                ); ?>">
                    <?php echo esc_html($form_message); ?>
                </div>
                <?php endif; ?>
                <button type="submit" class="smi-collab__btn" data-smi-collab-submit disabled aria-disabled="true"><?php echo esc_html($collab_btn); ?></button>
            </form>
        </div>

        <div class="smi-list">
            <?php if ($smi_query->have_posts()): ?>
                <?php
                while ($smi_query->have_posts()):

                    $smi_query->the_post();
                    $source_name = get_field("smi_source_name");
                    $source_url = get_field("smi_source_url");
                    $source_logo = get_field("smi_source_logo"); // возвращает массив ['url', 'alt', ...]
                    $source_logo_url = is_array($source_logo)
                        ? $source_logo["url"]
                        : $source_logo;
                    ?>
                <article class="smi-card">

                    <!-- Контент + фото -->
                    <div class="smi-card-main">
                        <div class="smi-card-body">
                            <div class="smi-card-content">
                            <time class="smi-card-date"><?php echo get_the_date(
                                "d.m.Y",
                            ); ?></time>
                            <h2 class="smi-card-title"><?php the_title(); ?></h2>
                            <div class="smi-card-meta">
                                <?php
                                $terms = get_the_terms(get_the_ID(), 'smi_category');
                                $cat_name = ($terms && !is_wp_error($terms)) ? $terms[0]->name : '';
                                $time_ago = human_time_diff(get_the_time('U'), current_time('timestamp'));
                                if ($cat_name) {
                                    echo esc_html($cat_name) . ' (' . esc_html($time_ago) . ' назад)';
                                } else {
                                    echo '(' . esc_html($time_ago) . ' назад)';
                                }
                                ?>
                            </div>
                            <div class="smi-card-excerpt"><?php the_excerpt(); ?></div>
                            <?php if ($source_url): ?>
                                <div class="smi-card-source">
                                    <?php if ($source_logo_url): ?>
                                        <img class="smi-source-logo-inline"
                                             src="<?php echo esc_url(
                                                 $source_logo_url,
                                             ); ?>"
                                             alt="<?php echo esc_attr(
                                                 $source_name,
                                             ); ?>">
                                    <?php elseif ($source_name): ?>
                                        <span class="smi-source-name-inline"><?php echo esc_html(
                                            $source_name,
                                        ); ?></span>
                                    <?php endif; ?>
                                    <a href="<?php echo esc_url(
                                        $source_url,
                                    ); ?>"
                                       target="_blank" rel="noopener noreferrer"
                                       class="smi-card-link">
                                        Ссылка на статью:<br>
                                        «<?php echo esc_html(
                                            $source_name ?: "Читать",
                                        ); ?>»
                                    </a>
                                </div>
                            <?php endif; ?>
                            </div>
                            <?php if (has_post_thumbnail()): ?>
                                <div class="smi-card-image">
                                    <?php the_post_thumbnail("large"); ?>
                                </div>
                            <?php endif; ?>
                        </div>


                    </div>

                </article>
                <?php
                endwhile;
                wp_reset_postdata();
                ?>

            <?php else: ?>
                <p class="smi-empty">Публикации не найдены.</p>
            <?php endif; ?>
        </div><!-- .smi-list -->
        </div><!-- .smi-layout -->

    </div>
</main>

<?php get_footer(); ?>
