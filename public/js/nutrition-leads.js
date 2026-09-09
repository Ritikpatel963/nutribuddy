document.addEventListener('DOMContentLoaded', () => {
  const selectAll = document.querySelector('[data-select-all]');
  selectAll?.addEventListener('change', () => document.querySelectorAll('[data-lead-checkbox]').forEach(box => { box.checked = selectAll.checked; }));

  document.querySelectorAll('[data-assign-url]').forEach(select => select.addEventListener('change', () => {
    if (!select.value) return;
    const form = document.createElement('form');
    form.method = 'POST'; form.action = select.dataset.assignUrl;
    form.innerHTML = `<input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}"><input type="hidden" name="_method" value="PATCH"><input type="hidden" name="assigned_to" value="${select.value}">`;
    document.body.appendChild(form); form.submit();
  }));
});
