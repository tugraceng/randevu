document.querySelectorAll('[data-template-var]').forEach(btn => {
  btn.addEventListener('click', () => {
    const ta = document.getElementById('template_body');
    if (ta) {
      const v = btn.dataset.templateVar;
      const start = ta.selectionStart;
      const end = ta.selectionEnd;
      ta.value = ta.value.substring(0, start) + v + ta.value.substring(end);
    }
  });
});
