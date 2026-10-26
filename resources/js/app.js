const sidebar = document.querySelector('.sidebar');
const backdrop = document.querySelector('#sidebar-backdrop');
const menuToggle = document.querySelector('#menu-toggle');

const setSidebarOpen = (open) => {
    if (!sidebar) return;
    sidebar.classList.toggle('open', open);
    if (backdrop) backdrop.hidden = !open;
};

menuToggle?.addEventListener('click', () => {
    setSidebarOpen(!sidebar?.classList.contains('open'));
});

backdrop?.addEventListener('click', () => setSidebarOpen(false));

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && sidebar?.classList.contains('open')) {
        setSidebarOpen(false);
    }
});

document.querySelector('[data-toggle-password]')?.addEventListener('click', () => {
    const input = document.querySelector('#password');
    if (input instanceof HTMLInputElement) input.type = input.type === 'password' ? 'text' : 'password';
});

document.querySelectorAll('[data-dialog]').forEach(button => button.addEventListener('click', () => document.querySelector(`#${button.dataset.dialog}`)?.showModal()));
document.querySelectorAll('[data-close-dialog]').forEach(button => button.addEventListener('click', () => button.closest('dialog')?.close()));
