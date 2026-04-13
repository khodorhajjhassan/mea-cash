import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('delete-modal');
    const modalForm = document.getElementById('delete-modal-form');
    const modalText = document.getElementById('delete-modal-text');

    document.querySelectorAll('.js-delete-button').forEach((button) => {
        button.addEventListener('click', () => {
            if (!modal || !modalForm || !modalText) {
                return;
            }

            const action = button.getAttribute('data-action');
            const name = button.getAttribute('data-name') || 'this record';

            modalForm.setAttribute('action', action ?? '#');
            modalText.textContent = `Are you sure you want to delete "${name}"? This action cannot be undone.`;
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
        });
    });

    if (modal) {
        modal.addEventListener('click', (event) => {
            if ((event.target).id === 'delete-modal' || (event.target).hasAttribute('data-close-delete-modal')) {
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
            }
        });
    }

    document.querySelectorAll('[data-auto-dismiss]').forEach((alert) => {
        window.setTimeout(() => {
            alert.classList.add('opacity-0', 'transition', 'duration-500');
            window.setTimeout(() => alert.remove(), 500);
        }, 3500);
    });
});
