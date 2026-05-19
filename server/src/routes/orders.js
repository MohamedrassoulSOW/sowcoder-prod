import { Router } from "express";
import { saveOrder } from "../db/store.js";
import { sendNotification, formatOrderEmail } from "../services/email.js";
import { validate, schemas } from "../middleware/validate.js";

const router = Router();

router.post("/", validate(schemas.order), async (req, res, next) => {
  try {
    const data = req.validated;
    const record = await saveOrder(data);

    const mail = formatOrderEmail(data);
    const emailResult = await sendNotification(mail);

    res.status(201).json({
      success: true,
      message: "Votre demande de commande a été enregistrée.",
      id: record.id,
      emailSent: emailResult.sent,
    });
  } catch (err) {
    next(err);
  }
});

export default router;
