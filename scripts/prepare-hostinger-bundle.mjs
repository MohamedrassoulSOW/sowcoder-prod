import fs from "node:fs";
import path from "node:path";
import { fileURLToPath } from "node:url";

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), "..");
const bundleRoot = path.join(root, ".deploy", "hostinger");
const distDir = path.join(root, "dist");

function ensureDir(dir) {
  fs.mkdirSync(dir, { recursive: true });
}

function copyFileSafe(src, dest) {
  ensureDir(path.dirname(dest));
  fs.copyFileSync(src, dest);
}

function copyDirRecursive(src, dest) {
  const stat = fs.statSync(src);
  if (!stat.isDirectory()) {
    copyFileSafe(src, dest);
    return;
  }
  ensureDir(dest);
  for (const entry of fs.readdirSync(src)) {
    copyDirRecursive(path.join(src, entry), path.join(dest, entry));
  }
}

function resetBundle() {
  fs.rmSync(bundleRoot, { recursive: true, force: true });
  ensureDir(bundleRoot);
}

if (!fs.existsSync(distDir)) {
  console.error("Erreur: dist/ introuvable. Lancez d'abord: npm run build:prod");
  process.exit(1);
}

resetBundle();

// Runtime files only (minimal upload for Hostinger Node app)
copyDirRecursive(path.join(root, "dist"), path.join(bundleRoot, "dist"));
copyDirRecursive(path.join(root, "server", "src"), path.join(bundleRoot, "server", "src"));
copyFileSafe(
  path.join(root, "server", "package.json"),
  path.join(bundleRoot, "server", "package.json")
);
copyFileSafe(
  path.join(root, "server", "package-lock.json"),
  path.join(bundleRoot, "server", "package-lock.json")
);

// Content + uploads directory
copyFileSafe(
  path.join(root, "server", "data", "site-content.json"),
  path.join(bundleRoot, "server", "data", "site-content.json")
);
ensureDir(path.join(bundleRoot, "server", "data", "uploads"));
copyFileSafe(
  path.join(root, "server", "data", "uploads", ".gitkeep"),
  path.join(bundleRoot, "server", "data", "uploads", ".gitkeep")
);

// Deployment env template
copyFileSafe(
  path.join(root, ".env.hostinger.example"),
  path.join(bundleRoot, ".env.hostinger.example")
);

console.log("OK: bundle Hostinger généré dans .deploy/hostinger");
console.log("   Uploadez le CONTENU de .deploy/hostinger vers votre dossier app Node.");
