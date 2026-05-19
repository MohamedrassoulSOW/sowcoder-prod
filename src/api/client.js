const API_BASE = import.meta.env.VITE_API_URL || "";
const TOKEN_KEY = "sowcoder_token";

export function getToken() {
  return localStorage.getItem(TOKEN_KEY);
}

export function setToken(token) {
  if (token) localStorage.setItem(TOKEN_KEY, token);
  else localStorage.removeItem(TOKEN_KEY);
}

async function request(path, options = {}) {
  const token = getToken();
  const headers = {
    "Content-Type": "application/json",
    ...options.headers,
  };
  if (token) headers.Authorization = `Bearer ${token}`;

  let res;
  try {
    res = await fetch(`${API_BASE}${path}`, {
      ...options,
      headers,
    });
  } catch {
    throw new Error(
      "Impossible de joindre le serveur. Lancez l'API avec : npm run dev:server"
    );
  }

  const raw = await res.text();
  let data = {};
  if (raw) {
    try {
      data = JSON.parse(raw);
    } catch {
      const plain = raw.replace(/<[^>]+>/g, " ").replace(/\s+/g, " ").trim();
      if (!res.ok) {
        throw new Error(
          plain.slice(0, 200) || `Erreur serveur (${res.status})`
        );
      }
    }
  }

  if (!res.ok) {
    if (res.status === 502 || res.status === 503) {
      throw new Error(
        "L'API n'est pas démarrée. Lancez : npm run dev:server (ou npm run dev:all)"
      );
    }
    if (res.status === 404 && path.startsWith("/api/auth")) {
      throw new Error(
        "API obsolète détectée. Redémarrez le serveur : npm run dev:server"
      );
    }
    const message =
      data.errors?.map((e) => e.message).join(", ") ||
      data.error ||
      data.message ||
      `Erreur serveur (${res.status})`;
    throw new Error(message);
  }

  return data;
}

export const api = {
  health: () => request("/api/health"),
  getContent: () => request("/api/content"),
  sendContact: (body) =>
    request("/api/contact", { method: "POST", body: JSON.stringify(body) }),
  createOrder: (body) =>
    request("/api/orders", { method: "POST", body: JSON.stringify(body) }),
  createInscription: (body) =>
    request("/api/inscriptions", {
      method: "POST",
      body: JSON.stringify(body),
    }),
  register: (body) =>
    request("/api/auth/register", {
      method: "POST",
      body: JSON.stringify(body),
    }),
  login: (body) =>
    request("/api/auth/login", { method: "POST", body: JSON.stringify(body) }),
  me: () => request("/api/auth/me"),
  checkoutCart: (body) =>
    request("/api/cart/checkout", {
      method: "POST",
      body: JSON.stringify(body),
    }),
  getAdminStats: () => request("/api/admin/stats"),
  getAdminSubmissions: () => request("/api/admin/submissions"),
  getAdminSubmissionsByType: (type, { limit = 50, offset = 0 } = {}) => {
    const params = new URLSearchParams({
      limit: String(limit),
      offset: String(offset),
    });
    return request(`/api/admin/submissions/${type}?${params}`);
  },
  deleteAdminSubmission: (type, id) =>
    request(`/api/admin/submissions/${type}/${id}`, { method: "DELETE" }),
  getAdminContent: () => request("/api/admin/content"),
  updateAdminContent: (body) =>
    request("/api/admin/content", {
      method: "PUT",
      body: JSON.stringify(body),
    }),
  getBlogArticles: () => request("/api/blog"),
  getBlogArticle: (slug, visitorId) => {
    const params = visitorId
      ? `?visitorId=${encodeURIComponent(visitorId)}`
      : "";
    return request(`/api/blog/${encodeURIComponent(slug)}${params}`);
  },
  getBlogComments: (slug, { limit = 50, offset = 0 } = {}) => {
    const params = new URLSearchParams({
      limit: String(limit),
      offset: String(offset),
    });
    return request(
      `/api/blog/${encodeURIComponent(slug)}/comments?${params}`
    );
  },
  postBlogComment: (slug, body) =>
    request(`/api/blog/${encodeURIComponent(slug)}/comments`, {
      method: "POST",
      body: JSON.stringify(body),
    }),
  toggleBlogLike: (slug, visitorId) =>
    request(`/api/blog/${encodeURIComponent(slug)}/like`, {
      method: "POST",
      body: JSON.stringify({ visitorId }),
    }),
  uploadImage: async (file) => {
    const token = getToken();
    const formData = new FormData();
    formData.append("image", file);

    let res;
    try {
      res = await fetch(`${API_BASE}/api/admin/upload`, {
        method: "POST",
        headers: token ? { Authorization: `Bearer ${token}` } : {},
        body: formData,
      });
    } catch {
      throw new Error(
        "Impossible de joindre le serveur. Lancez l'API avec : npm run dev:server"
      );
    }

    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
      throw new Error(data.error || `Erreur serveur (${res.status})`);
    }
    return data;
  },
};
