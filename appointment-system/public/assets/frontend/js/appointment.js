(function () {
  const form = document.getElementById('appointment-form');
  if (!form || !window.APP_BASE) return;

  let step = 1;
  const panels = form.querySelectorAll('.step-panel');
  const steps = form.querySelectorAll('.step-indicator .step');
  const btnPrev = document.getElementById('btn-prev');
  const btnNext = document.getElementById('btn-next');
  const btnSubmit = document.getElementById('btn-submit');
  const serviceSelect = document.getElementById('service_id');
  const staffSelect = document.getElementById('staff_id');
  const dateInput = document.getElementById('appointment_date');
  const slotsContainer = document.getElementById('slots-container');
  const startTimeInput = document.getElementById('start_time');

  function showStep(n) {
    step = n;
    panels.forEach(p => p.classList.toggle('d-none', parseInt(p.dataset.panel, 10) !== n));
    steps.forEach(s => s.classList.toggle('active', parseInt(s.dataset.step, 10) === n));
    btnPrev.disabled = n === 1;
    btnNext.classList.toggle('d-none', n === 4);
    btnSubmit.classList.toggle('d-none', n !== 4);
    if (n === 4) buildSummary();
  }

  function buildSummary() {
    const box = document.getElementById('appointment-summary');
    if (!box) return;
    const svc = serviceSelect?.selectedOptions[0]?.text || '';
    const stf = staffSelect?.selectedOptions[0]?.text || 'Farketmez';
    box.innerHTML = `<p><strong>Hizmet:</strong> ${svc}</p>
      <p><strong>Personel:</strong> ${stf}</p>
      <p><strong>Tarih:</strong> ${dateInput?.value || ''}</p>
      <p><strong>Saat:</strong> ${startTimeInput?.value || ''}</p>`;
  }

  async function loadSlots() {
    if (!serviceSelect?.value || !dateInput?.value || !slotsContainer) return;
    slotsContainer.innerHTML = '<span class="text-muted small">Yükleniyor...</span>';
    const params = new URLSearchParams({
      service_id: serviceSelect.value,
      date: dateInput.value,
      staff_id: staffSelect?.value || '',
    });
    try {
      const res = await fetch(`${window.APP_BASE}/api/slots?${params}`);
      const data = await res.json();
      slotsContainer.innerHTML = '';
      if (!data.slots?.length) {
        slotsContainer.innerHTML = '<p class="text-muted small mb-0">Uygun saat bulunamadı.</p>';
        return;
      }
      data.slots.forEach(slot => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-outline-primary btn-sm slot-btn';
        btn.textContent = slot.start;
        btn.addEventListener('click', () => {
          slotsContainer.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          if (startTimeInput) startTimeInput.value = slot.start;
        });
        slotsContainer.appendChild(btn);
      });
    } catch {
      slotsContainer.innerHTML = '<p class="text-danger small">Saatler yüklenemedi.</p>';
    }
  }

  btnPrev?.addEventListener('click', () => showStep(Math.max(1, step - 1)));
  btnNext?.addEventListener('click', () => {
    if (step === 1 && !serviceSelect?.value) { alert('Hizmet seçin'); return; }
    if (step === 3 && !startTimeInput?.value) { alert('Saat seçin'); return; }
    showStep(Math.min(4, step + 1));
  });
  serviceSelect?.addEventListener('change', loadSlots);
  staffSelect?.addEventListener('change', loadSlots);
  dateInput?.addEventListener('change', loadSlots);

  form.addEventListener('submit', async e => {
    e.preventDefault();
    const loading = document.getElementById('appointment-loading');
    form.classList.add('d-none');
    loading?.classList.remove('d-none');
    const fd = new FormData(form);
    try {
      const res = await fetch(`${window.APP_BASE}/api/appointment`, { method: 'POST', body: fd });
      const data = await res.json();
      if (data.redirect) window.location.href = data.redirect;
      else if (data.success) {
        alert(data.message || 'Randevunuz oluşturuldu!');
        bootstrap.Modal.getInstance(document.getElementById('appointmentModal'))?.hide();
        form.reset();
        showStep(1);
      } else alert(data.message || 'İşlem başarısız.');
    } catch {
      alert('Bağlantı hatası.');
    } finally {
      loading?.classList.add('d-none');
      form.classList.remove('d-none');
    }
  });
})();
