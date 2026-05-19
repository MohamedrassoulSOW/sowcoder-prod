import { useEffect, useState } from "react";
import { X, Send, CheckCircle, Loader2 } from "lucide-react";
import { api } from "../api/client";
import { useApp } from "../context/AppContext";

const inputClass =
  "w-full rounded-xl border border-white/10 bg-slate-950/50 px-4 py-3 text-sm text-white placeholder:text-slate-500 transition focus:border-cyan-500/50 focus:outline-none focus:ring-1 focus:ring-cyan-500/30";

export default function RequestModal({ open, onClose, type, itemTitle }) {
  const { user } = useApp();
  const [form, setForm] = useState({
    name: "",
    email: "",
    phone: "",
    message: "",
  });

  useEffect(() => {
    if (open && user) {
      setForm((f) => ({
        ...f,
        name: user.name,
        email: user.email,
      }));
    }
  }, [open, user]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState(false);

  if (!open) return null;

  const isOrder = type === "order";
  const title = isOrder ? "Commander" : "S'inscrire à la formation";
  const submitLabel = isOrder ? "Envoyer la commande" : "Envoyer l'inscription";

  const handleChange = (e) => {
    setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError("");

    try {
      const payload = {
        name: form.name,
        email: form.email,
        phone: form.phone,
        message: form.message,
      };

      if (isOrder) {
        await api.createOrder({ ...payload, productTitle: itemTitle });
      } else {
        await api.createInscription({
          ...payload,
          formationTitle: itemTitle,
        });
      }

      setSuccess(true);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  const handleClose = () => {
    setSuccess(false);
    setError("");
    setForm({ name: "", email: "", phone: "", message: "" });
    onClose();
  };

  return (
    <div
      className="fixed inset-0 z-[60] flex items-center justify-center p-4"
      role="dialog"
      aria-modal="true"
    >
      <button
        type="button"
        className="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"
        onClick={handleClose}
        aria-label="Fermer"
      />
      <div className="relative w-full max-w-md rounded-2xl border border-white/10 bg-slate-900 p-6 shadow-2xl">
        <button
          type="button"
          onClick={handleClose}
          className="absolute right-4 top-4 rounded-lg p-1 text-slate-400 hover:bg-white/5 hover:text-white"
          aria-label="Fermer"
        >
          <X className="h-5 w-5" />
        </button>

        {success ? (
          <div className="py-6 text-center">
            <CheckCircle className="mx-auto h-12 w-12 text-cyan-400" />
            <p className="mt-4 font-semibold text-white">Demande envoyée</p>
            <p className="mt-2 text-sm text-slate-400">
              Nous vous contacterons très prochainement.
            </p>
            <button
              type="button"
              onClick={handleClose}
              className="mt-6 text-sm text-cyan-400 hover:underline"
            >
              Fermer
            </button>
          </div>
        ) : (
          <>
            <h3 className="pr-8 text-lg font-semibold text-white">{title}</h3>
            <p className="mt-1 text-sm text-cyan-400">{itemTitle}</p>

            <form onSubmit={handleSubmit} className="mt-6 space-y-4">
              <div>
                <label htmlFor="req-name" className="mb-1 block text-xs text-slate-400">
                  Nom *
                </label>
                <input
                  id="req-name"
                  name="name"
                  required
                  value={form.name}
                  onChange={handleChange}
                  className={inputClass}
                />
              </div>
              <div className="grid gap-4 sm:grid-cols-2">
                <div>
                  <label htmlFor="req-email" className="mb-1 block text-xs text-slate-400">
                    Email *
                  </label>
                  <input
                    id="req-email"
                    name="email"
                    type="email"
                    required
                    value={form.email}
                    onChange={handleChange}
                    className={inputClass}
                  />
                </div>
                <div>
                  <label htmlFor="req-phone" className="mb-1 block text-xs text-slate-400">
                    Téléphone
                  </label>
                  <input
                    id="req-phone"
                    name="phone"
                    type="tel"
                    value={form.phone}
                    onChange={handleChange}
                    className={inputClass}
                  />
                </div>
              </div>
              <div>
                <label htmlFor="req-message" className="mb-1 block text-xs text-slate-400">
                  Message (optionnel)
                </label>
                <textarea
                  id="req-message"
                  name="message"
                  rows={3}
                  value={form.message}
                  onChange={handleChange}
                  className={`${inputClass} resize-none`}
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
                {loading ? (
                  <Loader2 className="h-4 w-4 animate-spin" />
                ) : (
                  <Send className="h-4 w-4" />
                )}
                {loading ? "Envoi..." : submitLabel}
              </button>
            </form>
          </>
        )}
      </div>
    </div>
  );
}
