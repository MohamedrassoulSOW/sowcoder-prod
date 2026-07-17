# SowCoder — Site vitrine PHP

Site vitrine **SowCoder** (PHP + MySQL), compatible WAMP et Hostinger.

## Prérequis

- PHP **≥ 8.2** (`pdo_mysql`, `mbstring`, `curl`, `openssl`)
- MySQL / MariaDB
- Composer

## Installation locale (WAMP)

1. Démarrez Apache + MySQL.
2. `composer install`
3. Copiez `.env.example` → `.env` et renseignez Mailjet / DB.
4. Importez la base :

```bash
mysql -u root < database/install.sql
mysql -u root < database/site_tables.sql
php scripts/seed-site.php
```

5. Ouvrez : http://localhost/mon-site-vitrine/

## Admin (après seed)

- URL : `/?page=admin`
- Compte créé par `database/install.sql` — **changez le mot de passe dès la première connexion** (ne laissez jamais le mot de passe par défaut en production).

## Production Hostinger

1. Créez une base MySQL dans hPanel (notez host, nom, user, mot de passe).
2. Choisissez **PHP 8.2 ou 8.3** (ou 8.4).
3. Uploadez le projet dans `public_html` (sans `.git`, sans `.env` local).
4. Sur le serveur : créez `.env` à partir de `.env.example` :

```env
DB_HOST=localhost
DB_NAME=uXXXX_sowcoder
DB_USER=uXXXX_user
DB_PASS=********
MAILJET_API_KEY=…
MAILJET_API_SECRET=…
MAILJET_FROM_EMAIL=contact@sowcoder.com
MAILJET_FROM_NAME=SowCoser
MAILJET_NEWSLETTER_LIST_ID=<id réel>
MAIL_DEBUG=false
```

5. `composer install --no-dev --optimize-autoloader` (SSH) **ou** uploadez un `vendor/` généré pour PHP 8.2.
6. Importez `database/install.sql` + `database/site_tables.sql` (adaptez le nom de BDD).
7. Vérifiez que `/.env`, `/app/`, `/vendor/` renvoient **403**.
8. Connectez-vous en admin et **changez immédiatement** le mot de passe.

## Structure

```
mon-site-vitrine/
├── index.php
├── .env.example
├── .htaccess
├── app/           # PHP (config, pages, auth, contenu)
├── assets/        # CSS / JS
├── database/      # SQL
├── scripts/       # seed-site.php
├── src/           # MailjetService
└── vendor/        # Composer (gitignored)
```

Config technique : `.env` + repli `app/config.php`. Contenu du site : tables `site_*` + dashboard.
