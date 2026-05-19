import { X } from "lucide-react";
import { useApp } from "../context/AppContext";
import { useSiteContent } from "../context/ContentContext";

export default function LegalModal() {
  const { legalPage, closeLegal, openContact } = useApp();
  const { content } = useSiteContent();

  if (!legalPage) return null;

  const page = content.legalPages[legalPage];
  if (!page) return null;

  return (
    <div
      className="fixed inset-0 z-[70] flex items-center justify-center p-4"
      role="dialog"
      aria-modal="true"
    >
      <button
        type="button"
        className="absolute inset-0 bg-slate-950/85 backdrop-blur-sm"
        onClick={closeLegal}
        aria-label="Fermer"
      />
      <div className="relative max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl border border-white/10 bg-slate-900 p-6 shadow-2xl sm:p-8">
        <button
          type="button"
          onClick={closeLegal}
          className="absolute right-4 top-4 rounded-lg p-1 text-slate-400 hover:bg-white/5"
        >
          <X className="h-5 w-5" />
        </button>

        <h2 className="pr-8 text-xl font-bold text-white">{page.title}</h2>
        <ul className="mt-6 space-y-3 text-sm leading-relaxed text-slate-400">
          {page.content.map((line) => (
            <li key={line.slice(0, 50)} className="flex gap-2">
              <span className="text-cyan-400">•</span>
              <span>{line}</span>
            </li>
          ))}
        </ul>
        <button
          type="button"
          onClick={() => {
            closeLegal();
            openContact({ subject: page.title });
          }}
          className="mt-8 text-sm font-medium text-cyan-400 hover:underline"
        >
          Nous contacter →
        </button>
      </div>
    </div>
  );
}
