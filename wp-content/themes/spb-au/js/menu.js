document.addEventListener('DOMContentLoaded', function () {

    // ── Mobile menu (burger-btn) ──────────────────────────────
    const burgerBtn  = document.querySelector('.burger-btn');
    const mobileMenu = document.querySelector('.mobile-menu');
    const mobileClose = document.querySelector('.mobile-menu__close');
    const mobileOverlay = document.querySelector('.mobile-menu__overlay');

    if (burgerBtn && mobileMenu) {
        function openMobile() {
            mobileMenu.classList.add('is-open');
            mobileMenu.setAttribute('aria-hidden', 'false');
            burgerBtn.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
        }
        function closeMobile() {
            mobileMenu.classList.remove('is-open');
            mobileMenu.setAttribute('aria-hidden', 'true');
            burgerBtn.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        }
        burgerBtn.addEventListener('click', openMobile);
        if (mobileClose)   mobileClose.addEventListener('click', closeMobile);
        if (mobileOverlay) mobileOverlay.addEventListener('click', closeMobile);
        mobileMenu.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', closeMobile);
        });
    }

    // ── Desktop nav dropdown (icon-btn "Меню") ────────────────
    const iconMenuBtn = document.querySelector('.nav-toggle');
    const headerNav   = document.querySelector('.header-nav');

    if (iconMenuBtn && headerNav) {
        function closeDropdown() {
            headerNav.classList.remove('is-open');
            iconMenuBtn.setAttribute('aria-expanded', 'false');
        }
        iconMenuBtn.addEventListener('click', function () {
            var isOpen = headerNav.classList.toggle('is-open');
            iconMenuBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
        headerNav.querySelectorAll('.nav-dropdown__list a').forEach(function (a) {
            a.addEventListener('click', closeDropdown);
        });
        document.addEventListener('click', function (e) {
            if (!headerNav.contains(e.target)) closeDropdown();
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        if (document.querySelector('.mobile-menu.is-open'))  closeMobile();
        if (document.querySelector('.header-nav.is-open'))   closeDropdown();
    });
});
