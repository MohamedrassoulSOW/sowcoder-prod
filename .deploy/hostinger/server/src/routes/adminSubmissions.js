import { Router } from "express";
import {
  deleteSubmission,
  getSubmission,
  isValidSubmissionType,
  listSubmissions,
} from "../db/submissions.js";

const router = Router();

router.get("/:type", (req, res, next) => {
  try {
    const { type } = req.params;
    if (!isValidSubmissionType(type)) {
      return res.status(400).json({
        success: false,
        error: "Type invalide (contacts, orders, inscriptions)",
      });
    }

    const data = listSubmissions(type, {
      limit: req.query.limit,
      offset: req.query.offset,
    });
    res.json({ success: true, data });
  } catch (err) {
    next(err);
  }
});

router.get("/:type/:id", (req, res, next) => {
  try {
    const { type, id } = req.params;
    if (!isValidSubmissionType(type)) {
      return res.status(400).json({ success: false, error: "Type invalide" });
    }

    const item = getSubmission(type, id);
    res.json({ success: true, data: item });
  } catch (err) {
    next(err);
  }
});

router.delete("/:type/:id", (req, res, next) => {
  try {
    const { type, id } = req.params;
    if (!isValidSubmissionType(type)) {
      return res.status(400).json({ success: false, error: "Type invalide" });
    }

    deleteSubmission(type, id);
    res.json({ success: true, message: "Supprimé" });
  } catch (err) {
    next(err);
  }
});

export default router;
