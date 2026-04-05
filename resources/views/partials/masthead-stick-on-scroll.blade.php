<script>
    document.addEventListener('DOMContentLoaded', () => {
        const utilityBar = document.querySelector('.utility-bar');
        const masthead = document.querySelector('.masthead');

        if (! utilityBar || ! masthead) {
            return;
        }

        const spacer = document.createElement('div');
        spacer.className = 'masthead-spacer';
        masthead.insertAdjacentElement('afterend', spacer);
        masthead.classList.add('is-fixed');

        const syncMasthead = () => {
            const utilityBottom = Math.max(0, utilityBar.getBoundingClientRect().bottom);
            masthead.style.top = `${utilityBottom}px`;
            spacer.style.height = `${masthead.offsetHeight}px`;
        };

        syncMasthead();
        window.addEventListener('scroll', syncMasthead, { passive: true });
        window.addEventListener('resize', syncMasthead);
    });
</script>
