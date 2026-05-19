import { Link } from "react-router-dom";
import { ArrowRight, Calendar, Clock } from "lucide-react";
import { useSiteContent } from "../context/ContentContext";
import ContentImage from "./ContentImage";

export default function Blog() {
  const { content } = useSiteContent();
  const { blogPosts } = content;

  return (
    <section id="blog" className="relative py-24">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="mx-auto max-w-2xl text-center">
          <span className="text-sm font-semibold uppercase tracking-widest text-cyan-400">
            BLOG & ACTUALITÉS
          </span>
          <h2 className="mt-3 text-3xl font-bold sm:text-4xl">
            Insights & Conseils Digitaux
          </h2>
          <p className="mt-4 text-slate-400">
            Restez informé des dernières tendances et bonnes pratiques pour
            booster votre présence en ligne.
          </p>
          <Link
            to="/blog"
            className="mt-6 inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-white/10"
          >
            Voir tous les articles
            <ArrowRight className="h-4 w-4" />
          </Link>
        </div>

        <div className="mt-16 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          {blogPosts.map((post) => (
            <article
              key={post.slug}
              className="card-shine group flex flex-col overflow-hidden rounded-2xl border border-white/5 bg-slate-900/50 transition hover:border-cyan-500/30"
            >
              <Link to={`/blog/${post.slug}`} className="block">
                <div className="relative h-44 overflow-hidden">
                  <ContentImage
                    src={post.image}
                    alt={post.title}
                    className="h-full w-full object-cover"
                    fallbackClassName="h-full w-full bg-gradient-to-br from-cyan-500/20 via-slate-800 to-violet-500/20"
                  />
                  <span className="absolute bottom-4 left-4 rounded-full bg-cyan-500/20 px-3 py-1 text-xs font-medium text-cyan-300 backdrop-blur-sm">
                    {post.category}
                  </span>
                </div>
              </Link>
              <div className="flex flex-1 flex-col p-6">
                <div className="flex items-center gap-4 text-xs text-slate-500">
                  <span className="flex items-center gap-1">
                    <Calendar className="h-3.5 w-3.5" />
                    {post.date}
                  </span>
                  <span className="flex items-center gap-1">
                    <Clock className="h-3.5 w-3.5" />
                    {post.readTime}
                  </span>
                </div>
                <h3 className="mt-3 text-lg font-semibold text-white transition group-hover:text-cyan-300">
                  <Link to={`/blog/${post.slug}`}>{post.title}</Link>
                </h3>
                <p className="mt-2 flex-1 text-sm text-slate-400">
                  {post.excerpt}
                </p>
                <Link
                  to={`/blog/${post.slug}`}
                  className="mt-4 inline-flex items-center gap-1 text-sm font-medium text-cyan-400 transition group-hover:gap-2"
                >
                  Lire l&apos;article
                  <ArrowRight className="h-4 w-4" />
                </Link>
              </div>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
}
