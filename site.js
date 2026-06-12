/* Fossa-IT — shared site behaviour */
(function () {
  'use strict';

  /* ---------- nav dropdown ---------- */
  document.querySelectorAll('.nav-drop').forEach(function (drop) {
    var btn = drop.querySelector('.nav-drop-btn');
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      drop.classList.toggle('open');
      btn.setAttribute('aria-expanded', drop.classList.contains('open'));
    });
  });
  document.addEventListener('click', function (e) {
    document.querySelectorAll('.nav-drop.open').forEach(function (d) {
      if (!d.contains(e.target)) d.classList.remove('open');
    });
  });

  /* ---------- mobile burger ---------- */
  var burger = document.querySelector('.nav-burger');
  if (burger) {
    burger.addEventListener('click', function () {
      var links = document.querySelector('.nav-links');
      links.classList.toggle('open');
      burger.setAttribute('aria-expanded', links.classList.contains('open'));
    });
  }

  /* ---------- FAQ accordion ---------- */
  document.querySelectorAll('.faq-item').forEach(function (item) {
    var q = item.querySelector('.faq-q');
    var a = item.querySelector('.faq-a');
    q.addEventListener('click', function () {
      var open = item.classList.contains('open');
      // close siblings
      item.parentElement.querySelectorAll('.faq-item.open').forEach(function (o) {
        o.classList.remove('open');
        o.querySelector('.faq-a').style.maxHeight = '0px';
        o.querySelector('.faq-q').setAttribute('aria-expanded', 'false');
      });
      if (!open) {
        item.classList.add('open');
        a.style.maxHeight = a.scrollHeight + 'px';
        q.setAttribute('aria-expanded', 'true');
      }
    });
  });

  /* ---------- scroll reveal ---------- */
  document.documentElement.classList.add('js');
  var rvEls = Array.prototype.slice.call(document.querySelectorAll('.rv'));
  function rvCheck(instant) {
    if (!rvEls.length) return;
    var vh = window.innerHeight || document.documentElement.clientHeight;
    var shown = [];
    rvEls = rvEls.filter(function (el) {
      var r = el.getBoundingClientRect();
      if (r.top < vh - 30 && r.bottom > -10) { shown.push(el); return false; }
      return true;
    });
    shown.forEach(function (el) {
      if (instant === true) el.style.transition = 'none'; // show immediately, no transition on first paint
      el.classList.add('in');
    });
    if (instant === true && shown.length) {
      setTimeout(function () {
        shown.forEach(function (el) { el.style.transition = ''; });
      }, 60);
    }
  }
  rvCheck(true); // reveal everything already in the viewport immediately
  document.addEventListener('scroll', rvCheck, { passive: true, capture: true });
  window.addEventListener('resize', rvCheck, { passive: true });
  window.addEventListener('load', rvCheck);
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    rvEls.forEach(function (el) { io.observe(el); });
  }

  /* ---------- analytics goal stub (Yandex.Metrika placeholder) ----------
     Replace METRIKA_ID and uncomment the loader snippet before launch.   */
  window.fossaGoal = function (goal) {
    if (window.ym && window.METRIKA_ID) window.ym(window.METRIKA_ID, 'reachGoal', goal);
    // console.info('[goal]', goal);
  };
  document.addEventListener('click', function (e) {
    var a = e.target.closest('a');
    if (!a) return;
    var href = a.getAttribute('href') || '';
    if (href.indexOf('tel:') === 0) window.fossaGoal('phone_click');
    if (href.indexOf('t.me') !== -1) window.fossaGoal('telegram_click');
  });

  /* ---------- forms ---------- */
  document.querySelectorAll('form.form').forEach(function (form) {
    form.setAttribute('novalidate', '');
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var valid = true;
      form.querySelectorAll('.field').forEach(function (f) {
        var input = f.querySelector('input,textarea,select');
        if (!input) return;
        var bad = input.hasAttribute('required') && !input.value.trim();
        if (!bad && input.type === 'email' && input.value.trim()) {
          bad = !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(input.value.trim());
        }
        f.classList.toggle('invalid', bad);
        if (bad) valid = false;
      });
      if (!valid) {
        var firstBad = form.querySelector('.field.invalid input,.field.invalid textarea');
        if (firstBad) firstBad.focus();
        return;
      }
      form.classList.add('sent');
      window.fossaGoal(form.dataset.goal || 'form_submit');
    });
    form.querySelectorAll('input,textarea').forEach(function (inp) {
      inp.addEventListener('input', function () {
        inp.closest('.field').classList.remove('invalid');
      });
    });
  });

  /* ---------- RU / EN toggle ----------
     Elements carry data-en="english text"; RU original is cached in data-ru.
     Placeholders use data-en-ph. Preference persists in localStorage.     */
  function applyLang(lang) {
    document.documentElement.lang = lang;
    document.querySelectorAll('[data-en]').forEach(function (el) {
      if (!el.dataset.ru) el.dataset.ru = el.textContent;
      el.textContent = lang === 'en' ? el.dataset.en : el.dataset.ru;
    });
    document.querySelectorAll('[data-en-ph]').forEach(function (el) {
      if (!el.dataset.ruPh) el.dataset.ruPh = el.getAttribute('placeholder') || '';
      el.setAttribute('placeholder', lang === 'en' ? el.dataset.enPh : el.dataset.ruPh);
    });
    document.querySelectorAll('.lang-toggle').forEach(function (b) {
      b.textContent = lang === 'en' ? 'RU' : 'EN';
      b.setAttribute('aria-label', lang === 'en' ? 'Переключить на русский' : 'Switch to English');
    });
  }
  var saved = 'ru';
  try { saved = localStorage.getItem('fossa-lang') || 'ru'; } catch (e) {}
  if (saved === 'en') applyLang('en');
  document.querySelectorAll('.lang-toggle').forEach(function (b) {
    b.addEventListener('click', function () {
      var next = document.documentElement.lang === 'en' ? 'ru' : 'en';
      try { localStorage.setItem('fossa-lang', next); } catch (e) {}
      applyLang(next);
    });
  });

  /* ---------- footer year ---------- */
  document.querySelectorAll('[data-year]').forEach(function (el) {
    el.textContent = new Date().getFullYear();
  });
})();
