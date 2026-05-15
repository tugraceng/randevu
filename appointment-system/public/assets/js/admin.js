document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('adminSidebar');
  const overlay = document.getElementById('sidebarOverlay');
  document.getElementById('sidebarOpen')?.addEventListener('click', () => {
    sidebar?.classList.add('open');
    overlay?.classList.add('show');
  });
  const close = () => { sidebar?.classList.remove('open'); overlay?.classList.remove('show'); };
  document.getElementById('sidebarClose')?.addEventListener('click', close);
  overlay?.addEventListener('click', close);

  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
      if (!confirm(el.dataset.confirm || 'Emin misiniz?')) e.preventDefault();
    });
  });

  document.querySelectorAll('[data-insert-var]').forEach(btn => {
    btn.addEventListener('click', () => {
      const ta = document.querySelector(btn.dataset.target);
      if (ta) {
        const v = '{' + btn.dataset.insertVar + '}';
        ta.value = (ta.value || '') + v;
      }
    });
  });

  if (document.getElementById('dashboardCharts')) {
    loadDashboardCharts();
  }

  const customerSelect = document.getElementById('customer_id');
  if (customerSelect?.dataset.packagesUrl) {
    customerSelect.addEventListener('change', async () => {
      const pkg = document.getElementById('customer_package_id');
      if (!pkg) return;
      const res = await fetch(customerSelect.dataset.packagesUrl + '&customer_id=' + customerSelect.value);
      const data = await res.json();
      pkg.innerHTML = '<option value="">Paket kullanma</option>';
      (data.packages || []).forEach(p => {
        const o = document.createElement('option');
        o.value = p.id;
        o.textContent = `${p.package_name} (${p.remaining_sessions} seans)`;
        o.dataset.serviceId = p.service_id;
        pkg.appendChild(o);
      });
    });
  }

  const serviceSelect = document.getElementById('service_id');
  const staffSelect = document.getElementById('staff_id');
  const dateInput = document.getElementById('appointment_date');
  const slotsContainer = document.getElementById('slots-container');
  async function loadAdminSlots() {
    if (!serviceSelect?.value || !dateInput?.value || !slotsContainer) return;
    const params = new URLSearchParams({
      service_id: serviceSelect.value,
      date: dateInput.value,
      staff_id: staffSelect?.value || '',
    });
    const res = await fetch(`${window.APP_BASE}/api/slots?${params}`);
    const data = await res.json();
    slotsContainer.innerHTML = '';
    (data.slots || []).forEach(slot => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'btn btn-outline-primary btn-sm m-1';
      btn.textContent = slot.start;
      btn.onclick = () => {
        document.getElementById('start_time').value = slot.start;
        slotsContainer.querySelectorAll('.btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
      };
      slotsContainer.appendChild(btn);
    });
  }
  serviceSelect?.addEventListener('change', loadAdminSlots);
  staffSelect?.addEventListener('change', loadAdminSlots);
  dateInput?.addEventListener('change', loadAdminSlots);
});

async function loadDashboardCharts() {
  try {
    const res = await fetch(window.ADMIN_BASE + '?route=api/charts');
    const data = await res.json();
    renderChart('chartDaily', 'line', data.daily?.map(r => r.d) || [], data.daily?.map(r => r.cnt) || [], 'Günlük Randevu');
    renderChart('chartService', 'doughnut', data.by_service?.map(r => r.label) || [], data.by_service?.map(r => r.cnt) || []);
    renderChart('chartStaff', 'bar', data.by_staff?.map(r => r.label) || [], data.by_staff?.map(r => r.cnt) || []);
    renderChart('chartPayment', 'pie', data.payments?.map(r => r.label) || [], data.payments?.map(r => r.cnt) || []);
    renderChart('chartPackage', 'bar', data.packages?.map(r => r.label) || [], data.packages?.map(r => r.cnt) || [], 'Paket Satış');
  } catch (e) { console.warn('Charts', e); }
}

function renderChart(id, type, labels, values, label = '') {
  const el = document.getElementById(id);
  if (!el || !window.Chart) return;
  new Chart(el, {
    type,
    data: {
      labels,
      datasets: [{ label: label || id, data: values, backgroundColor: ['#4f46e5','#0ea5e9','#10b981','#f59e0b','#ef4444','#8b5cf6'] }],
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
  });
}
