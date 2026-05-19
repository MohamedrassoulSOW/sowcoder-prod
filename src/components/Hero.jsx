import { ArrowRight, Sparkles } from "lucide-react";
import { useApp } from "../context/AppContext";
import { useSiteContent } from "../context/ContentContext";
import ContentImage from "./ContentImage";

export default function Hero() {
  const { openContact, navigateTo } = useApp();
  const { content } = useSiteContent();
  const { hero, stats } = content;

  return (
    <section
      id="accueil"
      className="relative min-h-screen overflow-hidden pt-24 hero-glow"
    >
      {hero.image ? (
        <ContentImage
          src={hero.image}
          alt=""
          className="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-20"
        />
      ) : null}
      <div className="pointer-events-none absolute inset-0 overflow-hidden">
        <div className="absolute -left-32 top-20 h-96 w-96 rounded-full bg-cyan-500/10 blur-3xl" />
        <div className="absolute -right-32 top-40 h-80 w-80 rounded-full bg-violet-500/10 blur-3xl" />
        <div className="absolute bottom-0 left-1/2 h-px w-full max-w-4xl -translate-x-1/2 bg-gradient-to-r from-transparent via-cyan-500/30 to-transparent" />
      </div>

      <div className="relative mx-auto max-w-7xl px-4 pb-20 pt-16 sm:px-6 lg:px-8 lg:pt-24">
        <div className="mx-auto max-w-4xl text-center">
          <div className="animate-fade-up mb-6 inline-flex items-center gap-2 rounded-full border border-cyan-500/20 bg-cyan-500/5 px-4 py-1.5 text-sm text-cyan-300">
            <Sparkles className="h-4 w-4" />
            {hero.badge}
          </div>

          <h1
            className="animate-fade-up text-4xl font-extrabold leading-tight tracking-tight sm:text-5xl lg:text-6xl"
            style={{ animationDelay: "0.1s" }}
          >
            {hero.title}{" "}
            <span className="gradient-text">{hero.titleHighlight}</span>
          </h1>

          <p
            className="animate-fade-up mx-auto mt-6 max-w-2xl text-lg text-slate-400 sm:text-xl"
            style={{ animationDelay: "0.2s" }}
          >
            {hero.subtitle}
          </p>

          <div
            className="animate-fade-up mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row"
            style={{ animationDelay: "0.3s" }}
          >
            <button
              type="button"
              onClick={() => openContact({ subject: hero.ctaPrimary })}
              className="group flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 px-8 py-4 text-base font-semibold text-white shadow-xl shadow-cyan-500/25 transition hover:scale-[1.02] hover:shadow-cyan-500/40"
            >
              {hero.ctaPrimary}
              <ArrowRight className="h-5 w-5 transition group-hover:translate-x-1" />
            </button>
            <button
              type="button"
              onClick={() => navigateTo("#services")}
              className="rounded-xl border border-white/10 bg-white/5 px-8 py-4 text-base font-semibold text-white backdrop-blur transition hover:bg-white/10"
            >
              {hero.ctaSecondary}
            </button>
          </div>
        </div>

        <div
          className="animate-fade-up mx-auto mt-20 grid max-w-3xl grid-cols-2 gap-6 sm:grid-cols-4"
          style={{ animationDelay: "0.4s" }}
        >
          {stats.map((stat) => (
            <button
              key={stat.label}
              type="button"
              onClick={() => navigateTo("#contact")}
              className="rounded-2xl border border-white/5 bg-white/[0.02] p-5 text-center backdrop-blur transition hover:border-cyan-500/20 hover:bg-white/[0.04]"
            >
              <div className="font-mono text-2xl font-bold text-cyan-400 sm:text-3xl">
                {stat.value}
              </div>
              <div className="mt-1 text-xs text-slate-400 sm:text-sm">
                {stat.label}
              </div>
            </button>
          ))}
        </div>
      </div>
    </section>
  );
}
