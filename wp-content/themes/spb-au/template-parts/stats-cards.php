<?php
if (!defined('ABSPATH')) exit;

$cards = [];
for ($i = 1; $i <= 3; $i++) {
    $num = get_field("sc{$i}_number", 'option');
    if (!$num) continue;
    $cards[] = [
        'stat_number'   => $num,
        'stat_subtitle' => get_field("sc{$i}_subtitle", 'option'),
        'stat_desc'     => get_field("sc{$i}_desc", 'option'),
        'stat_btn_text' => get_field("sc{$i}_btn_text", 'option'),
        'stat_btn_url'  => get_field("sc{$i}_btn_url", 'option'),
    ];
}
if (!$cards) return;
?>
<section class="stats-cards">
    <div class="stats-cards__inner">
        <?php foreach ($cards as $card): ?>
        <div class="stats-card">
            <div class="stats-card__top">
                <p class="stats-card__number"><?php echo esc_html($card['stat_number']); ?></p>
                <?php if ($card['stat_subtitle']): ?>
                <p class="stats-card__subtitle"><?php echo esc_html($card['stat_subtitle']); ?></p>
                <?php endif; ?>
            </div>
            <?php if ($card['stat_desc']): ?>
            <p class="stats-card__desc"><?php echo wp_kses_post($card['stat_desc']); ?></p>
            <?php endif; ?>
            <?php if ($card['stat_btn_text']): ?>
            <a href="<?php echo esc_url($card['stat_btn_url'] ?: '#'); ?>" class="stats-card__btn">
                <?php echo esc_html($card['stat_btn_text']); ?>
            </a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</section>
