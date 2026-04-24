<?php
if (!defined('ABSPATH')) exit;

get_header();
the_post();

$video_source = get_field('case_video_source');
$video       = get_field('case_video');
$video_file  = get_field('case_video_file');
$gallery     = get_field('case_gallery');
$client      = get_field('case_client');
$amount      = get_field('case_amount');
$number      = get_field('case_number');
$docs        = get_field('case_docs');
$review      = get_field('case_review_text');
$archive_title = get_field('cases_archive_title', 'option') ?: 'Завершённые дела';
$case_photo  = get_field('case_photo');
$case_photo_url = '';
$case_photo_alt = '';
$case_placeholder = get_template_directory_uri() . '/images/case-placeholder.svg';
if (is_array($case_photo) && !empty($case_photo['url'])) {
    $case_photo_url = (string) $case_photo['url'];
    $case_photo_alt = (string) ($case_photo['alt'] ?? '');
} elseif (is_numeric($case_photo)) {
    $case_photo_url = (string) wp_get_attachment_url((int) $case_photo);
}
if ($case_photo_url === '' && has_post_thumbnail()) {
    $thumb_id = (int) get_post_thumbnail_id();
    $thumb_src = wp_get_attachment_image_src($thumb_id, 'full');
    if (is_array($thumb_src) && !empty($thumb_src[0])) {
        $case_photo_url = (string) $thumb_src[0];
        $case_photo_alt = (string) get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
    }
}
if ($case_photo_url === '') {
    $case_photo_url = $case_placeholder;
}
if ($case_photo_alt === '') {
    $case_photo_alt = get_the_title();
}
$video_file_url = '';
$video_file_type = '';
if (is_array($video_file) && !empty($video_file['url'])) {
    $video_file_url = (string) $video_file['url'];
    $video_file_type = (string) ($video_file['mime_type'] ?? '');
} elseif (is_numeric($video_file)) {
    $video_file_url = (string) wp_get_attachment_url((int) $video_file);
    $video_file_type = (string) get_post_mime_type((int) $video_file);
} elseif (is_string($video_file) && $video_file !== '') {
    $video_file_url = $video_file;
}
if ($video_file_url !== '' && $video_file_type === '') {
    $video_file_type_data = wp_check_filetype($video_file_url);
    $video_file_type = (string) ($video_file_type_data['type'] ?? '');
}
$render_video_file = $video_source === 'file' && $video_file_url !== '';
$has_media = $render_video_file || !empty($video) || !empty($gallery);
?>

<main class="single-case">
    <div class="single-case__inner">

        <?php get_template_part('template-parts/breadcrumb'); ?>
        <h1 class="single-case__archive-title"><?php echo esc_html($archive_title); ?></h1>

        <div class="single-case__layout">

            <aside class="single-case__sidebar">
                <?php get_template_part('template-parts/calculator-widget'); ?>
            </aside>

            <div class="single-case__content">
                <h2 class="single-case__title"><?php the_title(); ?></h2>

                <?php if ($render_video_file): ?>
                <div class="single-case__media">
                    <video controls preload="metadata" playsinline>
                        <source src="<?php echo esc_url($video_file_url); ?>"<?php echo $video_file_type !== '' ? ' type="' . esc_attr($video_file_type) . '"' : ''; ?>>
                    </video>
                </div>
                <?php elseif ($video): ?>
                <div class="single-case__media"><?php echo $video; ?></div>
                <?php elseif ($gallery): ?>
                <div class="single-case__media">
                    <div class="swiper single-case__swiper">
                        <div class="swiper-wrapper">
                            <?php foreach ($gallery as $img): ?>
                            <div class="swiper-slide">
                                <img src="<?php echo esc_url($img['url']); ?>" alt="<?php echo esc_attr($img['alt']); ?>">
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($gallery) > 1): ?>
                        <div class="swiper-pagination"></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="single-case__media">
                    <img src="<?php echo esc_url($case_photo_url); ?>" alt="<?php echo esc_attr($case_photo_alt); ?>">
                </div>
                <?php endif; ?>

                <div class="case-card__row single-case__details-row">
                    <div class="case-card__body single-case__details-body">
                        <div class="single-case__meta">
                            <?php if ($client): ?>
                            <div class="case-meta-row">
                                <span class="case-meta-label">Клиент:</span>
                                <span class="case-meta-value"><?php echo esc_html($client); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($amount): ?>
                            <div class="case-meta-row">
                                <span class="case-meta-label">Сумма:</span>
                                <span class="case-meta-value"><?php echo esc_html($amount); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($number): ?>
                            <div class="case-meta-row">
                                <span class="case-meta-label">Дело:</span>
                                <span class="case-meta-value"><?php echo esc_html($number); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($docs): ?>
                        <div class="case-card__docs">
                            <div class="case-docs-label">Документы:</div>
                            <div class="case-docs-list">
                                <?php foreach ($docs as $doc):
                                    $icon = $doc['doc_icon'];
                                    $url  = $doc['doc_url'];
                                ?>
                                <a href="<?php echo esc_url($url ?: '#'); ?>" class="case-doc-pill" target="_blank">
                                    <?php if ($icon): ?>
                                        <img src="<?php echo esc_url($icon['url']); ?>" alt="">
                                    <?php endif; ?>
                                    <span><?php echo esc_html($doc['doc_title']); ?></span>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($review): ?>
                        <div class="single-case__review">
                            <p class="single-case__review-label">Текст отзыва:</p>
                            <div class="single-case__review-text"><?php echo wp_kses_post($review); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($has_media): ?>
                    <div class="case-card__photo single-case__photo">
                        <img src="<?php echo esc_url($case_photo_url); ?>" alt="<?php echo esc_attr($case_photo_alt); ?>">
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <?php
    $related = new WP_Query([
        'post_type'      => 'case',
        'posts_per_page' => 4,
        'post_status'    => 'publish',
        'post__not_in'   => [get_the_ID()],
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
    if ($related->have_posts()):
    ?>
    <section class="cases-preview">
        <div class="cases-preview__inner">
            <div class="cases-preview__grid">
                <?php while ($related->have_posts()): $related->the_post();
                    get_template_part('template-parts/case-card');
                endwhile;
                wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var mediaSwiper = document.querySelector('.single-case__swiper');
    if (mediaSwiper) new Swiper(mediaSwiper, { pagination: { el: '.swiper-pagination', clickable: true } });
});
</script>

<?php get_footer(); ?>
