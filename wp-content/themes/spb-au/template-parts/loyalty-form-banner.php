<?php
if (!defined('ABSPATH')) exit;

$title    = get_field('lf_title',    'option') ?: 'Готовы стать официальным партнером и получать доход от привлеченных клиентов?';
$btn_text = get_field('lf_btn_text', 'option') ?: 'Получить консультацию';
$image    = get_field('lf_image',    'option');

$form_status  = isset($_GET['lfbanner_status'])  ? sanitize_key($_GET['lfbanner_status'])                          : '';
$form_message = isset($_GET['lfbanner_message']) ? sanitize_text_field(wp_unslash($_GET['lfbanner_message'])) : '';
?>
<section class="lf-banner">
    <div class="lf-banner__inner">

        <div class="lf-banner__left">
            <h2 class="lf-banner__title"><?php echo esc_html($title); ?></h2>

            <form class="lf-banner__form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" novalidate>
                <input type="hidden" name="action" value="spbau_lfbanner_submit">
                <?php wp_nonce_field('spbau_lfbanner_submit', 'spbau_lfbanner_nonce'); ?>

                <div class="lf-banner__field">
                    <label class="lf-banner__label">Ваше имя</label>
                    <input type="text" name="lf_name" class="lf-banner__input" placeholder="Имя">
                </div>

                <div class="lf-banner__field">
                    <label class="lf-banner__label">Номер телефона</label>
                    <div class="lf-banner__phone-wrap">
                        <input type="tel" name="lf_phone" class="lf-banner__input" placeholder="___-___-__-__">
                    </div>
                </div>

                <div class="lf-banner__field">
                    <label class="lf-banner__label">Способ связи</label>
                    <div class="lf-banner__select-wrap">
                        <select name="lf_contact" class="lf-banner__select">
                            <option value="call">Телефон</option>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="telegram">Telegram</option>
                            <option value="max">MAX</option>
                            <option value="email">Email</option>
                        </select>
                    </div>
                </div>

                <label class="lf-banner__consent">
                    <input type="checkbox" name="lf_agree" class="lf-banner__consent-input">
                    <span class="lf-banner__consent-box" aria-hidden="true">
                        <svg width="12" height="10" viewBox="0 0 12 10" fill="none">
                            <path d="M1 5L4.2 8.2L11 1.2" stroke="#2d6bd9" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span>Отправляя форму, вы даёте согласие<br>на обработку персональных данных</span>
                </label>

                <button type="submit" class="lf-banner__btn"><?php echo esc_html($btn_text); ?></button>
            </form>

            <?php if ($form_status && $form_message): ?>
            <div class="lf-banner__notice lf-banner__notice--<?php echo esc_attr($form_status); ?>">
                <?php echo esc_html($form_message); ?>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($image): ?>
        <div class="lf-banner__right">
            <span class="lf-banner__deco lf-banner__deco--sq"></span>
            <span class="lf-banner__deco lf-banner__deco--ci"></span>
            <div class="lf-banner__circle-wrap">
                <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" class="lf-banner__photo">
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>
