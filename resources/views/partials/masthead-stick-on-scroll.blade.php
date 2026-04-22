<script>
    document.addEventListener('DOMContentLoaded', () => {
        const masthead = document.querySelector('.masthead');
        const utilityBar = document.querySelector('.utility-bar');
        const mobileViewport = window.matchMedia('(max-width: 920px)');

        if (!masthead || !utilityBar) {
            return;
        }

        const spacer = document.createElement('div');
        spacer.className = 'masthead-spacer';
        utilityBar.insertAdjacentElement('afterend', spacer);

        const sync = () => {
            if (mobileViewport.matches) {
                masthead.classList.remove('is-fixed');
                masthead.style.top = '';
                utilityBar.classList.remove('is-fixed');
                utilityBar.style.top = '';
                spacer.style.height = '0px';
                return;
            }

            const shouldFix = masthead.getBoundingClientRect().top <= 0;
            masthead.classList.toggle('is-fixed', shouldFix);
            utilityBar.classList.toggle('is-fixed', shouldFix);

            if (shouldFix) {
                const mh = masthead.offsetHeight;
                masthead.style.top = '0px';
                utilityBar.style.top = mh + 'px';
                spacer.style.height = (mh + utilityBar.offsetHeight) + 'px';
            } else {
                masthead.style.top = '';
                utilityBar.style.top = '';
                spacer.style.height = '0px';
            }
        };

        sync();
        window.addEventListener('scroll', sync, { passive: true });
        window.addEventListener('resize', sync);
    });
</script>
