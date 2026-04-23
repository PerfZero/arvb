<?php
if (!defined('ABSPATH')) exit;

// Можно передать title/badge/subtitle через $args при вызове get_template_part()
// или они подтягиваются из ACF текущей страницы
$title    = $args['title']    ?? get_field('faq_title');
$badge    = $args['badge']    ?? get_field('faq_badge');
$subtitle = $args['subtitle'] ?? get_field('faq_subtitle');

$faq_videos = new WP_Query([
    'post_type'      => 'faq_video',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
]);
if (!$faq_videos->have_posts()) return;
?>
<section class="faq-vid">
    <div class="container">
        <div class="faq-vid__header">
            <div class="faq-vid__header-left">
                <?php if ($badge): ?>
                <span class="faq-vid__badge"><?php echo esc_html($badge); ?></span>
                <?php endif; ?>
                <?php if ($title): ?>
                <h2 class="faq-vid__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
                <?php if ($subtitle): ?>
                <p class="faq-vid__subtitle"><?php echo esc_html($subtitle); ?></p>
                <?php endif; ?>
            </div>
            <div class="faq-vid__nav">
                <button class="faq-vid__prev" aria-label="Назад">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M15 19l-7-7 7-7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button class="faq-vid__next" aria-label="Вперёд">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        </div>

        <div class="swiper faq-vid__swiper">
            <div class="swiper-wrapper">
                <?php while ($faq_videos->have_posts()): $faq_videos->the_post();
                    $video_file   = get_field('fv_video_file');
                    $thumb        = get_field('fv_thumbnail');
                    $thumb_url    = $thumb ? $thumb['url'] : get_the_post_thumbnail_url(null, 'large');
                    $source_label = get_field('fv_source_label');
                ?>
                <div class="swiper-slide faq-vid__slide">
                    <div class="faq-vid__card">
                        <p class="faq-vid__card-title"><?php the_title(); ?></p>
                        <div class="faq-vid__card-video">
                            <video
                                src="<?php echo esc_url($video_file['url'] ?? ''); ?>"
                                <?php if ($thumb_url): ?>poster="<?php echo esc_url($thumb_url); ?>"<?php endif; ?>
                                playsinline
                                preload="none"
                                loop
                            ></video>
                            <button class="faq-vid__play" aria-label="Воспроизвести">
                                <svg width="28" height="32" viewBox="0 0 28 32" fill="none"><path d="M2 2l24 14L2 30V2z" fill="white"/></svg>
                            </button>
                            <?php if ($source_label): ?>
                            <span class="faq-vid__source"><?php echo esc_html($source_label); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            <div class="swiper-scrollbar faq-vid__scrollbar"></div>
        </div>
    </div>
</section>
<script>
window.addEventListener('load', function () {
    new Swiper('.faq-vid__swiper', {
        slidesPerView: 1.3,
        spaceBetween: 16,
        scrollbar: { el: '.faq-vid__scrollbar', draggable: true, dragSize: 120 },
        navigation: { prevEl: '.faq-vid__prev', nextEl: '.faq-vid__next' },
        breakpoints: {
            600:  { slidesPerView: 2.3 },
            900:  { slidesPerView: 3.3 },
            1200: { slidesPerView: 4.5 },
        },
    });

    document.querySelectorAll('.faq-vid__card-video').forEach(function (wrap) {
        var btn   = wrap.querySelector('.faq-vid__play');
        var video = wrap.querySelector('video');
        if (!btn || !video) return;
        btn.addEventListener('click', function () {
            btn.style.display = 'none';
            video.controls = true;
            video.play();
        });
        video.addEventListener('pause', function () { btn.style.display = 'flex'; });
        video.addEventListener('ended', function () {
            btn.style.display = 'flex';
            video.controls = false;
        });
    });
});
</script>
