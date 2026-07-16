# SowCoder — Site vitrine PHP

Site vitrine **SowCoder** pour **WAMP** (PHP + MySQL).

## Démarrage

1. Démarrez Apache + MySQL dans WAMP.
2. Installez la base :

```bash
mysql -u root < database/install.sql
mysql -u root < database/site_tables.sql
php scripts/seed-site.php
```

3. Ouvrez : [http://localhost/mon-site-vitrine/](http://localhost/mon-site-vitrine/)

## Admin

- URL : `/?page=admin`
- E-mail : `admin@sowcoder.com`
- Mot de passe : `Admin123!`

## Structure

```
mon-site-vitrine/
├── index.php
├── app/           # PHP (config, pages, auth, contenu)
├── assets/        # CSS / JS
├── database/      # SQL
└── scripts/       # seed-site.php
```

Connexion MySQL : `app/config.php` — contenu du site : tables `site_*` + dashboard.
