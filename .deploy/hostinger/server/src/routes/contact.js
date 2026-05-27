import { Router } from "express";
import { saveContact } from "../db/store.js";
import { sendNotification, formatContactEmail } from "../services/email.js";
import { validate, schemas } from "../middleware/validate.js";

const router = Router();

router.post("/", validate(schemas.contact), async (req, res, next) => {
  try {
    const data = req.validated;
    const record = await saveContact(data);

    const mail = formatContactEmail(data);
    const emailResult = await sendNotification(mail);

    res.status(201).json({
      success: true,
      message: "Votre message a bien été envoyé. Nous vous répondrons sous 24h.",
      id: record.id,
      emailSent: emailResult.sent,
    });
  } catch (err) {
    next(err);
  }
});

export default router;
