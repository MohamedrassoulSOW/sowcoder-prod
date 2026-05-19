import { useCallback, useEffect, useState } from "react";
import { Link, useParams } from "react-router-dom";
import {
  ArrowLeft,
  ArrowRight,
  Calendar,
  Clock,
  Heart,
  Loader2,
  MessageCircle,
  Send,
  User,
} from "lucide-react";
import { api } from "../api/client";
import { useApp } from "../context/AppContext";
import ContentImage from "../components/ContentImage";
import { getBlogVisitorId } from "../utils/blogVisitor";

const inputClass =
  "w-full rounded-xl border border-white/10 bg-slate-950/50 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-cyan-500/50 focus:outline-none focus:ring-1 focus:ring-cyan-500/30";

function formatCommentDate(iso) {
  try {
    return new Date(iso).toLocaleDateString("fr-FR", {
      day: "numeric",
      month: "long",
      year: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  } catch {
    return iso;
  }
}

export default function BlogArticlePage() {
  const { slug } = useParams();
  const { user, openContact } = useApp();
  const visitorId = getBlogVisitorId();

  const [article, setArticle] = useState(null);
  const [comments, setComments] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [likeLoading, setLikeLoading] = useState(false);
  const [commentForm, setCommentForm] = useState({
    authorName: "",
    authorEmail: "",
    body: "",
  });
  const [commentLoading, setCommentLoading] = useState(false);
  const [commentError, setCommentError] = useState("");
  const [commentSuccess, setCommentSuccess] = useState(false);

  const loadArticle = useCallback(async () => {
    const res = await api.getBlogArticle(slug, visitorId);
    setArticle(res.data?.article ?? null);
  }, [slug, visitorId]);

  const loadComments = useCallback(async () => {
    const res = await api.getBlogComments(slug);
    setComments(res.data?.items ?? []);
  }, [slug]);

  useEffect(() => {
    setLoading(true);
    setError("");
    Promise.all([loadArticle(), loadComments()])
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, [loadArticle, loadComments]);

  useEffect(() => {
    if (user) {
      setCommentForm((f) => ({
        ...f,
        authorName: user.name,
        authorEmail: user.email,
      }));
    }
  }, [user]);

  const handleLike = async () => {
    if (!article || likeLoading) return;
    setLikeLoading(true);
    try {
      const res = await api.toggleBlogLike(slug, visitorId);
      setArticle((a) => ({
        ...a,
        liked: res.liked,
        likeCount: res.likeCount,
      }));
    } catch (err) {
      setError(err.message);
    } finally {
      setLikeLoading(false);
    }
  };

  const handleCommentChange = (e) => {
    setCommentForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const handleCommentSubmit = async (e) => {
    e.preventDefault();
    setCommentLoading(true);
    setCommentError("");
    setCommentSuccess(false);
    try {
      await api.postBlogComment(slug, {
        authorName: commentForm.authorName,
        authorEmail: commentForm.authorEmail || null,
        body: commentForm.body,
        visitorId,
      });
      setCommentForm((f) => ({ ...f, body: "" }));
      setCommentSuccess(true);
      await loadComments();
      const res = await api.getBlogArticle(slug, visitorId);
      setArticle(res.data?.article ?? article);
    } catch (err) {
      setCommentError(err.message);
    } finally {
      setCommentLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex min-h-screen items-center justify-center pt-24">
        <Loader2 className="h-8 w-8 animate-spin text-cyan-400" />
      </div>
    );
  }

  if (error && !article) {
    return (
      <div className="mx-auto max-w-3xl px-4 pt-28 pb-16 text-center">
        <p className="text-red-400">{error}</p>
        <Link to="/blog" className="mt-4 inline-block text-cyan-400 hover:underline">
          Retour au blog
        </Link>
      </div>
    );
  }

  if (!article) {
    return (
      <div className="mx-auto max-w-3xl px-4 pt-28 pb-16 text-center">
        <p className="text-slate-400">Article introuvable.</p>
        <Link to="/blog" className="mt-4 inline-block text-cyan-400 hover:underline">
          Retour au blog
        </Link>
      </div>
    );
  }

  return (
    <article className="min-h-screen pt-24 pb-20">
      <div className="mx-auto max-w-3xl px-4 sm:px-6">
        <nav className="mb-8 flex flex-wrap items-center gap-3 text-sm text-slate-500">
          <Link to="/" className="hover:text-cyan-400">
            Accueil
          </Link>
          <span>/</span>
          <Link to="/blog" className="hover:text-cyan-400">
            Blog
          </Link>
          <span>/</span>
          <span className="text-slate-300 truncate max-w-[200px] sm:max-w-none">
            {article.title}
          </span>
        </nav>

        {article.image && (
          <ContentImage
            src={article.image}
            alt={article.title}
            className="mb-8 h-56 w-full rounded-2xl object-cover sm:h-72"
          />
        )}

        <span className="inline-block rounded-full bg-cyan-500/20 px-3 py-1 text-xs font-medium text-cyan-300">
          {article.category}
        </span>

        <h1 className="mt-4 text-3xl font-bold leading-tight text-white sm:text-4xl">
          {article.title}
        </h1>

        <div className="mt-4 flex flex-wrap items-center gap-4 text-sm text-slate-500">
          <span className="flex items-center gap-1">
            <Calendar className="h-4 w-4" />
            {article.date}
          </span>
          <span className="flex items-center gap-1">
            <Clock className="h-4 w-4" />
            {article.readTime}
          </span>
          {article.author && (
            <span className="flex items-center gap-1">
              <User className="h-4 w-4" />
              {article.author}
            </span>
          )}
        </div>

        <div className="mt-6 flex flex-wrap items-center gap-4">
          <button
            type="button"
            onClick={handleLike}
            disabled={likeLoading}
            className={`inline-flex items-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-medium transition disabled:opacity-60 ${
              article.liked
                ? "border-rose-500/40 bg-rose-500/15 text-rose-300"
                : "border-white/10 bg-white/5 text-slate-300 hover:border-rose-500/30 hover:text-rose-300"
            }`}
          >
            <Heart
              className={`h-4 w-4 ${article.liked ? "fill-current" : ""}`}
            />
            {article.likeCount ?? 0} J&apos;aime
          </button>
          <span className="inline-flex items-center gap-2 text-sm text-slate-400">
            <MessageCircle className="h-4 w-4 text-cyan-400" />
            {article.commentCount ?? 0} commentaire
            {(article.commentCount ?? 0) !== 1 ? "s" : ""}
          </span>
        </div>

        <p className="mt-8 text-lg leading-relaxed text-slate-300">
          {article.excerpt}
        </p>

        <div className="mt-8 space-y-5 text-base leading-relaxed text-slate-300">
          {article.body?.map((paragraph) => (
            <p key={paragraph.slice(0, 48)}>{paragraph}</p>
          ))}
        </div>

        <button
          type="button"
          onClick={() =>
            openContact({
              subject: `Article : ${article.title}`,
              message: `Bonjour, je souhaite en savoir plus suite à votre article « ${article.title} ».`,
            })
          }
          className="mt-10 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 px-6 py-3 font-semibold text-white"
        >
          Discuter de ce sujet
          <ArrowRight className="h-4 w-4" />
        </button>

        <section className="mt-16 border-t border-white/10 pt-12">
          <h2 className="flex items-center gap-2 text-xl font-semibold text-white">
            <MessageCircle className="h-5 w-5 text-cyan-400" />
            Commentaires des lecteurs
          </h2>

          <form
            onSubmit={handleCommentSubmit}
            className="mt-6 space-y-4 rounded-2xl border border-white/10 bg-slate-900/50 p-5"
          >
            <p className="text-sm text-slate-400">
              Partagez votre avis sur cet article.
            </p>
            {!user && (
              <div className="grid gap-4 sm:grid-cols-2">
                <input
                  name="authorName"
                  required
                  value={commentForm.authorName}
                  onChange={handleCommentChange}
                  className={inputClass}
                  placeholder="Votre nom *"
                />
                <input
                  name="authorEmail"
                  type="email"
                  value={commentForm.authorEmail}
                  onChange={handleCommentChange}
                  className={inputClass}
                  placeholder="Email (optionnel)"
                />
              </div>
            )}
            <textarea
              name="body"
              required
              rows={4}
              minLength={3}
              value={commentForm.body}
              onChange={handleCommentChange}
              className={`${inputClass} resize-none`}
              placeholder="Votre commentaire *"
            />
            {commentError && (
              <p className="text-sm text-red-400">{commentError}</p>
            )}
            {commentSuccess && (
              <p className="text-sm text-cyan-400">
                Merci ! Votre commentaire a été publié.
              </p>
            )}
            <button
              type="submit"
              disabled={commentLoading}
              className="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 px-5 py-2.5 text-sm font-semibold text-white disabled:opacity-60"
            >
              {commentLoading ? (
                <Loader2 className="h-4 w-4 animate-spin" />
              ) : (
                <Send className="h-4 w-4" />
              )}
              Publier le commentaire
            </button>
          </form>

          <ul className="mt-10 space-y-4">
            {comments.length === 0 ? (
              <li className="rounded-xl border border-dashed border-white/10 py-10 text-center text-sm text-slate-500">
                Aucun commentaire pour le moment. Soyez le premier à réagir !
              </li>
            ) : (
              comments.map((c) => (
                <li
                  key={c.id}
                  className="rounded-xl border border-white/5 bg-slate-950/50 p-5"
                >
                  <div className="flex items-start justify-between gap-3">
                    <div>
                      <p className="font-medium text-white">{c.authorName}</p>
                      <p className="text-xs text-slate-500">
                        {formatCommentDate(c.createdAt)}
                      </p>
                    </div>
                  </div>
                  <p className="mt-3 text-sm leading-relaxed text-slate-300 whitespace-pre-wrap">
                    {c.body}
                  </p>
                </li>
              ))
            )}
          </ul>
        </section>

        <Link
          to="/blog"
          className="mt-12 inline-flex items-center gap-2 text-sm text-cyan-400 hover:underline"
        >
          <ArrowLeft className="h-4 w-4" />
          Tous les articles
        </Link>
      </div>
    </article>
  );
}
