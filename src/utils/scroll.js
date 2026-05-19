export function scrollToSection(sectionId) {
  const id = sectionId.replace(/^#/, "");
  const el = document.getElementById(id);
  if (el) {
    el.scrollIntoView({ behavior: "smooth", block: "start" });
    return true;
  }
  return false;
}

/** Réessaie le scroll jusqu'à ce que la section soit dans le DOM (après navigation). */
export function scrollToSectionWhenReady(sectionId, maxAttempts = 20) {
  let attempts = 0;
  const tick = () => {
    if (scrollToSection(sectionId) || attempts >= maxAttempts) return;
    attempts += 1;
    requestAnimationFrame(tick);
  };
  tick();
}
