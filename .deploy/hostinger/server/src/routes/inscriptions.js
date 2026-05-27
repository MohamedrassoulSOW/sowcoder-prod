import { Router } from "express";
import { saveInscription } from "../db/store.js";
import { sendNotification, formatInscriptionEmail } from "../services/email.js";
import { validate, schemas } from "../middleware/validate.js";

const router = Router();

router.post("/", validate(schemas.inscription), async (req, res, next) => {
  try {
    const data = req.validated;
    const record = await saveInscription(data);

    const mail = formatInscriptionEmail(data);
    const emailResult = await sendNotification(mail);

    res.status(201).json({
      success: true,
      message: "Votre demande d'inscription a été enregistrée.",
      id: record.id,
      emailSent: emailResult.sent,
    });
  } catch (err) {
    next(err);
  }
});

export default router;
