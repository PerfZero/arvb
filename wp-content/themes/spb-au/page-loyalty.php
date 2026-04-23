<?php
/*
 * Template Name: Программа лояльности
 */
if (!defined('ABSPATH')) exit;

get_header();

// Hero
$hero_title = get_field('loy_hero_title') ?: 'Станьте нашим <span class="loy-hero__accent">официальным партнером</span> и получайте пассивный доход вместе с «СПбАу»';
$hero_subtitle     = get_field('loy_hero_subtitle');
$hero_btn_text     = get_field('loy_hero_btn_text') ?: 'Стать Партнером';
$hero_btn_url      = get_field('loy_hero_btn_url');
$hero_tip          = get_field('loy_hero_tip');
$hero_image        = get_field('loy_hero_image');

// Earnings
$earn_title    = get_field('loy_earn_title')    ?: 'Рекомендуя нас, вы зарабатываете';
$earn_subtitle = get_field('loy_earn_subtitle');
$card1_amount  = get_field('loy_card1_amount')   ?: '+12 000₽';
$card1_desc    = get_field('loy_card1_desc');
$card1_btn     = get_field('loy_card1_btn_text') ?: 'Перейти к обучению';
$card1_url     = get_field('loy_card1_btn_url');
$card2_amount  = get_field('loy_card2_amount')   ?: '+14 000₽';
$card2_desc    = get_field('loy_card2_desc');
$card2_btn     = get_field('loy_card2_btn_text') ?: 'Смотреть отзывы';
$card2_url     = get_field('loy_card2_btn_url');
$card3_amount  = get_field('loy_card3_amount')   ?: '+3 000₽';
$card3_desc    = get_field('loy_card3_desc');
$card3_btn     = get_field('loy_card3_btn_text') ?: 'Получить личную ссылку';
$card3_url     = get_field('loy_card3_btn_url');

// Steps
$steps_title    = get_field('loy_steps_title')    ?: 'Как это работает:';
$steps_subtitle = get_field('loy_steps_subtitle') ?: '4 шага к вашему бонусу';
$steps_items    = get_field('loy_steps_items');
$steps_btn      = get_field('loy_steps_btn_text') ?: 'Стать Партнером';
$steps_btn_url  = get_field('loy_steps_btn_url');

// Growth / statuses
$growth_title    = get_field('loy_growth_title')    ?: 'Финансовый рост вместе с «СПБАУ»';
$growth_subtitle = get_field('loy_growth_subtitle');
$s1_title   = get_field('loy_s1_title')   ?: 'Статус «Агент»';
$s1_sub     = get_field('loy_s1_sub')     ?: 'в рамках Программы лояльности';
$s1_desc    = get_field('loy_s1_desc');
$s1_amount  = get_field('loy_s1_amount')  ?: '+10 000₽';
$s1_btn     = get_field('loy_s1_btn_text') ?: 'Перейти к обучению';
$s1_btn_url = get_field('loy_s1_btn_url');
$s1_image   = get_field('loy_s1_image');
$s2_title   = get_field('loy_s2_title')   ?: 'Статус «Партнер»';
$s2_sub     = get_field('loy_s2_sub')     ?: 'После прохождения бесплатного обучения';
$s2_items   = get_field('loy_s2_items');
$s2_amount  = get_field('loy_s2_amount')  ?: '+12 000₽';
$s2_amount_desc = get_field('loy_s2_amount_desc') ?: 'за каждого привлеченного клиента';
$s2_image   = get_field('loy_s2_image');
$vip_title  = get_field('loy_vip_title')  ?: 'Статус «VIP Партнер» СПБАУ';
$vip_sub    = get_field('loy_vip_sub')    ?: 'После привлечения 6 успешных сделок';
$vip_items  = get_field('loy_vip_items');
$vip_a1     = get_field('loy_vip_amount1')      ?: '+14 000₽';
$vip_a1d    = get_field('loy_vip_amount1_desc') ?: 'за каждого привлеченного клиента';
$vip_a2     = get_field('loy_vip_amount2')      ?: '+3 000₽';
$vip_a2d    = get_field('loy_vip_amount2_desc') ?: 'за каждого клиента, приведенного партнерами вашей сети';
$vip_image  = get_field('loy_vip_image');
$form_title = get_field('loy_form_title') ?: 'Получите консультацию о возможностях карьерного роста';
$form_cf7   = get_field('loy_form_cf7');
$form_btn   = get_field('loy_form_btn')   ?: 'Получить консультацию';
?>

<main class="loyalty-page">
<div class="loyalty-page__inner">

    <?php get_template_part('template-parts/breadcrumb'); ?>

    <!-- Hero -->
    <section class="loy-hero">
        <div class="loy-hero__left">
            <h1 class="loy-hero__title"><?php echo wp_kses($hero_title, ['span' => ['class' => []], 'br' => []]); ?></h1>

            <?php if ($hero_subtitle): ?>
            <p class="loy-hero__subtitle"><?php echo esc_html($hero_subtitle); ?></p>
            <?php endif; ?>

            <div class="loy-hero__actions">
                <a href="<?php echo esc_url($hero_btn_url ?: '#'); ?>" class="loy-hero__btn">
                    <?php echo esc_html($hero_btn_text); ?>
                </a>
                <?php if ($hero_tip): ?>
                <p class="loy-hero__tip"><?php echo esc_html($hero_tip); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($hero_image): ?>
        <div class="loy-hero__right">
            <div class="loy-hero__circle"></div>
            <img src="<?php echo esc_url($hero_image['url']); ?>" alt="<?php echo esc_attr($hero_image['alt']); ?>" class="loy-hero__img">
        </div>
        <?php endif; ?>
    </section>

    <!-- Earnings -->
    <section class="loy-earn">
        <h2 class="loy-earn__title"><?php echo esc_html($earn_title); ?></h2>
        <?php if ($earn_subtitle): ?>
        <p class="loy-earn__subtitle"><?php echo esc_html($earn_subtitle); ?></p>
        <?php endif; ?>

        <div class="loy-earn__cards">
            <div class="loy-earn-card loy-earn-card--blue">
                <p class="loy-earn-card__amount"><?php echo esc_html($card1_amount); ?></p>
                <?php if ($card1_desc): ?>
                <p class="loy-earn-card__desc"><?php echo esc_html($card1_desc); ?></p>
                <?php endif; ?>
                <a href="<?php echo esc_url($card1_url ?: '#'); ?>" class="loy-earn-card__btn">
                    <?php echo esc_html($card1_btn); ?>
                </a>
            </div>

            <div class="loy-earn-card loy-earn-card--dark">
                <p class="loy-earn-card__amount"><?php echo esc_html($card2_amount); ?></p>
                <?php if ($card2_desc): ?>
                <p class="loy-earn-card__desc"><?php echo esc_html($card2_desc); ?></p>
                <?php endif; ?>
                <a href="<?php echo esc_url($card2_url ?: '#'); ?>" class="loy-earn-card__btn loy-earn-card__btn--light">
                    <?php echo esc_html($card2_btn); ?>
                </a>
            </div>

            <div class="loy-earn-card loy-earn-card--white">
                <p class="loy-earn-card__amount loy-earn-card__amount--dark"><?php echo esc_html($card3_amount); ?></p>
                <?php if ($card3_desc): ?>
                <p class="loy-earn-card__desc loy-earn-card__desc--dark"><?php echo esc_html($card3_desc); ?></p>
                <?php endif; ?>
                <a href="<?php echo esc_url($card3_url ?: '#'); ?>" class="loy-earn-card__btn loy-earn-card__btn--outline">
                    <?php echo esc_html($card3_btn); ?>
                </a>
            </div>
        </div>
    </section>

    <!-- Steps -->
    <section class="loy-steps">
        <div class="loy-steps__heading">
            <h2 class="loy-steps__title"><?php echo esc_html($steps_title); ?></h2>
            <p class="loy-steps__subtitle"><?php echo esc_html($steps_subtitle); ?></p>
        </div>

        <div class="loy-steps__grid">
            <?php if ($steps_items): foreach ($steps_items as $n => $step): ?>
            <div class="loy-step-card">
                <div class="loy-step-card__head">
                    <span class="loy-step-card__num"><?php echo $n + 1; ?></span>
                    <h3 class="loy-step-card__title"><?php echo esc_html($step['step_title']); ?></h3>
                </div>
                <p class="loy-step-card__text"><?php echo wp_kses_post($step['step_text']); ?></p>
            </div>
            <?php endforeach; endif; ?>
        </div>

        <div class="loy-steps__cta">
            <a href="<?php echo esc_url($steps_btn_url ?: '#'); ?>" class="loy-steps__btn">
                <?php echo esc_html($steps_btn); ?>
            </a>
        </div>
    </section>

    <?php get_template_part('template-parts/faq-vid', null, [
        'title'    => 'Отзывы о нашей работе',
        'badge'    => null,
        'subtitle' => null,
    ]); ?>

    <!-- Growth / Statuses -->
    <section class="loy-growth">
        <h2 class="loy-growth__title"><?php echo esc_html($growth_title); ?></h2>
        <?php if ($growth_subtitle): ?>
        <p class="loy-growth__subtitle"><?php echo esc_html($growth_subtitle); ?></p>
        <?php endif; ?>

        <div class="loy-statuses">

            <!-- Top row: 2 cards -->
            <div class="loy-statuses__row">

                <!-- Card 1: Agent (white) -->
                <div class="loy-status-card loy-status-card--light">
                    <div class="loy-status-card__left">
                        <h3 class="loy-status-card__title"><?php echo esc_html($s1_title); ?></h3>
                        <p class="loy-status-card__sub"><?php echo esc_html($s1_sub); ?></p>
                        <?php if ($s1_desc): ?>
                        <p class="loy-status-card__desc"><?php echo esc_html($s1_desc); ?></p>
                        <?php endif; ?>
                        <p class="loy-status-card__amount"><?php echo esc_html($s1_amount); ?></p>
                        <a href="<?php echo esc_url($s1_btn_url ?: '#'); ?>" class="loy-status-card__btn">
                            <?php echo esc_html($s1_btn); ?>
                        </a>
                    </div>
                    <?php if ($s1_image): ?>
                    <div class="loy-status-card__img-wrap">
                        <img src="<?php echo esc_url($s1_image['url']); ?>" alt="<?php echo esc_attr($s1_image['alt']); ?>">
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Card 2: Partner (dark) -->
                <div class="loy-status-card loy-status-card--dark">
                    <div class="loy-status-card__left">
                        <h3 class="loy-status-card__title"><?php echo esc_html($s2_title); ?></h3>
                        <p class="loy-status-card__sub"><?php echo esc_html($s2_sub); ?></p>
                        <?php if ($s2_items): ?>
                        <ul class="loy-status-card__list">
                            <?php foreach ($s2_items as $item): ?>
                            <li><?php echo esc_html($item['item_text']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                        <p class="loy-status-card__amount"><?php echo esc_html($s2_amount); ?></p>
                        <?php if ($s2_amount_desc): ?>
                        <p class="loy-status-card__amount-desc"><?php echo esc_html($s2_amount_desc); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php if ($s2_image): ?>
                    <div class="loy-status-card__img-wrap">
                        <img src="<?php echo esc_url($s2_image['url']); ?>" alt="<?php echo esc_attr($s2_image['alt']); ?>">
                    </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- VIP card: wide blue -->
            <div class="loy-status-card loy-status-card--vip">
                <div class="loy-status-card__left">
                    <h3 class="loy-status-card__title"><?php echo esc_html($vip_title); ?></h3>
                    <p class="loy-status-card__sub"><?php echo esc_html($vip_sub); ?></p>
                    <?php if ($vip_items): ?>
                    <ul class="loy-status-card__list">
                        <?php foreach ($vip_items as $item): ?>
                        <li><?php echo esc_html($item['item_text']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                    <div class="loy-status-card__amounts">
                        <div>
                            <p class="loy-status-card__amount"><?php echo esc_html($vip_a1); ?></p>
                            <p class="loy-status-card__amount-desc"><?php echo esc_html($vip_a1d); ?></p>
                        </div>
                        <div>
                            <p class="loy-status-card__amount"><?php echo esc_html($vip_a2); ?></p>
                            <p class="loy-status-card__amount-desc"><?php echo esc_html($vip_a2d); ?></p>
                        </div>
                    </div>
                </div>
                <?php if ($vip_image): ?>
                <div class="loy-status-card__img-wrap loy-status-card__img-wrap--vip">
                    <img src="<?php echo esc_url($vip_image['url']); ?>" alt="<?php echo esc_attr($vip_image['alt']); ?>">
                </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Form -->
        <div class="loy-consult">
            <h3 class="loy-consult__title"><?php echo esc_html($form_title); ?></h3>
            <?php if ($form_cf7): ?>
                <?php echo do_shortcode($form_cf7); ?>
            <?php else: ?>
            <form class="loy-consult__form" action="#" method="post">
                <div class="loy-consult__field loy-consult__field--contact">
                    <label>Ваше имя</label>
                    <input type="text" name="name" placeholder="Имя">
                </div>
                <div class="loy-consult__field">
                    <label>Номер телефона</label>
                    <input type="tel" name="phone" placeholder="+7 ___ ___-__-__">
                </div>
                <div class="loy-consult__field">
                    <label>Способ связи</label>
                    <select name="contact_method">
                        <option value="">Позвоните мне</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="telegram">Telegram</option>
                        <option value="email">Email</option>
                    </select>
                    <label class="loy-consult__consent">
                        <input type="checkbox" name="consent" required>
                        <span class="loy-consult__checkbox"></span>
                        <span>Отправляя форму, вы даёте согласие<br>на обработку персональных данных</span>
                    </label>
                </div>
                <button type="submit" class="loy-consult__btn"><?php echo esc_html($form_btn); ?></button>
            </form>
            <?php endif; ?>
        </div>

    </section>

    <?php
    $partners_page = get_page_by_path('partnerskaya-programma');
    $partners_id = $partners_page ? $partners_page->ID : null;
    get_template_part('template-parts/partner-about', null, ['post_id' => $partners_id]);
    ?>

    <?php get_template_part('template-parts/stats-cards'); ?>

    <?php get_template_part('template-parts/loyalty-form-banner'); ?>

</div>
</main>

<?php get_footer(); ?>
