(() => {
    'use strict';
    const workspace = document.querySelector('.umrah-workspace');
    if (!workspace) return;

    const reveal = (target, scroll = true) => {
        if (!target) return;
        for (let current = target; current && current !== workspace; current = current.parentElement) {
            if (current.tagName === 'DETAILS') current.open = true;
        }
        if (scroll) {
            target.scrollIntoView({ block: 'start', behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
        }
    };
    const hashTarget = () => {
        try { return document.getElementById(decodeURIComponent(window.location.hash.slice(1))); }
        catch { return null; }
    };
    workspace.addEventListener('click', event => {
        const link = event.target.closest('a[href^="#"]');
        if (!link) return;
        const target = document.getElementById(link.getAttribute('href').slice(1));
        if (!target) return;
        reveal(target, false);
        workspace.querySelectorAll('.umrah-section-nav a').forEach(item => item.removeAttribute('aria-current'));
        if (link.closest('.umrah-section-nav')) link.setAttribute('aria-current', 'location');
    });
    window.addEventListener('hashchange', () => reveal(hashTarget()));
    if (window.location.hash) requestAnimationFrame(() => reveal(hashTarget()));

    // Reopen the submitted panel after server validation, preserving entered values.
    workspace.querySelectorAll('form[method="POST"], form[method="post"]').forEach(form => {
        const field = document.createElement('input');
        field.type = 'hidden';
        field.name = '_umrah_form';
        field.value = new URL(form.action).pathname;
        form.append(field);
        if (workspace.dataset.lastForm === field.value && workspace.querySelector('[data-umrah-errors]')) reveal(form, false);
    });

    workspace.addEventListener('invalid', event => reveal(event.target, false), true);
    workspace.addEventListener('focusin', event => {
        if (event.target.matches('input, textarea, select')) document.body.classList.add('umrah-editing');
    });
    workspace.addEventListener('focusout', () => {
        requestAnimationFrame(() => {
            if (!workspace.contains(document.activeElement) || !document.activeElement.matches('input, textarea, select')) {
                document.body.classList.remove('umrah-editing');
            }
        });
    });
    workspace.querySelectorAll('.umrah-records td:not([colspan])').forEach(cell => {
        if (!cell.textContent.trim() && !cell.querySelector('a, button, input, select')) cell.dataset.empty = 'true';
    });
})();
