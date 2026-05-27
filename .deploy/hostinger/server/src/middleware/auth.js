import jwt from "jsonwebtoken";
import { config } from "../config.js";
import { findUserById, toPublicUser } from "../db/store.js";

export function signToken(user) {
  return jwt.sign(
    { sub: user.id, role: user.role },
    config.jwtSecret,
    { expiresIn: config.jwtExpiresIn }
  );
}

export async function requireAuth(req, res, next) {
  const header = req.headers.authorization;
  if (!header?.startsWith("Bearer ")) {
    return res.status(401).json({
      success: false,
      error: "Connexion requise",
    });
  }

  try {
    const token = header.slice(7);
    const payload = jwt.verify(token, config.jwtSecret);
    const user = await findUserById(payload.sub);
    if (!user) {
      return res.status(401).json({ success: false, error: "Session invalide" });
    }
    req.user = toPublicUser(user);
    next();
  } catch {
    return res.status(401).json({ success: false, error: "Session expirée" });
  }
}

export async function requireAdmin(req, res, next) {
  const apiKey = req.headers["x-api-key"];
  if (config.adminApiKey && apiKey === config.adminApiKey) {
    req.adminVia = "api_key";
    return next();
  }

  const header = req.headers.authorization;
  if (!header?.startsWith("Bearer ")) {
    return res.status(401).json({
      success: false,
      error: "Connexion administrateur requise",
    });
  }

  try {
    const token = header.slice(7);
    const payload = jwt.verify(token, config.jwtSecret);
    const user = await findUserById(payload.sub);
    if (!user || user.role !== "admin") {
      return res.status(403).json({
        success: false,
        error: "Accès réservé aux administrateurs",
      });
    }
    req.user = toPublicUser(user);
    req.adminVia = "jwt";
    next();
  } catch {
    return res.status(401).json({
      success: false,
      error: "Session invalide ou expirée",
    });
  }
}
