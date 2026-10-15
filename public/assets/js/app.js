(function () {
  const qs = (sel, root = document) => root ? root.querySelector(sel) : null;
  const qsa = (sel, root = document) => root ? Array.from(root.querySelectorAll(sel)) : [];
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || window.AA_PACKAGES?.csrf || '';
  const baseUrl = window.AA_PACKAGES?.baseUrl || '/';
  const apiBaseUrl = window.AA_PACKAGES?.apiBaseUrl || baseUrl;
  const money = (value) => 'Rs. ' + Math.round(Number(value || 0)).toLocaleString();

  const siteNav = qs('[data-nav]');
  const navToggle = qs('[data-nav-toggle]');
  const closeNav = () => {
    siteNav?.classList.remove('is-open');
    navToggle?.setAttribute('aria-expanded', 'false');
    document.body.classList.remove('nav-open');
    document.documentElement.classList.remove('nav-open');
  };
  const openNav = () => {
    if (!siteNav) return;
    siteNav.scrollTop = 0;
    siteNav?.classList.add('is-open');
    navToggle?.setAttribute('aria-expanded', 'true');
    document.body.classList.add('nav-open');
    document.documentElement.classList.add('nav-open');
  };
  navToggle?.addEventListener('click', () => siteNav?.classList.contains('is-open') ? closeNav() : openNav());
  qsa('[data-nav-close]').forEach((btn) => btn.addEventListener('click', closeNav));
  qsa('a', siteNav).forEach((link) => link.addEventListener('click', closeNav));
  window.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeNav();
  });
  window.addEventListener('resize', () => {
    if (window.innerWidth > 1100) closeNav();
  });

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

  const adminLogoutModal = qs('[data-admin-logout-modal]');
  const closeAdminLogout = () => {
    if (!adminLogoutModal) return;
    if (typeof adminLogoutModal.close === 'function' && adminLogoutModal.open) adminLogoutModal.close();
    else adminLogoutModal.removeAttribute('open');
    document.body.classList.remove('modal-open');
    document.documentElement.classList.remove('modal-open');
  };
  qs('[data-admin-logout-open]')?.addEventListener('click', (event) => {
    event.preventDefault();
    if (!adminLogoutModal) return;
    if (!adminLogoutModal.open) {
      if (typeof adminLogoutModal.showModal === 'function') adminLogoutModal.showModal();
      else adminLogoutModal.setAttribute('open', '');
    }
    document.body.classList.add('modal-open');
    document.documentElement.classList.add('modal-open');
    qs('[data-admin-logout-cancel]', adminLogoutModal)?.focus({ preventScroll: true });
  });
  qs('[data-admin-logout-cancel]')?.addEventListener('click', closeAdminLogout);
  adminLogoutModal?.addEventListener('click', (event) => {
    if (event.target !== adminLogoutModal) return;
    closeAdminLogout();
  });
  adminLogoutModal?.addEventListener('cancel', (event) => {
    event.preventDefault();
    closeAdminLogout();
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

  const postForm = async (page, form, rootUrl = baseUrl) => {
    const fd = new FormData(form);
    if (!fd.get('csrf_token')) fd.append('csrf_token', csrf);
    const res = await fetch(`${rootUrl}${page}`, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
    let data;
    try {
      data = await res.json();
    } catch (_) {
      data = { ok: false, message: res.status === 419 ? 'Your form session expired. Refresh the page and try again.' : 'Something went wrong. Please try again.' };
    }
    if (res.status === 401 && data.login_url) {
      window.location.assign(data.login_url);
      return data;
    }
    if (!res.ok && !data.message) data.message = 'Something went wrong. Please try again.';
    return data;
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
  if (qs('[data-mini-cart]')) fetchCart();

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

  const storageGet = (key) => {
    try { return sessionStorage.getItem(key); } catch (_) { return null; }
  };
  const storageSet = (key, value) => {
    try { sessionStorage.setItem(key, value); } catch (_) { /* Storage can be unavailable in private contexts. */ }
  };
  const discountModal = qs('[data-discount-modal]');
  const discountReopen = qs('[data-discount-reopen]');
  const discountForm = qs('[data-discount-form]');
  const discountStatus = qs('[data-discount-status]');
  const discountSuccess = qs('[data-discount-success]');
  const discountSubmitted = storageGet('aa_discount_submitted') === '1';

  const openDiscount = (automatic = false) => {
    if (!discountModal || discountSubmitted) return;
    discountModal.hidden = false;
    document.body.classList.add('modal-open');
    if (automatic) storageSet('aa_discount_seen', '1');
    qs('input[name="email"]', discountModal)?.focus();
  };
  const closeDiscount = () => {
    if (!discountModal) return;
    discountModal.hidden = true;
    document.body.classList.remove('modal-open');
    if (storageGet('aa_discount_submitted') !== '1' && discountReopen) discountReopen.hidden = false;
  };

  if (discountModal) {
    if (!discountSubmitted && storageGet('aa_discount_seen') !== '1') {
      openDiscount(true);
    } else if (!discountSubmitted && discountReopen) {
      discountReopen.hidden = false;
    }
    qs('[data-discount-close]')?.addEventListener('click', closeDiscount);
    discountReopen?.addEventListener('click', () => openDiscount(false));
    discountModal.addEventListener('click', (event) => {
      if (event.target === discountModal) closeDiscount();
    });
  }

  discountForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    if (!discountForm.checkValidity()) {
      discountForm.reportValidity();
      return;
    }
    const button = qs('button[type="submit"]', discountForm);
    if (button) { button.disabled = true; button.textContent = 'Saving...'; }
    if (discountStatus) { discountStatus.textContent = ''; discountStatus.className = 'form-status'; }
    try {
      const data = await postForm('discount-lead', discountForm, apiBaseUrl);
      if (!data.ok) throw new Error(data.message || 'Could not save your details.');
      storageSet('aa_discount_submitted', '1');
      if (discountReopen) discountReopen.hidden = true;
      discountForm.hidden = true;
      if (discountSuccess) discountSuccess.hidden = false;
      const successMessage = qs('[data-discount-success-message]');
      if (successMessage) successMessage.textContent = data.message;
      setTimeout(closeDiscount, 2200);
    } catch (error) {
      if (discountStatus) { discountStatus.textContent = error.message; discountStatus.classList.add('is-error'); }
    } finally {
      if (button) { button.disabled = false; button.textContent = 'Get My 5% Discount'; }
    }
  });

  const contactForm = qs('[data-contact-form]');
  contactForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    if (!contactForm.checkValidity()) {
      contactForm.reportValidity();
      return;
    }
    const button = qs('button[type="submit"]', contactForm);
    const status = qs('[data-contact-status]', contactForm);
    if (button) { button.disabled = true; button.textContent = 'Sending...'; }
    if (status) { status.textContent = ''; status.className = 'form-status'; }
    try {
      const data = await postForm('contact-submit', contactForm, apiBaseUrl);
      if (!data.ok) throw new Error(data.message || 'Could not send your message.');
      if (status) { status.textContent = data.message; status.classList.add('is-success'); }
      contactForm.reset();
    } catch (error) {
      if (status) { status.textContent = error.message; status.classList.add('is-error'); }
    } finally {
      if (button) { button.disabled = false; button.textContent = 'Send Message'; }
    }
  });

  const couponForm = qs('[data-coupon-form]');
  couponForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    if (!couponForm.checkValidity()) { couponForm.reportValidity(); return; }
    const button = qs('button[type="submit"]', couponForm);
    const status = qs('[data-coupon-status]');
    if (button) { button.disabled = true; button.textContent = 'Applying...'; }
    try {
      const data = await postForm('cart-coupon', couponForm);
      if (!data.ok) throw new Error(data.message || 'Coupon could not be applied.');
      if (status) { status.textContent = data.message; status.className = 'form-status is-success'; }
      window.location.reload();
    } catch (error) {
      if (status) { status.textContent = error.message; status.className = 'form-status is-error'; }
    } finally {
      if (button) { button.disabled = false; button.textContent = 'Apply'; }
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    if (discountModal && !discountModal.hidden) closeDiscount();
    if (adminLogoutModal?.open) closeAdminLogout();
  });
  function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[char]));
  }
})();

