import { useState } from "react";
import { X, LogIn, UserPlus, Loader2 } from "lucide-react";
import { useApp } from "../context/AppContext";

const inputClass =
  "w-full rounded-xl border border-white/10 bg-slate-950/50 px-4 py-3 text-sm text-white placeholder:text-slate-500 transition focus:border-cyan-500/50 focus:outline-none focus:ring-1 focus:ring-cyan-500/30";

export default function AuthModal() {
  const { authModal, closeAuth, openLogin, openRegister, login, register } =
    useApp();
  const [form, setForm] = useState({ name: "", email: "", password: "" });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  if (!authModal) return null;

  const isLogin = authModal === "login";

  const handleChange = (e) => {
    setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const switchMode = (mode) => {
    setError("");
    setForm({ name: "", email: "", password: "" });
    if (mode === "login") openLogin();
    else openRegister();
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError("");
    try {
      if (isLogin) {
        await login(form.email, form.password);
      } else {
        await register(form.name, form.email, form.password);
      }
      setForm({ name: "", email: "", password: "" });
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div
      className="fixed inset-0 z-[70] flex items-center justify-center p-4"
      role="dialog"
      aria-modal="true"
    >
      <button
        type="button"
        className="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"
        onClick={closeAuth}
        aria-label="Fermer"
      />
      <div className="relative w-full max-w-md rounded-2xl border border-white/10 bg-slate-900 p-6 shadow-2xl">
        <button
          type="button"
          onClick={closeAuth}
          className="absolute right-4 top-4 rounded-lg p-1 text-slate-400 hover:bg-white/5 hover:text-white"
        >
          <X className="h-5 w-5" />
        </button>

        <div className="mb-6 flex gap-2 rounded-xl bg-slate-950/80 p-1">
          <button
            type="button"
            onClick={() => switchMode("login")}
            className={`flex flex-1 items-center justify-center gap-2 rounded-lg py-2.5 text-sm font-medium transition ${
              isLogin
                ? "bg-gradient-to-r from-cyan-500 to-violet-500 text-white"
                : "text-slate-400 hover:text-white"
            }`}
          >
            <LogIn className="h-4 w-4" />
            Connexion
          </button>
          <button
            type="button"
            onClick={() => switchMode("register")}
            className={`flex flex-1 items-center justify-center gap-2 rounded-lg py-2.5 text-sm font-medium transition ${
              !isLogin
                ? "bg-gradient-to-r from-cyan-500 to-violet-500 text-white"
                : "text-slate-400 hover:text-white"
            }`}
          >
            <UserPlus className="h-4 w-4" />
            Inscription
          </button>
        </div>

        <h2 className="text-xl font-bold text-white">
          {isLogin ? "Bon retour !" : "Créer un compte"}
        </h2>
        <p className="mt-1 text-sm text-slate-400">
          {isLogin
            ? "Connectez-vous à votre espace SowCoder"
            : "Rejoignez SowCoder pour commander et suivre vos projets"}
        </p>

        <form onSubmit={handleSubmit} className="mt-6 space-y-4">
          {!isLogin && (
            <div>
              <label htmlFor="auth-name" className="mb-1 block text-xs text-slate-400">
                Nom complet *
              </label>
              <input
                id="auth-name"
                name="name"
                required
                value={form.name}
                onChange={handleChange}
                className={inputClass}
                placeholder="Votre nom"
              />
            </div>
          )}
          <div>
            <label htmlFor="auth-email" className="mb-1 block text-xs text-slate-400">
              Email *
            </label>
            <input
              id="auth-email"
              name="email"
              type="email"
              required
              value={form.email}
              onChange={handleChange}
              className={inputClass}
              placeholder="vous@exemple.com"
            />
          </div>
          <div>
            <label htmlFor="auth-password" className="mb-1 block text-xs text-slate-400">
              Mot de passe *
            </label>
            <input
              id="auth-password"
              name="password"
              type="password"
              required
              minLength={isLogin ? 1 : 8}
              value={form.password}
              onChange={handleChange}
              className={inputClass}
              placeholder={isLogin ? "••••••••" : "8 caractères minimum"}
            />
          </div>

          {error && (
            <p className="rounded-lg bg-red-500/10 px-3 py-2 text-sm text-red-400">
              {error}
            </p>
          )}

          <button
            type="submit"
            disabled={loading}
            className="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 py-3 font-semibold text-white disabled:opacity-60"
          >
            {loading && <Loader2 className="h-4 w-4 animate-spin" />}
            {loading
              ? "Chargement..."
              : isLogin
                ? "Se connecter"
                : "S'inscrire"}
          </button>
        </form>
      </div>
    </div>
  );
}
