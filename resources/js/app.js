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

    const canTilt = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    if (canTilt) {
        document.querySelectorAll('[data-tilt-card]').forEach((card) => {
            const maxTilt = 10;

            card.addEventListener('pointermove', (event) => {
                const rect = card.getBoundingClientRect();
                const x = ((event.clientX - rect.left) / rect.width) - 0.5;
                const y = ((event.clientY - rect.top) / rect.height) - 0.5;

                card.classList.add('is-tilting');
                card.style.setProperty('--sf-tilt-x', `${(-y * maxTilt).toFixed(2)}deg`);
                card.style.setProperty('--sf-tilt-y', `${(x * maxTilt).toFixed(2)}deg`);
            });

            card.addEventListener('pointerleave', () => {
                card.classList.remove('is-tilting');
                card.style.setProperty('--sf-tilt-x', '0deg');
                card.style.setProperty('--sf-tilt-y', '0deg');
            });
        });
    }
});
