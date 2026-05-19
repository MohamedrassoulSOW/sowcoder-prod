import { useEffect } from "react";
import { Route, Routes, useLocation } from "react-router-dom";
import { AppProvider } from "./context/AppContext";
import { ContentProvider } from "./context/ContentContext";
import Header from "./components/Header";
import Footer from "./components/Footer";
import ChatWidget from "./components/ChatWidget";
import ScrollToTop from "./components/ScrollToTop";
import AuthModal from "./components/AuthModal";
import CartDrawer from "./components/CartDrawer";
import Dashboard from "./components/Dashboard";
import LegalModal from "./components/LegalModal";
import HomePage from "./pages/HomePage";
import BlogListPage from "./pages/BlogListPage";
import BlogArticlePage from "./pages/BlogArticlePage";

function ScrollToTopOnNavigate() {
  const { pathname } = useLocation();
  useEffect(() => {
    window.scrollTo(0, 0);
  }, [pathname]);
  return null;
}

export default function App() {
  return (
    <ContentProvider>
      <AppProvider>
        <ScrollToTopOnNavigate />
        <Header />
        <main>
          <Routes>
            <Route path="/" element={<HomePage />} />
            <Route path="/blog" element={<BlogListPage />} />
            <Route path="/blog/:slug" element={<BlogArticlePage />} />
          </Routes>
        </main>
        <Footer />
        <ChatWidget />
        <ScrollToTop />
        <AuthModal />
        <CartDrawer />
        <Dashboard />
        <LegalModal />
      </AppProvider>
    </ContentProvider>
  );
}
