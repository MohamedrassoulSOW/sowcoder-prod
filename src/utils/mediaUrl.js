const API_BASE = import.meta.env.VITE_API_URL || "";

/** URL absolue pour une image stockée sur l'API ou une URL externe. */
export function mediaUrl(path) {
  if (!path) return "";
  if (path.startsWith("http://") || path.startsWith("https://")) return path;
  return `${API_BASE}${path}`;
}
