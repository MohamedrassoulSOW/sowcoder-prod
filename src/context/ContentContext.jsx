import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
} from "react";
import { api } from "../api/client";
import { defaultSiteContent } from "../data/content";

const ContentContext = createContext(null);

export function ContentProvider({ children }) {
  const [content, setContent] = useState(defaultSiteContent);
  const [loading, setLoading] = useState(true);

  const refreshContent = useCallback(async () => {
    try {
      const res = await api.getContent();
      if (res?.data) setContent({ ...defaultSiteContent, ...res.data });
    } catch {
      setContent(defaultSiteContent);
    }
  }, []);

  useEffect(() => {
    refreshContent().finally(() => setLoading(false));
  }, [refreshContent]);

  const applyContent = useCallback((next) => {
    setContent(next);
  }, []);

  const value = useMemo(
    () => ({
      content,
      loading,
      refreshContent,
      applyContent,
    }),
    [content, loading, refreshContent, applyContent]
  );

  return (
    <ContentContext.Provider value={value}>{children}</ContentContext.Provider>
  );
}

export function useSiteContent() {
  const ctx = useContext(ContentContext);
  if (!ctx) {
    throw new Error("useSiteContent doit être utilisé dans ContentProvider");
  }
  return ctx;
}
