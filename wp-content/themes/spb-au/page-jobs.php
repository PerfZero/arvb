<?php
/*
 * Template Name: Работа у нас
 */
if (!defined('ABSPATH')) exit;

get_header();

$title = get_field('jobs_title') ?: 'Работа у нас';
$desc  = get_field('jobs_desc');

// Временный массив вакансий — заменить на HH API
$vacancies = [
    [
        'title'  => 'Руководитель HR-отдела / HDR',
        'salary' => 'От 120 000 ₽ за месяц, на руки',
        'url'    => '#',
    ],
    [
        'title'  => 'Дизайнер',
        'salary' => 'От 100 000 ₽ за месяц, на руки',
        'url'    => '#',
    ],
];
?>

<main class="jobs-page">
    <div class="jobs-page__inner">

        <?php get_template_part('template-parts/breadcrumb'); ?>

        <h1 class="jobs-page__title"><?php echo esc_html($title); ?></h1>

        <?php if ($desc): ?>
        <p class="jobs-page__desc"><?php echo wp_kses_post($desc); ?></p>
        <?php endif; ?>

        <div class="jobs-list">
            <?php foreach ($vacancies as $v): ?>
            <div class="job-card">
                <div class="job-card__info">
                    <h2 class="job-card__title"><?php echo esc_html($v['title']); ?></h2>
                    <p class="job-card__salary"><?php echo esc_html($v['salary']); ?></p>
                </div>
                <a href="<?php echo esc_url($v['url']); ?>" class="job-card__btn">Подробнее</a>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</main>

<?php get_footer(); ?>
