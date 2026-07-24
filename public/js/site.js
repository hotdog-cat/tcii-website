(() => {
    const header = document.querySelector('.site-header');
    const toggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('.main-nav');
    const services = document.querySelector('.services-menu');
    const servicesButton = services?.querySelector('button');

    const closeMenu = () => {
        toggle?.classList.remove('is-open');
        nav?.classList.remove('is-open');
        services?.classList.remove('is-open');
        toggle?.setAttribute('aria-expanded', 'false');
        servicesButton?.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    };

    addEventListener('scroll', () => header?.classList.toggle('is-scrolled', scrollY > 24), { passive: true });
    header?.classList.toggle('is-scrolled', scrollY > 24);
    toggle?.addEventListener('click', () => {
        const open = !nav?.classList.contains('is-open');
        toggle.classList.toggle('is-open', open);
        nav?.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', String(open));
        document.body.style.overflow = open ? 'hidden' : '';
    });
    servicesButton?.addEventListener('click', () => {
        const open = !services?.classList.contains('is-open');
        services?.classList.toggle('is-open', open);
        servicesButton.setAttribute('aria-expanded', String(open));
    });
    nav?.querySelectorAll('a').forEach(link => link.addEventListener('click', closeMenu));
    addEventListener('keydown', event => { if (event.key === 'Escape') closeMenu(); });
})();
