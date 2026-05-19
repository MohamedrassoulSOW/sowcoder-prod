import { MessageCircle, Clock, ShieldCheck, Mail, Phone } from "lucide-react";
import ContactForm from "./ContactForm";
import { useApp } from "../context/AppContext";
import { useSiteContent } from "../context/ContentContext";

const badges = [
  { icon: MessageCircle, text: "Devis gratuit" },
  { icon: Clock, text: "Réponse sous 24h" },
  { icon: ShieldCheck, text: "Sans engagement" },
];

export default function CTA() {
  const { openContact } = useApp();
  const { content } = useSiteContent();
  const { footerLinks } = content;

  return (
    <section id="contact" className="relative py-24">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="grid gap-12 lg:grid-cols-2 lg:items-start">
          <div className="relative overflow-hidden rounded-3xl border border-cyan-500/20 bg-gradient-to-br from-slate-900 via-slate-900 to-cyan-950/30 p-8 sm:p-10">
            <div className="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-cyan-500/10 blur-3xl" />
            <div className="pointer-events-none absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-violet-500/10 blur-3xl" />

            <h2 className="relative text-3xl font-bold sm:text-4xl">
              Prêt à Transformer Votre Vision en Réalité ?
            </h2>
            <p className="relative mt-4 text-slate-400">
              Discutons de votre projet et découvrons ensemble comment SowCoder
              peut vous aider à atteindre vos objectifs digitaux.
            </p>

            <div className="relative mt-6 flex flex-wrap gap-4">
              {badges.map(({ icon: Icon, text }) => (
                <span
                  key={text}
                  className="flex items-center gap-2 text-sm text-slate-300"
                >
                  <Icon className="h-4 w-4 text-cyan-400" />
                  {text}
                </span>
              ))}
            </div>

            <ul className="relative mt-8 space-y-3 border-t border-white/5 pt-8">
              <li>
                <a
                  href={`mailto:${footerLinks.contact.email}`}
                  className="flex items-center gap-3 text-slate-300 transition hover:text-cyan-400"
                >
                  <Mail className="h-5 w-5 text-cyan-400" />
                  {footerLinks.contact.email}
                </a>
              </li>
              {footerLinks.contact.phones.map(({ label, number }) => (
                <li key={number}>
                  <a
                    href={`tel:${number.replace(/\s/g, "")}`}
                    className="flex items-center gap-3 text-slate-300 transition hover:text-cyan-400"
                  >
                    <Phone className="h-5 w-5 shrink-0 text-cyan-400" />
                    <span>
                      {label && (
                        <span className="text-slate-500">{label} · </span>
                      )}
                      {number}
                    </span>
                  </a>
                </li>
              ))}
            </ul>
          </div>

          <div className="rounded-3xl border border-white/5 bg-slate-900/60 p-8 backdrop-blur">
            <h3 className="mb-6 text-xl font-semibold text-white">
              Envoyez-nous un message
            </h3>
            <ContactForm />
          </div>
        </div>
      </div>
    </section>
  );
}
