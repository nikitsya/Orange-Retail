<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Live search via fetch — updates page content without full reload
        document.querySelectorAll('form[data-live-search]').forEach((form) => {
            const input = form.querySelector('input[type="search"]');
            if (!input) return;

            let timer = null;

            const doSearch = async () => {
                const params = new URLSearchParams(new FormData(form));
                const url = form.action + '?' + params.toString();

                try {
                    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const html = await res.text();
                    const doc = new DOMParser().parseFromString(html, 'text/html');

                    // Replace <main> content
                    const newMain = doc.querySelector('main');
                    const curMain = document.querySelector('main');
                    if (newMain && curMain) curMain.replaceWith(newMain);

                    // Replace catalog/inventory nav shell if present
                    const newNav = doc.querySelector('.catalog-nav-shell');
                    const curNav = document.querySelector('.catalog-nav-shell');
                    if (newNav && curNav) curNav.replaceWith(newNav);

                    history.pushState(null, '', url);
                } catch (_) {
                    form.requestSubmit();
                }

                input.focus();
                const v = input.value; input.value = ''; input.value = v;
            };

            input.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(doSearch, 450);
            });
        });

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
