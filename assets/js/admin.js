(() => {
  const addButtons = document.querySelectorAll("[data-add-row]");

  addButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const target = button.getAttribute("data-target");
      if (!target) return;

      const list = document.querySelector(`[data-repeat="${target}"]`);
      const template = document.querySelector(`[data-template="${target}"]`);
      if (!list || !template) return;

      const index = list.querySelectorAll("[data-repeat-item]").length;
      const html = template.innerHTML.replaceAll("__INDEX__", String(index));
      const wrap = document.createElement("div");
      wrap.innerHTML = html.trim();
      const item = wrap.firstElementChild;
      if (item) list.appendChild(item);
    });
  });

  document.addEventListener("click", (event) => {
    const target = event.target;
    if (!(target instanceof Element)) return;
    const removeBtn = target.closest("[data-remove-row]");
    if (!removeBtn) return;
    const item = removeBtn.closest("[data-repeat-item]");
    if (item) item.remove();
  });
})();
