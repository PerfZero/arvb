<?php
/*
 * Template Name: Работа у нас
 */
if (!defined('ABSPATH')) exit;

get_header();

$title = get_field('jobs_title') ?: 'Работа у нас';
$desc  = get_field('jobs_desc');
$vacancies = new WP_Query([
    'post_type' => 'vacancy',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => [
        'menu_order' => 'ASC',
        'date' => 'DESC',
    ],
]);
?>

<main class="jobs-page">
    <div class="jobs-page__inner">

        <?php get_template_part('template-parts/breadcrumb'); ?>

        <h1 class="jobs-page__title"><?php echo esc_html($title); ?></h1>

        <?php if ($desc): ?>
        <p class="jobs-page__desc"><?php echo wp_kses_post($desc); ?></p>
        <?php endif; ?>

        <?php if ($vacancies->have_posts()): ?>
        <div class="jobs-list">
            <?php while ($vacancies->have_posts()): $vacancies->the_post(); ?>
            <?php
                $v_title = get_the_title();
                $v_salary = trim((string) get_field('vacancy_salary'));
                $v_url = trim((string) get_field('vacancy_url'));
                $v_btn_text = trim((string) get_field('vacancy_btn_text'));
                if ($v_btn_text === '') {
                    $v_btn_text = 'Подробнее';
                }
            ?>
            <div class="job-card">
                <div class="job-card__info">
                    <h2 class="job-card__title"><?php echo esc_html($v_title); ?></h2>
                    <?php if ($v_salary !== ''): ?>
                    <p class="job-card__salary"><?php echo esc_html($v_salary); ?></p>
                    <?php endif; ?>
                </div>
                <?php if ($v_url !== ''): ?>
                <a href="<?php echo esc_url($v_url); ?>" class="job-card__btn"><?php echo esc_html($v_btn_text); ?></a>
                <?php else: ?>
                <span class="job-card__btn is-disabled"><?php echo esc_html($v_btn_text); ?></span>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        </div>
        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>
