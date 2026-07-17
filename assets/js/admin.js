(() => {
  const dropdown = document.querySelector("[data-admin-dropdown]");
  const toggle = document.querySelector("[data-admin-dropdown-toggle]");
  const menu = document.querySelector("[data-admin-dropdown-menu]");

  const closeDropdown = () => {
    if (!dropdown || !toggle || !menu) return;
    dropdown.classList.remove("is-open");
    toggle.setAttribute("aria-expanded", "false");
    menu.hidden = true;
  };

  const openDropdown = () => {
    if (!dropdown || !toggle || !menu) return;
    dropdown.classList.add("is-open");
    toggle.setAttribute("aria-expanded", "true");
    menu.hidden = false;
  };

  const unlockItem = (item) => {
    if (!(item instanceof HTMLElement)) return;
    item.classList.remove("is-locked");
    const editBtn = item.querySelector("[data-edit-row]");
    if (editBtn instanceof HTMLButtonElement) {
      editBtn.textContent = "Édition";
      editBtn.disabled = true;
    }
    const focusable = item.querySelector("input, textarea, select");
    if (focusable instanceof HTMLElement) focusable.focus();
  };

  if (dropdown && toggle && menu) {
    toggle.addEventListener("click", (event) => {
      event.stopPropagation();
      if (menu.hidden) {
        openDropdown();
      } else {
        closeDropdown();
      }
    });

    document.addEventListener("click", (event) => {
      if (!(event.target instanceof Element)) return;
      if (!dropdown.contains(event.target)) {
        closeDropdown();
      }
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") closeDropdown();
    });
  }

  document.querySelectorAll("[data-add-row]").forEach((button) => {
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
      if (!item) return;

      list.appendChild(item);
      unlockItem(item);
    });
  });

  document.addEventListener("click", (event) => {
    const target = event.target;
    if (!(target instanceof Element)) return;

    const editBtn = target.closest("[data-edit-row]");
    if (editBtn) {
      unlockItem(editBtn.closest("[data-editable-item]"));
      return;
    }

    const removeBtn = target.closest("[data-remove-row]");
    if (!removeBtn) return;
    const item = removeBtn.closest("[data-repeat-item]");
    if (item) item.remove();
  });
})();
