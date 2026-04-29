<?php
if (!defined('ABSPATH')) exit;

$title   = get_field('faqform_title',   'option') ?: 'Не нашли ответ на свой вопрос?';
$text    = get_field('faqform_text',    'option');
$btn     = get_field('faqform_btn_text','option') ?: 'Получить консультацию';
$consent = get_field('faqform_consent', 'option') ?: 'Отправляя форму, вы даёте согласие на обработку персональных данных';
$image   = get_field('faqform_image',   'option');
$form_status = isset($_GET["faqform_status"])
    ? sanitize_key($_GET["faqform_status"])
    : "";
$form_message = isset($_GET["faqform_message"])
    ? sanitize_text_field(wp_unslash($_GET["faqform_message"]))
    : "";
?>
<section class="faq-form" id="faq-form">
    <div class="faq-form__inner">

        <div class="faq-form__left">
            <h2 class="faq-form__title"><?php echo esc_html($title); ?></h2>
            <?php if ($text): ?>
            <p class="faq-form__text"><?php echo wp_kses_post($text); ?></p>
            <?php endif; ?>

            <form class="faq-form__form" action="<?php echo esc_url(
                admin_url("admin-post.php"),
            ); ?>" method="post" novalidate>
                <input type="hidden" name="action" value="spbau_faqform_submit">
                <?php wp_nonce_field("spbau_faqform_submit", "spbau_faqform_nonce"); ?>
                <div class="faq-form__body">
                    <!-- Поля слева -->
                    <div class="faq-form__fields">
                        <div class="faq-form__field faq-form__field--full">
                            <label class="faq-form__label">Опишите ваш вопрос</label>
                            <input type="text" name="faqform_question" class="faq-form__input" placeholder="Опишите ваш вопрос">
                        </div>
                        <div class="faq-form__row">
                            <div class="faq-form__field">
                                <label class="faq-form__label">Ваше имя</label>
                                <input type="text" name="faqform_name" class="faq-form__input" placeholder="Имя">
                            </div>
                            <div class="faq-form__field">
                                <label class="faq-form__label">Номер телефона</label>
                                <div class="faq-form__phone-wrap">
                                    <span class="faq-form__phone-flag">🇷🇺 +7</span>
                                    <input type="tel" name="faqform_phone" class="faq-form__input faq-form__input--phone" placeholder="___-___-__-__">
                                </div>
                            </div>
                            <div class="faq-form__field">
                                <label class="faq-form__label">Способ связи</label>
                                <div class="faq-form__select-wrap">
                                    <select name="faqform_contact" class="faq-form__select">
                                        <option value="call">Телефон</option>
                                        <option value="whatsapp">WhatsApp</option>
                                        <option value="telegram">Telegram</option>
                                        <option value="max">MAX</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Кнопка + согласие справа -->
                    <div class="faq-form__action">
                        <button type="submit" class="faq-form__btn"><?php echo esc_html($btn); ?></button>
                        <label class="faq-form__consent">
                            <input type="checkbox" name="faqform_agree" class="faq-form__consent-input">
                            <span class="faq-form__consent-box" aria-hidden="true">
                                <svg width="12" height="10" viewBox="0 0 12 10" fill="none">
                                    <path d="M1 5L4.2 8.2L11 1.2" stroke="#2d6bd9" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span><?php echo esc_html($consent); ?></span>
                        </label>
                    </div>
                </div>
            </form>
            <?php if ($form_status && $form_message): ?>
            <div class="faq-form__notice faq-form__notice--<?php echo esc_attr($form_status); ?>">
                <?php echo esc_html($form_message); ?>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($image): ?>
        <div class="faq-form__right">
            <span class="faq-form__circle"></span>
            <img class="faq-form__photo" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
        </div>
        <?php endif; ?>

    </div>
</section>
