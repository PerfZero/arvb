<?php
if (!defined("ABSPATH")) {
    exit();
}

get_header();

// ── Hero ──────────────────────────────────────────────────────────────────────

$h_title = get_field("hero_title");
$h_quote = get_field("hero_quote");
$h_stat_num = get_field("hero_stat_number");
$h_stat_text = get_field("hero_stat_text");
$h_image = get_field("hero_image");
$h_btn_text = get_field("hero_btn_text") ?: "Решить мою проблему";
$h_btn_url = get_field("hero_btn_url");
$h_card_title = get_field("hero_card_title");
$h_card_text = get_field("hero_card_text");
$h_card_btn_t = get_field("hero_card_btn_text") ?: "Пройти тест";
$h_card_btn_url = get_field("hero_card_btn_url");
$h_yandex_img = get_field("hero_yandex_image");
$h_ticker = get_field("hero_ticker");
?>

<main class="homepage">

    <!-- ═══ HERO ═══ -->
    <section class="hero">
        <div class="hero__inner">

            <?php if ($h_title): ?>
            <h1 class="hero__title"><?php echo wp_kses_post($h_title); ?></h1>
            <?php endif; ?>

            <div class="hero__body">

                <!-- Left -->
                <div class="hero__left">
                    <?php if ($h_quote): ?>
                    <div class="hero__quote">
                        <svg width="26" height="20" viewBox="0 0 26 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.34482 -0.000372914L10.3085 2.69963C9.54482 3.24508 8.83573 3.89963 8.18118 4.66326C7.52664 5.4269 6.95391 6.24508 6.463 7.11781C5.91754 7.99054 5.53572 8.86326 5.31754 9.73599C6.73573 9.84508 7.88118 10.3087 8.75391 11.1269C9.62663 11.9451 10.063 13.036 10.063 14.3996C10.063 15.9269 9.59936 17.0996 8.67209 17.9178C7.69027 18.736 6.463 19.1451 4.99027 19.1451C3.29936 19.0905 2.04482 18.3814 1.22663 17.0178C0.408452 15.7087 -0.000639465 14.2633 -0.000639465 12.6814C-0.000639465 10.8814 0.381179 9.10872 1.14481 7.36326C1.90845 5.61781 2.94482 4.09054 4.25391 2.78145C5.50845 1.47235 6.87209 0.545082 8.34482 -0.000372914ZM23.8085 -0.000372914L25.7721 2.69963C25.0085 3.24508 24.2994 3.89963 23.6448 4.66326C22.9903 5.4269 22.4175 6.24508 21.9266 7.11781C21.3812 7.99054 20.9994 8.86326 20.7812 9.73599C22.1994 9.84508 23.3448 10.3087 24.2175 11.1269C25.0903 11.9451 25.5266 13.036 25.5266 14.3996C25.5266 15.9269 25.063 17.0996 24.1357 17.9178C23.1539 18.736 21.9266 19.1451 20.4539 19.1451C18.763 19.0905 17.5085 18.3814 16.6903 17.0178C15.8721 15.7087 15.463 14.2633 15.463 12.6814C15.463 10.8814 15.8721 9.10872 16.6903 7.36326C17.4539 5.61781 18.463 4.09054 19.7175 2.78145C20.9721 1.47235 22.3357 0.545082 23.8085 -0.000372914Z" fill="black"/>
                        </svg>

                        <?php echo wp_kses_post($h_quote); ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($h_stat_num): ?>
                    <div class="hero__stat">
                        <div class="hero__stat-number"><?php echo esc_html(
                            $h_stat_num,
                        ); ?></div>
                        <div class="hero__stat-text"><?php echo esc_html(
                            $h_stat_text,
                        ); ?></div>
                    </div>
                    <?php endif; ?>


                </div>

                <!-- Center -->
                <div class="hero__center">
                    <div class="hero__circle"></div>
                    <?php if ($h_image): ?>
                    <img class="hero__people" src="<?php echo esc_url(
                        $h_image["url"],
                    ); ?>" alt="<?php echo esc_attr($h_image["alt"]); ?>">
                    <?php else: ?>
                    <div class="hero__people-placeholder"></div>
                    <?php endif; ?>

                    <button type="button" class="hero__cta" data-consult-open>
                        <?php echo esc_html($h_btn_text); ?>
                    </button>
                </div>

                <!-- Right -->
                <div class="hero__right">
                    <?php if ($h_card_title || $h_card_text): ?>
                    <div class="hero__card">
                        <div class="hero__card-bookmark"><svg width="24" height="56" viewBox="0 0 24 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M0 55.2809V0H23.3408V55.2809L11.6704 35.234L0 55.2809Z" fill="url(#paint0_linear_735_19212)" />
                          <defs>
                            <linearGradient id="paint0_linear_735_19212" x1="11.6704" y1="0" x2="11.6704" y2="55.2809" gradientUnits="userSpaceOnUse">
                              <stop stop-color="#2D6BD9" />
                              <stop offset="1" stop-color="#75A7FF" />
                            </linearGradient>
                          </defs>
                        </svg></div>
                        <?php if ($h_card_title): ?>
                        <div class="hero__card-title"><?php echo wp_kses_post(
                            $h_card_title,
                        ); ?></div>
                        <?php endif; ?>
                        <?php if ($h_card_text): ?>
                        <div class="hero__card-text"><?php echo wp_kses_post(
                            $h_card_text,
                        ); ?></div>
                        <?php endif; ?>
                        <?php if ($h_card_btn_url): ?>
                        <a href="<?php echo esc_url(
                            $h_card_btn_url,
                        ); ?>" class="hero__card-btn">
                            <?php echo esc_html($h_card_btn_t); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($h_yandex_img): ?>
                    <div class="hero__yandex">
                        <img src="<?php echo esc_url(
                            $h_yandex_img["url"],
                        ); ?>" alt="<?php echo esc_attr(
    $h_yandex_img["alt"],
); ?>">
                    </div>
                    <?php else: ?>
                    <div class="hero__yandex hero__yandex--placeholder">
                        <div class="hero__yandex-logo">Яндекс</div>
                        <div class="hero__yandex-rating">★★★★★ 5,0</div>
                        <div class="hero__yandex-count">315+ оценок</div>
                    </div>
                    <?php endif; ?>
                </div>

            </div><!-- /.hero__body -->



        </div><!-- /.hero__inner -->

        <?php if ($h_ticker): ?>
        <div class="hero__ticker-wrap">
            <div class="hero__ticker">
                <span><?php echo esc_html($h_ticker); ?></span>
                <span><?php echo esc_html($h_ticker); ?></span>
                <span><?php echo esc_html($h_ticker); ?></span>
            </div>
        </div>
        <?php endif; ?>
    </section>

    <?php
    // ── Expertise ──────────────────────────────────────────────────────────────
    $exp_badge = get_field("exp_badge") ?: "ЭКСПЕРТИЗА";
    $exp_stats = get_field("exp_stats");
    $exp_video_thumb = get_field("exp_video_thumb");
    $exp_video_source_raw = get_field("exp_video_source");
    $exp_video_source = in_array($exp_video_source_raw, ["url", "file"], true)
        ? $exp_video_source_raw
        : "url";
    $exp_video_url_raw = get_field("exp_video_url");
    $exp_video_url = is_string($exp_video_url_raw)
        ? trim($exp_video_url_raw)
        : "";
    $exp_video_file_raw = get_field("exp_video_file");
    $exp_video_file_url = "";
    if (is_array($exp_video_file_raw) && !empty($exp_video_file_raw["url"])) {
        $exp_video_file_url = (string) $exp_video_file_raw["url"];
    } elseif (is_string($exp_video_file_raw)) {
        $exp_video_file_url = $exp_video_file_raw;
    } elseif (is_numeric($exp_video_file_raw)) {
        $video_file_attachment_url = wp_get_attachment_url(
            (int) $exp_video_file_raw,
        );
        if (is_string($video_file_attachment_url)) {
            $exp_video_file_url = $video_file_attachment_url;
        }
    }
    $exp_video_file_url = esc_url_raw(trim($exp_video_file_url));
    $exp_video_url = esc_url_raw($exp_video_url);
    $exp_video_link = "";
    if ($exp_video_source === "file" && $exp_video_file_url !== "") {
        $exp_video_link = $exp_video_file_url;
    } elseif ($exp_video_source === "url" && $exp_video_url !== "") {
        $exp_video_link = $exp_video_url;
    } elseif ($exp_video_file_url !== "") {
        $exp_video_link = $exp_video_file_url;
    } elseif ($exp_video_url !== "") {
        $exp_video_link = $exp_video_url;
    }
    $exp_name = get_field("exp_name");
    $exp_quote = get_field("exp_quote");
    $exp_tg_title = get_field("exp_tg_title");
    $exp_tg_text = get_field("exp_tg_text");
    $exp_tg_url_raw = get_field("exp_tg_url");
    $exp_tg_url = is_string($exp_tg_url_raw) ? trim($exp_tg_url_raw) : "";
    if (function_exists("spbau_normalize_telegram_url")) {
        $exp_tg_url = spbau_normalize_telegram_url($exp_tg_url);
    }
    if ($exp_tg_url === "") {
        $exp_tg_fallback_raw = get_field("marathon_button_url", "option");
        if (is_string($exp_tg_fallback_raw)) {
            $exp_tg_fallback_raw = trim($exp_tg_fallback_raw);
            $exp_tg_url = function_exists("spbau_normalize_telegram_url")
                ? spbau_normalize_telegram_url($exp_tg_fallback_raw)
                : $exp_tg_fallback_raw;
        }
    }
    $exp_tg_btn = get_field("exp_tg_btn_text") ?: "Перейти в Telegram";
    $expertise_form_status = isset($_GET["expertise_form_status"])
        ? sanitize_key($_GET["expertise_form_status"])
        : "";
    $expertise_form_message = isset($_GET["expertise_form_message"])
        ? sanitize_text_field(wp_unslash($_GET["expertise_form_message"]))
        : "";
    ?>

    <!-- ═══ EXPERTISE ═══ -->
    <section class="expertise" id="expertise">
        <div class="expertise__inner">

            <!-- Badge -->
            <div class="expertise__top">
                <span class="expertise__badge"><?php echo esc_html(
                    $exp_badge,
                ); ?></span>
                <span class="expertise__diamond"></span>
            </div>

            <!-- Stats row -->
            <?php if ($exp_stats): ?>
            <div class="expertise__stats">
                <?php foreach ($exp_stats as $stat): ?>
                <div class="expertise__stat-card">
                    <div class="expertise__stat-header">
                        <span class="expertise__stat-label"><?php echo esc_html(
                            $stat["exp_stat_label"],
                        ); ?></span>
                        <?php if ($stat["exp_stat_icon"]): ?>
                        <img class="expertise__stat-icon" src="<?php echo esc_url(
                            $stat["exp_stat_icon"]["url"],
                        ); ?>" alt="">
                        <?php endif; ?>
                    </div>
                    <div class="expertise__stat-number"><?php echo esc_html(
                        $stat["exp_stat_number"],
                    ); ?></div>
                    <div class="expertise__stat-desc"><?php echo esc_html(
                        $stat["exp_stat_desc"],
                    ); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Bottom: video + about -->
            <div class="expertise__bottom">

                <!-- Video -->
                <div class="expertise__video-wrap">
                    <?php if ($exp_video_link): ?>
                    <a href="<?php echo esc_url(
                        $exp_video_link,
                    ); ?>" class="expertise__video glightbox" data-type="video">
                    <?php else: ?>
                    <div class="expertise__video">
                    <?php endif; ?>
                        <?php if ($exp_video_thumb): ?>
                        <img src="<?php echo esc_url(
                            $exp_video_thumb["url"],
                        ); ?>" alt="<?php echo esc_attr(
    $exp_video_thumb["alt"],
); ?>">
                        <?php else: ?>
                        <div class="expertise__video-placeholder"></div>
                        <?php endif; ?>
                        <div class="expertise__play">
                            <svg width="24" height="28" viewBox="0 0 24 28" fill="none">
                                <path d="M23 14L1 27V1L23 14Z" fill="white"/>
                            </svg>
                        </div>
                    <?php echo $exp_video_link ? "</a>" : "</div>"; ?>
                </div>

                <!-- About -->
                <div class="expertise__about">
                    <?php if ($exp_name): ?>
                    <h2 class="expertise__name"><?php echo wp_kses_post(
                        $exp_name,
                    ); ?></h2>
                    <?php endif; ?>
                    <?php if ($exp_quote): ?>
                    <p class="expertise__quote">"<?php echo esc_html(
                        $exp_quote,
                    ); ?>"</p>
                    <?php endif; ?>

                    <?php if ($exp_tg_title || $exp_tg_text): ?>
                    <div class="expertise__tg-card">
                        <div class="expertise__tg-plane">
                            <img src="<?php echo get_template_directory_uri(); ?>/images/tg.png" alt="">

                        </div>
                        <?php if ($exp_tg_title): ?>
                        <div class="expertise__tg-title"><?php echo esc_html(
                            $exp_tg_title,
                        ); ?></div>
                        <?php endif; ?>
                        <?php if ($exp_tg_text): ?>
                        <div class="expertise__tg-text"><?php echo wp_kses_post(
                            $exp_tg_text,
                        ); ?></div>
                        <?php endif; ?>
                        <form class="expertise__tg-form" data-expertise-tg-form action="<?php echo esc_url(
                            admin_url("admin-post.php"),
                        ); ?>" method="post" novalidate>
                            <input type="hidden" name="action" value="spbau_expertise_tg_submit">
                            <?php wp_nonce_field(
                                "spbau_expertise_tg_submit",
                                "spbau_expertise_tg_nonce",
                            ); ?>
                            <?php if ($exp_tg_url): ?>
                            <input type="hidden" name="expertise_tg_target" value="<?php echo esc_url(
                                $exp_tg_url,
                            ); ?>">
                            <?php endif; ?>
                            <div class="expertise__tg-actions">
                                <input class="expertise__tg-phone" type="tel" name="expertise_tg_phone" placeholder="+7 (___)-___-__-__" required data-expertise-phone>
                                <button type="submit" class="expertise__tg-btn" data-expertise-submit>
                                    <img src="<?php echo get_template_directory_uri(); ?>/images/icon-telegram.svg" alt="" width="18" height="18">
                                    <?php echo esc_html($exp_tg_btn); ?>
                                </button>
                            </div>
                            <?php if (
                                $expertise_form_status &&
                                $expertise_form_message
                            ): ?>
                            <div class="expertise__tg-notice expertise__tg-notice--<?php echo esc_attr(
                                $expertise_form_status,
                            ); ?>">
                                <?php echo esc_html($expertise_form_message); ?>
                            </div>
                            <?php endif; ?>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>

            </div><!-- /.expertise__bottom -->

        </div><!-- /.expertise__inner -->
    </section>

    <?php
    // ── Problems ───────────────────────────────────────────────────────────────
    $prob_title = get_field("prob_title");
    $prob_items = get_field("prob_items") ?: [];
    $prob_key_img = get_field("prob_key_image");
    $prob_docs_img = get_field("prob_docs_image");
    $prob_cta_title = get_field("prob_cta_title");
    $prob_cta_text = get_field("prob_cta_text");
    $prob_cta_btn_t =
        get_field("prob_cta_btn_text") ?: "Записаться на консультацию";
    $prob_cta_btn_u = get_field("prob_cta_btn_url");
    $prob_cta_is_popup = trim((string) $prob_cta_btn_u) === "#";
    $prob_cta_img = get_field("prob_cta_image");
    $prob_text_allowed = [
        "br" => [],
    ];
    ?>

    <!-- ═══ PROBLEMS ═══ -->
    <section class="problems">
        <div class="problems__inner">

            <div class="prob-grid">

                <!-- div1: title, col 1-2, row 1 -->
                <div class="prob-grid__title">
                    <?php if ($prob_title): ?>
                    <h2><?php echo wp_kses_post($prob_title); ?></h2>
                    <?php endif; ?>
                    <span class="prob-deco prob-deco--1"></span>
                    <span class="prob-deco prob-deco--2"></span>
                    <span class="prob-deco prob-deco--3"></span>
                </div>

                <div class="prob-mobile" data-prob-mobile>
                    <div class="prob-mobile__viewport" data-prob-viewport>
                        <div class="prob-mobile__track">
                            <?php foreach ($prob_items ?: [] as $item): ?>
                            <?php if (empty($item["prob_item_text"])) {
                                continue;
                            } ?>
                            <article class="prob-mobile__card">
                                <?php if (!empty($item["prob_item_icon"])): ?>
                                <div class="prob-mobile__icon">
                                    <img src="<?php echo esc_url(
                                        $item["prob_item_icon"]["url"],
                                    ); ?>" alt="">
                                </div>
                                <?php endif; ?>
                                <p class="prob-mobile__text"><?php echo wp_kses(
                                    $item["prob_item_text"],
                                    $prob_text_allowed,
                                ); ?></p>
                            </article>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="prob-mobile__nav">
                        <button type="button" class="prob-mobile__arrow prob-mobile__arrow--prev" data-prob-prev aria-label="Предыдущая карточка">
                            <span aria-hidden="true">‹</span>
                        </button>
                        <button type="button" class="prob-mobile__arrow prob-mobile__arrow--next" data-prob-next aria-label="Следующая карточка">
                            <span aria-hidden="true">›</span>
                        </button>
                    </div>
                </div>

                <!-- div2: card 1, col 3, row 1 -->
                <?php $c = $prob_items[0] ?? null; ?>
                <div class="prob-card prob-div2">
                    <?php if ($c && $c["prob_item_icon"]): ?>
                    <div class="prob-card__icon"><img src="<?php echo esc_url(
                        $c["prob_item_icon"]["url"],
                    ); ?>" alt=""></div>
                    <?php endif; ?>
                    <p class="prob-card__text"><?php echo wp_kses(
                        $c["prob_item_text"] ?? "",
                        $prob_text_allowed,
                    ); ?></p>
                </div>

                <!-- div3: card 2 + key image, col 4, rows 1-2 -->
                <?php $c = $prob_items[1] ?? null; ?>
                <div class="prob-card prob-card--tall prob-div3">
                    <?php if ($c && $c["prob_item_icon"]): ?>
                    <div class="prob-card__icon"><img src="<?php echo esc_url(
                        $c["prob_item_icon"]["url"],
                    ); ?>" alt=""></div>
                    <?php endif; ?>
                    <p class="prob-card__text"><?php echo wp_kses(
                        $c["prob_item_text"] ?? "",
                        $prob_text_allowed,
                    ); ?></p>
                    <?php if ($prob_key_img): ?>
                    <div class="prob-card__photo">
                        <img src="<?php echo esc_url(
                            $prob_key_img["url"],
                        ); ?>" alt="<?php echo esc_attr(
    $prob_key_img["alt"],
); ?>">
                    </div>
                    <?php endif; ?>
                </div>

                <!-- div4: card 3, col 1, row 2 -->
                <?php $c = $prob_items[2] ?? null; ?>
                <div class="prob-card prob-div4">
                    <?php if ($c && $c["prob_item_icon"]): ?>
                    <div class="prob-card__icon"><img src="<?php echo esc_url(
                        $c["prob_item_icon"]["url"],
                    ); ?>" alt=""></div>
                    <?php endif; ?>
                    <p class="prob-card__text"><?php echo wp_kses(
                        $c["prob_item_text"] ?? "",
                        $prob_text_allowed,
                    ); ?></p>
                </div>

                <!-- div5: card 4, col 2, row 2 -->
                <?php $c = $prob_items[3] ?? null; ?>
                <div class="prob-card prob-div5">
                    <?php if ($c && $c["prob_item_icon"]): ?>
                    <div class="prob-card__icon"><img src="<?php echo esc_url(
                        $c["prob_item_icon"]["url"],
                    ); ?>" alt=""></div>
                    <?php endif; ?>
                    <p class="prob-card__text"><?php echo wp_kses(
                        $c["prob_item_text"] ?? "",
                        $prob_text_allowed,
                    ); ?></p>
                </div>

                <!-- div6: card 5, col 3, row 2 -->
                <?php $c = $prob_items[4] ?? null; ?>
                <div class="prob-card prob-div6">
                    <?php if ($c && $c["prob_item_icon"]): ?>
                    <div class="prob-card__icon"><img src="<?php echo esc_url(
                        $c["prob_item_icon"]["url"],
                    ); ?>" alt=""></div>
                    <?php endif; ?>
                    <p class="prob-card__text"><?php echo wp_kses(
                        $c["prob_item_text"] ?? "",
                        $prob_text_allowed,
                    ); ?></p>
                </div>

                <!-- div7: card 6 + docs image, cols 1-2, row 3 -->
                <?php $c = $prob_items[5] ?? null; ?>
                <div class="prob-card prob-card--wide prob-div7">
                    <?php if ($c && $c["prob_item_icon"]): ?>
                    <div class="prob-card__icon"><img src="<?php echo esc_url(
                        $c["prob_item_icon"]["url"],
                    ); ?>" alt=""></div>
                    <?php endif; ?>
                    <p class="prob-card__text"><?php echo wp_kses(
                        $c["prob_item_text"] ?? "",
                        $prob_text_allowed,
                    ); ?></p>
                    <?php if ($prob_docs_img): ?>
                    <div class="prob-card__photo prob-card__photo--wide">
                        <img src="<?php echo esc_url(
                            $prob_docs_img["url"],
                        ); ?>" alt="<?php echo esc_attr(
    $prob_docs_img["alt"],
); ?>">
                    </div>
                    <?php endif; ?>
                </div>

                <!-- div8: card 7, col 3, row 3 -->
                <?php $c = $prob_items[6] ?? null; ?>
                <div class="prob-card prob-div8">
                    <?php if ($c && $c["prob_item_icon"]): ?>
                    <div class="prob-card__icon"><img src="<?php echo esc_url(
                        $c["prob_item_icon"]["url"],
                    ); ?>" alt=""></div>
                    <?php endif; ?>
                    <p class="prob-card__text"><?php echo wp_kses(
                        $c["prob_item_text"] ?? "",
                        $prob_text_allowed,
                    ); ?></p>
                </div>

                <!-- div9: card 8, col 4, row 3 -->
                <?php $c = $prob_items[7] ?? null; ?>
                <div class="prob-card prob-div9">
                    <?php if ($c && $c["prob_item_icon"]): ?>
                    <div class="prob-card__icon"><img src="<?php echo esc_url(
                        $c["prob_item_icon"]["url"],
                    ); ?>" alt=""></div>
                    <?php endif; ?>
                    <p class="prob-card__text"><?php echo wp_kses(
                        $c["prob_item_text"] ?? "",
                        $prob_text_allowed,
                    ); ?></p>
                </div>

                <!-- div10: CTA banner, all 4 cols, row 4 -->
                <?php if ($prob_cta_title): ?>
                <div class="prob-cta prob-div10">
                    <div class="prob-cta__left">
                        <h2 class="prob-cta__title"><?php echo wp_kses_post(
                            $prob_cta_title,
                        ); ?></h2>
                        <?php if ($prob_cta_text): ?>
                        <p class="prob-cta__text"><?php echo wp_kses_post(
                            $prob_cta_text,
                        ); ?></p>
                        <?php endif; ?>
                        <?php if ($prob_cta_btn_u): ?>
                        <a href="<?php echo esc_url(
                            $prob_cta_btn_u,
                        ); ?>" class="prob-cta__btn"<?php echo $prob_cta_is_popup
    ? " data-consult-open"
    : ""; ?>>
                            <?php echo esc_html($prob_cta_btn_t); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="prob-cta__right">
                        <div class="prob-cta__circle"></div>
                        <?php if ($prob_cta_img): ?>
                        <img class="prob-cta__person" src="<?php echo esc_url(
                            $prob_cta_img["url"],
                        ); ?>" alt="<?php echo esc_attr(
    $prob_cta_img["alt"],
); ?>">
                        <?php endif; ?>
                        <?php if ($prob_cta_btn_u): ?>
                        <a href="#open" class="prob-cta__btn prob-cta__btn--mobile"<?php echo $prob_cta_is_popup
                            ? " data-consult-open"
                            : ""; ?>>
                            <?php echo esc_html($prob_cta_btn_t); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div><!-- /.prob-grid -->

        </div><!-- /.problems__inner -->
    </section>

    <script>
    document.querySelectorAll('[data-prob-mobile]').forEach(function(block) {
        var viewport = block.querySelector('[data-prob-viewport]');
        var prev = block.querySelector('[data-prob-prev]');
        var next = block.querySelector('[data-prob-next]');
        if (!viewport || !prev || !next) return;

        function stepSize() {
            return Math.round(viewport.clientWidth * 0.86);
        }

        prev.addEventListener('click', function() {
            viewport.scrollBy({ left: -stepSize(), behavior: 'smooth' });
        });

        next.addEventListener('click', function() {
            viewport.scrollBy({ left: stepSize(), behavior: 'smooth' });
        });
    });
    </script>

    <!-- ═══ CASES PREVIEW ═══ -->
    <?php
    $cases_badge = get_field("cases_badge") ?: "КЕЙСЫ";
    $cases_title = get_field("cases_title");
    $cases_btn_t = get_field("cases_btn_text") ?: "Смотреть больше →";
    $cases_btn_url = get_field("cases_btn_url");

    $cases_q = new WP_Query([
        "post_type" => "case",
        "posts_per_page" => 4,
        "post_status" => "publish",
        "orderby" => "menu_order",
        "order" => "ASC",
    ]);
    ?>


    <?php get_template_part("template-parts/marathon"); ?>


    <section class="cases-preview">
        <div class="cases-preview__inner">
            <div class="cases-preview__header">
                <div class="cases-preview__header-left">
                    <span class="service-badge"><?php echo esc_html(
                        $cases_badge,
                    ); ?></span>
                    <?php if ($cases_title): ?>
                    <h2 class="cases-preview__title"><?php echo esc_html(
                        $cases_title,
                    ); ?></h2>
                    <?php endif; ?>
                </div>
                <?php if ($cases_btn_url): ?>
                <a href="<?php echo esc_url(
                    $cases_btn_url,
                ); ?>" class="cases-preview__btn">
                    <?php echo esc_html($cases_btn_t); ?>
                </a>
                <?php endif; ?>
            </div>

            <div class="cases-preview__grid">
                <?php
                while ($cases_q->have_posts()):
                    $cases_q->the_post(); ?>
                    <?php get_template_part("template-parts/case-card"); ?>
                <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
            <?php if ($cases_btn_url): ?>
            <a href="<?php echo esc_url(
                $cases_btn_url,
            ); ?>" class="cases-preview__btn cases-preview__btn--mobile">
                <?php echo esc_html($cases_btn_t); ?>
            </a>
            <?php endif; ?>
        </div>
    </section>
    <?php get_template_part("template-parts/cta-banner"); ?>


    <?php get_template_part("template-parts/services-list"); ?>

    <?php get_template_part("template-parts/booking"); ?>

    <?php
    // ── About ──────────────────────────────────────────────────────────────────
    $ab_badge = get_field("about_badge") ?: "О НАС";
    $ab_title = get_field("about_title");
    $ab_text = get_field("about_text");
    $ab_list = get_field("about_checklist") ?: [];
    $ab_btn_t = get_field("about_btn_text") ?: "О компании";
    $ab_btn_url = get_field("about_btn_url");
    if (
        is_string($ab_btn_url) &&
        $ab_btn_url !== "" &&
        (stripos($ab_btn_url, "/wp-admin/") !== false ||
            stripos($ab_btn_url, "/wp-login.php") !== false)
    ) {
        $ab_btn_url = "";
    }
    $ab_photo = get_field("about_photo");
    ?>

    <!-- ═══ ABOUT ═══ -->
    <section class="about">
        <div class="about__inner">

            <!-- Header row: title + badge -->
            <div class="about__header">
                <?php if ($ab_title): ?>
                <h2 class="about__title"><?php echo wp_kses_post(
                    $ab_title,
                ); ?></h2>
                <?php endif; ?>
                <span class="about__badge"><?php echo esc_html(
                    $ab_badge,
                ); ?></span>
            </div>

            <!-- Body: two cards -->
            <div class="about__body">

                <!-- Left card -->
                <div class="about__card">

                    <div class="about__stars">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                        <svg width="23" height="22" viewBox="0 0 23 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M10.3859 0.560364C10.6284 -0.185897 11.6841 -0.185898 11.9266 0.560364L14.0053 6.95803C14.1138 7.29177 14.4248 7.51772 14.7757 7.51772H21.5026C22.2873 7.51772 22.6135 8.52181 21.9787 8.98303L16.5365 12.937C16.2526 13.1433 16.1338 13.5089 16.2423 13.8426L18.321 20.2403C18.5635 20.9865 17.7093 21.6071 17.0745 21.1459L11.6324 17.1919C11.3485 16.9856 10.964 16.9856 10.6801 17.1919L5.23797 21.1459C4.60316 21.6071 3.74903 20.9865 3.9915 20.2403L6.07023 13.8426C6.17867 13.5089 6.05988 13.1433 5.77598 12.937L0.333805 8.98303C-0.301003 8.52181 0.0252442 7.51772 0.80991 7.51772H7.53681C7.88772 7.51772 8.19873 7.29177 8.30717 6.95803L10.3859 0.560364Z" fill="#2D6BD9" />
                        </svg>
                        <?php endfor; ?>
                    </div>

                    <?php if ($ab_text): ?>
                    <p class="about__text"><?php echo wp_kses_post(
                        $ab_text,
                    ); ?></p>
                    <?php endif; ?>

                    <?php if ($ab_list): ?>
                    <ul class="about__list">
                        <?php foreach ($ab_list as $item): ?>
                        <li class="about__list-item"><?php echo esc_html(
                            $item["about_check_text"],
                        ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>

                    <?php if ($ab_btn_url): ?>
                    <a href="<?php echo esc_url(
                        $ab_btn_url,
                    ); ?>" class="about__btn">
                        <?php echo esc_html($ab_btn_t); ?>
                    </a>
                    <?php endif; ?>

                </div><!-- /.about__card -->

                <!-- Right card: photo -->
                <?php if ($ab_photo): ?>
                <div class="about__photo-card">
                    <img src="<?php echo esc_url(
                        $ab_photo["url"],
                    ); ?>" alt="<?php echo esc_attr($ab_photo["alt"]); ?>">
                </div>
                <?php endif; ?>

            </div><!-- /.about__body -->

        </div>
    </section>

    <?php // ── Stats ──────────────────────────────────────────────────────────────────

$stats_cards = get_field("stats_cards") ?: []; ?>

    <?php if ($stats_cards): ?>
    <!-- ═══ STATS ═══ -->
    <section class="stats">
        <div class="stats__inner">
            <?php foreach ($stats_cards as $card): ?>
            <div class="stats__card">
                <div class="stats__card-top">
                    <div class="stats__number"><?php echo esc_html(
                        $card["stats_number"],
                    ); ?></div>
                    <?php if ($card["stats_sublabel"]): ?>
                    <div class="stats__sublabel"><?php echo esc_html(
                        $card["stats_sublabel"],
                    ); ?></div>
                    <?php endif; ?>
                </div>
                <div class="stats__text-wrapper">
                <?php if ($card["stats_text"]): ?>
                <p class="stats__text"><?php echo nl2br(
                    esc_html($card["stats_text"]),
                ); ?></p>
                <?php endif; ?>
                <?php
                $stats_btn_url = is_string($card["stats_btn_url"] ?? "")
                    ? trim($card["stats_btn_url"])
                    : "";
                if (
                    $stats_btn_url !== "" &&
                    (stripos($stats_btn_url, "/wp-admin/") !== false ||
                        stripos($stats_btn_url, "/wp-login.php") !== false)
                ) {
                    $parsed = wp_parse_url($stats_btn_url);
                    $path = isset($parsed["path"])
                        ? (string) $parsed["path"]
                        : "";
                    if (
                        $path !== "" &&
                        stripos($path, "/wp-admin/post.php") !== false &&
                        !empty($parsed["query"])
                    ) {
                        $query_params = [];
                        parse_str((string) $parsed["query"], $query_params);
                        $post_id = isset($query_params["post"])
                            ? (int) $query_params["post"]
                            : 0;
                        $permalink =
                            $post_id > 0 ? get_permalink($post_id) : "";
                        $stats_btn_url = is_string($permalink)
                            ? trim($permalink)
                            : "";
                    } else {
                        $stats_btn_url = "";
                    }
                }
                ?>
                <?php if ($stats_btn_url): ?>
                <a href="<?php echo esc_url(
                    $stats_btn_url,
                ); ?>" class="stats__btn">
                    <?php echo esc_html(
                        $card["stats_btn_text"] ?: "Подробнее",
                    ); ?>
                </a>
                <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php
    // ── Partner ────────────────────────────────────────────────────────────────
    $pt_title = get_field("partner_title");
    $pt_text = get_field("partner_text");
    $pt_btn_t = get_field("partner_btn_text") ?: "Стать Партнёром";
    $pt_btn_url = get_field("partner_btn_url");
    $pt_hint = get_field("partner_hint");
    $pt_image = get_field("partner_image");
    ?>

    <!-- ═══ PARTNER ═══ -->
    <section class="partner">
        <div class="partner__inner">

            <div class="partner__content">

                <?php if ($pt_title): ?>
                <h2 class="partner__title"><?php echo wp_kses_post(
                    $pt_title,
                ); ?></h2>
                <?php endif; ?>

                <?php if ($pt_text): ?>
                <p class="partner__text"><?php echo wp_kses_post(
                    $pt_text,
                ); ?></p>
                <?php endif; ?>

                <div class="partner__actions">
                    <?php if ($pt_btn_url): ?>
                    <a href="<?php echo esc_url(
                        $pt_btn_url,
                    ); ?>" class="partner__btn">
                        <?php echo esc_html($pt_btn_t); ?>
                    </a>
                    <?php endif; ?>
                    <?php if ($pt_hint): ?>
                    <div class="partner__hint">
                        <?php echo wp_kses_post($pt_hint); ?>
                    </div>
                    <?php endif; ?>
                </div>

            </div><!-- /.partner__content -->

            <?php if ($pt_image): ?>
            <div class="partner__image">
                <img src="<?php echo esc_url(
                    $pt_image["url"],
                ); ?>" alt="<?php echo esc_attr($pt_image["alt"]); ?>">
            </div>
            <?php endif; ?>

        </div>
    </section>

    <?php
    // ── Earn ───────────────────────────────────────────────────────────────────
    $earn_title = get_field("earn_title");
    $earn_text = get_field("earn_text");
    $earn_cards = get_field("earn_cards") ?: [];
    ?>

    <!-- ═══ EARN ═══ -->
    <section class="earn">
        <div class="earn__inner">

            <div class="earn__header">
                <?php if ($earn_title): ?>
                <h2 class="earn__title"><?php echo wp_kses_post(
                    $earn_title,
                ); ?></h2>
                <?php endif; ?>
                <?php if ($earn_text): ?>
                <p class="earn__subtitle"><?php echo wp_kses_post(
                    $earn_text,
                ); ?></p>
                <?php endif; ?>
            </div>

            <?php if ($earn_cards): ?>
            <div class="earn__grid">
                <?php foreach ($earn_cards as $card):
                    $style = $card["earn_card_style"] ?: "white"; ?>
                <div class="earn__card earn__card--<?php echo esc_attr(
                    $style,
                ); ?>">
                    <div class="earn__amount"><?php echo esc_html(
                        $card["earn_card_amount"],
                    ); ?></div>
                    <?php if ($card["earn_card_text"]): ?>
                    <p class="earn__card-text"><?php echo nl2br(
                        esc_html($card["earn_card_text"]),
                    ); ?></p>
                    <?php endif; ?>
                    <?php if ($card["earn_card_btn_url"]): ?>
                    <a href="<?php echo esc_url(
                        $card["earn_card_btn_url"],
                    ); ?>" class="earn__btn">
                        <?php echo esc_html(
                            $card["earn_card_btn_text"] ?: "Подробнее",
                        ); ?>
                    </a>
                    <?php endif; ?>
                </div>
                <?php
                endforeach; ?>
            </div>
            <?php endif; ?>

        </div>
    </section>


    <?php get_template_part("template-parts/zavod-preview"); ?>
    <?php get_template_part("template-parts/calculator"); ?>

    <?php
    $why_title = get_field("why_title");
    $why_cards = get_field("why_cards");
    if ($why_title || $why_cards): ?>
    <section class="why">
        <div class="container">
            <?php if ($why_title): ?>
            <h2 class="why__title"><?php echo wp_kses_post($why_title); ?></h2>
            <?php endif; ?>

            <?php if ($why_cards): ?>
            <?php
            // cards 0-1: top row, 2-4: bottom row, 5: side
            $top_cards = array_slice($why_cards, 0, 2);
            $bottom_cards = array_slice($why_cards, 2, 3);
            $side_card = $why_cards[5] ?? null;
            ?>
            <div class="why__layout">
                <div class="why__left">
                    <div class="why__row">
                        <?php foreach ($top_cards as $i => $card):

                            $n = $i + 1;
                            $style = $card["why_card_style"] ?: "light";
                            $icon = $card["why_card_icon"];
                            ?>
                        <div class="why__card why__card-<?php echo $n; ?> why__card--<?php echo esc_attr(
     $style,
 ); ?>">
                            <?php if ($icon): ?>
                            <div class="why__card-icon"><img src="<?php echo esc_url(
                                $icon["url"],
                            ); ?>" alt="<?php echo esc_attr(
    $icon["alt"],
); ?>"></div>
                            <?php endif; ?>
                            <?php if ($card["why_card_title"]): ?>
                            <h3 class="why__card-title"><?php echo wp_kses_post(
                                $card["why_card_title"],
                            ); ?></h3>
                            <?php endif; ?>
                            <?php if ($card["why_card_text"]): ?>
                            <p class="why__card-text"><?php echo nl2br(
                                esc_html($card["why_card_text"]),
                            ); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php
                        endforeach; ?>
                    </div>
                    <div class="why__row">
                        <?php foreach ($bottom_cards as $i => $card):

                            $n = $i + 3;
                            $style = $card["why_card_style"] ?: "light";
                            $icon = $card["why_card_icon"];
                            ?>
                        <div class="why__card why__card-<?php echo $n; ?> why__card--<?php echo esc_attr(
     $style,
 ); ?>">
                            <?php if ($icon): ?>
                            <div class="why__card-icon"><img src="<?php echo esc_url(
                                $icon["url"],
                            ); ?>" alt="<?php echo esc_attr(
    $icon["alt"],
); ?>"></div>
                            <?php endif; ?>
                            <?php if ($card["why_card_title"]): ?>
                            <h3 class="why__card-title"><?php echo wp_kses_post(
                                $card["why_card_title"],
                            ); ?></h3>
                            <?php endif; ?>
                            <?php if ($card["why_card_text"]): ?>
                            <p class="why__card-text"><?php echo nl2br(
                                esc_html($card["why_card_text"]),
                            ); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php
                        endforeach; ?>
                    </div>
                </div>
                <?php if ($side_card):

                    $style = $side_card["why_card_style"] ?: "light";
                    $icon = $side_card["why_card_icon"];
                    ?>
                <div class="why__card why__card-6 why__card--side why__card--<?php echo esc_attr(
                    $style,
                ); ?>">
                    <?php if ($icon): ?>
                    <div class="why__card-icon"><img src="<?php echo esc_url(
                        $icon["url"],
                    ); ?>" alt="<?php echo esc_attr($icon["alt"]); ?>"></div>
                    <?php endif; ?>
                    <?php if ($side_card["why_card_title"]): ?>
                    <h3 class="why__card-title"><?php echo wp_kses_post(
                        $side_card["why_card_title"],
                    ); ?></h3>
                    <?php endif; ?>
                    <?php if ($side_card["why_card_text"]): ?>
                    <p class="why__card-text"><?php echo nl2br(
                        esc_html($side_card["why_card_text"]),
                    ); ?></p>
                    <?php endif; ?>
                </div>
                <?php
                endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif;
    ?>



<div style="display: flex; justify-content: center;margin-top: 20px;border-radius: 20px;">
  <iframe title="Виджет с отзывами «Карусель» от MyReviews" style="width: 100%; height: 100%; max-width: 100%; border: none; outline: none; padding: 0; margin: 0" id="myReviews__block-widget">
  </iframe>
</div>



<script src="https://myreviews.dev/widget/dist/blockWidget.js" defer></script>
<script>
  (function (){
    var myReviewsInit = function () {
      new window.myReviews.BlockWidget({
      uuid: "48c536c0-b0eb-4228-b425-da75a959f88b",
      name: "g98341406",
      additionalFrame:"none",
      lang:"ru",
      widgetId: "1"
      }).init();

    };
  if (document.readyState === "loading") {
    document.addEventListener('DOMContentLoaded', function () {
        myReviewsInit()
    })
  } else {
    myReviewsInit()
  }
  })()
</script>



    <?php
    $top5_title = get_field("top5_title");
    $top5_subtitle = get_field("top5_subtitle");
    $top5_note = get_field("top5_note");
    $top5_cards = get_field("top5_cards");
    if ($top5_title || $top5_cards): ?>
    <section class="top5">
        <div class="container">
            <?php if ($top5_title): ?>
            <h2 class="top5__title"><?php echo wp_kses_post(
                $top5_title,
            ); ?></h2>
            <?php endif; ?>
            <?php if ($top5_subtitle): ?>
            <p class="top5__subtitle"><?php echo esc_html(
                $top5_subtitle,
            ); ?></p>
            <?php endif; ?>
            <?php if ($top5_note): ?>
            <p class="top5__note"><?php echo esc_html($top5_note); ?></p>
            <?php endif; ?>

            <?php if ($top5_cards): ?>
            <div class="top5__layout">
                <?php foreach ($top5_cards as $i => $card):

                    $n = $i + 1;
                    $icon = $card["top5_card_icon"];
                    ?>
                <div class="top5__card top5__card-<?php echo $n; ?>">
                    <?php if ($icon): ?>
                    <div class="top5__card-icon"><img src="<?php echo esc_url(
                        $icon["url"],
                    ); ?>" alt="<?php echo esc_attr($icon["alt"]); ?>"></div>
                    <?php endif; ?>
                    <?php if ($card["top5_card_title"]): ?>
                    <h3 class="top5__card-title"><?php echo wp_kses_post(
                        $card["top5_card_title"],
                    ); ?></h3>
                    <?php endif; ?>
                    <?php if ($card["top5_card_text"]): ?>
                    <div class="top5__card-text"><?php echo wp_kses_post(
                        $card["top5_card_text"],
                    ); ?></div>
                    <?php endif; ?>
                </div>
                <?php
                endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif;
    ?>

    <?php get_template_part("template-parts/proc"); ?>

    <?php
    $cons_badge = get_field("cons_badge");
    $cons_title = get_field("cons_title");
    $cons_text = get_field("cons_text");
    $cons_btn = get_field("cons_btn") ?: "Получить консультацию";
    $cons_consent =
        get_field("cons_consent") ?:
        "Отправляя форму, вы даёте согласие на обработку персональных данных";
    ?>
    <section class="cons">
        <div class="container">
            <div class="cons__inner">
                <div class="romb_1"></div>
                <div class="romb_2"></div>

                <div class="romb_3"></div>

                <div class="cons__left">
                    <?php if ($cons_badge): ?>
                    <span class="cons__badge"><?php echo esc_html(
                        $cons_badge,
                    ); ?></span>
                    <?php endif; ?>
                    <?php if ($cons_title): ?>
                    <h2 class="cons__title"><?php echo wp_kses_post(
                        $cons_title,
                    ); ?></h2>
                    <?php endif; ?>
                    <?php if ($cons_text): ?>
                    <div class="cons__text"><?php echo wp_kses_post(
                        $cons_text,
                    ); ?></div>
                    <?php endif; ?>
                </div>
                <div class="cons__right">
                    <form class="cons__form" action="" method="post" novalidate>
                        <div class="cons__field">
                            <label class="cons__label">Ваше имя</label>
                            <input type="text" name="cons_name" class="cons__input" placeholder="Имя">
                        </div>
                        <div class="cons__field">
                            <label class="cons__label">Номер телефона</label>
                            <div class="cons__phone-wrap">
                                <span class="cons__phone-flag">🇷🇺 +7</span>
                                <input type="tel" name="cons_phone" class="cons__input cons__input--phone" placeholder="___-___-__-__">
                            </div>
                        </div>
                        <div class="cons__field">
                            <label class="cons__label">Способ связи</label>
                            <div class="cons__select-wrap">
                                <select name="cons_contact" class="cons__select">
                                    <option value="call">Позвоните мне</option>
                                    <option value="whatsapp">WhatsApp</option>
                                    <option value="telegram">Telegram</option>
                                </select>
                            </div>
                        </div>
                        <label class="cons__consent">
                            <input type="checkbox" name="cons_agree">
                            <span><?php echo esc_html($cons_consent); ?></span>
                        </label>
                        <button type="submit" class="cons__submit"><?php echo esc_html(
                            $cons_btn,
                        ); ?></button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php
    get_template_part("template-parts/faq-vid");

    $team_members = new WP_Query([
        "post_type" => "team_member",
        "posts_per_page" => -1,
        "orderby" => ["menu_order" => "ASC", "date" => "ASC"],
    ]);

    if ($team_members->have_posts()): ?>
    <section class="team-home" id="team">
        <div class="container">
            <span class="team-home__badge">КОМАНДА</span>
            <h2 class="team-home__title">Команда<br>профессионалов</h2>

            <div class="swiper team-home__swiper">
                <div class="swiper-wrapper">
                    <?php
                    while ($team_members->have_posts()):

                        $team_members->the_post();
                        $photo_url = get_the_post_thumbnail_url(null, "large");
                        $role = get_the_excerpt();
                        ?>
                    <div class="swiper-slide team-home__slide">
                        <article class="team-home__card">
                            <?php if ($photo_url): ?>
                            <img
                                class="team-home__photo"
                                src="<?php echo esc_url($photo_url); ?>"
                                alt="<?php echo esc_attr(get_the_title()); ?>"
                            >
                            <?php else: ?>
                            <div class="team-home__photo team-home__photo--placeholder"></div>
                            <?php endif; ?>

                            <div class="team-home__overlay">
                                <h3 class="team-home__name"><?php the_title(); ?></h3>
                                <?php if ($role): ?>
                                <p class="team-home__role"><?php echo esc_html(
                                    $role,
                                ); ?></p>
                                <?php endif; ?>
                            </div>
                        </article>
                    </div>
                    <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
                <div class="swiper-scrollbar team-home__scrollbar"></div>
            </div>
        </div>
    </section>
    <script>
    window.addEventListener('load', function () {
        var teamSlidesCount = document.querySelectorAll('.team-home__slide').length;
        var teamSwiperOptions = {
            slidesPerView: 1.15,
            spaceBetween: 18,
            scrollbar: { el: '.team-home__scrollbar', draggable: true, dragSize: 120 },
            breakpoints: {
                600:  { slidesPerView: 2.05 },
                900:  { slidesPerView: 3.05 },
                1200: { slidesPerView: 3.8 },
            },
        };

        if (teamSlidesCount <= 1) {
            teamSwiperOptions.slidesPerView = 1;
            teamSwiperOptions.spaceBetween = 0;
            teamSwiperOptions.allowTouchMove = false;
        }

        new Swiper('.team-home__swiper', {
            ...teamSwiperOptions,
        });
    });
    </script>
    <?php endif;
    ?>

    <?php
    $accordion_title = get_field("accordion_title");
    $accordion_items = get_field("accordion_items");
    if ($accordion_items): ?>
    <section class="faq-accordion" id="faq">
        <div class="faq-accordion__inner">
            <?php if ($accordion_title): ?>
            <h2 class="faq-accordion__title"><?php echo wp_kses_post(
                $accordion_title,
            ); ?></h2>
            <?php endif; ?>
            <div class="faq-accordion__list">
                <?php foreach ($accordion_items as $i => $item): ?>
                <div class="faq-accordion__item<?php echo $i === 0
                    ? " faq-accordion__item--open"
                    : ""; ?>">
                    <button class="faq-accordion__btn" aria-expanded="<?php echo $i ===
                    0
                        ? "true"
                        : "false"; ?>">
                        <span class="faq-accordion__question"><?php echo esc_html(
                            $item["question"],
                        ); ?></span>
                        <span class="faq-accordion__icon">
                            <svg width="25" height="25" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 8L10 13L15 8" stroke="#1a1a2e" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </button>
                    <div class="faq-accordion__answer">
                        <p><?php echo nl2br(esc_html($item["answer"])); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <script>
    document.querySelectorAll('.faq-accordion__btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var item = btn.closest('.faq-accordion__item');
            var isOpen = item.classList.contains('faq-accordion__item--open');
            document.querySelectorAll('.faq-accordion__item').forEach(function(el) {
                el.classList.remove('faq-accordion__item--open');
                el.querySelector('.faq-accordion__btn').setAttribute('aria-expanded', 'false');
            });
            if (!isOpen) {
                item.classList.add('faq-accordion__item--open');
                btn.setAttribute('aria-expanded', 'true');
            }
        });
    });
    </script>
    <?php endif;
    ?>

    <?php get_template_part("template-parts/faq-form"); ?>

    <?php get_template_part("template-parts/social-networks"); ?>

</main>

<?php get_template_part("template-parts/consult-modal"); ?>

<?php get_footer(); ?>
