<?php
if (!defined('ABSPATH')) exit;

// Find the Завод Банкротств page
$zavod_pages = get_posts([
    'post_type'      => 'page',
    'posts_per_page' => 1,
    'post_status'    => 'publish',
    'meta_key'       => '_wp_page_template',
    'meta_value'     => 'page-zavod.php',
]);
if (!$zavod_pages) return;

$zavod_id   = $zavod_pages[0]->ID;
$zavod_url  = get_permalink($zavod_id);

$hp_id      = (int) get_option('page_on_front');
$z_title    = ($hp_id ? get_field('zavod_hp_title', $hp_id) : '')
              ?: get_field('zavod_title', $zavod_id);
$z_desc     = ($hp_id ? get_field('zavod_hp_desc', $hp_id) : '')
              ?: get_field('zavod_description', $zavod_id);
$z_badge    = get_field('zavod_badge',          $zavod_id) ?: 'ЗАВОД БАНКРОТСТВ';
$z_btn_t    = get_field('zavod_hero_btn_text',  $zavod_id) ?: 'Подробнее о «Заводе Банкротств» →';
$z_img      = get_field('zavod_hero_image',     $zavod_id);
$z_sections = get_field('zavod_sections',       $zavod_id) ?: [];

$first = $z_sections[0] ?? null;
$steps = array_slice($first['section_steps'] ?? [], 0, 4);
?>

<section class="zavod-preview">
    <div class="zavod-preview__inner">

        <!-- Hero -->
        <div class="zavod-preview__hero">
            <div class="zavod-preview__hero-left">
                <?php if ($z_title): ?>
                <h2 class="zavod-preview__title"><?php echo wp_kses_post($z_title); ?></h2>
                <?php endif; ?>
                <?php if ($z_desc): ?>
                <p class="zavod-preview__desc"><?php echo nl2br(esc_html($z_desc)); ?></p>
                <?php endif; ?>
                <a href="<?php echo esc_url($zavod_url); ?>" class="zavod-preview__btn">
                    <?php echo esc_html($z_btn_t); ?>
                </a>
            </div>
            <div class="zavod-preview__hero-right">
                <?php if ($z_img): ?>
                <div class="zavod-preview__photo">
                    <img src="<?php echo esc_url($z_img['url']); ?>" alt="<?php echo esc_attr($z_img['alt']); ?>">
                </div>
                <?php endif; ?>
                <span class="zavod-preview__badge"><?php echo esc_html($z_badge); ?></span>
            </div>
        </div>

        <?php if ($first && $steps): ?>
        <!-- Stage + Steps -->
        <div class="zavod-preview__body">

            <!-- Stage card -->
            <div class="zavod-preview__stage">
                <div class="zavod-stage__content">
                    <?php if (!empty($first['section_stage_icon']['url'])): ?>
                    <img class="zavod-stage__icon"
                         src="<?php echo esc_url($first['section_stage_icon']['url']); ?>"
                         alt="">
                    <?php endif; ?>
                    <?php if (!empty($first['section_stage_title'])): ?>
                    <h3 class="zavod-stage__title"><?php echo esc_html($first['section_stage_title']); ?></h3>
                    <?php endif; ?>
                </div>
                <?php if (!empty($first['section_stage_text'])): ?>
                <p class="zavod-stage__text"><?php echo nl2br(esc_html($first['section_stage_text'])); ?></p>
                <?php endif; ?>
                <?php if (!empty($first['section_stage_btn_text'])): ?>
                <a href="<?php echo esc_url($first['section_stage_btn_url'] ?: '#'); ?>"
                   class="zavod-stage__btn">
                    <?php echo esc_html($first['section_stage_btn_text']); ?>
                </a>
                <?php endif; ?>
            </div>

            <!-- Steps accordion (first 4) -->
            <div class="zavod-preview__steps">
                <?php foreach ($steps as $step): ?>
                <div class="zavod-step">
                    <div class="zavod-step__header">
                        <span class="zavod-step__num"><?php echo esc_html($step['step_number'] ?? ''); ?></span>
                        <div class="zavod-step__info">
                            <div class="zavod-step__title"><?php echo esc_html($step['step_title'] ?? ''); ?></div>
                            <?php if (!empty($step['step_tags'])): ?>
                            <div class="zavod-step__tags">
                                <?php foreach ($step['step_tags'] as $tag): ?>
                                <span class="zavod-tag"><?php echo esc_html($tag['tag_label'] ?? ''); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <button class="zavod-step__toggle" aria-label="Раскрыть шаг">
                            <svg class="zavod-toggle-icon zavod-toggle-icon--open" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 9L7 4L12 9" stroke="#333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <svg class="zavod-toggle-icon zavod-toggle-icon--closed" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 5L7 10L12 5" stroke="#333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                    <?php if (!empty($step['step_content'])): ?>
                    <div class="zavod-step__body">
                        <?php echo wp_kses_post($step['step_content']); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

        </div><!-- /.zavod-preview__body -->
        <?php endif; ?>

        <!-- Show more -->
        <div class="zavod-preview__footer">
            <a href="<?php echo esc_url($zavod_url); ?>" class="zavod-preview__more-btn">
                Показать больше
            </a>
        </div>

    </div>
</section>

<script>
(function(){
    document.querySelectorAll('.zavod-preview .zavod-step__header').forEach(function(h){
        h.addEventListener('click', function(){
            this.closest('.zavod-step').classList.toggle('is-open');
        });
    });
    var first = document.querySelector('.zavod-preview .zavod-step');
    if (first) first.classList.add('is-open');
})();
</script>
