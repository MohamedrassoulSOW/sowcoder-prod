import { useState } from "react";
import { ShoppingBag, ArrowRight, Plus } from "lucide-react";
import { useSiteContent } from "../context/ContentContext";
import ContentImage from "./ContentImage";
import { useApp } from "../context/AppContext";
import RequestModal from "./RequestModal";

export default function Boutique() {
  const { addToCart } = useApp();
  const { content } = useSiteContent();
  const { boutiqueProducts } = content;
  const [modal, setModal] = useState({ open: false, title: "" });

  return (
    <section id="boutique" className="relative py-24">
      <div className="absolute inset-0 bg-gradient-to-b from-transparent via-violet-950/10 to-transparent" />
      <div className="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="mx-auto max-w-2xl text-center">
          <span className="text-sm font-semibold uppercase tracking-widest text-violet-400">
            BOUTIQUE
          </span>
          <h2 className="mt-3 text-3xl font-bold sm:text-4xl">
            Offres & Packs Prêts à l&apos;Emploi
          </h2>
          <p className="mt-4 text-slate-400">
            Des solutions clés en main à tarifs transparents pour démarrer
            rapidement votre transformation digitale.
          </p>
        </div>

        <div className="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          {boutiqueProducts.map((product) => (
            <article
              key={product.title}
              className="card-shine group flex flex-col overflow-hidden rounded-2xl border border-white/5 bg-slate-900/50 transition hover:border-violet-500/30"
            >
              {product.image ? (
                <ContentImage
                  src={product.image}
                  alt={product.title}
                  className="h-32 w-full object-cover"
                />
              ) : null}
              <div className="flex flex-1 flex-col p-6">
              <div className="mb-4 flex items-center justify-between">
                <span className="rounded-lg bg-violet-500/20 px-2.5 py-1 text-xs font-medium text-violet-300">
                  {product.tag}
                </span>
                <ShoppingBag className="h-5 w-5 text-slate-600 transition group-hover:text-violet-400" />
              </div>
              <h3 className="text-lg font-semibold text-white">
                {product.title}
              </h3>
              <p className="mt-2 flex-1 text-sm text-slate-400">
                {product.description}
              </p>
              <p className="mt-4 font-mono text-lg font-bold text-cyan-400">
                {product.price}
              </p>
              <div className="mt-4 flex flex-col gap-2">
                <button
                  type="button"
                  onClick={() => addToCart(product)}
                  className="inline-flex items-center justify-center gap-1 rounded-xl bg-gradient-to-r from-cyan-500/20 to-violet-500/20 border border-cyan-500/30 py-2.5 text-sm font-medium text-cyan-300 transition hover:from-cyan-500/30 hover:to-violet-500/30"
                >
                  <Plus className="h-4 w-4" />
                  Ajouter au panier
                </button>
                <button
                  type="button"
                  onClick={() =>
                    setModal({ open: true, title: product.title })
                  }
                  className="inline-flex items-center justify-center gap-1 rounded-xl border border-white/10 bg-white/5 py-2.5 text-sm font-medium text-white transition hover:bg-white/10 group-hover:gap-2"
                >
                  Commander
                  <ArrowRight className="h-4 w-4" />
                </button>
              </div>
              </div>
            </article>
          ))}
        </div>
      </div>

      <RequestModal
        open={modal.open}
        onClose={() => setModal({ open: false, title: "" })}
        type="order"
        itemTitle={modal.title}
      />
    </section>
  );
}
