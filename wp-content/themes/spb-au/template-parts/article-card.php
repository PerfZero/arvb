<?php
if (!defined('ABSPATH')) {
    exit;
}

$cats     = get_the_category();
$cat_name = $cats ? $cats[0]->name : '';
$time_ago = human_time_diff(get_the_time('U'), current_time('timestamp'));
?>

<article class="article-card">
    <?php if (has_post_thumbnail()): ?>
        <a href="<?php the_permalink(); ?>" class="article-card__image">
            <?php the_post_thumbnail('large'); ?>
        </a>
    <?php endif; ?>

    <div class="article-card__body">
        <div class="article-card__meta">
            <?php if ($cat_name): ?>
                <?php echo esc_html($cat_name); ?>
            <?php endif; ?>
            (<?php echo esc_html($time_ago); ?> назад)
        </div>
        <h2 class="article-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>
        <div class="article-card__excerpt"><?php the_excerpt(); ?></div>
        <a href="<?php the_permalink(); ?>" class="article-card__more">
            Смотреть полностью
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M12.829 11.6889L8.00781 6.86771L9.38529 5.49023L15.584 11.6889L9.38529 17.8875L8.00781 16.5101L12.829 11.6889Z" fill="#2E3138"/>
            </svg>
        </a>
    </div>
</article>
