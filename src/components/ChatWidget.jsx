import { useState } from "react";
import { MessageSquare, X, Send, Loader2, CheckCircle } from "lucide-react";
import { api } from "../api/client";
import { useApp } from "../context/AppContext";

const inputClass =
  "w-full rounded-lg border border-white/10 bg-slate-800 px-3 py-2 text-sm text-white placeholder:text-slate-500 focus:border-cyan-500/50 focus:outline-none";

export default function ChatWidget() {
  const { user, openLogin, openContact } = useApp();
  const [open, setOpen] = useState(false);
  const [step, setStep] = useState("chat");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [form, setForm] = useState({ name: "", email: "", message: "" });

  const handleChange = (e) => {
    setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const handleContinue = () => {
    if (user) {
      setForm((f) => ({
        ...f,
        name: user.name,
        email: user.email,
        message: f.message || "",
      }));
      setStep("form");
    } else {
      openLogin();
      setOpen(false);
    }
  };

  const handleSend = async (e) => {
    e.preventDefault();
    const name = user?.name || form.name;
    const email = user?.email || form.email;

    if (!name || !email || !form.message.trim()) {
      setError("Remplissez tous les champs obligatoires.");
      return;
    }

    setLoading(true);
    setError("");
    try {
      await api.sendContact({
        name,
        email,
        phone: "",
        subject: "Message via le chat",
        message: form.message,
      });
      setStep("sent");
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  const handleContactSection = () => {
    setOpen(false);
    openContact({ subject: "Discussion via chat" });
  };

  const resetChat = () => {
    setStep("chat");
    setForm({ name: "", email: "", message: "" });
    setError("");
  };

  return (
    <>
      <button
        type="button"
        onClick={() => {
          setOpen(!open);
          if (!open) resetChat();
        }}
        className="fixed bottom-6 right-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-r from-cyan-500 to-violet-500 text-white shadow-xl shadow-cyan-500/30 transition hover:scale-105"
        aria-label="Support chat"
      >
        {open ? <X className="h-6 w-6" /> : <MessageSquare className="h-6 w-6" />}
      </button>

      {open && (
        <div className="fixed bottom-24 right-6 z-50 w-[min(100vw-2rem,360px)] overflow-hidden rounded-2xl border border-white/10 bg-slate-900 shadow-2xl">
          <div className="bg-gradient-to-r from-cyan-600 to-violet-600 px-4 py-3">
            <p className="font-semibold text-white">Support SowCoder</p>
            <p className="text-xs text-cyan-100">En ligne pour vous aider</p>
          </div>

          {step === "sent" ? (
            <div className="p-6 text-center">
              <CheckCircle className="mx-auto h-10 w-10 text-cyan-400" />
              <p className="mt-3 font-medium text-white">Message envoyé !</p>
              <p className="mt-1 text-xs text-slate-400">Réponse sous 24h.</p>
              <button
                type="button"
                onClick={() => {
                  resetChat();
                  setOpen(false);
                }}
                className="mt-4 text-sm text-cyan-400 hover:underline"
              >
                Fermer
              </button>
            </div>
          ) : step === "form" ? (
            <form onSubmit={handleSend} className="space-y-3 p-4">
              {!user && (
                <>
                  <input
                    name="name"
                    required
                    value={form.name}
                    onChange={handleChange}
                    className={inputClass}
                    placeholder="Votre nom *"
                  />
                  <input
                    name="email"
                    type="email"
                    required
                    value={form.email}
                    onChange={handleChange}
                    className={inputClass}
                    placeholder="Votre email *"
                  />
                </>
              )}
              <textarea
                name="message"
                required
                rows={3}
                value={form.message}
                onChange={handleChange}
                className={`${inputClass} resize-none`}
                placeholder="Votre message *"
              />
              {error && <p className="text-xs text-red-400">{error}</p>}
              <button
                type="submit"
                disabled={loading}
                className="flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-cyan-500 to-violet-500 py-2.5 text-sm font-semibold text-white disabled:opacity-60"
              >
                {loading ? (
                  <Loader2 className="h-4 w-4 animate-spin" />
                ) : (
                  <Send className="h-4 w-4" />
                )}
                Envoyer
              </button>
              <button
                type="button"
                onClick={() => setStep("chat")}
                className="w-full text-xs text-slate-500 hover:text-slate-300"
              >
                Retour
              </button>
            </form>
          ) : (
            <>
              <div className="max-h-48 space-y-3 overflow-y-auto p-4">
                <p className="text-center text-xs text-slate-500">Aujourd&apos;hui</p>
                <div className="rounded-xl rounded-tl-sm bg-slate-800 p-3 text-sm text-slate-300">
                  Bienvenue sur SowCoder. Comment pouvons-nous vous accompagner ?
                </div>
              </div>
              <div className="space-y-2 border-t border-white/5 p-3">
                <button
                  type="button"
                  onClick={handleContinue}
                  className="w-full rounded-lg bg-gradient-to-r from-cyan-500 to-violet-500 py-2.5 text-sm font-semibold text-white"
                >
                  {user ? "Écrire un message" : "Se connecter pour discuter"}
                </button>
                <button
                  type="button"
                  onClick={() => {
                    setStep("form");
                    setForm((f) => ({ ...f, message: "" }));
                  }}
                  className="w-full rounded-lg border border-white/10 py-2 text-sm text-slate-300 hover:bg-white/5"
                >
                  Envoyer sans compte
                </button>
                <button
                  type="button"
                  onClick={handleContactSection}
                  className="w-full text-center text-xs text-cyan-400 hover:underline"
                >
                  Formulaire de contact complet
                </button>
              </div>
            </>
          )}
        </div>
      )}
    </>
  );
}
