document.addEventListener('DOMContentLoaded', () => {
    const heroSection = document.querySelector('.category-hero');
    if (heroSection) {
        requestAnimationFrame(() => {
            heroSection.classList.add('category-hero--ready');
        });
    }

    const toolbarButtons = document.querySelectorAll('.category-toolbar .btn');
    toolbarButtons.forEach((button) => {
        button.addEventListener('focus', () => button.classList.add('is-focused'));
        button.addEventListener('blur', () => button.classList.remove('is-focused'));
    });
});
