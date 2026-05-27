import fs from "node:fs/promises";
import { randomUUID } from "node:crypto";
import { config } from "../config.js";
import { getDb } from "./database.js";

function now() {
  return new Date().toISOString();
}

function rowToUser(row) {
  if (!row) return null;
  return {
    id: row.id,
    name: row.name,
    email: row.email,
    passwordHash: row.password_hash,
    role: row.role,
    createdAt: row.created_at,
  };
}

export function toPublicUser(user) {
  return {
    id: user.id,
    name: user.name,
    email: user.email,
    role: user.role,
    createdAt: user.createdAt,
  };
}

export async function saveContact(entry) {
  const id = randomUUID();
  const createdAt = now();
  getDb()
    .prepare(
      `INSERT INTO contacts (id, name, email, phone, subject, message, created_at)
       VALUES (?, ?, ?, ?, ?, ?, ?)`
    )
    .run(
      id,
      entry.name,
      entry.email,
      entry.phone || null,
      entry.subject || null,
      entry.message,
      createdAt
    );
  return { id, type: "contact", createdAt, ...entry };
}

export async function saveOrder(entry) {
  const id = randomUUID();
  const createdAt = now();
  getDb()
    .prepare(
      `INSERT INTO orders (id, type, name, email, phone, product_title, message, created_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?)`
    )
    .run(
      id,
      entry.type || "order",
      entry.name,
      entry.email,
      entry.phone || null,
      entry.productTitle,
      entry.message || null,
      createdAt
    );
  return { id, type: entry.type || "order", createdAt, ...entry };
}

export async function saveInscription(entry) {
  const id = randomUUID();
  const createdAt = now();
  getDb()
    .prepare(
      `INSERT INTO inscriptions (id, name, email, phone, formation_title, message, created_at)
       VALUES (?, ?, ?, ?, ?, ?, ?)`
    )
    .run(
      id,
      entry.name,
      entry.email,
      entry.phone || null,
      entry.formationTitle,
      entry.message || null,
      createdAt
    );
  return { id, type: "inscription", createdAt, ...entry };
}

export async function saveCartOrder(entry) {
  return saveOrder({ ...entry, type: "cart_order" });
}

export async function getAllSubmissions() {
  const database = getDb();
  const contacts = database
    .prepare("SELECT * FROM contacts ORDER BY created_at DESC")
    .all()
    .map((r) => ({
      id: r.id,
      name: r.name,
      email: r.email,
      phone: r.phone,
      subject: r.subject,
      message: r.message,
      createdAt: r.created_at,
    }));

  const orders = database
    .prepare("SELECT * FROM orders ORDER BY created_at DESC")
    .all()
    .map((r) => ({
      id: r.id,
      type: r.type,
      name: r.name,
      email: r.email,
      phone: r.phone,
      productTitle: r.product_title,
      message: r.message,
      createdAt: r.created_at,
    }));

  const inscriptions = database
    .prepare("SELECT * FROM inscriptions ORDER BY created_at DESC")
    .all()
    .map((r) => ({
      id: r.id,
      name: r.name,
      email: r.email,
      phone: r.phone,
      formationTitle: r.formation_title,
      message: r.message,
      createdAt: r.created_at,
    }));

  const users = database
    .prepare(
      "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC"
    )
    .all()
    .map((r) => ({
      id: r.id,
      name: r.name,
      email: r.email,
      role: r.role,
      createdAt: r.created_at,
    }));

  return { contacts, orders, inscriptions, users };
}

export async function loadSiteContent() {
  const raw = await fs.readFile(config.contentPath, "utf-8");
  return JSON.parse(raw);
}

export async function saveSiteContent(content) {
  const payload = `${JSON.stringify(content, null, 2)}\n`;
  await fs.writeFile(config.contentPath, payload, "utf-8");
  return content;
}

export async function findUserByEmail(email) {
  const row = getDb()
    .prepare("SELECT * FROM users WHERE email = ?")
    .get(email.toLowerCase());
  return rowToUser(row);
}

export async function findUserById(id) {
  const row = getDb().prepare("SELECT * FROM users WHERE id = ?").get(id);
  return rowToUser(row);
}

export async function createUser({ name, email, passwordHash, role = "user" }) {
  const id = randomUUID();
  const createdAt = now();
  getDb()
    .prepare(
      `INSERT INTO users (id, name, email, password_hash, role, created_at)
       VALUES (?, ?, ?, ?, ?, ?)`
    )
    .run(id, name, email.toLowerCase(), passwordHash, role, createdAt);
  return { id, name, email: email.toLowerCase(), passwordHash, role, createdAt };
}

export async function getStats() {
  const database = getDb();
  return {
    contacts: database.prepare("SELECT COUNT(*) AS n FROM contacts").get().n,
    orders: database.prepare("SELECT COUNT(*) AS n FROM orders").get().n,
    inscriptions: database
      .prepare("SELECT COUNT(*) AS n FROM inscriptions")
      .get().n,
    users: database.prepare("SELECT COUNT(*) AS n FROM users").get().n,
    admins: database
      .prepare("SELECT COUNT(*) AS n FROM users WHERE role = 'admin'")
      .get().n,
  };
}

function normalizeVisitorId(visitorId) {
  if (typeof visitorId !== "string") return null;
  const v = visitorId.trim();
  return v.length > 0 ? v.slice(0, 120) : null;
}

export async function listBlogArticles(visitorId) {
  const content = await loadSiteContent();
  const articles = Array.isArray(content.blogPosts) ? content.blogPosts : [];
  const database = getDb();
  const normalizedVisitor = normalizeVisitorId(visitorId);

  return articles.map((article) => {
    const commentCount = database
      .prepare("SELECT COUNT(*) AS n FROM blog_comments WHERE slug = ?")
      .get(article.slug).n;
    const likeCount = database
      .prepare("SELECT COUNT(*) AS n FROM blog_likes WHERE slug = ?")
      .get(article.slug).n;
    const liked = normalizedVisitor
      ? Boolean(
          database
            .prepare(
              "SELECT 1 FROM blog_likes WHERE slug = ? AND visitor_id = ? LIMIT 1"
            )
            .get(article.slug, normalizedVisitor)
        )
      : false;

    return { ...article, commentCount, likeCount, liked };
  });
}

export async function getBlogArticle(slug, visitorId) {
  const articles = await listBlogArticles(visitorId);
  return articles.find((article) => article.slug === slug) || null;
}

export async function listBlogComments(slug, { limit = 50, offset = 0 } = {}) {
  const rows = getDb()
    .prepare(
      `SELECT id, slug, author_name, author_email, body, visitor_id, created_at
       FROM blog_comments
       WHERE slug = ?
       ORDER BY created_at DESC
       LIMIT ? OFFSET ?`
    )
    .all(slug, limit, offset);

  return rows.map((r) => ({
    id: r.id,
    slug: r.slug,
    authorName: r.author_name,
    authorEmail: r.author_email,
    body: r.body,
    visitorId: r.visitor_id,
    createdAt: r.created_at,
  }));
}

export async function addBlogComment({
  slug,
  authorName,
  authorEmail,
  body,
  visitorId,
}) {
  const id = randomUUID();
  const createdAt = now();

  getDb()
    .prepare(
      `INSERT INTO blog_comments
      (id, slug, author_name, author_email, body, visitor_id, created_at)
      VALUES (?, ?, ?, ?, ?, ?, ?)`
    )
    .run(
      id,
      slug,
      authorName,
      authorEmail || null,
      body,
      normalizeVisitorId(visitorId),
      createdAt
    );

  return { id, slug, authorName, authorEmail: authorEmail || null, body, createdAt };
}

export async function toggleBlogLike(slug, visitorId) {
  const normalizedVisitor = normalizeVisitorId(visitorId);
  if (!normalizedVisitor) {
    throw new Error("visitorId requis");
  }

  const database = getDb();
  const existing = database
    .prepare("SELECT id FROM blog_likes WHERE slug = ? AND visitor_id = ?")
    .get(slug, normalizedVisitor);

  if (existing) {
    database.prepare("DELETE FROM blog_likes WHERE id = ?").run(existing.id);
  } else {
    database
      .prepare(
        "INSERT INTO blog_likes (id, slug, visitor_id, created_at) VALUES (?, ?, ?, ?)"
      )
      .run(randomUUID(), slug, normalizedVisitor, now());
  }

  const likeCount = database
    .prepare("SELECT COUNT(*) AS n FROM blog_likes WHERE slug = ?")
    .get(slug).n;

  return { liked: !existing, likeCount };
}
