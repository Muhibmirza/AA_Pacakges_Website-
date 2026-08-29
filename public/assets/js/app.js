(function () {
  const qs = (sel, root = document) => root.querySelector(sel);
  const qsa = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || window.AA_PACKAGES?.csrf || '';
  const baseUrl = window.AA_PACKAGES?.baseUrl || '/aapackages/';
  const money = (value) => 'Rs. ' + Math.round(Number(value || 0)).toLocaleString();

  qsa('[data-nav-toggle]').forEach((btn) => btn.addEventListener('click', () => qs('[data-nav]')?.classList.toggle('is-open')));

  const revealObserver = 'IntersectionObserver' in window ? new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        revealObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.14 }) : null;
  qsa('.reveal, [data-category-banner]').forEach((el) => revealObserver ? revealObserver.observe(el) : el.classList.add('is-visible'));

  qsa('[data-hero-slider]').forEach((slider) => {
    const slides = qsa('.hero-slide', slider);
    const dotsWrap = qs('[data-slider-dots]', slider);
    let active = 0;
    let timer = null;
    const render = (index) => {
      active = (index + slides.length) % slides.length;
      slides.forEach((slide, i) => slide.classList.toggle('is-active', i === active));
      qsa('button', dotsWrap).forEach((dot, i) => dot.classList.toggle('active', i === active));
    };
    slides.forEach((_, i) => {
      const dot = document.createElement('button');
      dot.type = 'button';
      dot.addEventListener('click', () => render(i));
      dotsWrap.appendChild(dot);
    });
    const start = () => { timer = setInterval(() => render(active + 1), 4800); };
    const stop = () => clearInterval(timer);
    qs('[data-slide-prev]', slider)?.addEventListener('click', () => render(active - 1));
    qs('[data-slide-next]', slider)?.addEventListener('click', () => render(active + 1));
    slider.addEventListener('mouseenter', stop);
    slider.addEventListener('mouseleave', start);
    render(0);
    start();
  });

  qsa('[data-category-banner]').forEach((banner) => {
    const img = qs('[data-parallax-card]', banner);
    if (!img || matchMedia('(pointer: coarse)').matches || matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    let targetX = 0, targetY = 0, currentX = 0, currentY = 0, raf = null;
    const animate = () => {
      currentX += (targetX - currentX) * 0.08;
      currentY += (targetY - currentY) * 0.08;
      img.style.transform = `translateY(0) rotateX(${currentY}deg) rotateY(${currentX}deg) scale(1)`;
      raf = requestAnimationFrame(animate);
    };
    banner.addEventListener('mousemove', (event) => {
      const rect = banner.getBoundingClientRect();
      const x = (event.clientX - rect.left) / rect.width - 0.5;
      const y = (event.clientY - rect.top) / rect.height - 0.5;
      targetX = x * 10;
      targetY = -y * 8;
      if (!raf) animate();
    });
    banner.addEventListener('mouseleave', () => {
      targetX = 4;
      targetY = 2;
      setTimeout(() => {
        cancelAnimationFrame(raf);
        raf = null;
        img.style.transform = '';
      }, 500);
    });
  });

  qsa('[data-qty-minus], [data-qty-plus]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const input = btn.parentElement.querySelector('input[type="number"]');
      const delta = btn.matches('[data-qty-plus]') ? 1 : -1;
      input.value = Math.max(Number(input.min || 1), Number(input.value || 1) + delta);
    });
  });

  const openCart = () => document.body.classList.add('cart-open');
  const closeCart = () => document.body.classList.remove('cart-open');
  qsa('[data-cart-open]').forEach((btn) => btn.addEventListener('click', () => { fetchCart(); openCart(); }));
  qsa('[data-cart-close]').forEach((btn) => btn.addEventListener('click', closeCart));

  qsa('[data-logout-confirm]').forEach((link) => {
    link.addEventListener('click', (event) => {
      const modal = qs('[data-logout-modal]');
      if (!modal) return;
      event.preventDefault();
      modal.hidden = false;
      document.body.classList.add('modal-open');
    });
  });
  qsa('[data-logout-cancel], [data-logout-modal]').forEach((item) => {
    item.addEventListener('click', (event) => {
      if (event.target !== item && !item.matches('[data-logout-cancel]')) return;
      const modal = qs('[data-logout-modal]');
      if (modal) modal.hidden = true;
      document.body.classList.remove('modal-open');
    });
  });

  const renderCart = (data) => {
    const body = qs('[data-cart-items]');
    if (!body || !data.cart) return;
    body.innerHTML = data.cart.length ? data.cart.map((item) => `
      <article class="mini-cart-line">
        <img src="${baseUrl}${String(item.image).replace(/^\/+/, '')}" alt="">
        <div>
          <h3>${escapeHtml(item.name)}</h3>
          <p>${escapeHtml(item.variant || '')} | Qty ${item.qty}</p>
          <form data-cart-remove>
            <input type="hidden" name="csrf_token" value="${escapeHtml(csrf)}">
            <input type="hidden" name="product_id" value="${item.id}">
            <input type="hidden" name="qty" value="0">
            <button class="remove-btn" type="submit">Remove</button>
          </form>
        </div>
        <strong>${money(item.price * item.qty)}</strong>
      </article>
    `).join('') : '<div class="empty-state">Your cart is empty.</div>';
    qsa('[data-cart-count]').forEach((badge) => {
      badge.textContent = data.count || 0;
      badge.classList.add('pop');
      setTimeout(() => badge.classList.remove('pop'), 260);
    });
    qsa('[data-cart-subtotal]').forEach((el) => { el.textContent = money(data.totals?.subtotal || 0); });
  };

  const postForm = async (page, form) => {
    const fd = new FormData(form);
    if (!fd.get('csrf_token')) fd.append('csrf_token', csrf);
    const res = await fetch(`${baseUrl}${page}`, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    return res.json();
  };
  const fetchCart = async () => {
    const res = await fetch(`${baseUrl}cart-drawer`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    renderCart(await res.json());
  };
  qsa('[data-add-to-cart]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      const button = form.querySelector('button[type="submit"]');
      button.disabled = true;
      button.textContent = 'Adding...';
      try {
        const data = await postForm('cart-add', form);
        renderCart(data);
        openCart();
      } finally {
        button.disabled = false;
        button.textContent = 'Add to Cart';
      }
    });
  });
  qsa('[data-cart-update] input[name="qty"]').forEach((input) => {
    input.addEventListener('change', async () => {
      const data = await postForm('cart-update', input.form);
      renderCart(data);
      location.reload();
    });
  });
  document.addEventListener('submit', async (event) => {
    const form = event.target.closest('[data-cart-remove]');
    if (!form) return;
    event.preventDefault();
    const button = form.querySelector('button[type="submit"]');
    if (button) {
      button.disabled = true;
      button.textContent = 'Removing...';
    }
    const data = await postForm('cart-update', form);
    renderCart(data);
    if (qs('[data-cart-page-list]')) {
      location.reload();
    }
  });
  fetchCart();

  qsa('[data-thumb]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const main = qs('[data-main-product-image]');
      if (main) main.src = btn.dataset.thumb;
    });
  });
  qsa('[data-image-preview]').forEach((input) => {
    input.addEventListener('change', () => {
      const box = qs('[data-preview-box]');
      if (!box || !input.files?.[0]) return;
      const img = document.createElement('img');
      img.src = URL.createObjectURL(input.files[0]);
      box.innerHTML = '';
      box.appendChild(img);
    });
  });
  function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[char]));
  }
})();

