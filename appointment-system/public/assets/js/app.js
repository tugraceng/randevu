document.addEventListener('DOMContentLoaded', () => {
  const serviceSelect = document.getElementById('service_id');
  const staffSelect = document.getElementById('staff_id');
  const dateInput = document.getElementById('appointment_date');
  const slotsContainer = document.getElementById('slots-container');

  async function loadSlots() {
    if (!serviceSelect?.value || !dateInput?.value || !slotsContainer) return;
    const params = new URLSearchParams({
      service_id: serviceSelect.value,
      date: dateInput.value,
      staff_id: staffSelect?.value || '',
    });
    const res = await fetch(`${window.APP_BASE}/api/slots?${params}`);
    const data = await res.json();
    slotsContainer.innerHTML = '';
    if (!data.slots?.length) {
      slotsContainer.innerHTML = '<p class="text-muted">Uygun saat yok.</p>';
      return;
    }
    data.slots.forEach(slot => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'btn btn-outline-primary btn-sm m-1 slot-btn';
      btn.textContent = slot.start;
      btn.dataset.time = slot.start;
      btn.addEventListener('click', () => {
        document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('start_time').value = slot.start;
      });
      slotsContainer.appendChild(btn);
    });
  }

  serviceSelect?.addEventListener('change', loadSlots);
  staffSelect?.addEventListener('change', loadSlots);
  dateInput?.addEventListener('change', loadSlots);

  document.querySelectorAll('.service-select-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.service-select-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
      const input = document.getElementById('service_id');
      if (input) input.value = card.dataset.id;
    });
  });

  /* Premium UX additions for customer panel */
  const main = document.querySelector('.customer-content');
  if (main) main.classList.add('fade-up');

  const liftTargets = document.querySelectorAll('.c-card, .c-stat, .c-card-lg, .service-select-card, .loyalty-card');
  liftTargets.forEach(el => { if (!el.classList.contains('hover-lift')) el.classList.add('hover-lift'); });

  if ('IntersectionObserver' in window) {
    const items = document.querySelectorAll('.c-card, .c-stat, .c-card-lg');
    const io = new IntersectionObserver((entries) => {
      entries.forEach((e, i) => {
        if (!e.isIntersecting) return;
        e.target.style.transitionDelay = (Math.min(i * 30, 200)) + 'ms';
        e.target.classList.add('visible');
        io.unobserve(e.target);
      });
    }, { threshold: 0.08 });
    items.forEach(el => {
      el.setAttribute('data-reveal', '');
      io.observe(el);
    });
  }

  document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', () => {
      const btn = form.querySelector('button[type="submit"]');
      if (btn) btn.setAttribute('data-loading', 'true');
    });
  });
});
