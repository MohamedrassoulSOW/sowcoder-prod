import { getDb } from "./database.js";

const TYPES = {
  contacts: {
    table: "contacts",
    map: (r) => ({
      id: r.id,
      name: r.name,
      email: r.email,
      phone: r.phone,
      subject: r.subject,
      message: r.message,
      createdAt: r.created_at,
    }),
  },
  orders: {
    table: "orders",
    map: (r) => ({
      id: r.id,
      type: r.type,
      name: r.name,
      email: r.email,
      phone: r.phone,
      productTitle: r.product_title,
      message: r.message,
      createdAt: r.created_at,
    }),
  },
  inscriptions: {
    table: "inscriptions",
    map: (r) => ({
      id: r.id,
      name: r.name,
      email: r.email,
      phone: r.phone,
      formationTitle: r.formation_title,
      message: r.message,
      createdAt: r.created_at,
    }),
  },
};

export function isValidSubmissionType(type) {
  return Object.hasOwn(TYPES, type);
}

export function listSubmissions(type, { limit = 50, offset = 0 } = {}) {
  const cfg = TYPES[type];
  if (!cfg) {
    const err = new Error("Type de soumission invalide");
    err.status = 400;
    throw err;
  }

  const safeLimit = Math.min(Math.max(Number(limit) || 50, 1), 200);
  const safeOffset = Math.max(Number(offset) || 0, 0);
  const database = getDb();

  const total = database
    .prepare(`SELECT COUNT(*) AS n FROM ${cfg.table}`)
    .get().n;

  const items = database
    .prepare(
      `SELECT * FROM ${cfg.table} ORDER BY created_at DESC LIMIT ? OFFSET ?`
    )
    .all(safeLimit, safeOffset)
    .map(cfg.map);

  return { items, total, limit: safeLimit, offset: safeOffset };
}

export function getSubmission(type, id) {
  const cfg = TYPES[type];
  if (!cfg) {
    const err = new Error("Type de soumission invalide");
    err.status = 400;
    throw err;
  }

  const row = getDb()
    .prepare(`SELECT * FROM ${cfg.table} WHERE id = ?`)
    .get(id);

  if (!row) {
    const err = new Error("Soumission introuvable");
    err.status = 404;
    throw err;
  }

  return cfg.map(row);
}

export function deleteSubmission(type, id) {
  const cfg = TYPES[type];
  if (!cfg) {
    const err = new Error("Type de soumission invalide");
    err.status = 400;
    throw err;
  }

  const result = getDb()
    .prepare(`DELETE FROM ${cfg.table} WHERE id = ?`)
    .run(id);

  if (result.changes === 0) {
    const err = new Error("Soumission introuvable");
    err.status = 404;
    throw err;
  }

  return true;
}
