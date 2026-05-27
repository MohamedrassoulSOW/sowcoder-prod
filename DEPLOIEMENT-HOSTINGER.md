# Mise en ligne Hostinger — SowCoder

## Commande unique (sur votre PC)

```bash
npm ci
npm run build:prod
```

Vérifie que `backend/public/index.html` charge `/assets/....js` (pas `/src/main.jsx`).

---

## Arborescence sur Hostinger

```
domains/ms.sowcoder.com/
├── public_html/              ← CONTENU de backend/public/ (document root)
│   ├── index.html            ← build React
│   ├── assets/               ← JS + CSS
│   ├── favicon.svg
│   ├── index.php             ← API Symfony
│   └── .htaccess
├── backend/                  ← Symfony (hors web si possible)
│   ├── bin/
│   ├── config/
│   ├── public/               ← copie identique ou symlink vers public_html
│   ├── src/
│   ├── var/                  ← chmod 775 (cache, logs)
│   └── vendor/               ← composer install --no-dev
└── server/
    └── data/
        ├── site-content.json
        └── uploads/          ← chmod 775
```

Si Hostinger n’autorise qu’un seul `public_html`, mettez-y **uniquement** le contenu de `backend/public/` et placez le reste de `backend/` **au-dessus** (chemin hors `public_html`).

---

## Configuration backend

1. Copier `backend/.env.prod.dist` → `backend/.env`
2. Remplir MySQL, `APP_SECRET`, `JWT_PASSPHRASE`, mots de passe admin
3. SSH / terminal Hostinger :

```bash
cd backend
composer install --no-dev --optimize-autoloader
php bin/console lexik:jwt:generate-keypair --skip-if-exists
php bin/console doctrine:migrations:migrate --no-interaction --env=prod
php bin/console app:ensure-admin --env=prod
php bin/console cache:clear --env=prod
```

---

## Tests après upload

| URL | Attendu |
|-----|---------|
| https://ms.sowcoder.com/ | Page d’accueil |
| https://ms.sowcoder.com/blog | Liste blog |
| https://ms.sowcoder.com/api/health | JSON success |
| F12 Console | Pas d’erreur `main.jsx` |

---

## Erreur page blanche

Cause : `index.html` de **développement** déployé (`/src/main.jsx`).

Solution : `npm run build:prod` puis renvoyer **index.html** + **assets/** sur le serveur.
