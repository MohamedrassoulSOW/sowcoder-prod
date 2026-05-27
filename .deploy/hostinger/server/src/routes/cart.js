import { Router } from "express";
import { saveCartOrder } from "../db/store.js";
import { sendNotification } from "../services/email.js";
import { validate, schemas } from "../middleware/validate.js";
import { requireAuth } from "../middleware/auth.js";

const router = Router();

router.post("/checkout", validate(schemas.cartCheckout), async (req, res, next) => {
  try {
    const data = req.validated;
    const itemsList = data.items
      .map((i) => `• ${i.title} — ${i.price}`)
      .join("\n");

    const record = await saveCartOrder({
      ...data,
      productTitle: `Panier (${data.items.length} article${data.items.length > 1 ? "s" : ""})`,
      message: [data.message, "", "Articles:", itemsList].filter(Boolean).join("\n"),
    });

    const subject = `[Panier] Commande de ${data.name}`;
    await sendNotification({
      subject,
      text: [
        `Client: ${data.name}`,
        `Email: ${data.email}`,
        `Téléphone: ${data.phone || "—"}`,
        "",
        "Articles:",
        itemsList,
        data.message ? `\nMessage:\n${data.message}` : "",
      ].join("\n"),
      html: `<h2>Commande panier</h2><pre>${itemsList}</pre>`,
    });

    res.status(201).json({
      success: true,
      message: "Commande envoyée avec succès",
      id: record.id,
    });
  } catch (err) {
    next(err);
  }
});

router.get("/sync", requireAuth, (_req, res) => {
  res.json({ success: true, message: "Session active" });
});

export default router;
