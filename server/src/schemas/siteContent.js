import { z } from "zod";

const shortText = z.string().trim().min(1).max(500);
const mediumText = z.string().trim().min(1).max(2000);

const optionalImage = z
  .string()
  .max(500)
  .optional()
  .transform((val) => (val?.trim() ? val.trim() : ""));

const statSchema = z.object({
  value: shortText,
  label: shortText,
});

const serviceSchema = z.object({
  title: shortText,
  description: mediumText,
  icon: z.string().trim().min(1).max(50),
  image: optionalImage,
});

const featureSchema = z.object({
  title: shortText,
  description: mediumText,
  icon: z.string().trim().min(1).max(50),
});

const testimonialSchema = z.object({
  text: mediumText,
  name: shortText,
  role: shortText,
  initial: z.string().trim().min(1).max(3),
  image: optionalImage,
});

const formationSchema = z.object({
  title: shortText,
  duration: shortText,
  level: shortText,
  topics: z.array(shortText).min(1).max(20),
  image: optionalImage,
});

const blogPostSchema = z.object({
  slug: z
    .string()
    .trim()
    .min(2)
    .max(120)
    .regex(/^[a-z0-9]+(?:-[a-z0-9]+)*$/, "Slug invalide"),
  title: shortText,
  excerpt: mediumText,
  category: shortText,
  date: shortText,
  readTime: shortText,
  body: z.array(mediumText).min(1).max(50),
  image: optionalImage,
});

const boutiqueProductSchema = z.object({
  title: shortText,
  price: shortText,
  description: mediumText,
  tag: shortText,
  image: optionalImage,
});

const projectSchema = z.object({
  title: shortText,
  description: mediumText,
  category: shortText,
  client: z.string().trim().max(200).optional().default(""),
  year: z.string().trim().max(20).optional().default(""),
  url: z.string().trim().max(500).optional().default(""),
  technologies: z.array(shortText).min(0).max(15).optional().default([]),
  image: optionalImage,
});

const phoneSchema = z.object({
  label: shortText,
  number: shortText,
});

const linkSchema = z.object({
  label: shortText,
  href: z.string().trim().min(1).max(300),
});

const legalPageSchema = z.object({
  title: shortText,
  content: z.array(mediumText).min(1).max(30),
});

export const siteContentSchema = z.object({
  hero: z.object({
    badge: shortText,
    title: shortText,
    titleHighlight: shortText,
    subtitle: mediumText,
    ctaPrimary: shortText,
    ctaSecondary: shortText,
    image: optionalImage,
  }),
  stats: z.array(statSchema).min(1).max(12),
  services: z.array(serviceSchema).min(1).max(20),
  projects: z.array(projectSchema).min(0).max(40),
  whyUs: z.object({
    bullets: z.array(shortText).min(1).max(20),
    features: z.array(featureSchema).min(1).max(12),
  }),
  testimonials: z.array(testimonialSchema).min(0).max(30),
  formations: z.array(formationSchema).min(0).max(30),
  blogPosts: z.array(blogPostSchema).min(0).max(50),
  boutiqueProducts: z.array(boutiqueProductSchema).min(0).max(30),
  navLinks: z.array(linkSchema).min(1).max(20),
  footerLinks: z.object({
    services: z.array(linkSchema).min(0).max(20),
    links: z.array(linkSchema).min(0).max(20),
    contact: z.object({
      address: shortText,
      email: z.string().trim().email().max(200),
      phones: z.array(phoneSchema).min(1).max(10),
    }),
  }),
  socialLinks: z.array(
    z.object({
      label: shortText,
      href: z.string().trim().min(1).max(500),
    })
  ).min(0).max(20),
  legalPages: z.object({
    mentions: legalPageSchema,
    privacy: legalPageSchema,
  }),
});
