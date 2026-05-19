import express from "express";
import cors from "cors";
import helmet from "helmet";
import rateLimit from "express-rate-limit";
import path from "node:path";
import { config } from "./config.js";
import { errorHandler } from "./middleware/errorHandler.js";
import contactRoutes from "./routes/contact.js";
import orderRoutes from "./routes/orders.js";
import inscriptionRoutes from "./routes/inscriptions.js";
import contentRoutes from "./routes/content.js";
import adminRoutes from "./routes/admin.js";
import authRoutes from "./routes/auth.js";
import cartRoutes from "./routes/cart.js";
import uploadRoutes from "./routes/upload.js";
import fs from "node:fs";
import { getDb } from "./db/database.js";

const app = express();

fs.mkdirSync(config.uploadsDir, { recursive: true });

app.use(helmet({ crossOriginResourcePolicy: { policy: "cross-origin" } }));
app.use(
  cors({
    origin: config.corsOrigin.split(",").map((o) => o.trim()),
    methods: ["GET", "POST", "PUT", "DELETE", "OPTIONS"],
    allowedHeaders: ["Content-Type", "Authorization"],
  })
);
app.use(express.json({ limit: "2mb" }));

const apiLimiter = rateLimit({
  windowMs: 15 * 60 * 1000,
  max: 100,
  standardHeaders: true,
  legacyHeaders: false,
  message: { success: false, error: "Trop de requêtes, réessayez plus tard." },
});

const formLimiter = rateLimit({
  windowMs: 60 * 60 * 1000,
  max: 20,
  message: { success: false, error: "Limite d'envois atteinte pour cette heure." },
});

app.use("/api", apiLimiter);

app.get("/api/health", (_req, res) => {
  let database = "ok";
  try {
    getDb().prepare("SELECT 1").get();
  } catch {
    database = "error";
  }

  res.json({
    success: true,
    status: database === "ok" ? "ok" : "degraded",
    timestamp: new Date().toISOString(),
    services: {
      database,
      uploads: fs.existsSync(config.uploadsDir) ? "ok" : "error",
    },
  });
});

app.use("/api/contact", formLimiter, contactRoutes);
app.use("/api/orders", formLimiter, orderRoutes);
app.use("/api/inscriptions", formLimiter, inscriptionRoutes);
app.use("/api/content", contentRoutes);
app.use("/api/auth", formLimiter, authRoutes);
app.use("/api/cart", formLimiter, cartRoutes);
app.use("/api/admin", adminRoutes);
app.use("/api/admin/upload", uploadRoutes);
app.use(
  config.uploadsPublicPath,
  express.static(config.uploadsDir, { maxAge: config.nodeEnv === "production" ? "7d" : 0 })
);

if (config.nodeEnv === "production" && fs.existsSync(config.distPath)) {
  app.use(express.static(config.distPath));
  app.get(/^(?!\/api).*/, (_req, res) => {
    res.sendFile(path.join(config.distPath, "index.html"));
  });
}

app.use(errorHandler);

export default app;
