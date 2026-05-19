import { Router } from "express";
import {
  getAllSubmissions,
  getStats,
  loadSiteContent,
  saveSiteContent,
} from "../db/store.js";
import { requireAdmin } from "../middleware/auth.js";
import { validate } from "../middleware/validate.js";
import { siteContentSchema } from "../schemas/siteContent.js";
import adminSubmissionsRoutes from "./adminSubmissions.js";

const router = Router();

router.use(requireAdmin);

router.get("/content", async (_req, res, next) => {
  try {
    const content = await loadSiteContent();
    res.json({ success: true, data: content });
  } catch (err) {
    next(err);
  }
});

router.put("/content", validate(siteContentSchema), async (req, res, next) => {
  try {
    const content = await saveSiteContent(req.validated);
    res.json({ success: true, data: content });
  } catch (err) {
    next(err);
  }
});

router.get("/stats", async (_req, res, next) => {
  try {
    const stats = await getStats();
    res.json({ success: true, data: stats });
  } catch (err) {
    next(err);
  }
});

router.get("/submissions", async (_req, res, next) => {
  try {
    const data = await getAllSubmissions();
    res.json({ success: true, data });
  } catch (err) {
    next(err);
  }
});

router.use("/submissions", adminSubmissionsRoutes);

export default router;
