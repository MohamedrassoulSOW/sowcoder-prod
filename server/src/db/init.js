import { initDatabase } from "./database.js";
import { migrateFromJson } from "./migrate.js";
import { seedAdmin } from "./seed.js";

export async function setupDatabase() {
  initDatabase();

  const migration = migrateFromJson();
  if (migration.migrated) {
    console.log(`[db] Données JSON migrées vers SQLite (${migration.backupPath})`);
  }

  await seedAdmin();
}
