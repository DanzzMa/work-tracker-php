// Dark mode toggle & real-time timer helpers
document.addEventListener('DOMContentLoaded', () => {
  const themeBtn = document.querySelector('[data-theme-toggle]');
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const initial = localStorage.getItem('theme') || (prefersDark ? 'dark' : 'light');
  setTheme(initial);

  if (themeBtn) {
    themeBtn.addEventListener('click', () => {
      const current = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
      setTheme(current);
    });
  }

  function setTheme(mode) {
    document.documentElement.dataset.theme = mode;
    localStorage.setItem('theme', mode);
  }

  // Live timer
  const timerEl = document.getElementById('live-timer');
  if (timerEl && timerEl.dataset.start) {
    const start = new Date(timerEl.dataset.start);
    const tick = () => {
      const now = new Date();
      const diffMs = now - start;
      const mins = Math.floor(diffMs / 60000);
      const hrs = Math.floor(mins / 60);
      const rem = mins % 60;
      timerEl.textContent = `${String(hrs).padStart(2,'0')}:${String(rem).padStart(2,'0')}`;
    };
    tick();
    setInterval(tick, 1000 * 30);
  }
});
