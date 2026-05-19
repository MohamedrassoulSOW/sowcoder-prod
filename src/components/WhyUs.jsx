import { Check, Zap, Shield, Headphones, TrendingUp } from "lucide-react";
import { useSiteContent } from "../context/ContentContext";

const featureIcons = {
  zap: Zap,
  shield: Shield,
  headphones: Headphones,
  trending: TrendingUp,
};

export default function WhyUs() {
  const { content } = useSiteContent();
  const { whyUs } = content;

  return (
    <section id="apropos" className="relative py-24">
      <div className="absolute inset-0 bg-gradient-to-b from-transparent via-slate-900/50 to-transparent" />
      <div className="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="grid items-center gap-16 lg:grid-cols-2">
          <div>
            <span className="text-sm font-semibold uppercase tracking-widest text-violet-400">
              POURQUOI SOWCODER
            </span>
            <h2 className="mt-3 text-3xl font-bold sm:text-4xl">
              Votre Succès est Notre Priorité
            </h2>
            <p className="mt-4 text-slate-400">
              Nous combinons expertise technique, créativité et engagement pour
              vous offrir des solutions digitales qui font vraiment la
              différence.
            </p>
            <ul className="mt-8 space-y-4">
              {whyUs.bullets.map((item) => (
                <li key={item} className="flex items-start gap-3">
                  <span className="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-cyan-500/20 text-cyan-400">
                    <Check className="h-4 w-4" />
                  </span>
                  <span className="text-slate-300">{item}</span>
                </li>
              ))}
            </ul>
          </div>

          <div className="grid gap-4 sm:grid-cols-2">
            {whyUs.features.map((feature) => {
              const Icon = featureIcons[feature.icon];
              return (
                <div
                  key={feature.title}
                  className="rounded-2xl border border-white/5 bg-slate-900/60 p-6 backdrop-blur transition hover:border-violet-500/20"
                >
                  <div className="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-violet-500/20 text-violet-400">
                    <Icon className="h-5 w-5" />
                  </div>
                  <h3 className="font-semibold text-white">{feature.title}</h3>
                  <p className="mt-2 text-sm text-slate-400">
                    {feature.description}
                  </p>
                </div>
              );
            })}
          </div>
        </div>
      </div>
    </section>
  );
}
