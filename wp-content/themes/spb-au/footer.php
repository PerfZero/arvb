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
?>
<footer class="site-footer" id="site-footer">
    <div class="footer-inner">

        <div class="footer-top">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="footer-logo">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/logo-white.svg"
                     alt="СПБ АУ" width="140" height="52">
            </a>
        </div>

        <div class="footer-main">

            <nav class="footer-col footer-nav-col">
                <ul>
                    <li><a href="#">Услуги</a></li>
                    <li><a href="#">Отзывы</a></li>
                    <li><a href="#">Завершённые дела</a></li>
                    <li><a href="#">Завод Банкротств</a></li>
                    <li><a href="#">Статьи</a></li>
                    <li><a href="#">Партнёрская программа</a></li>
                    <li><a href="#">Программа лояльности</a></li>
                    <li><a href="#">Мы в СМИ</a></li>
                </ul>
            </nav>

            <nav class="footer-col footer-nav-col">
                <ul>
                    <li><a href="#">О компании</a></li>
                    <li><a href="#">Контакты</a></li>
                    <li><a href="#">Банкротство физ. лиц</a></li>
                    <li><a href="#">Долги</a></li>
                    <li><a href="#">Кредиты</a></li>
                    <li><a href="#">Коллекторы</a></li>
                </ul>
            </nav>

            <div class="footer-col footer-contacts-col">
                <div class="footer-contact-item">
                    <span class="footer-contact-label">Телефон:</span>
                    <a href="tel:+79999999999" class="footer-contact-value">+7 999 999-99-99</a>
                </div>
                <div class="footer-contact-item">
                    <span class="footer-contact-label">Время работы:</span>
                    <span class="footer-contact-value">9:00–21:00</span>
                </div>
                <div class="footer-contact-item">
                    <span class="footer-contact-label">Юр. адрес:</span>
                    <span class="footer-contact-value">195197, г.. Санкт Петербург,<br>ул.. Бестужевская, дом. 7,<br>корпус 3, квартира 379</span>
                </div>
            </div>

            <div class="footer-col footer-form-col">
                <h3 class="footer-form-title">Задать вопрос</h3>
                <form class="footer-form" action="<?php echo esc_url(
                    admin_url("admin-post.php"),
                ); ?>" method="post">
                    <input type="hidden" name="action" value="spbau_footer_form_submit">
                    <?php wp_nonce_field(
                        "spbau_footer_form_submit",
                        "spbau_footer_form_nonce",
                    ); ?>
                    <label class="footer-form-label">Номер телефона</label>
                    <div class="footer-phone-input">
                        <span class="footer-phone-flag">🇷🇺 +7</span>
                        <input type="tel" name="phone" placeholder="___-__-__" autocomplete="tel">
                    </div>
                    <label class="footer-form-consent">
                        <input type="checkbox" name="consent" required>
                        <span>Отправляя форму, вы даёте согласие<br>на обработку персональных данных</span>
                    </label>
                    <button type="submit" class="footer-form-btn">Получить консультацию</button>
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
                    <span class="footer-messengers-label">Наши мессенджеры:</span>
                    <div class="footer-messengers-icons">
                        <a href="#" class="messenger-btn" aria-label="Telegram">
                            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/icon-telegram.svg" alt="Telegram" width="44" height="44">
                        </a>
                        <a href="#" class="messenger-btn" aria-label="WhatsApp">
                            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/icon-whatsapp.svg" alt="WhatsApp" width="44" height="44">
                        </a>
                        <a href="#" class="messenger-btn" aria-label="Viber">
                            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/icon-viber.svg" alt="Viber" width="44" height="44">
                        </a>
                    </div>
                </div>
                <div class="footer-dev">
                    <a href="#">Разработка сайта</a>
                </div>
                <p class="footer-copy">© 2026 All Right Reserved</p>
                <p class="footer-copy">Копирование материалов без согласия автора запрещено</p>
            </div>

            <div class="footer-bottom-right">
                <p>ООО «Санкт-Петербургский Арбитражный Управляющий»</p>
                <p>ОГРН: 1187847086844</p>
                <p>ИНН: 7810725470</p>
                <div class="footer-legal-links">
                    <a href="#">Оферта за рекомендацию</a>
                    <a href="#">Политика конфиденциальности</a>
                    <a href="#">Согласие на обработку персональных данных</a>
                </div>
            </div>

        </div>

    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
