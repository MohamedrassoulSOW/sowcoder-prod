import { useState, useEffect } from "react";
import { Link, useLocation } from "react-router-dom";
import { Menu, X, ShoppingCart, User, LogIn, LogOut, Shield } from "lucide-react";
import { useApp } from "../context/AppContext";
import { useSiteContent } from "../context/ContentContext";
import NavLink from "./NavLink";

export default function Header() {
  const [open, setOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const location = useLocation();
  const {
    user,
    cartCount,
    openCart,
    openLogin,
    openRegister,
    logout,
    openAdmin,
    navigateTo,
  } = useApp();
  const { content } = useSiteContent();
  const { navLinks } = content;

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 20);
    window.addEventListener("scroll", onScroll);
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  const closeMobile = () => setOpen(false);

  return (
    <header
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${
        scrolled
          ? "bg-slate-950/90 backdrop-blur-xl border-b border-white/5 shadow-lg"
          : "bg-transparent"
      }`}
    >
      <div className="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <Link
          to="/"
          onClick={(e) => {
            if (location.pathname === "/") {
              e.preventDefault();
              navigateTo("#accueil");
            }
          }}
          className="flex items-center gap-2 group"
        >
          <span className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-400 to-violet-500 font-mono text-sm font-bold text-slate-950">
            SC
          </span>
          <span className="text-xl font-bold tracking-tight">
            Sow<span className="text-cyan-400">Coder</span>
          </span>
        </Link>

        <nav className="hidden items-center gap-1 lg:flex">
          <Link
            to="/blog"
            className="rounded-lg px-3 py-2 text-sm text-slate-300 transition hover:bg-white/5 hover:text-white"
          >
            Blog
          </Link>
          {navLinks.map((link) => (
            <NavLink
              key={link.href}
              href={link.href}
              className="rounded-lg px-3 py-2 text-sm text-slate-300 transition hover:bg-white/5 hover:text-white"
            >
              {link.label}
            </NavLink>
          ))}
        </nav>

        <div className="hidden items-center gap-2 lg:flex">
          <button
            type="button"
            onClick={openCart}
            className="relative rounded-lg p-2 text-slate-400 transition hover:bg-white/5 hover:text-white"
            aria-label="Ouvrir le panier"
          >
            <ShoppingCart className="h-5 w-5" />
            {cartCount > 0 && (
              <span className="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-cyan-500 text-[10px] font-bold text-slate-950">
                {cartCount > 9 ? "9+" : cartCount}
              </span>
            )}
          </button>

          {user ? (
            <>
              <span className="max-w-[120px] truncate text-sm text-slate-300">
                {user.name.split(" ")[0]}
                {user.role === "admin" && (
                  <span className="ml-1 text-violet-400">• admin</span>
                )}
              </span>
              {user.role === "admin" && (
                <button
                  type="button"
                  onClick={openAdmin}
                  className="flex items-center gap-1.5 rounded-lg border border-violet-500/30 bg-violet-500/10 px-3 py-2 text-sm text-violet-300 transition hover:bg-violet-500/20"
                >
                  <Shield className="h-4 w-4" />
                  Dashboard
                </button>
              )}
              <button
                type="button"
                onClick={logout}
                className="flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm text-slate-300 transition hover:bg-white/5 hover:text-white"
              >
                <LogOut className="h-4 w-4" />
                Déconnexion
              </button>
            </>
          ) : (
            <>
              <button
                type="button"
                onClick={openLogin}
                className="flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm text-slate-300 transition hover:bg-white/5 hover:text-white"
              >
                <LogIn className="h-4 w-4" />
                Connexion
              </button>
              <button
                type="button"
                onClick={openRegister}
                className="flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-cyan-500/20 transition hover:opacity-90"
              >
                <User className="h-4 w-4" />
                S&apos;inscrire
              </button>
            </>
          )}
        </div>

        <button
          type="button"
          className="rounded-lg p-2 text-slate-300 lg:hidden"
          onClick={() => setOpen(!open)}
          aria-label="Menu"
        >
          {open ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
        </button>
      </div>

      {open && (
        <div className="border-t border-white/5 bg-slate-950/95 backdrop-blur-xl lg:hidden">
          <nav className="flex flex-col gap-1 px-4 py-4">
            <Link
              to="/blog"
              onClick={closeMobile}
              className="rounded-lg px-3 py-2.5 text-slate-300 transition hover:bg-white/5 hover:text-white"
            >
              Blog
            </Link>
            {navLinks.map((link) => (
              <NavLink
                key={link.href}
                href={link.href}
                onAfterNavigate={closeMobile}
                className="rounded-lg px-3 py-2.5 text-slate-300 transition hover:bg-white/5 hover:text-white"
              >
                {link.label}
              </NavLink>
            ))}
            <div className="mt-3 flex flex-col gap-2 border-t border-white/5 pt-3">
              <button
                type="button"
                onClick={() => {
                  openCart();
                  closeMobile();
                }}
                className="flex items-center justify-center gap-2 rounded-lg px-3 py-2.5 text-slate-300 hover:bg-white/5"
              >
                <ShoppingCart className="h-4 w-4" />
                Panier {cartCount > 0 && `(${cartCount})`}
              </button>
              {user ? (
                <>
                  <p className="px-3 text-center text-sm text-cyan-400">
                    Bonjour, {user.name}
                    {user.role === "admin" && " (admin)"}
                  </p>
                  {user.role === "admin" && (
                    <button
                      type="button"
                      onClick={() => {
                        openAdmin();
                        closeMobile();
                      }}
                      className="flex items-center justify-center gap-2 rounded-lg border border-violet-500/30 bg-violet-500/10 px-3 py-2.5 text-violet-300"
                    >
                      <Shield className="h-4 w-4" />
                      Dashboard
                    </button>
                  )}
                  <button
                    type="button"
                    onClick={() => {
                      logout();
                      closeMobile();
                    }}
                    className="rounded-lg px-3 py-2.5 text-center text-slate-300 hover:bg-white/5"
                  >
                    Déconnexion
                  </button>
                </>
              ) : (
                <>
                  <button
                    type="button"
                    onClick={() => {
                      openLogin();
                      closeMobile();
                    }}
                    className="rounded-lg px-3 py-2.5 text-center text-slate-300 hover:bg-white/5"
                  >
                    Connexion
                  </button>
                  <button
                    type="button"
                    onClick={() => {
                      openRegister();
                      closeMobile();
                    }}
                    className="rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 py-2.5 text-center font-semibold text-white"
                  >
                    S&apos;inscrire
                  </button>
                </>
              )}
            </div>
          </nav>
        </div>
      )}
    </header>
  );
}
