import path from "node:path";
import { fileURLToPath } from "node:url";
import dotenv from "dotenv";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const rootDir = path.resolve(__dirname, "..");

dotenv.config({ path: path.resolve(rootDir, "..", ".env") });
dotenv.config({ path: path.resolve(rootDir, ".env") });

export const config = {
  port: Number(process.env.PORT) || 3001,
  nodeEnv: process.env.NODE_ENV || "development",
  corsOrigin:
    process.env.CORS_ORIGIN || "http://localhost:5173,http://localhost:5174",
  adminApiKey: process.env.ADMIN_API_KEY || "",
  jwtSecret:
    process.env.JWT_SECRET || "dev-secret-changez-en-production",
  jwtExpiresIn: process.env.JWT_EXPIRES_IN || "7d",
  contactEmail: process.env.CONTACT_EMAIL || "contact@sowcoder.com",
  smtp: {
    host: process.env.SMTP_HOST || "",
    port: Number(process.env.SMTP_PORT) || 587,
    secure: process.env.SMTP_SECURE === "true",
    user: process.env.SMTP_USER || "",
    pass: process.env.SMTP_PASS || "",
  },
  dataDir: path.resolve(rootDir, "data"),
  contentPath: path.resolve(rootDir, "data", "site-content.json"),
  uploadsDir: path.resolve(rootDir, "data", "uploads"),
  uploadsPublicPath: "/uploads",
  sqlitePath: path.resolve(rootDir, "data", "sowcoder.db"),
  legacyJsonPath: path.resolve(rootDir, "data", "submissions.json"),
  distPath: path.resolve(rootDir, "..", "dist"),
  adminName: process.env.ADMIN_NAME || "Administrateur SowCoder",
  adminEmail: process.env.ADMIN_EMAIL || "admin@sowcoder.sn",
  adminPassword: process.env.ADMIN_PASSWORD || "",
};
