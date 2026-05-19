import nodemailer from "nodemailer";
import { config } from "../config.js";

function isSmtpConfigured() {
  return Boolean(config.smtp.host && config.smtp.user && config.smtp.pass);
}

function getTransporter() {
  if (!isSmtpConfigured()) return null;
  return nodemailer.createTransport({
    host: config.smtp.host,
    port: config.smtp.port,
    secure: config.smtp.secure,
    auth: {
      user: config.smtp.user,
      pass: config.smtp.pass,
    },
  });
}

export async function sendNotification({ subject, html, text }) {
  if (!isSmtpConfigured()) {
    console.info("[email] SMTP non configuré — notification ignorée:", subject);
    return { sent: false, reason: "smtp_not_configured" };
  }

  const transporter = getTransporter();
  await transporter.sendMail({
    from: `"SowCoder Site" <${config.smtp.user}>`,
    to: config.contactEmail,
    replyTo: config.contactEmail,
    subject,
    text,
    html,
  });

  return { sent: true };
}

export function formatContactEmail(data) {
  const subject = `[Contact] ${data.subject || "Nouveau message"} — ${data.name}`;
  const text = [
    `Nom: ${data.name}`,
    `Email: ${data.email}`,
    `Téléphone: ${data.phone || "—"}`,
    `Sujet: ${data.subject || "—"}`,
    "",
    data.message,
  ].join("\n");

  const html = `
    <h2>Nouveau message de contact</h2>
    <p><strong>Nom:</strong> ${escapeHtml(data.name)}</p>
    <p><strong>Email:</strong> ${escapeHtml(data.email)}</p>
    <p><strong>Téléphone:</strong> ${escapeHtml(data.phone || "—")}</p>
    <p><strong>Sujet:</strong> ${escapeHtml(data.subject || "—")}</p>
    <hr />
    <p>${escapeHtml(data.message).replace(/\n/g, "<br>")}</p>
  `;

  return { subject, text, html };
}

export function formatOrderEmail(data) {
  const subject = `[Commande] ${data.productTitle} — ${data.name}`;
  const text = [
    `Produit: ${data.productTitle}`,
    `Nom: ${data.name}`,
    `Email: ${data.email}`,
    `Téléphone: ${data.phone || "—"}`,
    data.message ? `\nMessage:\n${data.message}` : "",
  ].join("\n");

  const html = `
    <h2>Nouvelle demande de commande</h2>
    <p><strong>Produit:</strong> ${escapeHtml(data.productTitle)}</p>
    <p><strong>Nom:</strong> ${escapeHtml(data.name)}</p>
    <p><strong>Email:</strong> ${escapeHtml(data.email)}</p>
    <p><strong>Téléphone:</strong> ${escapeHtml(data.phone || "—")}</p>
    ${data.message ? `<p><strong>Message:</strong><br>${escapeHtml(data.message).replace(/\n/g, "<br>")}</p>` : ""}
  `;

  return { subject, text, html };
}

export function formatInscriptionEmail(data) {
  const subject = `[Inscription] ${data.formationTitle} — ${data.name}`;
  const text = [
    `Formation: ${data.formationTitle}`,
    `Nom: ${data.name}`,
    `Email: ${data.email}`,
    `Téléphone: ${data.phone || "—"}`,
    data.message ? `\nMessage:\n${data.message}` : "",
  ].join("\n");

  const html = `
    <h2>Nouvelle inscription formation</h2>
    <p><strong>Formation:</strong> ${escapeHtml(data.formationTitle)}</p>
    <p><strong>Nom:</strong> ${escapeHtml(data.name)}</p>
    <p><strong>Email:</strong> ${escapeHtml(data.email)}</p>
    <p><strong>Téléphone:</strong> ${escapeHtml(data.phone || "—")}</p>
    ${data.message ? `<p><strong>Message:</strong><br>${escapeHtml(data.message).replace(/\n/g, "<br>")}</p>` : ""}
  `;

  return { subject, text, html };
}

function escapeHtml(str) {
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}
