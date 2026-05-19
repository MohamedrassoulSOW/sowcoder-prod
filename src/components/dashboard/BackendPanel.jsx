import { useCallback, useEffect, useState } from "react";
import {
  Loader2,
  Trash2,
  Mail,
  Phone,
  Calendar,
  ChevronLeft,
  ChevronRight,
  RefreshCw,
} from "lucide-react";
import { api } from "../../api/client";

const TYPE_LABELS = {
  contacts: "Messages contact",
  orders: "Commandes & devis",
  inscriptions: "Inscriptions formations",
};

const PAGE_SIZE = 20;

function formatDate(iso) {
  try {
    return new Date(iso).toLocaleString("fr-FR", {
      dateStyle: "medium",
      timeStyle: "short",
    });
  } catch {
    return iso;
  }
}

export default function BackendPanel({ type }) {
  const [items, setItems] = useState([]);
  const [total, setTotal] = useState(0);
  const [offset, setOffset] = useState(0);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [selected, setSelected] = useState(null);
  const [deleting, setDeleting] = useState(null);

  const load = useCallback(async () => {
    setLoading(true);
    setError("");
    try {
      const res = await api.getAdminSubmissionsByType(type, {
        limit: PAGE_SIZE,
        offset,
      });
      setItems(res.data.items);
      setTotal(res.data.total);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }, [type, offset]);

  useEffect(() => {
    setOffset(0);
    setSelected(null);
  }, [type]);

  useEffect(() => {
    load();
  }, [load]);

  const handleDelete = async (id) => {
    if (!window.confirm("Supprimer définitivement cette entrée ?")) return;

    setDeleting(id);
    try {
      await api.deleteAdminSubmission(type, id);
      if (selected?.id === id) setSelected(null);
      await load();
    } catch (err) {
      setError(err.message);
    } finally {
      setDeleting(null);
    }
  };

  const page = Math.floor(offset / PAGE_SIZE) + 1;
  const totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));

  if (loading && !items.length) {
    return (
      <div className="flex justify-center py-16">
        <Loader2 className="h-8 w-8 animate-spin text-cyan-400" />
      </div>
    );
  }

  return (
    <div className="flex h-full min-h-[400px] flex-col gap-4 lg:flex-row">
      <div className="flex w-full flex-col lg:w-96 lg:shrink-0">
        <div className="mb-3 flex items-center justify-between">
          <p className="text-sm text-slate-400">
            {total} {TYPE_LABELS[type]?.toLowerCase() || "entrées"}
          </p>
          <button
            type="button"
            onClick={load}
            className="rounded-lg p-2 text-slate-400 hover:bg-white/5 hover:text-white"
            title="Actualiser"
          >
            <RefreshCw className="h-4 w-4" />
          </button>
        </div>

        {error && (
          <p className="mb-3 rounded-lg border border-red-500/30 bg-red-500/10 px-3 py-2 text-sm text-red-300">
            {error}
          </p>
        )}

        <ul className="max-h-[50vh] flex-1 space-y-2 overflow-y-auto lg:max-h-[calc(90vh-12rem)]">
          {items.length === 0 ? (
            <li className="rounded-lg border border-white/5 bg-slate-900/40 px-4 py-8 text-center text-sm text-slate-500">
              Aucune donnée pour le moment
            </li>
          ) : (
            items.map((item) => (
              <li key={item.id}>
                <button
                  type="button"
                  onClick={() => setSelected(item)}
                  className={`w-full rounded-lg border px-3 py-3 text-left transition ${
                    selected?.id === item.id
                      ? "border-cyan-500/40 bg-cyan-500/10"
                      : "border-white/5 bg-slate-900/60 hover:border-white/10"
                  }`}
                >
                  <p className="truncate text-sm font-medium text-white">
                    {item.name}
                  </p>
                  <p className="mt-0.5 truncate text-xs text-slate-500">
                    {item.email}
                  </p>
                  <p className="mt-1 text-xs text-slate-600">
                    {formatDate(item.createdAt)}
                  </p>
                </button>
              </li>
            ))
          )}
        </ul>

        {totalPages > 1 && (
          <div className="mt-3 flex items-center justify-between gap-2">
            <button
              type="button"
              disabled={offset === 0}
              onClick={() => setOffset((o) => Math.max(0, o - PAGE_SIZE))}
              className="flex items-center gap-1 rounded-lg border border-white/10 px-3 py-1.5 text-xs text-slate-400 disabled:opacity-40"
            >
              <ChevronLeft className="h-4 w-4" />
              Préc.
            </button>
            <span className="text-xs text-slate-500">
              {page} / {totalPages}
            </span>
            <button
              type="button"
              disabled={offset + PAGE_SIZE >= total}
              onClick={() => setOffset((o) => o + PAGE_SIZE)}
              className="flex items-center gap-1 rounded-lg border border-white/10 px-3 py-1.5 text-xs text-slate-400 disabled:opacity-40"
            >
              Suiv.
              <ChevronRight className="h-4 w-4" />
            </button>
          </div>
        )}
      </div>

      <div className="min-w-0 flex-1 rounded-xl border border-white/5 bg-slate-900/40 p-5">
        {!selected ? (
          <p className="py-12 text-center text-sm text-slate-500">
            Sélectionnez une entrée pour voir le détail
          </p>
        ) : (
          <SubmissionDetail
            type={type}
            item={selected}
            deleting={deleting === selected.id}
            onDelete={() => handleDelete(selected.id)}
          />
        )}
      </div>
    </div>
  );
}

function SubmissionDetail({ type, item, deleting, onDelete }) {
  return (
    <div>
      <div className="flex items-start justify-between gap-4">
        <div>
          <h3 className="text-lg font-semibold text-white">{item.name}</h3>
          <p className="mt-1 flex items-center gap-1 text-sm text-slate-400">
            <Calendar className="h-3.5 w-3.5" />
            {formatDate(item.createdAt)}
          </p>
        </div>
        <button
          type="button"
          onClick={onDelete}
          disabled={deleting}
          className="flex items-center gap-1.5 rounded-lg border border-red-500/30 bg-red-500/10 px-3 py-2 text-sm text-red-300 hover:bg-red-500/20 disabled:opacity-50"
        >
          {deleting ? (
            <Loader2 className="h-4 w-4 animate-spin" />
          ) : (
            <Trash2 className="h-4 w-4" />
          )}
          Supprimer
        </button>
      </div>

      <dl className="mt-6 space-y-4">
        <DetailRow icon={Mail} label="Email">
          <a href={`mailto:${item.email}`} className="text-cyan-400 hover:underline">
            {item.email}
          </a>
        </DetailRow>

        {item.phone && (
          <DetailRow icon={Phone} label="Téléphone">
            <a
              href={`tel:${item.phone.replace(/\s/g, "")}`}
              className="text-cyan-400 hover:underline"
            >
              {item.phone}
            </a>
          </DetailRow>
        )}

        {type === "contacts" && item.subject && (
          <DetailRow label="Sujet">{item.subject}</DetailRow>
        )}

        {type === "orders" && (
          <>
            <DetailRow label="Produit">{item.productTitle}</DetailRow>
            {item.type && (
              <DetailRow label="Type">
                {item.type === "cart_order" ? "Panier" : "Commande"}
              </DetailRow>
            )}
          </>
        )}

        {type === "inscriptions" && (
          <DetailRow label="Formation">{item.formationTitle}</DetailRow>
        )}

        {item.message && (
          <DetailRow label="Message">
            <p className="whitespace-pre-wrap rounded-lg bg-slate-950/60 p-3 text-sm text-slate-300">
              {item.message}
            </p>
          </DetailRow>
        )}
      </dl>
    </div>
  );
}

function DetailRow({ icon: Icon, label, children }) {
  return (
    <div>
      <dt className="flex items-center gap-1.5 text-xs font-medium uppercase tracking-wide text-slate-500">
        {Icon && <Icon className="h-3.5 w-3.5" />}
        {label}
      </dt>
      <dd className="mt-1 text-sm text-slate-200">{children}</dd>
    </div>
  );
}
