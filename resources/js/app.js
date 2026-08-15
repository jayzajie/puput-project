document.querySelector('#menu-toggle')?.addEventListener('click', () => document.querySelector('.sidebar')?.classList.toggle('open'));
document.querySelector('[data-toggle-password]')?.addEventListener('click', () => {
    const input = document.querySelector('#password');
    if (input instanceof HTMLInputElement) input.type = input.type === 'password' ? 'text' : 'password';
});
document.querySelectorAll('[data-dialog]').forEach(button => button.addEventListener('click', () => document.querySelector(`#${button.dataset.dialog}`)?.showModal()));
document.querySelectorAll('[data-close-dialog]').forEach(button => button.addEventListener('click', () => button.closest('dialog')?.close()));
