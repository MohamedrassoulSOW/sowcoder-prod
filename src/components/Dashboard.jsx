import { useCallback, useEffect, useState } from "react";
import {
  X,
  LayoutDashboard,
  Loader2,
  Save,
  Plus,
  Trash2,
  BarChart3,
  FileText,
  Inbox,
  ShoppingBag,
  GraduationCap,
  Database,
} from "lucide-react";
import { api } from "../api/client";
import { useApp } from "../context/AppContext";
import { useSiteContent } from "../context/ContentContext";
import ImageUpload from "./ImageUpload";
import BackendPanel from "./dashboard/BackendPanel";

const BACKEND_SECTIONS = [
  { id: "overview", label: "Vue d'ensemble", icon: BarChart3 },
  { id: "contacts", label: "Messages contact", icon: Inbox },
  { id: "orders", label: "Commandes", icon: ShoppingBag },
  { id: "inscriptions", label: "Inscriptions", icon: GraduationCap },
];

const CONTENT_SECTIONS = [
  { id: "hero", label: "Accueil", icon: FileText },
  { id: "stats", label: "Statistiques", icon: FileText },
  { id: "services", label: "Services", icon: FileText },
  { id: "projects", label: "Projets réalisés", icon: FileText },
  { id: "formations", label: "Formations", icon: FileText },
  { id: "whyUs", label: "Pourquoi nous", icon: FileText },
  { id: "testimonials", label: "Témoignages", icon: FileText },
  { id: "blogPosts", label: "Blog", icon: FileText },
  { id: "boutiqueProducts", label: "Boutique", icon: FileText },
  { id: "navLinks", label: "Navigation", icon: FileText },
  { id: "footerLinks", label: "Pied de page", icon: FileText },
  { id: "socialLinks", label: "Réseaux sociaux", icon: FileText },
  { id: "legalPages", label: "Pages légales", icon: FileText },
];

const BACKEND_IDS = new Set(BACKEND_SECTIONS.map((s) => s.id));

const inputClass =
  "w-full rounded-lg border border-white/10 bg-slate-950/80 px-3 py-2 text-sm text-white placeholder:text-slate-600 focus:border-cyan-500/50 focus:outline-none";

function Field({ label, children }) {
  return (
    <label className="block">
      <span className="mb-1 block text-xs font-medium text-slate-400">
        {label}
      </span>
      {children}
    </label>
  );
}

function ImageField({ value, onChange, label = "Photo" }) {
  return (
    <ImageUpload
      label={label}
      value={value || ""}
      onChange={onChange}
    />
  );
}

function slugify(text) {
  return text
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-|-$/g, "");
}

function Card({ title, onRemove, children }) {
  return (
    <div className="rounded-xl border border-white/5 bg-slate-900/60 p-4">
      <div className="mb-3 flex items-center justify-between gap-2">
        <h3 className="text-sm font-semibold text-slate-200">{title}</h3>
        {onRemove && (
          <button
            type="button"
            onClick={onRemove}
            className="rounded-lg p-1.5 text-red-400 hover:bg-red-500/10"
            title="Supprimer"
          >
            <Trash2 className="h-4 w-4" />
          </button>
        )}
      </div>
      <div className="space-y-3">{children}</div>
    </div>
  );
}

export default function Dashboard() {
  const { user, adminOpen, closeAdmin } = useApp();
  const { content, applyContent, refreshContent } = useSiteContent();
  const [active, setActive] = useState("overview");
  const [draft, setDraft] = useState(content);
  const [stats, setStats] = useState(null);
  const [submissions, setSubmissions] = useState(null);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");

  useEffect(() => {
    setDraft(content);
  }, [content]);

  useEffect(() => {
    if (!adminOpen || user?.role !== "admin") return;

    setLoading(true);
    setError("");
    Promise.all([
      api.getAdminStats(),
      api.getAdminSubmissions(),
      api.getAdminContent(),
    ])
      .then(([statsRes, subsRes, contentRes]) => {
        setStats(statsRes.data);
        setSubmissions(subsRes.data);
        if (contentRes.data) {
          setDraft(contentRes.data);
          applyContent(contentRes.data);
        }
      })
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, [adminOpen, user?.role, applyContent]);

  const updateDraft = useCallback((updater) => {
    setDraft((prev) =>
      typeof updater === "function" ? updater(prev) : updater
    );
    setSuccess("");
  }, []);

  const handleSave = async () => {
    setSaving(true);
    setError("");
    setSuccess("");
    try {
      const res = await api.updateAdminContent(draft);
      applyContent(res.data);
      setDraft(res.data);
      await refreshContent();
      setSuccess("Contenu enregistré avec succès.");
    } catch (err) {
      setError(err.message);
    } finally {
      setSaving(false);
    }
  };

  if (!adminOpen || user?.role !== "admin") return null;

  const isContentSection = !BACKEND_IDS.has(active);

  return (
    <div className="fixed inset-0 z-[80] flex bg-slate-950">
      <aside className="flex w-64 shrink-0 flex-col border-r border-white/5 bg-slate-900/80">
        <div className="flex items-center gap-2 border-b border-white/5 px-4 py-4">
          <LayoutDashboard className="h-5 w-5 text-violet-400" />
          <span className="font-semibold text-white">Dashboard</span>
        </div>
        <nav className="flex-1 overflow-y-auto p-2">
          <p className="mb-1 flex items-center gap-1.5 px-3 py-1 text-[10px] font-semibold uppercase tracking-widest text-slate-600">
            <Database className="h-3 w-3" />
            Backend
          </p>
          {BACKEND_SECTIONS.map(({ id, label, icon: Icon }) => (
            <SidebarLink
              key={id}
              id={id}
              label={label}
              icon={Icon}
              active={active}
              onSelect={setActive}
            />
          ))}
          <p className="mb-1 mt-4 flex items-center gap-1.5 px-3 py-1 text-[10px] font-semibold uppercase tracking-widest text-slate-600">
            <FileText className="h-3 w-3" />
            Contenu du site
          </p>
          {CONTENT_SECTIONS.map(({ id, label, icon: Icon }) => (
            <SidebarLink
              key={id}
              id={id}
              label={label}
              icon={Icon}
              active={active}
              onSelect={setActive}
            />
          ))}
        </nav>
        <div className="border-t border-white/5 p-3">
          <button
            type="button"
            onClick={closeAdmin}
            className="flex w-full items-center justify-center gap-2 rounded-lg border border-white/10 py-2 text-sm text-slate-400 hover:bg-white/5"
          >
            <X className="h-4 w-4" />
            Fermer
          </button>
        </div>
      </aside>

      <div className="flex min-w-0 flex-1 flex-col">
        <header className="flex items-center justify-between gap-4 border-b border-white/5 bg-slate-900/50 px-6 py-4">
          <div>
            <h1 className="text-lg font-semibold text-white">
              {[...BACKEND_SECTIONS, ...CONTENT_SECTIONS].find(
                (s) => s.id === active
              )?.label}
            </h1>
            <p className="text-xs text-slate-500">
              Connecté en tant que {user.email}
            </p>
          </div>
          {isContentSection && (
            <button
              type="button"
              onClick={handleSave}
              disabled={saving || loading}
              className="flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 px-5 py-2.5 text-sm font-semibold text-white disabled:opacity-50"
            >
              {saving ? (
                <Loader2 className="h-4 w-4 animate-spin" />
              ) : (
                <Save className="h-4 w-4" />
              )}
              Enregistrer
            </button>
          )}
        </header>

        <div className="flex-1 overflow-y-auto p-6">
          {error && (
            <p className="mb-4 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
              {error}
            </p>
          )}
          {success && (
            <p className="mb-4 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
              {success}
            </p>
          )}

          {loading ? (
            <div className="flex justify-center py-20">
              <Loader2 className="h-10 w-10 animate-spin text-cyan-400" />
            </div>
          ) : active === "overview" ? (
            <Overview stats={stats} submissions={submissions} />
          ) : ["contacts", "orders", "inscriptions"].includes(active) ? (
            <BackendPanel type={active} />
          ) : (
            <SectionEditor
              section={active}
              draft={draft}
              updateDraft={updateDraft}
            />
          )}
        </div>
      </div>
    </div>
  );
}

function SidebarLink({ id, label, icon: Icon, active, onSelect }) {
  return (
    <button
      type="button"
      onClick={() => onSelect(id)}
      className={`mb-0.5 flex w-full items-center gap-2 rounded-lg px-3 py-2.5 text-left text-sm transition ${
        active === id
          ? "bg-violet-500/20 text-violet-200"
          : "text-slate-400 hover:bg-white/5 hover:text-white"
      }`}
    >
      <Icon className="h-4 w-4 shrink-0" />
      {label}
    </button>
  );
}

function Overview({ stats, submissions }) {
  return (
    <div className="max-w-3xl space-y-6">
      <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
        {[
          { label: "Contacts", value: stats?.contacts },
          { label: "Commandes", value: stats?.orders },
          { label: "Inscriptions", value: stats?.inscriptions },
          { label: "Utilisateurs", value: stats?.users },
        ].map(({ label, value }) => (
          <div
            key={label}
            className="rounded-xl border border-white/5 bg-slate-900/60 p-4 text-center"
          >
            <p className="font-mono text-2xl font-bold text-cyan-400">
              {value ?? 0}
            </p>
            <p className="mt-1 text-xs text-slate-500">{label}</p>
          </div>
        ))}
      </div>
      <SubmissionList
        title="Derniers contacts"
        items={submissions?.contacts?.slice(0, 5)}
        render={(c) => `${c.name} — ${c.email}`}
      />
      <SubmissionList
        title="Dernières commandes"
        items={submissions?.orders?.slice(0, 5)}
        render={(o) => `${o.productTitle} — ${o.name}`}
      />
      <SubmissionList
        title="Dernières inscriptions"
        items={submissions?.inscriptions?.slice(0, 5)}
        render={(i) => `${i.formationTitle} — ${i.name}`}
      />
      <p className="text-xs text-slate-600">
        Ouvrez « Messages contact », « Commandes » ou « Inscriptions » pour tout
        gérer depuis le backend.
      </p>
    </div>
  );
}

function SubmissionList({ title, items, render }) {
  if (!items?.length) {
    return (
      <div>
        <h3 className="text-sm font-semibold text-slate-300">{title}</h3>
        <p className="mt-2 text-xs text-slate-500">Aucune donnée</p>
      </div>
    );
  }
  return (
    <div>
      <h3 className="text-sm font-semibold text-slate-300">{title}</h3>
      <ul className="mt-2 space-y-1.5">
        {items.map((item) => (
          <li
            key={item.id}
            className="rounded-lg bg-slate-900/60 px-3 py-2 text-xs text-slate-400"
          >
            {render(item)}
          </li>
        ))}
      </ul>
    </div>
  );
}

function SectionEditor({ section, draft, updateDraft }) {
  const patch = (path, value) => {
    updateDraft((prev) => {
      const next = structuredClone(prev);
      const keys = path.split(".");
      let cur = next;
      for (let i = 0; i < keys.length - 1; i++) cur = cur[keys[i]];
      cur[keys[keys.length - 1]] = value;
      return next;
    });
  };

  const patchArray = (key, index, field, value) => {
    updateDraft((prev) => {
      const next = structuredClone(prev);
      next[key][index] = { ...next[key][index], [field]: value };
      return next;
    });
  };

  const removeFromArray = (key, index) => {
    updateDraft((prev) => {
      const next = structuredClone(prev);
      next[key] = next[key].filter((_, i) => i !== index);
      return next;
    });
  };

  const addToArray = (key, item) => {
    updateDraft((prev) => ({
      ...prev,
      [key]: [...(prev[key] || []), item],
    }));
  };

  switch (section) {
    case "hero":
      return (
        <div className="max-w-xl space-y-4">
          <ImageField
            label="Image de fond (optionnelle)"
            value={draft.hero.image}
            onChange={(url) =>
              patch("hero", { ...draft.hero, image: url })
            }
          />
          {Object.entries(draft.hero)
            .filter(([key]) => key !== "image")
            .map(([key, val]) => (
            <Field key={key} label={key}>
              {key === "subtitle" ? (
                <textarea
                  className={`${inputClass} min-h-[100px]`}
                  value={val}
                  onChange={(e) =>
                    patch("hero", { ...draft.hero, [key]: e.target.value })
                  }
                />
              ) : (
                <input
                  className={inputClass}
                  value={val}
                  onChange={(e) =>
                    patch("hero", { ...draft.hero, [key]: e.target.value })
                  }
                />
              )}
            </Field>
          ))}
        </div>
      );

    case "stats":
      return (
        <ListSection
          items={draft.stats}
          onAdd={() => addToArray("stats", { value: "0", label: "Nouveau" })}
          renderItem={(item, i) => (
            <Card
              key={i}
              title={`Stat ${i + 1}`}
              onRemove={() => removeFromArray("stats", i)}
            >
              <Field label="Valeur">
                <input
                  className={inputClass}
                  value={item.value}
                  onChange={(e) =>
                    patchArray("stats", i, "value", e.target.value)
                  }
                />
              </Field>
              <Field label="Libellé">
                <input
                  className={inputClass}
                  value={item.label}
                  onChange={(e) =>
                    patchArray("stats", i, "label", e.target.value)
                  }
                />
              </Field>
            </Card>
          )}
        />
      );

    case "services":
      return (
        <ListSection
          items={draft.services}
          onAdd={() =>
            addToArray("services", {
              title: "Nouveau service",
              description: "Description",
              icon: "code",
              image: "",
            })
          }
          renderItem={(item, i) => (
            <Card
              key={i}
              title={item.title || `Service ${i + 1}`}
              onRemove={() => removeFromArray("services", i)}
            >
              <Field label="Titre">
                <input
                  className={inputClass}
                  value={item.title}
                  onChange={(e) =>
                    patchArray("services", i, "title", e.target.value)
                  }
                />
              </Field>
              <Field label="Description">
                <textarea
                  className={`${inputClass} min-h-[80px]`}
                  value={item.description}
                  onChange={(e) =>
                    patchArray("services", i, "description", e.target.value)
                  }
                />
              </Field>
              <Field label="Icône (code, megaphone, palette…)">
                <input
                  className={inputClass}
                  value={item.icon}
                  onChange={(e) =>
                    patchArray("services", i, "icon", e.target.value)
                  }
                />
              </Field>
              <ImageField
                value={item.image}
                onChange={(url) => patchArray("services", i, "image", url)}
              />
            </Card>
          )}
        />
      );

    case "formations":
      return (
        <ListSection
          items={draft.formations}
          onAdd={() =>
            addToArray("formations", {
              title: "Nouvelle formation",
              duration: "4 semaines",
              level: "Débutant",
              topics: ["Sujet 1"],
              image: "",
            })
          }
          renderItem={(item, i) => (
            <Card
              key={i}
              title={item.title}
              onRemove={() => removeFromArray("formations", i)}
            >
              <Field label="Titre">
                <input
                  className={inputClass}
                  value={item.title}
                  onChange={(e) =>
                    patchArray("formations", i, "title", e.target.value)
                  }
                />
              </Field>
              <Field label="Durée">
                <input
                  className={inputClass}
                  value={item.duration}
                  onChange={(e) =>
                    patchArray("formations", i, "duration", e.target.value)
                  }
                />
              </Field>
              <Field label="Niveau">
                <input
                  className={inputClass}
                  value={item.level}
                  onChange={(e) =>
                    patchArray("formations", i, "level", e.target.value)
                  }
                />
              </Field>
              <Field label="Sujets (séparés par des virgules)">
                <input
                  className={inputClass}
                  value={item.topics.join(", ")}
                  onChange={(e) =>
                    patchArray(
                      "formations",
                      i,
                      "topics",
                      e.target.value.split(",").map((t) => t.trim()).filter(Boolean)
                    )
                  }
                />
              </Field>
              <ImageField
                value={item.image}
                onChange={(url) => patchArray("formations", i, "image", url)}
              />
            </Card>
          )}
        />
      );

    case "whyUs":
      return (
        <div className="max-w-2xl space-y-6">
          <div>
            <div className="mb-3 flex items-center justify-between">
              <h3 className="text-sm font-semibold text-slate-300">
                Points forts
              </h3>
              <AddButton
                onClick={() =>
                  updateDraft((prev) => ({
                    ...prev,
                    whyUs: {
                      ...prev.whyUs,
                      bullets: [...prev.whyUs.bullets, "Nouveau point"],
                    },
                  }))
                }
              />
            </div>
            {draft.whyUs.bullets.map((bullet, i) => (
              <div key={i} className="mb-2 flex gap-2">
                <input
                  className={inputClass}
                  value={bullet}
                  onChange={(e) =>
                    updateDraft((prev) => {
                      const bullets = [...prev.whyUs.bullets];
                      bullets[i] = e.target.value;
                      return { ...prev, whyUs: { ...prev.whyUs, bullets } };
                    })
                  }
                />
                <button
                  type="button"
                  onClick={() =>
                    updateDraft((prev) => ({
                      ...prev,
                      whyUs: {
                        ...prev.whyUs,
                        bullets: prev.whyUs.bullets.filter((_, j) => j !== i),
                      },
                    }))
                  }
                  className="shrink-0 rounded-lg p-2 text-red-400 hover:bg-red-500/10"
                >
                  <Trash2 className="h-4 w-4" />
                </button>
              </div>
            ))}
          </div>
          <ListSection
            items={draft.whyUs.features}
            onAdd={() =>
              updateDraft((prev) => ({
                ...prev,
                whyUs: {
                  ...prev.whyUs,
                  features: [
                    ...prev.whyUs.features,
                    {
                      title: "Nouvelle fonctionnalité",
                      description: "Description",
                      icon: "zap",
                    },
                  ],
                },
              }))
            }
            renderItem={(item, i) => (
              <Card
                key={i}
                title={item.title}
                onRemove={() =>
                  updateDraft((prev) => ({
                    ...prev,
                    whyUs: {
                      ...prev.whyUs,
                      features: prev.whyUs.features.filter((_, j) => j !== i),
                    },
                  }))
                }
              >
                <Field label="Titre">
                  <input
                    className={inputClass}
                    value={item.title}
                    onChange={(e) =>
                      updateDraft((prev) => {
                        const features = [...prev.whyUs.features];
                        features[i] = { ...features[i], title: e.target.value };
                        return { ...prev, whyUs: { ...prev.whyUs, features } };
                      })
                    }
                  />
                </Field>
                <Field label="Description">
                  <textarea
                    className={`${inputClass} min-h-[80px]`}
                    value={item.description}
                    onChange={(e) =>
                      updateDraft((prev) => {
                        const features = [...prev.whyUs.features];
                        features[i] = {
                          ...features[i],
                          description: e.target.value,
                        };
                        return { ...prev, whyUs: { ...prev.whyUs, features } };
                      })
                    }
                  />
                </Field>
                <Field label="Icône">
                  <input
                    className={inputClass}
                    value={item.icon}
                    onChange={(e) =>
                      updateDraft((prev) => {
                        const features = [...prev.whyUs.features];
                        features[i] = { ...features[i], icon: e.target.value };
                        return { ...prev, whyUs: { ...prev.whyUs, features } };
                      })
                    }
                  />
                </Field>
              </Card>
            )}
          />
        </div>
      );

    case "testimonials":
      return (
        <ListSection
          items={draft.testimonials}
          onAdd={() =>
            addToArray("testimonials", {
              text: "Témoignage",
              name: "Nom",
              role: "Entreprise",
              initial: "N",
              image: "",
            })
          }
          renderItem={(item, i) => (
            <Card
              key={i}
              title={item.name}
              onRemove={() => removeFromArray("testimonials", i)}
            >
              {["text", "name", "role", "initial"].map((field) => (
                <Field key={field} label={field}>
                  <input
                    className={inputClass}
                    value={item[field]}
                    onChange={(e) =>
                      patchArray("testimonials", i, field, e.target.value)
                    }
                  />
                </Field>
              ))}
              <ImageField
                label="Photo du client"
                value={item.image}
                onChange={(url) => patchArray("testimonials", i, "image", url)}
              />
            </Card>
          )}
        />
      );

    case "blogPosts":
      return (
        <ListSection
          items={draft.blogPosts}
          onAdd={() =>
            addToArray("blogPosts", {
              slug: "nouvel-article",
              title: "Nouvel article",
              excerpt: "Résumé",
              category: "Actualité",
              date: new Date().toLocaleDateString("fr-FR"),
              readTime: "5 min",
              body: ["Paragraphe 1"],
              image: "",
            })
          }
          renderItem={(item, i) => (
            <Card
              key={i}
              title={item.title}
              onRemove={() => removeFromArray("blogPosts", i)}
            >
              <ImageField
                label="Image de couverture"
                value={item.image}
                onChange={(url) => patchArray("blogPosts", i, "image", url)}
              />
              <Field label="Titre">
                <input
                  className={inputClass}
                  value={item.title}
                  onChange={(e) => {
                    const title = e.target.value;
                    updateDraft((prev) => {
                      const blogPosts = [...prev.blogPosts];
                      blogPosts[i] = {
                        ...blogPosts[i],
                        title,
                        slug: blogPosts[i].slug || slugify(title),
                      };
                      return { ...prev, blogPosts };
                    });
                  }}
                />
              </Field>
              <Field label="Slug (URL)">
                <input
                  className={inputClass}
                  value={item.slug}
                  onChange={(e) =>
                    patchArray("blogPosts", i, "slug", e.target.value)
                  }
                />
              </Field>
              <Field label="Extrait">
                <textarea
                  className={`${inputClass} min-h-[60px]`}
                  value={item.excerpt}
                  onChange={(e) =>
                    patchArray("blogPosts", i, "excerpt", e.target.value)
                  }
                />
              </Field>
              {["category", "date", "readTime"].map((field) => (
                <Field key={field} label={field}>
                  <input
                    className={inputClass}
                    value={item[field]}
                    onChange={(e) =>
                      patchArray("blogPosts", i, field, e.target.value)
                    }
                  />
                </Field>
              ))}
              <Field label="Paragraphes (un par ligne)">
                <textarea
                  className={`${inputClass} min-h-[120px]`}
                  value={item.body.join("\n")}
                  onChange={(e) =>
                    patchArray(
                      "blogPosts",
                      i,
                      "body",
                      e.target.value.split("\n").filter(Boolean)
                    )
                  }
                />
              </Field>
            </Card>
          )}
        />
      );

    case "projects":
      return (
        <ListSection
          items={draft.projects ?? []}
          onAdd={() =>
            addToArray("projects", {
              title: "Nouveau projet",
              description: "Description du projet réalisé",
              category: "Web",
              client: "",
              year: String(new Date().getFullYear()),
              url: "",
              technologies: ["React"],
              image: "",
            })
          }
          renderItem={(item, i) => (
            <Card
              key={i}
              title={item.title}
              onRemove={() => removeFromArray("projects", i)}
            >
              <ImageField
                label="Capture / visuel du projet"
                value={item.image}
                onChange={(url) => patchArray("projects", i, "image", url)}
              />
              {["title", "category", "client", "year", "url"].map((field) => (
                <Field
                  key={field}
                  label={
                    field === "url"
                      ? "Lien du projet (optionnel)"
                      : field === "year"
                        ? "Année"
                        : field === "client"
                          ? "Client (optionnel)"
                          : field === "category"
                            ? "Catégorie"
                            : "Titre"
                  }
                >
                  <input
                    className={inputClass}
                    value={item[field] ?? ""}
                    onChange={(e) =>
                      patchArray("projects", i, field, e.target.value)
                    }
                    placeholder={
                      field === "url" ? "https://exemple.com" : undefined
                    }
                  />
                </Field>
              ))}
              <Field label="Description">
                <textarea
                  className={`${inputClass} min-h-[80px]`}
                  value={item.description}
                  onChange={(e) =>
                    patchArray("projects", i, "description", e.target.value)
                  }
                />
              </Field>
              <Field label="Technologies (une par ligne)">
                <textarea
                  className={`${inputClass} min-h-[60px]`}
                  value={(item.technologies ?? []).join("\n")}
                  onChange={(e) =>
                    patchArray(
                      "projects",
                      i,
                      "technologies",
                      e.target.value.split("\n").filter(Boolean)
                    )
                  }
                  placeholder={"React\nSymfony\nMySQL"}
                />
              </Field>
            </Card>
          )}
        />
      );

    case "boutiqueProducts":
      return (
        <ListSection
          items={draft.boutiqueProducts}
          onAdd={() =>
            addToArray("boutiqueProducts", {
              title: "Nouveau produit",
              price: "0 FCFA",
              description: "Description",
              tag: "Web",
              image: "",
            })
          }
          renderItem={(item, i) => (
            <Card
              key={i}
              title={item.title}
              onRemove={() => removeFromArray("boutiqueProducts", i)}
            >
              <ImageField
                value={item.image}
                onChange={(url) =>
                  patchArray("boutiqueProducts", i, "image", url)
                }
              />
              {["title", "price", "description", "tag"].map((field) => (
                <Field key={field} label={field}>
                  <input
                    className={inputClass}
                    value={item[field]}
                    onChange={(e) =>
                      patchArray("boutiqueProducts", i, field, e.target.value)
                    }
                  />
                </Field>
              ))}
            </Card>
          )}
        />
      );

    case "navLinks":
      return (
        <ListSection
          items={draft.navLinks}
          onAdd={() =>
            addToArray("navLinks", { label: "Lien", href: "#section" })
          }
          renderItem={(item, i) => (
            <Card
              key={i}
              title={item.label}
              onRemove={() => removeFromArray("navLinks", i)}
            >
              <Field label="Libellé">
                <input
                  className={inputClass}
                  value={item.label}
                  onChange={(e) =>
                    patchArray("navLinks", i, "label", e.target.value)
                  }
                />
              </Field>
              <Field label="Ancre (#services…)">
                <input
                  className={inputClass}
                  value={item.href}
                  onChange={(e) =>
                    patchArray("navLinks", i, "href", e.target.value)
                  }
                />
              </Field>
            </Card>
          )}
        />
      );

    case "footerLinks":
      return (
        <div className="max-w-xl space-y-6">
          <h3 className="text-sm font-semibold text-slate-300">Contact</h3>
          <Field label="Adresse">
            <input
              className={inputClass}
              value={draft.footerLinks.contact.address}
              onChange={(e) =>
                updateDraft((prev) => ({
                  ...prev,
                  footerLinks: {
                    ...prev.footerLinks,
                    contact: {
                      ...prev.footerLinks.contact,
                      address: e.target.value,
                    },
                  },
                }))
              }
            />
          </Field>
          <Field label="Email">
            <input
              className={inputClass}
              value={draft.footerLinks.contact.email}
              onChange={(e) =>
                updateDraft((prev) => ({
                  ...prev,
                  footerLinks: {
                    ...prev.footerLinks,
                    contact: {
                      ...prev.footerLinks.contact,
                      email: e.target.value,
                    },
                  },
                }))
              }
            />
          </Field>
          {draft.footerLinks.contact.phones.map((phone, i) => (
            <Card
              key={i}
              title={`Téléphone ${i + 1}`}
              onRemove={() =>
                updateDraft((prev) => ({
                  ...prev,
                  footerLinks: {
                    ...prev.footerLinks,
                    contact: {
                      ...prev.footerLinks.contact,
                      phones: prev.footerLinks.contact.phones.filter(
                        (_, j) => j !== i
                      ),
                    },
                  },
                }))
              }
            >
              <Field label="Pays / libellé">
                <input
                  className={inputClass}
                  value={phone.label}
                  onChange={(e) =>
                    updateDraft((prev) => {
                      const phones = [...prev.footerLinks.contact.phones];
                      phones[i] = { ...phones[i], label: e.target.value };
                      return {
                        ...prev,
                        footerLinks: {
                          ...prev.footerLinks,
                          contact: { ...prev.footerLinks.contact, phones },
                        },
                      };
                    })
                  }
                />
              </Field>
              <Field label="Numéro">
                <input
                  className={inputClass}
                  value={phone.number}
                  onChange={(e) =>
                    updateDraft((prev) => {
                      const phones = [...prev.footerLinks.contact.phones];
                      phones[i] = { ...phones[i], number: e.target.value };
                      return {
                        ...prev,
                        footerLinks: {
                          ...prev.footerLinks,
                          contact: { ...prev.footerLinks.contact, phones },
                        },
                      };
                    })
                  }
                />
              </Field>
            </Card>
          ))}
          <AddButton
            label="Ajouter un téléphone"
            onClick={() =>
              updateDraft((prev) => ({
                ...prev,
                footerLinks: {
                  ...prev.footerLinks,
                  contact: {
                    ...prev.footerLinks.contact,
                    phones: [
                      ...prev.footerLinks.contact.phones,
                      { label: "Pays", number: "+221" },
                    ],
                  },
                },
              }))
            }
          />
        </div>
      );

    case "socialLinks":
      return (
        <ListSection
          items={draft.socialLinks}
          onAdd={() =>
            addToArray("socialLinks", {
              label: "Réseau",
              href: "https://",
            })
          }
          renderItem={(item, i) => (
            <Card
              key={i}
              title={item.label}
              onRemove={() => removeFromArray("socialLinks", i)}
            >
              <Field label="Libellé">
                <input
                  className={inputClass}
                  value={item.label}
                  onChange={(e) =>
                    patchArray("socialLinks", i, "label", e.target.value)
                  }
                />
              </Field>
              <Field label="URL">
                <input
                  className={inputClass}
                  value={item.href}
                  onChange={(e) =>
                    patchArray("socialLinks", i, "href", e.target.value)
                  }
                />
              </Field>
            </Card>
          )}
        />
      );

    case "legalPages":
      return (
        <div className="max-w-xl space-y-8">
          {["mentions", "privacy"].map((key) => (
            <div key={key}>
              <h3 className="mb-3 text-sm font-semibold text-slate-300">
                {draft.legalPages[key].title}
              </h3>
              <Field label="Titre de la page">
                <input
                  className={inputClass}
                  value={draft.legalPages[key].title}
                  onChange={(e) =>
                    updateDraft((prev) => ({
                      ...prev,
                      legalPages: {
                        ...prev.legalPages,
                        [key]: {
                          ...prev.legalPages[key],
                          title: e.target.value,
                        },
                      },
                    }))
                  }
                />
              </Field>
              <Field label="Paragraphes (un par ligne)">
                <textarea
                  className={`${inputClass} mt-2 min-h-[140px]`}
                  value={draft.legalPages[key].content.join("\n")}
                  onChange={(e) =>
                    updateDraft((prev) => ({
                      ...prev,
                      legalPages: {
                        ...prev.legalPages,
                        [key]: {
                          ...prev.legalPages[key],
                          content: e.target.value
                            .split("\n")
                            .filter(Boolean),
                        },
                      },
                    }))
                  }
                />
              </Field>
            </div>
          ))}
        </div>
      );

    default:
      return null;
  }
}

function ListSection({ items, onAdd, renderItem }) {
  return (
    <div className="max-w-2xl space-y-4">
      <AddButton onClick={onAdd} />
      {items.map((item, i) => renderItem(item, i))}
    </div>
  );
}

function AddButton({ onClick, label = "Ajouter" }) {
  return (
    <button
      type="button"
      onClick={onClick}
      className="flex items-center gap-2 rounded-lg border border-dashed border-cyan-500/30 px-4 py-2 text-sm text-cyan-400 hover:bg-cyan-500/5"
    >
      <Plus className="h-4 w-4" />
      {label}
    </button>
  );
}
