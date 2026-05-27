import { Router } from "express";
import {
  addBlogComment,
  getBlogArticle,
  listBlogArticles,
  listBlogComments,
  toggleBlogLike,
} from "../db/store.js";
import { validate } from "../middleware/validate.js";
import { blogCommentSchema, blogLikeSchema } from "../middleware/validate.js";

const router = Router();

router.get("/", async (req, res, next) => {
  try {
    const articles = await listBlogArticles(req.query.visitorId);
    res.json({ success: true, data: { articles } });
  } catch (err) {
    next(err);
  }
});

router.get("/:slug", async (req, res, next) => {
  try {
    const article = await getBlogArticle(req.params.slug, req.query.visitorId);
    if (!article) {
      return res.status(404).json({ success: false, error: "Article introuvable" });
    }
    res.json({ success: true, data: { article } });
  } catch (err) {
    next(err);
  }
});

router.get("/:slug/comments", async (req, res, next) => {
  try {
    const limit = Number.parseInt(req.query.limit, 10) || 50;
    const offset = Number.parseInt(req.query.offset, 10) || 0;
    const items = await listBlogComments(req.params.slug, { limit, offset });
    res.json({ success: true, data: { items } });
  } catch (err) {
    next(err);
  }
});

router.post("/:slug/comments", validate(blogCommentSchema), async (req, res, next) => {
  try {
    const article = await getBlogArticle(req.params.slug);
    if (!article) {
      return res.status(404).json({ success: false, error: "Article introuvable" });
    }
    const item = await addBlogComment({ slug: req.params.slug, ...req.validated });
    res.status(201).json({ success: true, data: item });
  } catch (err) {
    next(err);
  }
});

router.post("/:slug/like", validate(blogLikeSchema), async (req, res, next) => {
  try {
    const article = await getBlogArticle(req.params.slug);
    if (!article) {
      return res.status(404).json({ success: false, error: "Article introuvable" });
    }
    const result = await toggleBlogLike(req.params.slug, req.validated.visitorId);
    res.json({ success: true, ...result });
  } catch (err) {
    next(err);
  }
});

export default router;
