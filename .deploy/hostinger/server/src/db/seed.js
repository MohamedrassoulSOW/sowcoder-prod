import bcrypt from "bcryptjs";
import { randomUUID } from "node:crypto";
import { config } from "../config.js";
import { getDb } from "./database.js";

export async function seedAdmin() {
  const { adminEmail, adminPassword, adminName } = config;

  if (!adminEmail || !adminPassword) {
    console.warn(
      "[db] ADMIN_EMAIL et ADMIN_PASSWORD non définis — compte admin non créé."
    );
    return null;
  }

  const database = getDb();
  const existing = database
    .prepare("SELECT id, role FROM users WHERE email = ?")
    .get(adminEmail.toLowerCase());

  if (existing) {
    if (existing.role !== "admin") {
      database
        .prepare("UPDATE users SET role = 'admin' WHERE id = ?")
        .run(existing.id);
      console.log(`[db] Utilisateur ${adminEmail} promu administrateur.`);
    }
    const passwordHash = await bcrypt.hash(adminPassword, 12);
    database
      .prepare("UPDATE users SET password_hash = ?, role = 'admin' WHERE id = ?")
      .run(passwordHash, existing.id);
    console.log(`[db] Mot de passe administrateur synchronisé (${adminEmail}).`);
    return existing.id;
  }

  const passwordHash = await bcrypt.hash(adminPassword, 12);
  const id = randomUUID();
  const now = new Date().toISOString();

  database
    .prepare(
      `INSERT INTO users (id, name, email, password_hash, role, created_at)
       VALUES (?, ?, ?, ?, 'admin', ?)`
    )
    .run(id, adminName, adminEmail.toLowerCase(), passwordHash, now);

  console.log(`[db] Compte administrateur créé : ${adminEmail}`);
  return id;
}
