import path from "node:path";
import { fileURLToPath } from "node:url";
import dotenv from "dotenv";

const serverDir = path.resolve(
  path.dirname(fileURLToPath(import.meta.url)),
  "../.."
);
const projectRoot = path.resolve(serverDir, "..");
dotenv.config({ path: path.join(projectRoot, ".env") });
dotenv.config({ path: path.join(serverDir, ".env") });

const { initDatabase } = await import("../db/database.js");
const { seedAdmin } = await import("../db/seed.js");

initDatabase();
await seedAdmin();
