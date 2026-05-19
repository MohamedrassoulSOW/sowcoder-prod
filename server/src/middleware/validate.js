import { z } from "zod";

const contactSchema = z.object({
  name: z.string().trim().min(2, "Nom trop court").max(120),
  email: z.string().trim().email("Email invalide").max(200),
  phone: z.string().trim().max(30).optional().or(z.literal("")),
  subject: z.string().trim().max(200).optional().or(z.literal("")),
  message: z.string().trim().min(10, "Message trop court").max(5000),
});

const orderSchema = z.object({
  name: z.string().trim().min(2).max(120),
  email: z.string().trim().email().max(200),
  phone: z.string().trim().max(30).optional().or(z.literal("")),
  productTitle: z.string().trim().min(2).max(200),
  message: z.string().trim().max(2000).optional().or(z.literal("")),
});

const inscriptionSchema = z.object({
  name: z.string().trim().min(2).max(120),
  email: z.string().trim().email().max(200),
  phone: z.string().trim().max(30).optional().or(z.literal("")),
  formationTitle: z.string().trim().min(2).max(200),
  message: z.string().trim().max(2000).optional().or(z.literal("")),
});

const registerSchema = z.object({
  name: z.string().trim().min(2, "Nom trop court").max(120),
  email: z.string().trim().email("Email invalide").max(200),
  password: z
    .string()
    .min(8, "Le mot de passe doit contenir au moins 8 caractères")
    .max(128),
});

const loginSchema = z.object({
  email: z.string().trim().email("Email invalide"),
  password: z.string().min(1, "Mot de passe requis"),
});

const cartItemSchema = z.object({
  title: z.string().trim().min(1).max(200),
  price: z.string().trim().min(1).max(50),
  tag: z.string().trim().max(50).optional(),
});

const cartCheckoutSchema = z.object({
  name: z.string().trim().min(2).max(120),
  email: z.string().trim().email().max(200),
  phone: z.string().trim().max(30).optional().or(z.literal("")),
  message: z.string().trim().max(2000).optional().or(z.literal("")),
  items: z.array(cartItemSchema).min(1, "Le panier est vide"),
});

export function validate(schema) {
  return (req, res, next) => {
    const result = schema.safeParse(req.body);
    if (!result.success) {
      const errors = result.error.issues.map((e) => ({
        field: e.path.join("."),
        message: e.message,
      }));
      return res.status(400).json({
        success: false,
        error: "Validation échouée",
        errors,
      });
    }
    req.validated = result.data;
    next();
  };
}

export const schemas = {
  contact: contactSchema,
  order: orderSchema,
  inscription: inscriptionSchema,
  register: registerSchema,
  login: loginSchema,
  cartCheckout: cartCheckoutSchema,
};
