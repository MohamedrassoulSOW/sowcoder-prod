import { ExternalLink, FolderKanban } from "lucide-react";
import { useSiteContent } from "../context/ContentContext";
import ContentImage from "./ContentImage";

export default function Projects() {
  const { content } = useSiteContent();
  const projects = content.projects ?? [];

  if (projects.length === 0) return null;

  return (
    <section id="projets" className="relative py-24">
      <div className="absolute inset-0 bg-gradient-to-b from-transparent via-cyan-950/10 to-transparent" />
      <div className="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="mx-auto max-w-2xl text-center">
          <span className="text-sm font-semibold uppercase tracking-widest text-cyan-400">
            RÉALISATIONS
          </span>
          <h2 className="mt-3 text-3xl font-bold sm:text-4xl">
            Projets réalisés par SowCoder
          </h2>
          <p className="mt-4 text-slate-400">
            Découvrez une sélection de sites web, applications et identités
            visuelles livrés pour nos clients au Sénégal et en Afrique.
          </p>
        </div>

        <div className="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {projects.map((project) => (
            <article
              key={`${project.title}-${project.year}`}
              className="card-shine group flex flex-col overflow-hidden rounded-2xl border border-white/5 bg-slate-900/50 transition hover:border-cyan-500/30"
            >
              <div className="relative h-48 overflow-hidden">
                <ContentImage
                  src={project.image}
                  alt={project.title}
                  className="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                  fallbackClassName="flex h-full w-full items-center justify-center bg-gradient-to-br from-cyan-500/15 via-slate-800 to-violet-500/15"
                />
                {!project.image && (
                  <div className="pointer-events-none absolute inset-0 flex items-center justify-center">
                    <FolderKanban className="h-14 w-14 text-slate-600" />
                  </div>
                )}
                <span className="absolute left-4 top-4 rounded-full bg-violet-500/25 px-3 py-1 text-xs font-medium text-violet-200 backdrop-blur-sm">
                  {project.category}
                </span>
                {project.year ? (
                  <span className="absolute right-4 top-4 rounded-full bg-slate-950/70 px-2.5 py-1 text-xs text-slate-300 backdrop-blur-sm">
                    {project.year}
                  </span>
                ) : null}
              </div>

              <div className="flex flex-1 flex-col p-6">
                <h3 className="text-lg font-semibold text-white transition group-hover:text-cyan-300">
                  {project.title}
                </h3>
                {project.client ? (
                  <p className="mt-1 text-xs text-slate-500">{project.client}</p>
                ) : null}
                <p className="mt-3 flex-1 text-sm leading-relaxed text-slate-400">
                  {project.description}
                </p>

                {project.technologies?.length > 0 ? (
                  <div className="mt-4 flex flex-wrap gap-1.5">
                    {project.technologies.map((tech) => (
                      <span
                        key={tech}
                        className="rounded-md bg-white/5 px-2 py-0.5 text-xs text-slate-400"
                      >
                        {tech}
                      </span>
                    ))}
                  </div>
                ) : null}

                {project.url?.trim() ? (
                  <a
                    href={project.url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="mt-5 inline-flex items-center gap-1.5 text-sm font-medium text-cyan-400 transition hover:gap-2"
                  >
                    Voir le projet
                    <ExternalLink className="h-4 w-4" />
                  </a>
                ) : null}
              </div>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
}
