(function () {
  'use strict';
  var key = 'creacontable-theme';
  var saved = null;
  try { saved = localStorage.getItem(key) || localStorage.getItem('theme'); } catch (e) {}
  var theme = saved === 'dark' ? 'dark' : 'light';
  function applyTheme(next) {
    theme = next;
    document.body.classList.toggle('theme-dark', theme === 'dark');
    document.body.classList.toggle('theme-light', theme === 'light');
    document.documentElement.setAttribute('data-theme', theme);
    var button = document.getElementById('creacontable-theme-toggle');
    if (button) {
      button.innerHTML = theme === 'dark' ? '<span aria-hidden="true">☀️</span><span>Modo claro</span>' : '<span aria-hidden="true">🌙</span><span>Modo oscuro</span>';
      button.setAttribute('aria-label', theme === 'dark' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro');
      button.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
    }
    try { localStorage.setItem(key, theme); localStorage.setItem('theme', theme); } catch (e) {}
  }
  function init() {
    applyTheme(theme);
    if (!document.getElementById('creacontable-theme-toggle')) {
      var button = document.createElement('button');
      button.type = 'button'; button.id = 'creacontable-theme-toggle'; button.className = 'theme-toggle';
      button.addEventListener('click', function () { applyTheme(theme === 'dark' ? 'light' : 'dark'); });
      document.body.appendChild(button);
      applyTheme(theme);
    }
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
