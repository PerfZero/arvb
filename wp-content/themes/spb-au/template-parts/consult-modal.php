<?php
if (!defined('ABSPATH')) exit;

$title   = get_field('consultmodal_title',   'option') ?: 'Оставьте заявку<br>на бесплатную консультацию';
$text    = get_field('consultmodal_text',    'option') ?: 'Запишитесь к нам на бесплатную консультацию, за 11 лет мы завершили уже более 1300 дел о банкротстве со 100% результатом успеха. И на практике мы столкнулись с десятками тысяч разных сложных ситуаций, поэтому у нас точно есть ответ на ваш вопрос.';
$btn     = get_field('consultmodal_btn_text','option') ?: 'Отправить';
$consent = get_field('consultmodal_consent', 'option') ?: 'Отправляя форму, вы даёте согласие на обработку персональных данных';
?>
<div class="consult-modal" id="consult-modal" hidden aria-modal="true" role="dialog" aria-labelledby="consult-modal-title">
    <div class="consult-modal__overlay" data-consult-close></div>
    <div class="consult-modal__dialog">
        <button type="button" class="consult-modal__close" data-consult-close aria-label="Закрыть">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M1 1L17 17M17 1L1 17" stroke="#55627a" stroke-width="2" stroke-linecap="round"/></svg>
        </button>

        <span class="consult-modal__shape consult-modal__shape--1"></span>
        <span class="consult-modal__shape consult-modal__shape--2"></span>
        <span class="consult-modal__shape consult-modal__shape--3"></span>
        <span class="consult-modal__shape consult-modal__shape--4"></span>

        <h2 id="consult-modal-title" class="consult-modal__title"><?php echo wp_kses_post($title); ?></h2>
        <?php if ($text): ?>
        <p class="consult-modal__text"><?php echo wp_kses_post($text); ?></p>
        <?php endif; ?>

        <form class="consult-modal__form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" novalidate>
            <input type="hidden" name="action" value="spbau_consultmodal_submit">
            <?php wp_nonce_field('spbau_consultmodal_submit', 'spbau_consultmodal_nonce'); ?>
            <input type="hidden" name="redirect_url" value="">

            <div class="consult-modal__fields">
                <div class="consult-modal__field">
                    <label class="consult-modal__label">Ваше имя</label>
                    <input type="text" name="cm_name" class="consult-modal__input" placeholder="Имя">
                </div>
                <div class="consult-modal__field">
                    <label class="consult-modal__label">Номер телефона</label>
                    <div class="consult-modal__phone-wrap">
                        <span class="consult-modal__phone-flag">🇷🇺 +7</span>
                        <input type="tel" name="cm_phone" class="consult-modal__input consult-modal__input--phone" placeholder="___-___-__-__">
                    </div>
                </div>
                <div class="consult-modal__field">
                    <label class="consult-modal__label">Способ связи</label>
                    <div class="consult-modal__select-wrap">
                        <select name="cm_contact" class="consult-modal__select">
                            <option value="call">Позвоните мне</option>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="telegram">Telegram</option>
                        </select>
                    </div>
                </div>
                <div class="consult-modal__action">
                    <button type="submit" class="consult-modal__btn"><?php echo esc_html($btn); ?></button>
                    <label class="consult-modal__consent">
                        <input type="checkbox" name="cm_agree" class="consult-modal__consent-input">
                        <span class="consult-modal__consent-box" aria-hidden="true">
                            <svg width="12" height="10" viewBox="0 0 12 10" fill="none">
                                <path d="M1 5L4.2 8.2L11 1.2" stroke="#2d6bd9" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span><?php echo esc_html($consent); ?></span>
                    </label>
                </div>
            </div>
        </form>

        <div class="consult-modal__notice" hidden></div>
    </div>
</div>
