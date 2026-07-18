// TechAssist — small vanilla enhancements (no libraries). The app works without JS.
(function () {
  'use strict';

  // Auto-dismiss flash messages after a few seconds.
  document.querySelectorAll('.flash').forEach(function (el) {
    setTimeout(function () { el.style.transition = 'opacity .4s'; el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 400); }, 5000);
  });

  // Star-rating widget: clicking a star sets the hidden input + fills stars.
  document.querySelectorAll('[data-stars]').forEach(function (widget) {
    var input = widget.parentElement.querySelector('input[name="rating"]');
    var stars = widget.querySelectorAll('button');
    function paint(n) {
      stars.forEach(function (s, i) { s.classList.toggle('on', i < n); });
    }
    stars.forEach(function (s, i) {
      s.addEventListener('click', function () { if (input) input.value = i + 1; paint(i + 1); });
    });
    paint(input ? parseInt(input.value || '0', 10) : 0);
  });

  // Confirm dialogs for destructive actions.
  document.querySelectorAll('[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!window.confirm(form.getAttribute('data-confirm'))) e.preventDefault();
    });
  });

  // Register the service worker (PWA) when served over http(s).
  if ('serviceWorker' in navigator && location.protocol.startsWith('http')) {
    window.addEventListener('load', function () {
      var base = (window.__BASE__ || '');
      navigator.serviceWorker.register(base + '/sw.js').catch(function () {});
    });
  }
})();
