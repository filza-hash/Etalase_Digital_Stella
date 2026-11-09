/* app.js - interaksi & animasi Etalase Digital Stella (tanpa library) */
(function () {
  'use strict';
  window.__stella = true;

  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

  /* ---------- Navbar: bayangan saat scroll + menu mobile ---------- */
  var nav = $('#nav');
  if (nav) {
    var onScroll = function () { nav.classList.toggle('is-scrolled', window.scrollY > 6); };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

    var toggle = $('.nav-toggle', nav), links = $('#nav-links');
    if (toggle && links) {
      var setMenu = function (open) {
        links.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        toggle.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
      };
      toggle.addEventListener('click', function () { setMenu(!links.classList.contains('is-open')); });
      document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setMenu(false); });
      document.addEventListener('click', function (e) { if (!nav.contains(e.target)) setMenu(false); });
    }
  }

  /* ---------- Toast: tombol tutup ---------- */
  $$('.toast').forEach(function (t) {
    var close = function () {
      t.classList.add('is-closing');
      setTimeout(function () { t.remove(); }, 320);
    };
    var b = $('.toast-close', t);
    if (b) b.addEventListener('click', close);
    setTimeout(function () { if (t.parentNode) t.remove(); }, 5800);
  });

  /* ---------- Hero: cahaya mengikuti kursor ---------- */
  var hero = $('#hero');
  if (hero && window.matchMedia('(hover:hover)').matches) {
    hero.addEventListener('pointermove', function (e) {
      var r = hero.getBoundingClientRect();
      hero.style.setProperty('--mx', (e.clientX - r.left) + 'px');
      hero.style.setProperty('--my', (e.clientY - r.top) + 'px');
    });
  }

  /* ---------- Gambar kartu: hilangkan shimmer setelah termuat ---------- */
  $$('.thumb').forEach(function (th) {
    var img = $('img', th);
    if (!img) return;
    var done = function () { th.classList.add('is-loaded'); };
    if (img.complete) done();
    else { img.addEventListener('load', done); img.addEventListener('error', done); }
  });

  /* ---------- Kartu muncul bertahap ---------- */
  var cards = $$('.reveal');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      var k = 0;
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        en.target.style.setProperty('--d', (k++ * 70) + 'ms');
        en.target.classList.add('is-in');
        io.unobserve(en.target);
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -30px 0px' });
    cards.forEach(function (c) { io.observe(c); });
  } else {
    cards.forEach(function (c) { c.classList.add('is-in'); });
  }

  /* ---------- Filter instan saat mengetik ---------- */
  var input = $('[data-live-filter]');
  if (input && $('.project')) {
    var count = $('#count'), none = $('#no-result'), unggul = $('#unggulan');
    var total = count ? parseInt(count.dataset.total || '0', 10) : 0;
    var original = count ? count.textContent : '';
    var timer;
    input.addEventListener('input', function () {
      clearTimeout(timer);
      timer = setTimeout(function () {
        var q = input.value.trim().toLowerCase(), shown = 0, shownUnggul = 0;
        $$('.project').forEach(function (c) {
          var hit = !q || (c.dataset.search || '').indexOf(q) !== -1;
          c.classList.toggle('is-hidden', !hit);
          if (hit) { shown++; if (unggul && unggul.contains(c)) shownUnggul++; }
        });
        if (unggul) unggul.style.display = shownUnggul ? '' : 'none';
        if (count) count.textContent = q ? shown + ' karya ditemukan' : original;
        if (none) none.classList.toggle('is-show', total > 0 && shown === 0);
      }, 120);
    });
  }

  /* ---------- Dialog konfirmasi hapus ---------- */
  var confirmDlg;
  function buildConfirm() {
    confirmDlg = document.createElement('dialog');
    confirmDlg.setAttribute('aria-labelledby', 'cf-title');
    confirmDlg.innerHTML =
      '<div class="dlg">' +
      '<div class="dlg-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14M10 11v6M14 11v6"/></svg></div>' +
      '<h3 id="cf-title"></h3><p id="cf-text"></p>' +
      '<div class="dlg-actions"><button type="button" class="btn" data-cancel autofocus>Batal</button>' +
      '<button type="button" class="btn btn-solid-danger" data-yes></button></div></div>';
    document.body.appendChild(confirmDlg);
    confirmDlg.addEventListener('click', function (e) { if (e.target === confirmDlg) confirmDlg.close(); });
    $('[data-cancel]', confirmDlg).addEventListener('click', function () { confirmDlg.close(); });
  }
  function askConfirm(form) {
    if (typeof HTMLDialogElement === 'undefined') return window.confirm(form.dataset.confirm);
    if (!confirmDlg) buildConfirm();
    $('#cf-title', confirmDlg).textContent = form.dataset.confirmTitle || 'Yakin ingin menghapus?';
    $('#cf-text', confirmDlg).textContent = form.dataset.confirm || '';
    var yes = $('[data-yes]', confirmDlg);
    yes.textContent = form.dataset.confirmYes || 'Ya, hapus';
    yes.onclick = function () {
      confirmDlg.close();
      var btn = form.querySelector('button');
      if (btn) btn.classList.add('is-loading');
      form.submit();               // submit() tidak memicu onsubmit bawaan
    };
    confirmDlg.showModal();
    return null;
  }
  // fase capture: berjalan sebelum onsubmit="return confirm()" bawaan
  document.addEventListener('submit', function (e) {
    var f = e.target;
    if (f && f.matches && f.matches('form[data-confirm]') && typeof HTMLDialogElement !== 'undefined') {
      e.preventDefault();
      e.stopImmediatePropagation();
      askConfirm(f);
    }
  }, true);

  /* ---------- Lightbox gambar ---------- */
  var lb;
  $$('[data-lightbox]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (typeof HTMLDialogElement === 'undefined') return;
      if (!lb) {
        lb = document.createElement('dialog');
        lb.setAttribute('aria-label', 'Pratinjau gambar');
        lb.innerHTML = '<div class="lightbox"><button type="button" class="lb-close" aria-label="Tutup">&times;</button><img alt=""><div class="lb-cap"></div></div>';
        document.body.appendChild(lb);
        lb.addEventListener('click', function (e) { if (e.target === lb || e.target.closest('.lb-close')) lb.close(); });
      }
      var img = $('img', lb);
      img.src = btn.dataset.lightbox;
      img.alt = btn.dataset.caption || '';
      $('.lb-cap', lb).textContent = btn.dataset.caption || '';
      lb.showModal();
    });
  });

  /* ---------- Tampilkan / sembunyikan password ---------- */
  var eyeOn = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>';
  var eyeOff = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3l18 18M10.6 5.1A10 10 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4.1M6.5 6.6A17 17 0 0 0 2 12s3.6 7 10 7a9.7 9.7 0 0 0 4-.9M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>';
  $$('.pw-toggle').forEach(function (b) {
    b.addEventListener('click', function () {
      var inp = $('input', b.parentNode), show = inp.type === 'password';
      inp.type = show ? 'text' : 'password';
      b.innerHTML = show ? eyeOff : eyeOn;
      b.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
    });
  });

  /* ---------- Dropzone: pratinjau gambar ---------- */
  $$('[data-dropzone]').forEach(function (dz) {
    var file = $('input[type=file]', dz), prev = $('.dz-preview', dz), name;
    ['dragenter', 'dragover'].forEach(function (ev) {
      dz.addEventListener(ev, function () { dz.classList.add('is-over'); });
    });
    ['dragleave', 'drop'].forEach(function (ev) {
      dz.addEventListener(ev, function () { dz.classList.remove('is-over'); });
    });
    file.addEventListener('change', function () {
      var f = file.files && file.files[0];
      if (!f || f.type.indexOf('image/') !== 0) { dz.classList.remove('has-file'); return; }
      if (prev.dataset.url) URL.revokeObjectURL(prev.dataset.url);
      var u = URL.createObjectURL(f);
      prev.dataset.url = u;
      prev.src = u;
      dz.classList.add('has-file');
      if (!name) { name = document.createElement('div'); name.className = 'dz-name'; dz.parentNode.insertBefore(name, dz.nextSibling); }
      name.textContent = f.name;
    });
  });

  /* ---------- Tombol: status loading saat submit ---------- */
  document.addEventListener('submit', function (e) {
    var f = e.target;
    if (e.defaultPrevented || !f || f.getAttribute('role') === 'search') return;
    var b = e.submitter || f.querySelector('button:not([type=button])');
    if (!b) return;
    b.classList.add('is-loading');
    setTimeout(function () { b.disabled = true; }, 0);
  });
  /* ---------- Apresiasi (hati) tanpa memuat ulang halaman ---------- */
  document.addEventListener('submit', function (e) {
    var f = e.target;
    if (!f || !f.matches || !f.matches('form[data-like]') || !window.fetch) return;
    e.preventDefault();
    var btn = $('.like', f);
    fetch(f.action, {
      method: 'POST',
      body: new FormData(f),
      headers: { 'X-Requested-With': 'fetch', 'Accept': 'application/json' },
      credentials: 'same-origin'
    }).then(function (r) {
      if (!r.ok) throw new Error('gagal');
      return r.json();
    }).then(function (d) {
      var id = f.dataset.project;
      $$('form[data-like][data-project="' + id + '"] .like').forEach(function (b) {
        b.classList.toggle('is-on', d.liked);
        b.setAttribute('aria-pressed', d.liked ? 'true' : 'false');
      });
      $$('[data-like-total="' + id + '"], [data-like-count="' + id + '"]').forEach(function (n) { n.textContent = d.count; });
      if (btn && d.liked) {
        btn.classList.remove('pop'); void btn.offsetWidth; btn.classList.add('pop');
      }
    }).catch(function () { f.submit(); });   // cadangan: kirim form biasa
  }, true);

  /* ---------- Angka statistik menghitung naik ---------- */
  var counters = $$('[data-count]');
  if (counters.length && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    counters.forEach(function (el) {
      var target = parseInt(el.dataset.count || '0', 10);
      if (!target) return;
      var t0 = null, dur = 900;
      el.textContent = '0';
      function tick(t) {
        if (t0 === null) t0 = t;
        var p = Math.min((t - t0) / dur, 1), ease = 1 - Math.pow(1 - p, 3);
        el.textContent = Math.round(target * ease);
        if (p < 1) requestAnimationFrame(tick);
      }
      requestAnimationFrame(tick);
    });
  }

  /* ---------- Penghitung karakter (bio) ---------- */
  $$('textarea[data-maxlen]').forEach(function (ta) {
    var out = $('[data-counter-for="' + ta.id + '"] span');
    if (!out) return;
    var upd = function () { out.textContent = ta.value.length; };
    ta.addEventListener('input', upd);
    upd();
  });

  window.addEventListener('pageshow', function (e) {
    if (e.persisted) $$('.btn.is-loading').forEach(function (b) { b.classList.remove('is-loading'); b.disabled = false; });
  });
})();
