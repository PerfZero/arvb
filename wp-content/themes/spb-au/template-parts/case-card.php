<?php
if (!defined('ABSPATH')) exit;

$cc_client = get_field('case_client');
$cc_amount = get_field('case_amount');
$cc_number = get_field('case_number');
$cc_docs   = get_field('case_docs');
$cc_photo  = get_field('case_photo');
$cc_placeholder = get_template_directory_uri() . "/images/case-placeholder.svg";
?>
<div class="cc">

    <a href="<?php the_permalink(); ?>" class="cc__photo">
        <img src="<?php echo esc_url($cc_photo['url'] ?? $cc_placeholder); ?>" alt="<?php echo esc_attr($cc_photo['alt'] ?? ''); ?>">
    </a>

    <div class="cc__body">
        <div class="cc__rows">
            <?php if ($cc_amount): ?>
            <div class="cc__row">
                <span class="cc__label">Сумма:</span>
                <span class="cc__val"><?php echo esc_html($cc_amount); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($cc_client): ?>
            <div class="cc__row">
                <span class="cc__label">Клиент:</span>
                <span class="cc__val"><?php echo esc_html($cc_client); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($cc_number): ?>
            <div class="cc__row">
                <span class="cc__label">Дело:</span>
                <span class="cc__val"><?php echo esc_html($cc_number); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($cc_docs): ?>
            <div class="cc__row cc__row--docs">
                <span class="cc__label">Документы:</span>
                <span class="cc__val">
                    <?php foreach ($cc_docs as $doc): ?>
                    <a href="<?php echo esc_url($doc['doc_url'] ?: '#'); ?>" class="cc__doc" target="_blank">
                        <?php echo esc_html($doc['doc_title']); ?>
                    </a>
                    <?php endforeach; ?>
                </span>
            </div>
            <?php endif; ?>
        </div>

        <a href="<?php the_permalink(); ?>" class="cc__btn">Смотреть полностью</a>
    </div>

</div>
