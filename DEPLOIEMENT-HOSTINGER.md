# Déploiement Hostinger — SowCoder

## Cause de la page blanche

Si la console affiche une erreur sur **`main.jsx`** avec le type MIME `text/plain`, vous avez déployé les **fichiers sources** (développement) au lieu du **build de production**.

Le bon `index.html` doit charger :

```html
<script type="module" crossorigin src="/assets/index-xxxxx.js"></script>
```

et **pas** `/src/main.jsx`.

## Build production (obligatoire)

Sur votre PC, à la racine du projet :

```bash
npm ci
npm run build:prod
```

Cela génère `dist/` puis copie tout dans `backend/public/` (avec `.htaccess`).

## Fichiers à envoyer sur Hostinger

**Document root** = contenu du dossier `backend/public/` :

| Fichier / dossier | Obligatoire |
|-------------------|-------------|
| `index.html` | Oui (build, pas celui à la racine du repo) |
| `assets/` | Oui (JS + CSS) |
| `favicon.svg` | Oui |
| `index.php` | Oui (API Symfony) |
| `.htaccess` | Oui |

**Aussi hors de public** (structure typique) :

```
/home/.../domains/ms.sowcoder.com/
  public_html/          ← contenu de backend/public/
  backend/              ← reste de Symfony (src, config, vendor, var…)
  server/data/          ← site-content.json + uploads/ (droits écriture)
```

Si tout est dans `public_html`, placez Symfony ainsi :

- `public_html` = fichiers de `backend/public/`
- `backend/` (vendor, src, config) **au-dessus** de `public_html`, hors web

Adaptez selon votre arborescence Hostinger (parfois `public_html` = racine du domaine).

## Variables d'environnement (backend/.env)

```env
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=<clé longue aléatoire>
DATABASE_URL="mysql://USER:PASS@localhost:3306/BASE?serverVersion=8.0&charset=utf8mb4"
CORS_ALLOW_ORIGIN='^https://(www\.)?ms\.sowcoder\.com$'
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=<secret>
ADMIN_EMAIL=admin@sowcoder.sn
ADMIN_PASSWORD=<mot de passe fort>
```

Puis sur le serveur (SSH) :

```bash
cd backend
composer install --no-dev --optimize-autologader
php bin/console lexik:jwt:generate-keypair --skip-if-exists
php bin/console doctrine:migrations:migrate --no-interaction --env=prod
php bin/console cache:clear --env=prod
chmod -R 775 var ../server/data/uploads
```

## Vérification

1. https://ms.sowcoder.com/ → page d'accueil (pas blanche)
2. https://ms.sowcoder.com/api/health → JSON `success: true`
3. F12 → plus d'erreur sur `main.jsx`

## Ne pas déployer

- `src/`, `node_modules/`, `index.html` à la racine du repo (dev)
- Dossier `server/` (API Node obsolète), sauf `server/data/`
