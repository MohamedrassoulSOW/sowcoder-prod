import { useState } from "react";
import { BookOpen, Users, Award, Clock, BarChart3 } from "lucide-react";
import { useSiteContent } from "../context/ContentContext";
import ContentImage from "./ContentImage";
import { useApp } from "../context/AppContext";
import RequestModal from "./RequestModal";

const highlights = [
  {
    icon: BookOpen,
    title: "Programmes pratiques",
    text: "Modules orientés projet avec exercices réels du marché.",
  },
  {
    icon: Users,
    title: "Formateurs experts",
    text: "Professionnels actifs en développement, design et marketing.",
  },
  {
    icon: Award,
    title: "Certification",
    text: "Attestation reconnue à la fin de chaque parcours.",
  },
];

export default function Formations() {
  const { openContact } = useApp();
  const { content } = useSiteContent();
  const { formations } = content;
  const [modal, setModal] = useState({ open: false, title: "" });

  return (
    <section id="formations" className="relative py-24">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="rounded-3xl border border-white/5 bg-slate-900/40 p-8 sm:p-12">
          <div className="grid gap-12 lg:grid-cols-2 lg:items-start">
            <div>
              <span className="text-sm font-semibold uppercase tracking-widest text-cyan-400">
                FORMATIONS
              </span>
              <h2 className="mt-3 text-3xl font-bold">
                Renforcez les compétences de votre équipe
              </h2>
              <p className="mt-4 text-slate-400">
                Bureautique, développement web, marketing digital, infographie,
                entrepreneuriat et cybersécurité — des parcours adaptés aux
                besoins des entreprises et des particuliers.
              </p>
              <button
                type="button"
                onClick={() =>
                  openContact({ subject: "Catalogue des formations" })
                }
                className="mt-6 inline-block rounded-xl bg-white/10 px-6 py-3 font-medium text-white transition hover:bg-white/15"
              >
                Voir le catalogue
              </button>
              <div className="mt-8 grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                {highlights.map(({ icon: Icon, title, text }) => (
                  <div
                    key={title}
                    className="rounded-xl border border-white/5 bg-slate-950/50 p-4"
                  >
                    <Icon className="h-7 w-7 text-cyan-400" />
                    <h3 className="mt-2 text-sm font-semibold">{title}</h3>
                    <p className="mt-1 text-xs text-slate-400">{text}</p>
                  </div>
                ))}
              </div>
            </div>

            <div className="grid gap-4">
              {formations.map((course) => (
                <article
                  key={course.title}
                  className="overflow-hidden rounded-xl border border-white/5 bg-slate-950/50 transition hover:border-cyan-500/20"
                >
                  {course.image ? (
                    <ContentImage
                      src={course.image}
                      alt={course.title}
                      className="h-28 w-full object-cover"
                    />
                  ) : null}
                  <div className="p-5">
                  <div className="flex flex-wrap items-start justify-between gap-2">
                    <h3 className="font-semibold text-white">{course.title}</h3>
                    <span className="rounded-lg bg-cyan-500/10 px-2 py-0.5 text-xs text-cyan-300">
                      {course.level}
                    </span>
                  </div>
                  <div className="mt-2 flex items-center gap-1 text-xs text-slate-500">
                    <Clock className="h-3.5 w-3.5" />
                    {course.duration}
                  </div>
                  <div className="mt-3 flex flex-wrap gap-2">
                    {course.topics.map((topic) => (
                      <span
                        key={topic}
                        className="rounded-md bg-white/5 px-2 py-0.5 text-xs text-slate-400"
                      >
                        {topic}
                      </span>
                    ))}
                  </div>
                  <button
                    type="button"
                    onClick={() =>
                      setModal({ open: true, title: course.title })
                    }
                    className="mt-4 inline-flex items-center gap-1 text-sm font-medium text-cyan-400 hover:underline"
                  >
                    <BarChart3 className="h-3.5 w-3.5" />
                    S&apos;inscrire
                  </button>
                  </div>
                </article>
              ))}
            </div>
          </div>
        </div>
      </div>

      <RequestModal
        open={modal.open}
        onClose={() => setModal({ open: false, title: "" })}
        type="inscription"
        itemTitle={modal.title}
      />
    </section>
  );
}
