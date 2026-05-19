import { Router } from "express";
import { loadSiteContent } from "../db/store.js";

const router = Router();

router.get("/", async (_req, res, next) => {
  try {
    const content = await loadSiteContent();
    res.json({ success: true, data: content });
  } catch (err) {
    next(err);
  }
});

export default router;
