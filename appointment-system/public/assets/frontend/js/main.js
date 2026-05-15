document.addEventListener('DOMContentLoaded', () => {
  const nav = document.getElementById('mainNav');
  if (nav) {
    window.addEventListener('scroll', () => {
      nav.classList.toggle('scrolled', window.scrollY > 40);
    });
  }

  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const id = a.getAttribute('href');
      if (id && id.length > 1) {
        const el = document.querySelector(id);
        if (el) {
          e.preventDefault();
          el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }
    });
  });

  const mobileCta = document.getElementById('mobile-cta-book');
  if (mobileCta) {
    mobileCta.addEventListener('click', () => {
      const modal = document.getElementById('appointmentModal');
      if (modal) bootstrap.Modal.getOrCreateInstance(modal).show();
    });
  }
});
