<?php
if (!defined("ABSPATH")) {
    exit();
}

$badge = get_field("marathon_badge", "option") ?: "МАРАФОН";
$title = get_field("marathon_title", "option") ?: "";
$bullets = get_field("marathon_bullets", "option") ?: [];
$btn_text = get_field("marathon_button_text", "option") ?: "Перейти в Telegram";
$image = get_field("marathon_image", "option");
$description = get_field("marathon_description", "option") ?: "";
$features = get_field("marathon_features", "option") ?: [];
$consent_text =
    "Отправляя форму, вы даёте согласие<br> на обработку персональных данных";
$form_status = isset($_GET["marathon_form_status"])
    ? sanitize_key($_GET["marathon_form_status"])
    : "";
$form_message = isset($_GET["marathon_form_message"])
    ? sanitize_text_field(wp_unslash($_GET["marathon_form_message"]))
    : "";
?>

<section class="marathon" id="marathon">
    <div class="marathon__inner">

        <!-- Левая часть -->
        <div class="marathon__left">
            <?php if ($badge): ?>
                <span class="marathon__badge"><?php echo esc_html(
                    $badge,
                ); ?></span>
            <?php endif; ?>

            <?php if ($title): ?>
                <h2 class="marathon__title"><?php echo esc_html($title); ?></h2>
            <?php endif; ?>

            <?php if ($bullets): ?>
                <ul class="marathon__bullets">
                    <?php foreach ($bullets as $item): ?>
                        <li><?php echo wp_kses($item["bullet_text"], [
                            "br" => [],
                        ]); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form class="marathon__form" action="<?php echo esc_url(
                admin_url("admin-post.php"),
            ); ?>" method="post" novalidate>
                <input type="hidden" name="action" value="spbau_marathon_submit">
                <?php wp_nonce_field(
                    "spbau_marathon_submit",
                    "spbau_marathon_nonce",
                ); ?>

                <div class="marathon__actions">
                    <div class="marathon__field">
                        <div class="marathon__phone-wrap">
                            <input type="tel" name="marathon_phone" class="marathon__input" placeholder="___-___-__-__" required>
                        </div>
                    </div>

                    <div class="marathon__submit-wrap">
                        <button type="submit" class="marathon__btn">
                            <?php echo esc_html($btn_text); ?>
                        </button>

                        <label class="marathon__consent">
                            <input type="checkbox" name="marathon_agree" class="marathon__consent-input" required>
                            <span class="marathon__consent-box" aria-hidden="true">
                                <svg width="12" height="10" viewBox="0 0 12 10" fill="none">
                                    <path d="M1 5L4.2 8.2L11 1.2" stroke="#2d6bd9" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="marathon__consent-text"><?php echo wp_kses(
                                $consent_text,
                                ["br" => []],
                            ); ?></span>
                        </label>
                    </div>
                </div>

                <?php if ($form_status && $form_message): ?>
                    <div class="marathon__notice marathon__notice--<?php echo esc_attr(
                        $form_status,
                    ); ?>">
                        <?php echo esc_html($form_message); ?>
                    </div>
                <?php endif; ?>

            </form>

            <?php if ($image): ?>
                <div class="marathon__image">
                    <img src="<?php echo esc_url($image["url"]); ?>"
                         alt="<?php echo esc_attr($image["alt"]); ?>">
                </div>
            <?php endif; ?>
        </div>

        <!-- Правая часть -->
        <div class="marathon__right">
            <?php if ($description): ?>
                <div class="marathon__desc"><?php echo wp_kses_post(
                    $description,
                ); ?></div>
            <?php endif; ?>

            <?php if ($features): ?>
                <div class="marathon__features">
                    <?php foreach ($features as $feat): ?>
                        <div class="marathon-feat">
                            <?php if (!empty($feat["feature_icon"])): ?>
                                <div class="marathon-feat__icon">
                                    <img src="<?php echo esc_url(
                                        $feat["feature_icon"]["url"],
                                    ); ?>"
                                         alt="">
                                </div>
                            <?php endif; ?>
                            <h3 class="marathon-feat__title"><?php echo esc_html(
                                $feat["feature_title"],
                            ); ?></h3>
                            <p class="marathon-feat__text"><?php echo esc_html(
                                $feat["feature_text"],
                            ); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>
