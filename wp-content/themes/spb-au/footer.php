<?php
if (!defined("ABSPATH")) {
    exit();
} ?>

<?php
$footer_form_status = isset($_GET["footer_form_status"])
    ? sanitize_key($_GET["footer_form_status"])
    : "";
$footer_form_message = isset($_GET["footer_form_message"])
    ? sanitize_text_field(wp_unslash($_GET["footer_form_message"]))
    : "";
$reviews_link = get_post_type_archive_link("review") ?: home_url("/review/");

$contacts_phone = trim((string) get_field("contacts_phone", "option"));
$contacts_hours = trim((string) get_field("contacts_hours", "option"));
$contacts_address = trim((string) get_field("contacts_address", "option"));

$footer_form_title = trim((string) get_field("footer_form_title", "option"));
$footer_form_label = trim((string) get_field("footer_form_label", "option"));
$footer_form_phone_prefix = trim(
    (string) get_field("footer_form_phone_prefix", "option"),
);
$footer_form_btn_text = trim((string) get_field("footer_form_button_text", "option"));
$footer_form_consent_text = trim(
    (string) get_field("footer_form_consent_text", "option"),
);

$footer_messengers_label = trim(
    (string) get_field("footer_messengers_label", "option"),
);
$footer_dev_text = trim((string) get_field("footer_dev_text", "option"));
$footer_dev_url = trim((string) get_field("footer_dev_url", "option"));
$footer_copy_1 = trim((string) get_field("footer_copy_1", "option"));
$footer_copy_2 = trim((string) get_field("footer_copy_2", "option"));
$footer_company_name = trim((string) get_field("footer_company_name", "option"));
$footer_ogrn = trim((string) get_field("footer_ogrn", "option"));
$footer_inn = trim((string) get_field("footer_inn", "option"));

if ($contacts_phone === "") {
    $contacts_phone = "+7 999 999-99-99";
}
if ($contacts_hours === "") {
    $contacts_hours = "9:00–21:00";
}
if ($contacts_address === "") {
    $contacts_address =
        "195197, г. Санкт-Петербург,<br>ул. Бестужевская, дом 7,<br>корпус 3, квартира 379";
}
if ($footer_form_title === "") {
    $footer_form_title = "Задать вопрос";
}
if ($footer_form_label === "") {
    $footer_form_label = "Номер телефона";
}
if ($footer_form_phone_prefix === "") {
    $footer_form_phone_prefix = "🇷🇺 +7";
}
if ($footer_form_btn_text === "") {
    $footer_form_btn_text = "Получить консультацию";
}
if ($footer_form_consent_text === "") {
    $footer_form_consent_text =
        "Отправляя форму, вы даёте согласие<br>на обработку персональных данных";
}
if ($footer_messengers_label === "") {
    $footer_messengers_label = "Наши мессенджеры:";
}
if ($footer_copy_1 === "") {
    $footer_copy_1 = "© " . wp_date("Y") . " All Right Reserved";
}
if ($footer_copy_2 === "") {
    $footer_copy_2 = "Копирование материалов без согласия автора запрещено";
}
if ($footer_company_name === "") {
    $footer_company_name = "ООО «Санкт-Петербургский Арбитражный Управляющий»";
}
if ($footer_ogrn === "") {
    $footer_ogrn = "ОГРН: 1187847086844";
}
if ($footer_inn === "") {
    $footer_inn = "ИНН: 7810725470";
}

$contacts_phone_href = preg_replace("/\D+/", "", $contacts_phone);
$footer_messengers = get_field("social_messengers", "option");
if (!is_array($footer_messengers)) {
    $footer_messengers = [];
}
$footer_messengers_prepared = [];
foreach ($footer_messengers as $messenger) {
    $msg_icon = $messenger["icon"] ?? null;
    $msg_url = trim((string) ($messenger["url"] ?? "#"));
    if (!is_array($msg_icon) || empty($msg_icon["url"])) {
        continue;
    }
    $footer_messengers_prepared[] = [
        "url" => $msg_url,
        "icon_url" => (string) $msg_icon["url"],
        "icon_alt" => (string) ($msg_icon["alt"] ?? ""),
    ];
}
?>
<footer class="site-footer" id="site-footer">
    <div class="footer-inner">

        <div class="footer-top">
            <a href="<?php echo esc_url(home_url("/")); ?>" class="footer-logo">
                <img src="<?php echo esc_url(
                    get_template_directory_uri(),
                ); ?>/images/logo-white.svg"
                     alt="СПБ АУ" width="140" height="52">
            </a>
        </div>

        <div class="footer-main">

            <nav class="footer-col footer-nav-col">
                <?php wp_nav_menu([
                    "theme_location" => "footer_primary",
                    "menu_class" => "",
                    "container" => false,
                    "fallback_cb" => function () use ($reviews_link) {
                        echo '<ul>
                            <li><a href="/uslugi_po_bankrotstvu/">Услуги</a></li>
                            <li><a href="' . esc_url($reviews_link) . '">Отзывы</a></li>
                            <li><a href="/cases/">Завершённые дела</a></li>
                            <li><a href="/uslugi_po_bankrotstvu/">Завод Банкротств</a></li>
                            <li><a href="/stati/">Статьи</a></li>
                            <li><a href="/partnerskaya-programma/">Партнёрская программа</a></li>
                            <li><a href="/programma-loyalnosti/">Программа лояльности</a></li>
                            <li><a href="/my-v-smi/">Мы в СМИ</a></li>
                        </ul>';
                    },
                ]); ?>
            </nav>

            <nav class="footer-col footer-nav-col">
                <?php wp_nav_menu([
                    "theme_location" => "footer_secondary",
                    "menu_class" => "",
                    "container" => false,
                    "fallback_cb" => static function () {
                        echo '<ul>
                            <li><a href="/kontakty/">Контакты</a></li>
                            <li><a href="/stati/?cat=17">Банкротство физ. лиц</a></li>
                            <li><a href="/stati/?cat=10">Долги</a></li>
                            <li><a href="/stati/?cat=18">Кредиты</a></li>
                            <li><a href="/stati/?cat=7">Коллекторы</a></li>
                        </ul>';
                    },
                ]); ?>
            </nav>

            <div class="footer-col footer-contacts-col">
                <div class="footer-contact-item">
                    <span class="footer-contact-label">Телефон:</span>
                    <a href="tel:<?php echo esc_attr(
                        $contacts_phone_href,
                    ); ?>" class="footer-contact-value"><?php echo esc_html(
     $contacts_phone,
 ); ?></a>
                </div>
                <div class="footer-contact-item">
                    <span class="footer-contact-label">Время работы:</span>
                    <span class="footer-contact-value"><?php echo esc_html(
                        $contacts_hours,
                    ); ?></span>
                </div>
                <div class="footer-contact-item">
                    <span class="footer-contact-label">Юр. адрес:</span>
                    <span class="footer-contact-value"><?php echo wp_kses_post(
                        $contacts_address,
                    ); ?></span>
                </div>
            </div>

            <div class="footer-col footer-form-col">
                <h3 class="footer-form-title"><?php echo esc_html(
                    $footer_form_title,
                ); ?></h3>
                <form class="footer-form" action="<?php echo esc_url(
                    admin_url("admin-post.php"),
                ); ?>" method="post">
                    <input type="hidden" name="action" value="spbau_footer_form_submit">
                    <?php wp_nonce_field(
                        "spbau_footer_form_submit",
                        "spbau_footer_form_nonce",
                    ); ?>
                    <label class="footer-form-label"><?php echo esc_html(
                        $footer_form_label,
                    ); ?></label>
                    <div class="footer-phone-input">
                        <span class="footer-phone-flag"><?php echo esc_html(
                            $footer_form_phone_prefix,
                        ); ?></span>
                        <input type="tel" name="phone" placeholder="___-__-__" autocomplete="tel">
                    </div>
                    <label class="footer-form-consent">
                        <input type="checkbox" name="consent" required>
                        <span><?php echo wp_kses_post(
                            $footer_form_consent_text,
                        ); ?></span>
                    </label>
                    <button type="submit" class="footer-form-btn"><?php echo esc_html(
                        $footer_form_btn_text,
                    ); ?></button>
                    <?php if ($footer_form_status && $footer_form_message): ?>
                    <div class="footer-form__notice footer-form__notice--<?php echo esc_attr(
                        $footer_form_status,
                    ); ?>">
                        <?php echo esc_html($footer_form_message); ?>
                    </div>
                    <?php endif; ?>
                </form>
            </div>

        </div>

        <div class="footer-bottom">

            <div class="footer-bottom-left">
                <div class="footer-messengers">
                    <span class="footer-messengers-label"><?php echo esc_html(
                        $footer_messengers_label,
                    ); ?></span>
                    <div class="footer-messengers-icons">
                        <?php if (!empty($footer_messengers_prepared)): ?>
                            <?php foreach ($footer_messengers_prepared as $index => $messenger): ?>
                                <a href="<?php echo esc_url(
                                    $messenger["url"],
                                ); ?>" class="messenger-btn" target="_blank" rel="noopener">
                                    <img src="<?php echo esc_url(
                                        $messenger["icon_url"],
                                    ); ?>" alt="<?php echo esc_attr(
     $messenger["icon_alt"] ?: ("Messenger " . ($index + 1)),
 ); ?>" width="44" height="44">
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <a href="#" class="messenger-btn" aria-label="Telegram">
                                <img src="<?php echo esc_url(
                                    get_template_directory_uri(),
                                ); ?>/images/icon-telegram.svg" alt="Telegram" width="44" height="44">
                            </a>
                            <a href="#" class="messenger-btn" aria-label="WhatsApp">
                                <img src="<?php echo esc_url(
                                    get_template_directory_uri(),
                                ); ?>/images/icon-whatsapp.svg" alt="WhatsApp" width="44" height="44">
                            </a>
                            <a href="#" class="messenger-btn" aria-label="Viber">
                                <img src="<?php echo esc_url(
                                    get_template_directory_uri(),
                                ); ?>/images/icon-viber.svg" alt="Viber" width="44" height="44">
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($footer_dev_text !== ""): ?>
                    <div class="footer-dev">
                        <?php if ($footer_dev_url !== ""): ?>
                            <a href="<?php echo esc_url(
                                $footer_dev_url,
                            ); ?>" target="_blank" rel="noopener"><?php echo esc_html(
     $footer_dev_text,
 ); ?></a>
                        <?php else: ?>
                            <span><?php echo esc_html($footer_dev_text); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <p class="footer-copy"><?php echo esc_html($footer_copy_1); ?></p>
                <p class="footer-copy"><?php echo esc_html($footer_copy_2); ?></p>
            </div>

            <div class="footer-bottom-right">
                <p><?php echo esc_html($footer_company_name); ?></p>
                <p><?php echo esc_html($footer_ogrn); ?></p>
                <p><?php echo esc_html($footer_inn); ?></p>
                <div class="footer-legal-links">
                    <?php wp_nav_menu([
                        "theme_location" => "footer_legal",
                        "menu_class" => "",
                        "container" => false,
                        "fallback_cb" => static function () {
                            echo '<ul>
                                <li><a href="#">Оферта за рекомендацию</a></li>
                                <li><a href="#">Политика конфиденциальности</a></li>
                                <li><a href="#">Согласие на обработку персональных данных</a></li>
                            </ul>';
                        },
                    ]); ?>
                </div>
            </div>

        </div>

    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
