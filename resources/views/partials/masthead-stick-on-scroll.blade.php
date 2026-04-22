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

        // AJAX: auto-update cart quantity while typing (debounced)
        const cartQtyTimers = new Map();
        document.addEventListener('input', (e) => {
            const input = e.target;
            if (!input.classList.contains('cart-qty-input')) return;
            const form = input.closest('.cart-qty-form');
            if (!form) return;

            clearTimeout(cartQtyTimers.get(form));
            cartQtyTimers.set(form, setTimeout(async () => {
                const body = new FormData(form);
                input.style.opacity = '0.5';
                try {
                    await fetch(form.action, {
                        method: 'POST',
                        body,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    const res = await fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const html = await res.text();
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newMain = doc.querySelector('main');
                    const curMain = document.querySelector('main');
                    if (newMain && curMain) curMain.replaceWith(newMain);
                } catch (_) {
                    input.style.opacity = '';
                    form.submit();
                }
            }, 500));
        });

        // AJAX: intercept cart + favorite form submits without page reload
        document.addEventListener('submit', async (e) => {
            const form = e.target;
            const cartBtn = form.querySelector('.compact-cart-button');
            const favBtn  = form.querySelector('.favorite-toggle-button');
            if (!cartBtn && !favBtn) return;

            e.preventDefault();
            const btn = cartBtn || favBtn;
            if (btn.disabled) return;
            btn.disabled = true;

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });

                if (res.ok || res.redirected) {
                    if (cartBtn) {
                        // Flash "✓ Added" then restore
                        const orig = btn.textContent;
                        btn.textContent = '✓';
                        // Bump "In cart: X" counter on the card
                        const card = form.closest('.product-card');
                        const slot = card?.querySelector('.catalog-status-slot');
                        if (slot) {
                            const existing = slot.querySelector('.cart-badge');
                            const n = existing ? parseInt(existing.textContent) + 1 : 1;
                            slot.innerHTML = `<span class="cart-badge">${n} in cart</span>`;
                        }
                        setTimeout(() => { btn.textContent = orig; btn.disabled = false; }, 900);

                    } else {
                        // Toggle heart icon + _method field
                        const isFav = btn.getAttribute('aria-label') === 'Remove from favourites';
                        if (isFav) {
                            btn.innerHTML = '&#9825;';
                            btn.setAttribute('aria-label', 'Add to favourites');
                            form.querySelector('[name="_method"]')?.remove();
                        } else {
                            btn.innerHTML = '&#9829;';
                            btn.setAttribute('aria-label', 'Remove from favourites');
                            const m = document.createElement('input');
                            m.type = 'hidden'; m.name = '_method'; m.value = 'DELETE';
                            form.appendChild(m);
                        }
                        btn.disabled = false;
                    }
                } else {
                    btn.disabled = false;
                }
            } catch (_) {
                btn.disabled = false;
                form.submit(); // fallback
            }
        });

        const masthead = document.querySelector('.masthead');
        const utilityBar = document.querySelector('.utility-bar');
        if (!masthead || !utilityBar) {
            return;
        }

        const spacer = document.createElement('div');
        spacer.className = 'masthead-spacer';
        utilityBar.insertAdjacentElement('afterend', spacer);

        const sync = () => {
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
