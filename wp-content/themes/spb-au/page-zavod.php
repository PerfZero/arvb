<?php
/*
 * Template Name: Завод Банкротств
 */
if (!defined("ABSPATH")) {
    exit();
}

get_header();

$zavod_title = get_field("zavod_title");
$zavod_description = get_field("zavod_description");
$zavod_badge = get_field("zavod_badge") ?: "ЗАВОД БАНКРОТСТВ";
$zavod_sections = get_field("zavod_sections");

$allowed = [
    "br" => [],
    "strong" => [],
    "em" => [],
    "a" => ["href" => [], "target" => []],
];
?>

<main class="zavod-page">
    <div class="zavod-inner">

        <?php get_template_part("template-parts/breadcrumb"); ?>

        <!-- Hero -->
        <div class="zavod-hero">
            <div class="zavod-hero__left">
                <?php if ($zavod_title): ?>
                    <h1 class="zavod-title"><?php echo $zavod_title; ?></h1>
                <?php endif; ?>
                <?php if ($zavod_description): ?>
                    <p class="zavod-desc"><?php echo wp_kses(
                        $zavod_description,
                        $allowed,
                    ); ?></p>
                <?php endif; ?>
            </div>
            <div class="zavod-hero__right">
                <span class="zavod-badge"><?php echo esc_html(
                    $zavod_badge,
                ); ?></span>
            </div>
        </div>

        <!-- Sections -->
        <?php if ($zavod_sections): ?>
            <?php foreach ($zavod_sections as $section):

                $card_right =
                    ($section["section_card_position"] ?? "left") === "right";
                $has_bg = !empty($section["section_has_background"]);
                $icon = $section["section_stage_icon"] ?? null;
                $steps = $section["section_steps"] ?? [];
                $classes = "zavod-main";
                if ($card_right) {
                    $classes .= " zavod-main--reversed";
                }
                if ($has_bg) {
                    $classes .= " zavod-main--dark";
                }
                ?>
            <div class="<?php echo $classes; ?>">

                <!-- Stage card -->
                <div class="zavod-left">
                    <div class="zavod-stage">
                        <div class="zavod-stage__content">
                        <?php if ($icon && !empty($icon["url"])): ?>
                            <img class="zavod-stage__icon"
                                 src="<?php echo esc_url($icon["url"]); ?>"
                                 alt="<?php echo esc_attr(
                                     $icon["alt"] ?? "",
                                 ); ?>">
                        <?php endif; ?>

                        <?php if (!empty($section["section_stage_title"])): ?>
                            <h2 class="zavod-stage__title"><?php echo esc_html(
                                $section["section_stage_title"],
                            ); ?></h2>
                        <?php endif; ?>
</div>
                        <?php if (!empty($section["section_stage_text"])): ?>
                            <p class="zavod-stage__text"><?php echo wp_kses(
                                $section["section_stage_text"],
                                $allowed,
                            ); ?></p>
                        <?php endif; ?>

                        <?php if (
                            !empty($section["section_stage_btn_text"])
                        ): ?>
                            <a href="<?php echo esc_url(
                                $section["section_stage_btn_url"] ?: "#",
                            ); ?>"
                               class="zavod-stage__btn">
                                <?php echo esc_html(
                                    $section["section_stage_btn_text"],
                                ); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Accordion steps -->
                <div class="zavod-right">
                    <?php foreach ($steps as $step): ?>
                        <div class="zavod-step">
                            <div class="zavod-step__header">
                                <span class="zavod-step__num"><?php echo esc_html(
                                    $step["step_number"] ?? "",
                                ); ?></span>
                                <div class="zavod-step__info">
                                    <div class="zavod-step__title"><?php echo esc_html(
                                        $step["step_title"] ?? "",
                                    ); ?></div>
                                    <?php if (!empty($step["step_tags"])): ?>
                                        <div class="zavod-step__tags">
                                            <?php foreach (
                                                $step["step_tags"]
                                                as $tag
                                            ): ?>
                                                <span class="zavod-tag"><?php echo esc_html(
                                                    $tag["tag_label"] ?? "",
                                                ); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <button class="zavod-step__toggle" aria-label="Раскрыть шаг">
                                    <svg class="zavod-toggle-icon zavod-toggle-icon--open" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M2 9L7 4L12 9" stroke="#333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <svg class="zavod-toggle-icon zavod-toggle-icon--closed" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M2 5L7 10L12 5" stroke="#333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="zavod-step__body">
                                <?php if (!empty($step["step_content"])): ?>
                                    <?php echo $step["step_content"]; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div><!-- .zavod-main -->
            <?php
            endforeach; ?>
        <?php endif; ?>

    </div><!-- .zavod-inner -->
    <?php get_template_part("template-parts/calculator"); ?>

</main>

<script>
document.querySelectorAll('.zavod-main').forEach(function(section) {
    var steps = Array.prototype.slice.call(section.querySelectorAll('.zavod-step'));
    if (!steps.length) return;

    var closeAll = function() {
        steps.forEach(function(step) {
            step.classList.remove('is-open');
        });
    };

    steps.forEach(function(step) {
        var header = step.querySelector('.zavod-step__header');
        if (!header) return;

        header.addEventListener('click', function() {
            var wasOpen = step.classList.contains('is-open');
            closeAll();
            if (!wasOpen) {
                step.classList.add('is-open');
            }
        });
    });

    // Открыть первый шаг каждой секции
    steps[0].classList.add('is-open');
});
</script>

<?php get_footer(); ?>
