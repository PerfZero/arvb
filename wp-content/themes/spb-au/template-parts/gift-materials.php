<?php
$badge    = get_field('gift_badge',    'option') ?: 'МАРАФОН';
$title    = get_field('gift_title',    'option');
$items    = get_field('gift_items',    'option');
$btn_text = get_field('gift_btn_text', 'option') ?: 'Перейти в Telegram';
$btn_url  = get_field('gift_btn_url',  'option');
$image    = get_field('gift_image',    'option');

if (!$title && !$items) return;
?>
<section class="gift-block">
    <div class="gift-inner">
        <div class="gift-left">
            <span class="service-badge"><?php echo esc_html($badge); ?></span>
            <?php if ($title): ?>
                <div class="gift-title"><?php echo wp_kses_post($title); ?></div>
            <?php endif; ?>
            <?php if ($items): ?>
                <div class="gift-list">
                    <?php foreach ($items as $item): ?>
                        <p><?php echo wp_kses($item['gift_item_text'], ['strong' => [], 'b' => [], 'em' => [], 'i' => []]); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ($btn_url): ?>
                <a href="<?php echo esc_url($btn_url); ?>" class="gift-btn">
                    <?php echo esc_html($btn_text); ?>
                </a>
            <?php endif; ?>
        </div>
        <?php if ($image): ?>
        <div class="gift-right">
            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
        </div>
        <?php endif; ?>
    </div>
</section>
