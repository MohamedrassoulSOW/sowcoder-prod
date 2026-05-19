import { useMemo, useState, useEffect } from "react";
import { Send, CheckCircle, Loader2 } from "lucide-react";
import { api } from "../api/client";
import { useApp } from "../context/AppContext";
import { useSiteContent } from "../context/ContentContext";

const inputClass =
  "w-full rounded-xl border border-white/10 bg-slate-950/50 px-4 py-3 text-sm text-white placeholder:text-slate-500 transition focus:border-cyan-500/50 focus:outline-none focus:ring-1 focus:ring-cyan-500/30";

const DEFAULT_SUBJECTS = [
  "Devis projet web",
  "Marketing digital",
  "Formation",
  "Boutique / Pack",
  "Demande de devis — Services",
  "Catalogue des formations",
  "Autre",
];

export default function ContactForm() {
  const { user, contactPreset, clearContactPreset } = useApp();
  const { content } = useSiteContent();
  const [sent, setSent] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [form, setForm] = useState({
    name: "",
    email: "",
    phone: "",
    subject: "",
    message: "",
  });

  const subjectOptions = useMemo(() => {
    const options = new Set(DEFAULT_SUBJECTS);
    content.services?.forEach((s) => {
      if (s.title) options.add(s.title);
    });
    if (form.subject) options.add(form.subject);
    if (contactPreset?.subject) options.add(contactPreset.subject);
    return Array.from(options);
  }, [content.services, form.subject, contactPreset?.subject]);

  const useCustomSubject =
    form.subject !== "" && !DEFAULT_SUBJECTS.includes(form.subject);

  useEffect(() => {
    if (user) {
      setForm((prev) => ({
        ...prev,
        name: prev.name || user.name,
        email: prev.email || user.email,
      }));
    }
  }, [user]);

  useEffect(() => {
    if (contactPreset) {
      setForm((prev) => ({
        ...prev,
        name: user?.name || prev.name,
        email: user?.email || prev.email,
        subject: contactPreset.subject || prev.subject,
        message: contactPreset.message || prev.message,
      }));
      clearContactPreset();
    }
  }, [contactPreset, user, clearContactPreset]);

  const handleChange = (e) => {
    setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError("");

    try {
      await api.sendContact(form);
      setSent(true);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  const resetForm = () => {
    setSent(false);
    setForm({ name: "", email: "", phone: "", subject: "", message: "" });
  };

  if (sent) {
    return (
      <div className="flex flex-col items-center justify-center rounded-2xl border border-cyan-500/20 bg-cyan-500/5 p-10 text-center">
        <CheckCircle className="h-12 w-12 text-cyan-400" />
        <p className="mt-4 font-semibold text-white">Message envoyé</p>
        <p className="mt-2 text-sm text-slate-400">
          Merci pour votre message. Notre équipe vous répondra sous 24h.
        </p>
        <button
          type="button"
          onClick={resetForm}
          className="mt-4 text-sm text-cyan-400 hover:underline"
        >
          Envoyer un autre message
        </button>
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-4 text-left">
      <div className="grid gap-4 sm:grid-cols-2">
        <div>
          <label htmlFor="name" className="mb-1.5 block text-sm text-slate-400">
            Nom complet *
          </label>
          <input
            id="name"
            name="name"
            type="text"
            required
            value={form.name}
            onChange={handleChange}
            className={inputClass}
            placeholder="Votre nom"
          />
        </div>
        <div>
          <label htmlFor="email" className="mb-1.5 block text-sm text-slate-400">
            Email *
          </label>
          <input
            id="email"
            name="email"
            type="email"
            required
            value={form.email}
            onChange={handleChange}
            className={inputClass}
            placeholder="vous@exemple.com"
          />
        </div>
      </div>
      <div className="grid gap-4 sm:grid-cols-2">
        <div>
          <label htmlFor="phone" className="mb-1.5 block text-sm text-slate-400">
            Téléphone
          </label>
          <input
            id="phone"
            name="phone"
            type="tel"
            value={form.phone}
            onChange={handleChange}
            className={inputClass}
            placeholder="+221 77 000 00 00"
          />
        </div>
        <div>
          <label htmlFor="subject" className="mb-1.5 block text-sm text-slate-400">
            Sujet
          </label>
          {useCustomSubject ? (
            <input
              id="subject"
              name="subject"
              type="text"
              value={form.subject}
              onChange={handleChange}
              className={inputClass}
              placeholder="Sujet de votre demande"
            />
          ) : (
            <select
              id="subject"
              name="subject"
              value={form.subject}
              onChange={handleChange}
              className={inputClass}
            >
              <option value="">Choisir un sujet</option>
              {subjectOptions.map((opt) => (
                <option key={opt} value={opt}>
                  {opt}
                </option>
              ))}
            </select>
          )}
        </div>
      </div>
      <div>
        <label htmlFor="message" className="mb-1.5 block text-sm text-slate-400">
          Message *
        </label>
        <textarea
          id="message"
          name="message"
          required
          rows={4}
          value={form.message}
          onChange={handleChange}
          className={`${inputClass} resize-none`}
          placeholder="Décrivez votre projet ou votre besoin..."
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
        className="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 py-3.5 font-semibold text-white shadow-lg shadow-cyan-500/20 transition hover:opacity-90 disabled:opacity-60"
      >
        {loading ? (
          <Loader2 className="h-4 w-4 animate-spin" />
        ) : (
          <Send className="h-4 w-4" />
        )}
        {loading ? "Envoi en cours..." : "Envoyer le message"}
      </button>
    </form>
  );
}
