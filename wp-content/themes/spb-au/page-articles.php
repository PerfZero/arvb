<?php
/*
 * Template Name: Статьи
 */
if (!defined("ABSPATH")) {
    exit();
}

get_header();

$current_cat = isset($_GET["cat"]) ? (int) $_GET["cat"] : 0;
$search_query = isset($_GET["s"]) ? sanitize_text_field($_GET["s"]) : "";
$paged = max(1, (int) get_query_var("paged"));

$args = [
    "post_type" => "post",
    "posts_per_page" => 12,
    "post_status" => "publish",
    "orderby" => "date",
    "order" => "DESC",
    "paged" => $paged,
];

if ($current_cat) {
    $args["cat"] = $current_cat;
}

if ($search_query) {
    $args["s"] = $search_query;
}

$posts_query = new WP_Query($args);

// Все категории для фильтра
$categories = get_categories(["hide_empty" => true]);

// Название активной категории
$active_cat_name = "Статьи";
if ($current_cat) {
    $cat_obj = get_category($current_cat);
    if ($cat_obj && !is_wp_error($cat_obj)) {
        $active_cat_name = $cat_obj->name;
    }
}

$page_url = get_permalink();
$pagination_args = [];
if ($current_cat) {
    $pagination_args["cat"] = $current_cat;
}
if ($search_query) {
    $pagination_args["s"] = $search_query;
}
?>

<main class="articles-page">
    <div class="articles-inner">

        <div class="articles-hero">
            <?php get_template_part("template-parts/breadcrumb"); ?>

            <div class="articles-filters-row">
                <div class="articles-filters">
                    <a href="<?php echo esc_url($page_url); ?>"
                       class="filter-btn <?php echo !$current_cat
                           ? "filter-btn--active"
                           : ""; ?>">
                        Все категории
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="<?php echo esc_url(
                            add_query_arg("cat", $cat->term_id, $page_url),
                        ); ?>"
                           class="filter-btn <?php echo $current_cat ===
                           $cat->term_id
                               ? "filter-btn--active"
                               : ""; ?>">
                            <?php echo esc_html($cat->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <form class="articles-search" action="" method="get">
                    <?php if ($current_cat): ?>
                        <input type="hidden" name="cat" value="<?php echo esc_attr(
                            $current_cat,
                        ); ?>">
                    <?php endif; ?>
                    <div class="articles-search-wrap">
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                            <path d="M2 5h14M2 9h10M2 13h7" stroke="#888" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        <input type="text" name="s" placeholder="Поиск по сайту"
                               value="<?php echo esc_attr($search_query); ?>">
                    </div>
                    <button type="submit" class="articles-search-btn">Найти</button>
                </form>
            </div>
        </div>

        <h1 class="articles-title"><?php echo esc_html(
            $active_cat_name,
        ); ?></h1>

        <div class="articles-grid">
            <?php if ($posts_query->have_posts()): ?>
                <?php
                while ($posts_query->have_posts()):
                    $posts_query->the_post();
                    get_template_part('template-parts/article-card');
                endwhile;
                wp_reset_postdata();
                ?>
            <?php else: ?>
                <p class="articles-empty">Статьи не найдены.</p>
            <?php endif; ?>
        </div>

        <?php if ($posts_query->max_num_pages > 1): ?>
            <nav class="articles-pagination" aria-label="Навигация по страницам">
                <?php
                echo paginate_links([
                    "base" => str_replace(
                        999999999,
                        "%#%",
                        esc_url(get_pagenum_link(999999999)),
                    ),
                    "format" => "?paged=%#%",
                    "current" => $paged,
                    "total" => $posts_query->max_num_pages,
                    "mid_size" => 1,
                    "end_size" => 1,
                    "prev_text" => "Назад",
                    "next_text" => "Вперед",
                    "type" => "list",
                    "add_args" => $pagination_args ?: false,
                ]);
                ?>
            </nav>
        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>
