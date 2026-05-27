import Database from "better-sqlite3";
import fs from "node:fs";
import { config } from "../config.js";

let db;

const schema = `
CREATE TABLE IF NOT EXISTS users (
  id TEXT PRIMARY KEY,
  name TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE COLLATE NOCASE,
  password_hash TEXT NOT NULL,
  role TEXT NOT NULL DEFAULT 'user' CHECK(role IN ('user', 'admin')),
  created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS contacts (
  id TEXT PRIMARY KEY,
  name TEXT NOT NULL,
  email TEXT NOT NULL,
  phone TEXT,
  subject TEXT,
  message TEXT NOT NULL,
  created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS orders (
  id TEXT PRIMARY KEY,
  type TEXT NOT NULL DEFAULT 'order',
  name TEXT NOT NULL,
  email TEXT NOT NULL,
  phone TEXT,
  product_title TEXT NOT NULL,
  message TEXT,
  created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS inscriptions (
  id TEXT PRIMARY KEY,
  name TEXT NOT NULL,
  email TEXT NOT NULL,
  phone TEXT,
  formation_title TEXT NOT NULL,
  message TEXT,
  created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS blog_comments (
  id TEXT PRIMARY KEY,
  slug TEXT NOT NULL,
  author_name TEXT NOT NULL,
  author_email TEXT,
  body TEXT NOT NULL,
  visitor_id TEXT,
  created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS blog_likes (
  id TEXT PRIMARY KEY,
  slug TEXT NOT NULL,
  visitor_id TEXT NOT NULL,
  created_at TEXT NOT NULL,
  UNIQUE(slug, visitor_id)
);

CREATE INDEX IF NOT EXISTS idx_contacts_created ON contacts(created_at DESC);
CREATE INDEX IF NOT EXISTS idx_orders_created ON orders(created_at DESC);
CREATE INDEX IF NOT EXISTS idx_inscriptions_created ON inscriptions(created_at DESC);
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_blog_comments_slug_created ON blog_comments(slug, created_at DESC);
CREATE INDEX IF NOT EXISTS idx_blog_likes_slug ON blog_likes(slug);
`;

export function getDb() {
  if (!db) {
    throw new Error("Base de données non initialisée. Appelez initDatabase() d'abord.");
  }
  return db;
}

export function initDatabase() {
  fs.mkdirSync(config.dataDir, { recursive: true });
  db = new Database(config.sqlitePath);
  db.pragma("journal_mode = WAL");
  db.pragma("foreign_keys = ON");
  db.exec(schema);
  return db;
}

export function closeDatabase() {
  if (db) {
    db.close();
    db = null;
  }
}
