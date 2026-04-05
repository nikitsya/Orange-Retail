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

        const syncMasthead = () => {
            const utilityHeight = utilityBar.offsetHeight;
            const utilityBottom = Math.max(0, utilityBar.getBoundingClientRect().bottom);
            const shouldFix = utilityBottom < utilityHeight;

            masthead.classList.toggle('is-fixed', shouldFix);

            if (! shouldFix) {
                masthead.style.top = '';
                spacer.style.height = '0px';
                return;
            }

            masthead.style.top = `${utilityBottom}px`;
            spacer.style.height = `${masthead.offsetHeight}px`;
        };

        syncMasthead();
        window.addEventListener('scroll', syncMasthead, { passive: true });
        window.addEventListener('resize', syncMasthead);
    });
</script>
