import { useState } from "react";
import {
  Code2,
  Megaphone,
  Palette,
  GraduationCap,
  Building2,
  Wrench,
  ArrowRight,
} from "lucide-react";
import { useApp } from "../context/AppContext";
import { useSiteContent } from "../context/ContentContext";
import ContentImage from "./ContentImage";
import ServiceInquiryModal from "./ServiceInquiryModal";

const iconMap = {
  code: Code2,
  megaphone: Megaphone,
  palette: Palette,
  graduation: GraduationCap,
  building: Building2,
  wrench: Wrench,
};

export default function Services() {
  const { openContact } = useApp();
  const { content } = useSiteContent();
  const { services } = content;
  const [inquiryService, setInquiryService] = useState(null);

  return (
    <section id="services" className="relative py-24">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="mx-auto max-w-2xl text-center">
          <span className="text-sm font-semibold uppercase tracking-widest text-cyan-400">
            NOS EXPERTISES
          </span>
          <h2 className="mt-3 text-3xl font-bold sm:text-4xl">
            Une Offre Complète
          </h2>
          <p className="mt-4 text-slate-400">
            De l&apos;audit initial à la maintenance évolutive, nous pilotons
            chaque aspect de votre infrastructure numérique avec rigueur et
            créativité.
          </p>
        </div>

        <div className="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {services.map((service) => {
            const Icon = iconMap[service.icon];
            return (
              <article
                key={service.title}
                className="card-shine group flex flex-col overflow-hidden rounded-2xl border border-white/5 bg-slate-900/50 transition hover:border-cyan-500/30 hover:bg-slate-900/80"
              >
                {service.image ? (
                  <ContentImage
                    src={service.image}
                    alt={service.title}
                    className="h-36 w-full object-cover"
                  />
                ) : null}
                <div className="flex flex-1 flex-col p-6">
                  <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-500/20 to-violet-500/20 text-cyan-400 transition group-hover:scale-110">
                    {Icon ? <Icon className="h-6 w-6" /> : null}
                  </div>
                  <h3 className="text-lg font-semibold text-white">
                    {service.title}
                  </h3>
                  <p className="mt-2 flex-1 text-sm leading-relaxed text-slate-400">
                    {service.description}
                  </p>
                  <button
                    type="button"
                    onClick={() => setInquiryService(service)}
                    className="mt-4 inline-flex items-center gap-1 text-sm font-medium text-cyan-400 transition group-hover:gap-2"
                  >
                    En savoir plus
                    <ArrowRight className="h-4 w-4" />
                  </button>
                </div>
              </article>
            );
          })}
        </div>

        <div className="mt-12 text-center">
          <button
            type="button"
            onClick={() => openContact({ subject: "Demande de devis — Services" })}
            className="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-6 py-3 font-medium text-white transition hover:bg-white/10"
          >
            Voir tous nos services
            <ArrowRight className="h-4 w-4" />
          </button>
        </div>
      </div>

      <ServiceInquiryModal
        open={inquiryService !== null}
        service={inquiryService}
        onClose={() => setInquiryService(null)}
      />
    </section>
  );
}
