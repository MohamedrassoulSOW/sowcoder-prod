import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import {
  ArrowLeft,
  ArrowRight,
  Calendar,
  Clock,
  Heart,
  MessageCircle,
} from "lucide-react";
import { api } from "../api/client";
import ContentImage from "../components/ContentImage";

export default function BlogListPage() {
  const [articles, setArticles] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    api
      .getBlogArticles()
      .then((res) => setArticles(res.data?.articles ?? []))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, []);

  return (
    <div className="min-h-screen pt-24 pb-16">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <Link
          to="/"
          className="mb-8 inline-flex items-center gap-2 text-sm text-slate-400 transition hover:text-cyan-400"
        >
          <ArrowLeft className="h-4 w-4" />
          Retour à l&apos;accueil
        </Link>

        <div className="mx-auto max-w-2xl text-center">
          <span className="text-sm font-semibold uppercase tracking-widest text-cyan-400">
            Blog & Actualités
          </span>
          <h1 className="mt-3 text-3xl font-bold sm:text-4xl">
            Insights & Conseils Digitaux
          </h1>
          <p className="mt-4 text-slate-400">
            Articles, tendances et bonnes pratiques pour votre transformation
            digitale.
          </p>
        </div>

        {loading && (
          <p className="mt-16 text-center text-slate-500">Chargement…</p>
        )}
        {error && (
          <p className="mt-16 text-center text-red-400">{error}</p>
        )}

        {!loading && !error && (
          <div className="mt-16 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            {articles.map((post) => (
              <article
                key={post.slug}
                className="card-shine group flex flex-col overflow-hidden rounded-2xl border border-white/5 bg-slate-900/50 transition hover:border-cyan-500/30"
              >
                <Link to={`/blog/${post.slug}`} className="block">
                  <div className="relative h-44 overflow-hidden">
                    <ContentImage
                      src={post.image}
                      alt={post.title}
                      className="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                      fallbackClassName="h-full w-full bg-gradient-to-br from-cyan-500/20 via-slate-800 to-violet-500/20"
                    />
                    <span className="absolute bottom-4 left-4 rounded-full bg-cyan-500/20 px-3 py-1 text-xs font-medium text-cyan-300 backdrop-blur-sm">
                      {post.category}
                    </span>
                  </div>
                </Link>
                <div className="flex flex-1 flex-col p-6">
                  <div className="flex flex-wrap items-center gap-3 text-xs text-slate-500">
                    <span className="flex items-center gap-1">
                      <Calendar className="h-3.5 w-3.5" />
                      {post.date}
                    </span>
                    <span className="flex items-center gap-1">
                      <Clock className="h-3.5 w-3.5" />
                      {post.readTime}
                    </span>
                    <span className="flex items-center gap-1 text-rose-400/90">
                      <Heart className="h-3.5 w-3.5" />
                      {post.likeCount ?? 0}
                    </span>
                    <span className="flex items-center gap-1 text-cyan-400/90">
                      <MessageCircle className="h-3.5 w-3.5" />
                      {post.commentCount ?? 0}
                    </span>
                  </div>
                  <h2 className="mt-3 text-lg font-semibold text-white transition group-hover:text-cyan-300">
                    <Link to={`/blog/${post.slug}`}>{post.title}</Link>
                  </h2>
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
        )}
      </div>
    </div>
  );
}
