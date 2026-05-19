import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
} from "react";
import { useLocation, useNavigate } from "react-router-dom";
import { api, getToken, setToken } from "../api/client";
import { scrollToSection, scrollToSectionWhenReady } from "../utils/scroll";

const CART_KEY = "sowcoder_cart";

const AppContext = createContext(null);

function loadCart() {
  try {
    const raw = localStorage.getItem(CART_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch {
    return [];
  }
}

export function AppProvider({ children }) {
  const navigate = useNavigate();
  const location = useLocation();
  const [user, setUser] = useState(null);
  const [authLoading, setAuthLoading] = useState(true);
  const [cart, setCart] = useState(loadCart);
  const [authModal, setAuthModal] = useState(null);
  const [cartOpen, setCartOpen] = useState(false);
  const [adminOpen, setAdminOpen] = useState(false);
  const [legalPage, setLegalPage] = useState(null);
  const [contactPreset, setContactPreset] = useState(null);

  useEffect(() => {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
  }, [cart]);

  useEffect(() => {
    const token = getToken();
    if (!token) {
      setAuthLoading(false);
      return;
    }
    api
      .me()
      .then((res) => setUser(res.user))
      .catch(() => setToken(null))
      .finally(() => setAuthLoading(false));
  }, []);

  const login = useCallback(async (email, password) => {
    const res = await api.login({ email, password });
    setToken(res.token);
    setUser(res.user);
    setAuthModal(null);
    return res;
  }, []);

  const register = useCallback(async (name, email, password) => {
    const res = await api.register({ name, email, password });
    setToken(res.token);
    setUser(res.user);
    setAuthModal(null);
    return res;
  }, []);

  const logout = useCallback(() => {
    setToken(null);
    setUser(null);
  }, []);

  const openLogin = useCallback(() => setAuthModal("login"), []);
  const openRegister = useCallback(() => setAuthModal("register"), []);
  const closeAuth = useCallback(() => setAuthModal(null), []);
  const openCart = useCallback(() => setCartOpen(true), []);
  const closeCart = useCallback(() => setCartOpen(false), []);
  const openAdmin = useCallback(() => setAdminOpen(true), []);
  const closeAdmin = useCallback(() => setAdminOpen(false), []);
  const openLegal = useCallback((type) => setLegalPage(type), []);
  const closeLegal = useCallback(() => setLegalPage(null), []);

  const navigateTo = useCallback((sectionId) => {
    scrollToSection(sectionId);
  }, []);

  const openContact = useCallback(
    (preset = null) => {
      if (location.pathname !== "/") {
        navigate("/", { state: { contactPreset: preset, scrollTo: "contact" } });
        return;
      }
      setContactPreset(preset);
      scrollToSectionWhenReady("contact");
    },
    [location.pathname, navigate]
  );

  useEffect(() => {
    const preset = location.state?.contactPreset;
    if (!preset || location.pathname !== "/") return;

    setContactPreset(preset);
    scrollToSectionWhenReady(location.state?.scrollTo || "contact");
    navigate(location.pathname, { replace: true, state: {} });
  }, [location.state, location.pathname, navigate]);

  const clearContactPreset = useCallback(() => setContactPreset(null), []);

  const addToCart = useCallback((product) => {
    setCart((prev) => {
      const exists = prev.find((p) => p.title === product.title);
      if (exists) {
        return prev.map((p) =>
          p.title === product.title ? { ...p, qty: (p.qty || 1) + 1 } : p
        );
      }
      return [...prev, { ...product, qty: 1 }];
    });
    setCartOpen(true);
  }, []);

  const removeFromCart = useCallback((title) => {
    setCart((prev) => prev.filter((p) => p.title !== title));
  }, []);

  const clearCart = useCallback(() => setCart([]), []);

  const cartCount = useMemo(
    () => cart.reduce((sum, item) => sum + (item.qty || 1), 0),
    [cart]
  );

  const value = useMemo(
    () => ({
      user,
      authLoading,
      login,
      register,
      logout,
      authModal,
      openLogin,
      openRegister,
      closeAuth,
      cart,
      cartCount,
      cartOpen,
      openCart,
      closeCart,
      addToCart,
      removeFromCart,
      clearCart,
      adminOpen,
      openAdmin,
      closeAdmin,
      legalPage,
      openLegal,
      closeLegal,
      navigateTo,
      contactPreset,
      openContact,
      clearContactPreset,
    }),
    [
      user,
      authLoading,
      login,
      register,
      logout,
      authModal,
      openLogin,
      openRegister,
      closeAuth,
      cart,
      cartCount,
      cartOpen,
      openCart,
      closeCart,
      addToCart,
      removeFromCart,
      clearCart,
      adminOpen,
      openAdmin,
      closeAdmin,
      legalPage,
      openLegal,
      closeLegal,
      navigateTo,
      contactPreset,
      openContact,
      clearContactPreset,
    ]
  );

  return <AppContext.Provider value={value}>{children}</AppContext.Provider>;
}

export function useApp() {
  const ctx = useContext(AppContext);
  if (!ctx) throw new Error("useApp doit être utilisé dans AppProvider");
  return ctx;
}
