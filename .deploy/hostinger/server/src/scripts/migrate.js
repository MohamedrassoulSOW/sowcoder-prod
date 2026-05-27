import { initDatabase } from "../db/database.js";
import { migrateFromJson } from "../db/migrate.js";

initDatabase();
const result = migrateFromJson();
if (result.migrated) {
  console.log(`Migration terminée. Sauvegarde : ${result.backupPath}`);
} else {
  console.log("Aucun fichier submissions.json à migrer.");
}
