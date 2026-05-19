import { MapPin, Phone, Mail, MessageCircle } from "lucide-react";
import { useApp } from "../context/AppContext";
import { useSiteContent } from "../context/ContentContext";
import NavLink from "./NavLink";

export default function Footer() {
  const { navigateTo, openContact, openLegal } = useApp();
  const { content } = useSiteContent();
  const { footerLinks, socialLinks } = content;

  return (
    <footer className="border-t border-white/5 bg-slate-950">
      <div className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div className="grid gap-12 lg:grid-cols-4">
          <div className="lg:col-span-1">
            <a
              href="#accueil"
              onClick={(e) => {
                e.preventDefault();
                navigateTo("#accueil");
              }}
              className="flex items-center gap-2"
            >
              <span className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-400 to-violet-500 font-mono text-sm font-bold text-slate-950">
                SC
              </span>
              <span className="text-xl font-bold">
                Sow<span className="text-cyan-400">Coder</span>
              </span>
            </a>
            <p className="mt-4 text-sm leading-relaxed text-slate-400">
              Votre partenaire de confiance pour toutes vos solutions digitales.
              Nous accompagnons les entreprises dans leur transformation
              numérique avec expertise et innovation.
            </p>
            <div className="mt-6 flex flex-wrap gap-3">
              {socialLinks.map(({ label, href }) => (
                <a
                  key={label}
                  href={href}
                  target="_blank"
                  rel="noopener noreferrer"
                  aria-label={label}
                  title={label}
                  className="flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 text-slate-400 transition hover:border-cyan-500/30 hover:text-cyan-400"
                >
                  <MessageCircle className="h-4 w-4" />
                </a>
              ))}
            </div>
          </div>

          <div>
            <h4 className="font-semibold text-white">Services</h4>
            <ul className="mt-4 space-y-2">
              {footerLinks.services.map((item) => (
                <li key={item.label}>
                  <NavLink
                    href={item.href}
                    className="text-sm text-slate-400 transition hover:text-cyan-400"
                  >
                    {item.label}
                  </NavLink>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h4 className="font-semibold text-white">Liens</h4>
            <ul className="mt-4 space-y-2">
              {footerLinks.links.map((item) => (
                <li key={item.label}>
                  <NavLink
                    href={item.href}
                    className="text-sm text-slate-400 transition hover:text-cyan-400"
                  >
                    {item.label}
                  </NavLink>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h4 className="font-semibold text-white">Contact</h4>
            <ul className="mt-4 space-y-3">
              <li className="flex items-start gap-2 text-sm text-slate-400">
                <MapPin className="mt-0.5 h-4 w-4 shrink-0 text-cyan-400" />
                {footerLinks.contact.address}
              </li>
              {footerLinks.contact.phones.map(({ label, number }) => (
                <li
                  key={number}
                  className="flex items-center gap-2 text-sm text-slate-400"
                >
                  <Phone className="h-4 w-4 shrink-0 text-cyan-400" />
                  <a
                    href={`tel:${number.replace(/\s/g, "")}`}
                    className="hover:text-cyan-400"
                  >
                    {label ? `${label} : ` : ""}
                    {number}
                  </a>
                </li>
              ))}
              <li className="flex items-center gap-2 text-sm text-slate-400">
                <Mail className="h-4 w-4 shrink-0 text-cyan-400" />
                <a
                  href={`mailto:${footerLinks.contact.email}`}
                  className="hover:text-cyan-400"
                >
                  {footerLinks.contact.email}
                </a>
              </li>
            </ul>

            <div className="mt-6 rounded-xl border border-cyan-500/20 bg-cyan-500/5 p-4">
              <p className="text-sm font-medium text-white">
                Besoin d&apos;un devis ?
              </p>
              <p className="mt-1 text-xs text-slate-400">
                Réponse rapide sous 24h pour votre projet.
              </p>
              <button
                type="button"
                onClick={() => openContact({ subject: "Demande de devis" })}
                className="mt-3 text-sm font-medium text-cyan-400 hover:underline"
              >
                Demander un devis →
              </button>
            </div>
          </div>
        </div>

        <div className="mt-12 flex flex-col items-center justify-between gap-4 border-t border-white/5 pt-8 sm:flex-row">
          <p className="text-sm text-slate-500">
            © 2018 – {new Date().getFullYear()} SowCoder. Tous droits réservés.
          </p>
          <div className="flex gap-4 text-sm text-slate-500">
            <button
              type="button"
              onClick={() => openLegal("mentions")}
              className="hover:text-slate-300"
            >
              Mentions Légales
            </button>
            <span>•</span>
            <button
              type="button"
              onClick={() => openLegal("privacy")}
              className="hover:text-slate-300"
            >
              Politique de Confidentialité
            </button>
          </div>
        </div>
      </div>
    </footer>
  );
}
