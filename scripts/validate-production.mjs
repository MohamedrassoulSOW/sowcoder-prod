import fs from "node:fs";
import path from "node:path";
import { fileURLToPath } from "node:url";

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), "..");
const distDir = path.join(root, "dist");
const indexPath = path.join(distDir, "index.html");
const assetsDir = path.join(distDir, "assets");

let ok = true;

function fail(msg) {
  console.error("ERREUR:", msg);
  ok = false;
}

function pass(msg) {
  console.log("OK:", msg);
}

if (!fs.existsSync(indexPath)) {
  fail("dist/index.html manquant — lancez npm run build:prod");
} else {
  const html = fs.readFileSync(indexPath, "utf8");
  if (html.includes("/src/main.jsx")) {
    fail(
      "index.html pointe encore vers /src/main.jsx (fichier DEV). Lancez npm run build:prod"
    );
  } else if (!html.includes("/assets/") || !html.includes(".js")) {
    fail("index.html ne référence pas de bundle /assets/*.js");
  } else {
    pass("index.html = build production");
  }
}

if (!fs.existsSync(assetsDir)) {
  fail("backend/public/assets/ manquant");
} else {
  const js = fs.readdirSync(assetsDir).filter((f) => f.endsWith(".js"));
  if (js.length === 0) fail("aucun fichier .js dans assets/");
  else pass(`assets/ contient ${js.length} fichier(s) JS`);
}

for (const file of ["favicon.svg"]) {
  if (!fs.existsSync(path.join(distDir, file))) {
    fail(`${file} manquant dans dist/`);
  }
}

if (fs.existsSync(path.join(distDir, "src"))) {
  fail("dossier dist/src/ ne doit pas exister en production");
}

process.exit(ok ? 0 : 1);
