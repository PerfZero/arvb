<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<nav class="breadcrumb" aria-label="Хлебные крошки">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumb__item">На главную</a>

    <?php if (is_page()): ?>
        <span class="breadcrumb__sep">—</span>
        <span class="breadcrumb__item breadcrumb__item--current"><?php the_title(); ?></span>

    <?php elseif (is_singular('smi')): ?>
        <span class="breadcrumb__sep">—</span>
        <a href="<?php echo esc_url(get_permalink(get_option('page_for_smi'))); ?>" class="breadcrumb__item">Мы в СМИ</a>
        <span class="breadcrumb__sep">—</span>
        <span class="breadcrumb__item breadcrumb__item--current"><?php the_title(); ?></span>

    <?php elseif (is_singular('post')): ?>
        <?php $cats = get_the_category(); if ($cats): ?>
            <span class="breadcrumb__sep">—</span>
            <a href="<?php echo esc_url(get_category_link($cats[0]->term_id)); ?>" class="breadcrumb__item">
                <?php echo esc_html($cats[0]->name); ?>
            </a>
        <?php endif; ?>
        <span class="breadcrumb__sep">—</span>
        <span class="breadcrumb__item breadcrumb__item--current"><?php the_title(); ?></span>

    <?php elseif (is_post_type_archive('case')): ?>
        <span class="breadcrumb__sep">—</span>
        <span class="breadcrumb__item breadcrumb__item--current">Завершённые дела</span>

    <?php elseif (is_singular('case')): ?>
        <span class="breadcrumb__sep">—</span>
        <a href="<?php echo esc_url(get_post_type_archive_link('case')); ?>" class="breadcrumb__item">Завершённые дела</a>
        <span class="breadcrumb__sep">—</span>
        <span class="breadcrumb__item breadcrumb__item--current"><?php the_title(); ?></span>

    <?php elseif (is_post_type_archive('review')): ?>
        <span class="breadcrumb__sep">—</span>
        <span class="breadcrumb__item breadcrumb__item--current">Отзывы</span>

    <?php elseif (is_singular('review')): ?>
        <span class="breadcrumb__sep">—</span>
        <a href="<?php echo esc_url(get_post_type_archive_link('review')); ?>" class="breadcrumb__item">Отзывы</a>
        <span class="breadcrumb__sep">—</span>
        <span class="breadcrumb__item breadcrumb__item--current"><?php the_title(); ?></span>

    <?php elseif (is_post_type_archive('service')): ?>
        <span class="breadcrumb__sep">—</span>
        <span class="breadcrumb__item breadcrumb__item--current">Услуги</span>

    <?php elseif (is_singular('service')): ?>
        <span class="breadcrumb__sep">—</span>
        <a href="<?php echo esc_url(get_post_type_archive_link('service')); ?>" class="breadcrumb__item">Услуги</a>
        <span class="breadcrumb__sep">—</span>
        <span class="breadcrumb__item breadcrumb__item--current"><?php the_title(); ?></span>

    <?php elseif (is_category()): ?>
        <span class="breadcrumb__sep">—</span>
        <span class="breadcrumb__item breadcrumb__item--current"><?php single_cat_title(); ?></span>

    <?php elseif (is_search()): ?>
        <span class="breadcrumb__sep">—</span>
        <span class="breadcrumb__item breadcrumb__item--current">Поиск: <?php echo esc_html(get_search_query()); ?></span>
    <?php endif; ?>
</nav>
