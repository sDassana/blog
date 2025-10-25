// Modern file input helper: pairs a hidden <input type="file"> with a styled <label>
// and a filename display span. Re-run on containers when adding dynamic content.

export function initModernFileInput(root = document) {
  const wrappers = root.querySelectorAll('.modern-file');
  wrappers.forEach((wrap) => {
    const input = wrap.querySelector('input[type="file"]');
    const nameEl = wrap.querySelector('.file-name');
    if (!input || !nameEl) return;
    if (input.dataset.bound === '1') return;
    input.dataset.bound = '1';

    const update = () => {
      const file = input.files && input.files[0];
      nameEl.textContent = file ? file.name : 'No file chosen';
    };
    input.addEventListener('change', update);
    update();
  });
}
