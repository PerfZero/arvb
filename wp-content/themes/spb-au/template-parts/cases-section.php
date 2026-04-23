<?php
$cases = new WP_Query([
    "post_type" => "case",
    "posts_per_page" => -1,
    "post_status" => "publish",
]);

$statuses = get_terms(["taxonomy" => "case_status", "hide_empty" => false]);
$debt_types = get_terms([
    "taxonomy" => "case_debt_type",
    "hide_empty" => false,
]);
$creditor_types = get_terms([
    "taxonomy" => "case_creditor_type",
    "hide_empty" => false,
]);
$age_ranges = [
    "all" => "Все",
    "lt30" => "До 30 лет",
    "30-39" => "30–39 лет",
    "40-49" => "40–49 лет",
    "50-59" => "50–59 лет",
    "60plus" => "60+ лет",
];
$creditors_ranges = [
    "all" => "Все",
    "1" => "1 кредитор",
    "2-5" => "2-5 кредиторов",
    "5plus" => "Более 5 кредиторов",
];
$problem_options = [];
if ($cases->have_posts()) {
    foreach ($cases->posts as $case_post) {
        $problem = get_field("case_problem", $case_post->ID);
        if (!empty($problem)) {
            $problem_options[$problem] = true;
        }
    }
}
$problem_options = array_keys($problem_options);
sort($problem_options, SORT_NATURAL | SORT_FLAG_CASE);
$per_page = 5;
$i = 0;
?>
<section class="cases-section">
    <div class="cases-inner">
        <?php if (is_post_type_archive('case')): get_template_part('template-parts/breadcrumb'); endif; ?>
        <h2 class="cases-title">Завершенные дела</h2>
        <div class="cases-layout">

            <div class="cases-filter-toggle-wrap">
                <button class="cases-filter-toggle" type="button">Фильтры</button>
            </div>

            <div class="cases-filter-overlay"></div>

            <!-- Sidebar filter -->
            <aside class="cases-filter">
                <button class="cases-filter-close" type="button">Закрыть</button>
                <div class="filter-group">
                    <div class="filter-group__title">Сумма долга</div>
                    <div class="filter-group__items">
                    <label class="filter-radio"><input type="radio" name="amount" value="all" checked> Все суммы</label>
                    <label class="filter-radio"><input type="radio" name="amount" value="350-500"> 350 000–500 000 рублей</label>
                    <label class="filter-radio"><input type="radio" name="amount" value="500-1000"> 500 000–1 000 000 рублей</label>
                    <label class="filter-radio"><input type="radio" name="amount" value="1000plus"> Более 1 000 000 рублей</label>
                </div>
                    </div>
                <div class="filter-group">
                    <div class="filter-group__title">Возраст</div>
                    <div class="filter-group__items">
                    <?php foreach ($age_ranges as $value => $label): ?>
                        <label class="filter-radio">
                            <input type="radio" name="age" value="<?php echo esc_attr($value); ?>" <?php echo $value === "all" ? "checked" : ""; ?>>
                            <?php echo esc_html($label); ?>
                        </label>
                    <?php endforeach; ?>
                    </div>
                </div>
                <div class="filter-group">
                    <div class="filter-group__title">Количество кредиторов</div>
                    <div class="filter-group__items">
                    <?php foreach ($creditors_ranges as $value => $label): ?>
                        <label class="filter-radio">
                            <input type="radio" name="creditors" value="<?php echo esc_attr($value); ?>" <?php echo $value === "all" ? "checked" : ""; ?>>
                            <?php echo esc_html($label); ?>
                        </label>
                    <?php endforeach; ?>
                    </div>
                </div>
                <?php if (!empty($debt_types) && !is_wp_error($debt_types)): ?>
                <div class="filter-group">
                    <div class="filter-group__title">Вид долгов</div>
                    <div class="filter-group__items">
                        <label class="filter-radio"><input type="radio" name="debt" value="all" checked> Все</label>
                        <?php foreach ($debt_types as $term): ?>
                        <label class="filter-radio">
                            <input type="radio" name="debt" value="<?php echo esc_attr(
                                $term->slug,
                            ); ?>">
                            <?php echo esc_html($term->name); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (!empty($creditor_types) && !is_wp_error($creditor_types)): ?>
                <div class="filter-group">
                    <div class="filter-group__title">Типы кредиторов</div>
                    <div class="filter-group__items">
                        <label class="filter-radio"><input type="radio" name="creditorType" value="all" checked> Все</label>
                        <?php foreach ($creditor_types as $term): ?>
                        <label class="filter-radio">
                            <input type="radio" name="creditorType" value="<?php echo esc_attr(
                                $term->slug,
                            ); ?>">
                            <?php echo esc_html($term->name); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (!empty($problem_options)): ?>
                <div class="filter-group">
                    <div class="filter-group__title">Проблема клиента</div>
                    <div class="filter-group__items">
                        <label class="filter-radio"><input type="radio" name="problem" value="all" checked> Все</label>
                        <?php foreach ($problem_options as $problem):
                            $problem_slug = sanitize_title($problem);
                        ?>
                        <label class="filter-radio">
                            <input type="radio" name="problem" value="<?php echo esc_attr($problem_slug); ?>">
                            <?php echo esc_html($problem); ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                <button class="cases-reset">Сбросить настройки</button>
            </aside>

            <!-- Cases list -->
            <div class="cases-list">
                <?php if ($cases->have_posts()):
                    while ($cases->have_posts()):

                        $cases->the_post();
                        $i++;
                        $client = get_field("case_client");
                        $age = (int) get_field("case_age");
                        $amount = get_field("case_amount");
                        $number = get_field("case_number");
                        $amount_r = get_field("case_amount_range") ?: "all";
                        $creditors = (int) get_field("case_creditors_count");
                        $problem = get_field("case_problem");
                        $photo = get_field("case_photo");
                        $placeholder_photo = get_template_directory_uri() . "/images/case-placeholder.svg";
                        $docs = get_field("case_docs");
                        $video = get_field("case_video");
                        $gallery = get_field("case_gallery");
                        $review = get_field("case_review_text");
                        $status_terms = get_the_terms(
                            get_the_ID(),
                            "case_status",
                        );
                        $debt_terms = get_the_terms(
                            get_the_ID(),
                            "case_debt_type",
                        );
                        $creditor_type_terms = get_the_terms(
                            get_the_ID(),
                            "case_creditor_type",
                        );
                        $status_slugs =
                            $status_terms && !is_wp_error($status_terms)
                                ? implode(
                                    ",",
                                    wp_list_pluck($status_terms, "slug"),
                                )
                                : "all";
                        $debt_slugs =
                            $debt_terms && !is_wp_error($debt_terms)
                                ? implode(
                                    ",",
                                    wp_list_pluck($debt_terms, "slug"),
                                )
                                : "all";
                        $creditor_type_slugs =
                            $creditor_type_terms &&
                            !is_wp_error($creditor_type_terms)
                                ? implode(
                                    ",",
                                    wp_list_pluck(
                                        $creditor_type_terms,
                                        "slug",
                                    ),
                                )
                                : "all";
                        $hidden_class =
                            $i > $per_page ? " case-card--hidden" : "";
                        $age_range = "all";
                        if ($age > 0) {
                            if ($age < 30) {
                                $age_range = "lt30";
                            } elseif ($age < 40) {
                                $age_range = "30-39";
                            } elseif ($age < 50) {
                                $age_range = "40-49";
                            } elseif ($age < 60) {
                                $age_range = "50-59";
                            } else {
                                $age_range = "60plus";
                            }
                        }
                        $creditors_range = "all";
                        if ($creditors > 0) {
                            if ($creditors === 1) {
                                $creditors_range = "1";
                            } elseif ($creditors <= 5) {
                                $creditors_range = "2-5";
                            } else {
                                $creditors_range = "5plus";
                            }
                        }
                        $problem_slug = $problem ? sanitize_title($problem) : "all";
                        ?>
                <?php $has_media = $video || $gallery; ?>
                <article class="case-card<?php echo $hidden_class; ?><?php echo $has_media ? ' case-card--media' : ''; ?>"
                    data-amount="<?php echo esc_attr($amount_r); ?>"
                    data-age="<?php echo esc_attr($age_range); ?>"
                    data-creditors="<?php echo esc_attr($creditors_range); ?>"
                    data-problem="<?php echo esc_attr($problem_slug); ?>"
                    data-status="<?php echo esc_attr($status_slugs); ?>"
                    data-debt="<?php echo esc_attr($debt_slugs); ?>"
                    data-creditor-type="<?php echo esc_attr(
                        $creditor_type_slugs,
                    ); ?>">

                    <h3 class="case-card__title">
                        <?php
                        if ($amount) {
                            echo esc_html(the_title('', '', false) . " — " . $amount);
                        } else {
                            the_title();
                        }
                        ?>
                    </h3>

                    <?php if ($has_media): ?>
                    <div class="case-card__media">
                        <?php if ($video): ?>
                            <div class="case-card__video"><?php echo $video; ?></div>
                        <?php elseif ($gallery): ?>
                            <div class="swiper case-card__swiper">
                                <div class="swiper-wrapper">
                                    <?php foreach ($gallery as $img): ?>
                                    <div class="swiper-slide">
                                        <img src="<?php echo esc_url($img['url']); ?>" alt="<?php echo esc_attr($img['alt']); ?>">
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if (count($gallery) > 1): ?>
                                <div class="swiper-pagination"></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div class="case-card__row">
                        <div class="case-card__body">
                            <div class="case-card__meta">
                                <?php if ($review): ?>
                                <div class="case-meta-row">
                                    <span class="case-meta-label">Отзыв:</span>
                                    <span class="case-meta-value"><?php echo wp_kses_post($review); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if ($client): ?>
                                <div class="case-meta-row">
                                    <span class="case-meta-label">Клиент:</span>
                                    <span class="case-meta-value"><?php echo esc_html($client); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if ($age): ?>
                                <div class="case-meta-row">
                                    <span class="case-meta-label">Возраст:</span>
                                    <span class="case-meta-value"><?php echo esc_html($age); ?> лет</span>
                                </div>
                                <?php endif; ?>
                                <?php if ($amount): ?>
                                <div class="case-meta-row">
                                    <span class="case-meta-label">Сумма:</span>
                                    <span class="case-meta-value"><?php echo esc_html($amount); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if ($number): ?>
                                <div class="case-meta-row">
                                    <span class="case-meta-label">Дело:</span>
                                    <span class="case-meta-value"><?php echo esc_html($number); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($docs): ?>
                            <div class="case-card__docs">
                                <div class="case-docs-label">Документы:</div>
                                <div class="case-docs-list">
                                    <?php foreach ($docs as $doc):
                                        $icon = $doc["doc_icon"];
                                        $url  = $doc["doc_url"];
                                        ?>
                                    <a href="<?php echo esc_url($url ?: "#"); ?>" class="case-doc-pill" target="_blank">
                                        <?php if ($icon): ?>
                                            <img src="<?php echo esc_url($icon["url"]); ?>" alt="">
                                        <?php endif; ?>
                                        <span><?php echo esc_html($doc["doc_title"]); ?></span>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            <a href="<?php the_permalink(); ?>" class="case-card__btn">Смотреть полностью</a>
                        </div>

                        <?php if (!$has_media): ?>
                        <div class="case-card__photo">
                            <img src="<?php echo esc_url($photo["url"] ?? $placeholder_photo); ?>" alt="<?php echo esc_attr($photo["alt"] ?? ""); ?>">
                        </div>
                        <?php endif; ?>
                    </div>
                </article>
                <?php
                    endwhile;
                    wp_reset_postdata();
                endif; ?>
            </div>
        </div>

        <!-- Load more -->
        <?php if ($cases->found_posts > $per_page): ?>
        <div class="cases-more-wrap">
            <button class="cases-more-btn" data-shown="<?php echo $per_page; ?>">Смотреть ещё</button>
        </div>
        <?php endif; ?>
    </div>

</section>
