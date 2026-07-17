(() => {
  const header = document.querySelector("[data-header]");
  const toggle = document.querySelector("[data-nav-toggle]");
  const nav = document.querySelector("[data-nav]");

  const onScroll = () => {
    if (!header) return;
    header.classList.toggle("is-scrolled", window.scrollY > 12);
  };

  window.addEventListener("scroll", onScroll, { passive: true });
  onScroll();

  if (toggle && nav) {
    toggle.addEventListener("click", (event) => {
      event.stopPropagation();
      const open = toggle.getAttribute("aria-expanded") === "true";
      toggle.setAttribute("aria-expanded", String(!open));
      nav.classList.toggle("is-open", !open);
    });

    nav.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", () => {
        toggle.setAttribute("aria-expanded", "false");
        nav.classList.remove("is-open");
      });
    });
  }

  const userMenu = document.querySelector("[data-user-menu]");
  const userMenuToggle = document.querySelector("[data-user-menu-toggle]");

  const setUserMenuOpen = (open) => {
    if (!userMenu || !userMenuToggle) return;
    userMenu.classList.toggle("is-open", open);
    userMenuToggle.setAttribute("aria-expanded", String(open));
  };

  if (userMenu && userMenuToggle) {
    userMenuToggle.addEventListener("click", (event) => {
      event.preventDefault();
      event.stopPropagation();
      const open = !userMenu.classList.contains("is-open");
      setUserMenuOpen(open);
    });

    userMenu.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", () => setUserMenuOpen(false));
    });

    document.addEventListener("click", (event) => {
      if (!userMenu.contains(event.target)) {
        setUserMenuOpen(false);
      }
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        setUserMenuOpen(false);
      }
    });
  }

  const reveals = document.querySelectorAll("[data-reveal]");
  if (!("IntersectionObserver" in window) || reveals.length === 0) {
    reveals.forEach((el) => el.classList.add("is-visible"));
  } else {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.16, rootMargin: "0px 0px -40px 0px" }
    );

    reveals.forEach((el) => observer.observe(el));
  }

  const iconEye =
    '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 12s3.5-7 9.5-7 9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7Z"/><circle cx="12" cy="12" r="3"/></svg>';
  const iconEyeOff =
    '<svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18"/><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.4 10.4 0 0 1 12 5c6 0 9.5 7 9.5 7a16.7 16.7 0 0 1-3.2 3.9"/><path d="M6.1 6.1A16.4 16.4 0 0 0 2.5 12S6 19 12 19a10.2 10.2 0 0 0 4.2-.9"/></svg>';

  document.querySelectorAll('input[type="password"]').forEach((input) => {
    if (input.closest(".password-field")) return;

    const wrap = document.createElement("div");
    wrap.className = "password-field";
    input.parentNode.insertBefore(wrap, input);
    wrap.appendChild(input);

    const button = document.createElement("button");
    button.type = "button";
    button.className = "password-toggle";
    button.setAttribute("aria-label", "Afficher le mot de passe");
    button.setAttribute("aria-pressed", "false");
    button.innerHTML = iconEye;
    wrap.appendChild(button);

    button.addEventListener("click", () => {
      const show = input.type === "password";
      input.type = show ? "text" : "password";
      button.setAttribute("aria-pressed", String(show));
      button.setAttribute(
        "aria-label",
        show ? "Masquer le mot de passe" : "Afficher le mot de passe"
      );
      button.innerHTML = show ? iconEyeOff : iconEye;
      input.focus();
    });
  });

  const avatarInput = document.querySelector("[data-avatar-input]");
  const avatarPreview = document.querySelector("[data-avatar-preview]");
  if (avatarInput && avatarPreview) {
    avatarInput.addEventListener("change", () => {
      const file = avatarInput.files && avatarInput.files[0];
      if (!file) return;

      const url = URL.createObjectURL(file);
      avatarPreview.innerHTML = `<img src="${url}" alt="Aperçu">`;
      avatarInput.closest("form")?.requestSubmit();
    });
  }
})();
