import { Quote } from "lucide-react";
import { useSiteContent } from "../context/ContentContext";
import ContentImage from "./ContentImage";

export default function Testimonials() {
  const { content } = useSiteContent();
  const { testimonials } = content;

  return (
    <section className="relative py-24">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="mx-auto max-w-2xl text-center">
          <span className="text-sm font-semibold uppercase tracking-widest text-cyan-400">
            TÉMOIGNAGES
          </span>
          <h2 className="mt-3 text-3xl font-bold sm:text-4xl">
            Ce que disent nos partenaires
          </h2>
          <p className="mt-4 text-slate-400">
            Découvrez les retours d&apos;expérience de nos clients satisfaits.
          </p>
        </div>

        <div className="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {testimonials.map((t) => (
            <blockquote
              key={t.name}
              className="flex flex-col rounded-2xl border border-white/5 bg-slate-900/40 p-6 transition hover:border-cyan-500/20"
            >
              <Quote className="h-8 w-8 text-cyan-500/30" />
              <p className="mt-4 flex-1 text-sm leading-relaxed text-slate-300">
                &ldquo;{t.text}&rdquo;
              </p>
              <footer className="mt-6 flex items-center gap-3 border-t border-white/5 pt-4">
                {t.image ? (
                  <ContentImage
                    src={t.image}
                    alt={t.name}
                    className="h-10 w-10 shrink-0 rounded-full object-cover"
                  />
                ) : (
                  <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-cyan-500 to-violet-500 text-sm font-bold text-slate-950">
                    {t.initial}
                  </span>
                )}
                <div>
                  <cite className="not-italic font-semibold text-white">
                    {t.name}
                  </cite>
                  <p className="text-xs text-slate-500">{t.role}</p>
                </div>
              </footer>
            </blockquote>
          ))}
        </div>
      </div>
    </section>
  );
}
