import { Router } from "express";
import bcrypt from "bcryptjs";
import {
  createUser,
  findUserByEmail,
  toPublicUser,
} from "../db/store.js";
import { config } from "../config.js";
import { signToken, requireAuth } from "../middleware/auth.js";
import { validate, schemas } from "../middleware/validate.js";

const router = Router();

router.post("/register", validate(schemas.register), async (req, res, next) => {
  try {
    const { name, email, password } = req.validated;

    if (
      config.adminEmail &&
      email.toLowerCase() === config.adminEmail.toLowerCase()
    ) {
      return res.status(403).json({
        success: false,
        error: "Cet email est réservé à l'administration",
      });
    }

    const existing = await findUserByEmail(email);
    if (existing) {
      return res.status(409).json({
        success: false,
        error: "Un compte existe déjà avec cet email",
      });
    }

    const passwordHash = await bcrypt.hash(password, 10);
    const user = await createUser({ name, email, passwordHash });
    const token = signToken(user);

    res.status(201).json({
      success: true,
      message: "Compte créé avec succès",
      token,
      user: toPublicUser(user),
    });
  } catch (err) {
    next(err);
  }
});

router.post("/login", validate(schemas.login), async (req, res, next) => {
  try {
    const { email, password } = req.validated;

    const user = await findUserByEmail(email);
    if (!user) {
      return res.status(401).json({
        success: false,
        error: "Email ou mot de passe incorrect",
      });
    }

    const valid = await bcrypt.compare(password, user.passwordHash);
    if (!valid) {
      return res.status(401).json({
        success: false,
        error: "Email ou mot de passe incorrect",
      });
    }

    const token = signToken(user);

    res.json({
      success: true,
      message: "Connexion réussie",
      token,
      user: toPublicUser(user),
    });
  } catch (err) {
    next(err);
  }
});

router.get("/me", requireAuth, (req, res) => {
  res.json({ success: true, user: req.user });
});

export default router;
