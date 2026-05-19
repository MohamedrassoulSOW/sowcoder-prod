/**
 * Copie le build Vite (dist/) vers backend/public/ pour la production.
 * Conserve index.php et .htaccess Symfony.
 */
import fs from "node:fs";
import path from "node:path";
import { fileURLToPath } from "node:url";

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), "..");
const distDir = path.join(root, "dist");
const publicDir = path.join(root, "backend", "public");
const preserve = new Set(["index.php", "router.php", ".htaccess"]);

function copyRecursive(src, dest) {
  const stat = fs.statSync(src);
  if (stat.isDirectory()) {
    if (!fs.existsSync(dest)) fs.mkdirSync(dest, { recursive: true });
    for (const name of fs.readdirSync(src)) {
      copyRecursive(path.join(src, name), path.join(dest, name));
    }
    return;
  }
  fs.copyFileSync(src, dest);
}

if (!fs.existsSync(distDir)) {
  console.error("Erreur : dossier dist/ introuvable. Lancez d'abord : npm run build");
  process.exit(1);
}

for (const name of fs.readdirSync(publicDir)) {
  if (preserve.has(name)) continue;
  const target = path.join(publicDir, name);
  fs.rmSync(target, { recursive: true, force: true });
}

for (const name of fs.readdirSync(distDir)) {
  copyRecursive(path.join(distDir, name), path.join(publicDir, name));
}

console.log("OK — dist/ copié vers backend/public/");
console.log("   Déployez le contenu de backend/public/ sur Hostinger (document root).");
