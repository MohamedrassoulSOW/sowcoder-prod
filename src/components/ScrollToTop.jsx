import { useState, useEffect } from "react";
import { ArrowUp } from "lucide-react";

export default function ScrollToTop() {
  const [visible, setVisible] = useState(false);

  useEffect(() => {
    const onScroll = () => setVisible(window.scrollY > 400);
    window.addEventListener("scroll", onScroll);
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  if (!visible) return null;

  return (
    <button
      type="button"
      onClick={() => window.scrollTo({ top: 0, behavior: "smooth" })}
      className="fixed bottom-24 left-6 z-40 flex h-11 w-11 items-center justify-center rounded-full border border-white/10 bg-slate-900/90 text-slate-300 backdrop-blur transition hover:border-cyan-500/30 hover:text-cyan-400"
      aria-label="Retour en haut"
    >
      <ArrowUp className="h-5 w-5" />
    </button>
  );
}
