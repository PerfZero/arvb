document.addEventListener('DOMContentLoaded', function () {
    var mainEl  = document.querySelector('.post-gallery__main');
    var thumbEl = document.querySelector('.post-gallery__thumbs');
    if (!mainEl) return;

    GLightbox({ selector: '.glightbox' });

    var thumbsSwiper = null;

    if (thumbEl) {
        thumbsSwiper = new Swiper(thumbEl, {
            spaceBetween: 12,
            slidesPerView: 3,
            freeMode: true,
            watchSlidesProgress: true,
            observer: true,
            observeParents: true,
        });
    }

    new Swiper(mainEl, {
        spaceBetween: 0,
        observer: true,
        observeParents: true,
        thumbs: thumbsSwiper ? { swiper: thumbsSwiper } : undefined,
    });
});
