import fs from "node:fs";
import { randomUUID } from "node:crypto";
import { config } from "../config.js";
import { getDb, initDatabase } from "./database.js";

const legacyPath = config.legacyJsonPath;

export function migrateFromJson() {
  if (!fs.existsSync(legacyPath)) return { migrated: false };

  const raw = fs.readFileSync(legacyPath, "utf-8");
  const legacy = JSON.parse(raw);
  const database = getDb();

  const insertContact = database.prepare(`
    INSERT OR IGNORE INTO contacts (id, name, email, phone, subject, message, created_at)
    VALUES (@id, @name, @email, @phone, @subject, @message, @created_at)
  `);

  const insertOrder = database.prepare(`
    INSERT OR IGNORE INTO orders (id, type, name, email, phone, product_title, message, created_at)
    VALUES (@id, @type, @name, @email, @phone, @product_title, @message, @created_at)
  `);

  const insertInscription = database.prepare(`
    INSERT OR IGNORE INTO inscriptions (id, name, email, phone, formation_title, message, created_at)
    VALUES (@id, @name, @email, @phone, @formation_title, @message, @created_at)
  `);

  const insertUser = database.prepare(`
    INSERT OR IGNORE INTO users (id, name, email, password_hash, role, created_at)
    VALUES (@id, @name, @email, @password_hash, @role, @created_at)
  `);

  const tx = database.transaction(() => {
    for (const c of legacy.contacts || []) {
      insertContact.run({
        id: c.id || randomUUID(),
        name: c.name,
        email: c.email,
        phone: c.phone || null,
        subject: c.subject || null,
        message: c.message,
        created_at: c.createdAt || new Date().toISOString(),
      });
    }

    for (const o of legacy.orders || []) {
      insertOrder.run({
        id: o.id || randomUUID(),
        type: o.type || "order",
        name: o.name,
        email: o.email,
        phone: o.phone || null,
        product_title: o.productTitle,
        message: o.message || null,
        created_at: o.createdAt || new Date().toISOString(),
      });
    }

    for (const i of legacy.inscriptions || []) {
      insertInscription.run({
        id: i.id || randomUUID(),
        name: i.name,
        email: i.email,
        phone: i.phone || null,
        formation_title: i.formationTitle,
        message: i.message || null,
        created_at: i.createdAt || new Date().toISOString(),
      });
    }

    for (const u of legacy.users || []) {
      insertUser.run({
        id: u.id || randomUUID(),
        name: u.name,
        email: u.email,
        password_hash: u.passwordHash,
        role: u.role || "user",
        created_at: u.createdAt || new Date().toISOString(),
      });
    }
  });

  tx();

  const backupPath = `${legacyPath}.migrated`;
  if (!fs.existsSync(backupPath)) {
    fs.renameSync(legacyPath, backupPath);
  }

  return { migrated: true, backupPath };
}
