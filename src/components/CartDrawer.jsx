import { useEffect, useState } from "react";
import { X, Trash2, ShoppingBag, Loader2, CheckCircle } from "lucide-react";
import { useApp } from "../context/AppContext";
import { api } from "../api/client";

const inputClass =
  "w-full rounded-xl border border-white/10 bg-slate-950/50 px-3 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-cyan-500/50 focus:outline-none";

export default function CartDrawer() {
  const {
    cart,
    cartCount,
    cartOpen,
    closeCart,
    removeFromCart,
    clearCart,
    user,
  } = useApp();

  const [checkout, setCheckout] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState(false);
  const [form, setForm] = useState({
    name: user?.name || "",
    email: user?.email || "",
    phone: "",
    message: "",
  });

  useEffect(() => {
    if (user) {
      setForm((f) => ({
        ...f,
        name: user.name,
        email: user.email,
      }));
    }
  }, [user]);

  if (!cartOpen) return null;

  const handleChange = (e) => {
    setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const handleCheckout = async (e) => {
    e.preventDefault();
    if (cart.length === 0) return;

    setLoading(true);
    setError("");
    try {
      await api.checkoutCart({
        name: form.name,
        email: form.email,
        phone: form.phone,
        message: form.message,
        items: cart.map(({ title, price, tag }) => ({ title, price, tag })),
      });
      setSuccess(true);
      clearCart();
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  const handleClose = () => {
    setCheckout(false);
    setSuccess(false);
    setError("");
    closeCart();
  };

  return (
    <div className="fixed inset-0 z-[65] flex justify-end" role="dialog" aria-modal="true">
      <button
        type="button"
        className="absolute inset-0 bg-slate-950/70 backdrop-blur-sm"
        onClick={handleClose}
        aria-label="Fermer le panier"
      />
      <aside className="relative flex h-full w-full max-w-md flex-col border-l border-white/10 bg-slate-900 shadow-2xl">
        <div className="flex items-center justify-between border-b border-white/5 px-5 py-4">
          <div className="flex items-center gap-2">
            <ShoppingBag className="h-5 w-5 text-cyan-400" />
            <h2 className="text-lg font-semibold text-white">
              Panier ({cartCount})
            </h2>
          </div>
          <button
            type="button"
            onClick={handleClose}
            className="rounded-lg p-1.5 text-slate-400 hover:bg-white/5 hover:text-white"
          >
            <X className="h-5 w-5" />
          </button>
        </div>

        {success ? (
          <div className="flex flex-1 flex-col items-center justify-center p-8 text-center">
            <CheckCircle className="h-14 w-14 text-cyan-400" />
            <p className="mt-4 text-lg font-semibold text-white">
              Commande envoyée !
            </p>
            <p className="mt-2 text-sm text-slate-400">
              Nous vous contacterons sous 24h.
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
            <div className="flex-1 overflow-y-auto p-5">
              {cart.length === 0 ? (
                <p className="py-12 text-center text-sm text-slate-500">
                  Votre panier est vide.
                  <br />
                  Parcourez la boutique pour ajouter des packs.
                </p>
              ) : (
                <ul className="space-y-3">
                  {cart.map((item) => (
                    <li
                      key={item.title}
                      className="flex gap-3 rounded-xl border border-white/5 bg-slate-950/50 p-4"
                    >
                      <div className="flex-1 min-w-0">
                        <p className="font-medium text-white truncate">
                          {item.title}
                        </p>
                        <p className="text-sm text-cyan-400">{item.price}</p>
                        {(item.qty || 1) > 1 && (
                          <p className="text-xs text-slate-500">
                            Qté: {item.qty}
                          </p>
                        )}
                      </div>
                      <button
                        type="button"
                        onClick={() => removeFromCart(item.title)}
                        className="shrink-0 rounded-lg p-2 text-slate-500 hover:bg-red-500/10 hover:text-red-400"
                        aria-label="Retirer"
                      >
                        <Trash2 className="h-4 w-4" />
                      </button>
                    </li>
                  ))}
                </ul>
              )}
            </div>

            {cart.length > 0 && (
              <div className="border-t border-white/5 p-5">
                {!checkout ? (
                  <button
                    type="button"
                    onClick={() => {
                      if (user) {
                        setForm((f) => ({
                          ...f,
                          name: user.name,
                          email: user.email,
                        }));
                      }
                      setCheckout(true);
                    }}
                    className="w-full rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 py-3 font-semibold text-white"
                  >
                    Passer la commande
                  </button>
                ) : (
                  <form onSubmit={handleCheckout} className="space-y-3">
                    <input
                      name="name"
                      required
                      value={form.name}
                      onChange={handleChange}
                      className={inputClass}
                      placeholder="Nom *"
                    />
                    <input
                      name="email"
                      type="email"
                      required
                      value={form.email}
                      onChange={handleChange}
                      className={inputClass}
                      placeholder="Email *"
                    />
                    <input
                      name="phone"
                      value={form.phone}
                      onChange={handleChange}
                      className={inputClass}
                      placeholder="Téléphone"
                    />
                    <textarea
                      name="message"
                      rows={2}
                      value={form.message}
                      onChange={handleChange}
                      className={`${inputClass} resize-none`}
                      placeholder="Message (optionnel)"
                    />
                    {error && (
                      <p className="text-xs text-red-400">{error}</p>
                    )}
                    <div className="flex gap-2">
                      <button
                        type="button"
                        onClick={() => setCheckout(false)}
                        className="flex-1 rounded-xl border border-white/10 py-2.5 text-sm text-slate-300"
                      >
                        Retour
                      </button>
                      <button
                        type="submit"
                        disabled={loading}
                        className="flex flex-1 items-center justify-center gap-1 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 py-2.5 text-sm font-semibold text-white disabled:opacity-60"
                      >
                        {loading && <Loader2 className="h-4 w-4 animate-spin" />}
                        Confirmer
                      </button>
                    </div>
                  </form>
                )}
              </div>
            )}
          </>
        )}
      </aside>
    </div>
  );
}
