import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.querySelectorAll('.tix-account').forEach((account) => {
    const trigger = account.querySelector('summary');

    const syncExpanded = () => {
        trigger?.setAttribute('aria-expanded', String(account.open));
    };

    account.addEventListener('toggle', syncExpanded);
    document.addEventListener('pointerdown', (event) => {
        if (!account.contains(event.target) && account.open) {
            account.open = false;
            syncExpanded();
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && account.open) {
            account.open = false;
            syncExpanded();
            trigger?.focus();
        }
    });
});
