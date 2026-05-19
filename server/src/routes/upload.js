import { Router } from "express";
import { requireAdmin } from "../middleware/auth.js";
import { uploadImage } from "../middleware/upload.js";

const router = Router();

router.post(
  "/",
  requireAdmin,
  (req, res, next) => {
    uploadImage.single("image")(req, res, (err) => {
      if (err) {
        return res.status(400).json({
          success: false,
          error: err.message || "Échec du téléversement",
        });
      }
      next();
    });
  },
  (req, res) => {
    if (!req.file) {
      return res.status(400).json({
        success: false,
        error: "Aucun fichier reçu",
      });
    }

    const url = `/uploads/${req.file.filename}`;
    res.json({
      success: true,
      data: { url, filename: req.file.filename },
    });
  }
);

export default router;
