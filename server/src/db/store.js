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
