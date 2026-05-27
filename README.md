# SowCoder — Site vitrine

Frontend **React + Vite**, API **Node.js (Express)**, contenu éditable via dashboard admin.

## Développement local

```bash
npm ci
npm --prefix server ci
npm run dev:all
```

- Site : http://localhost:5173  
- API : http://localhost:3001  

## Production (Node.js)

```bash
npm run build:prod
npm start
```

En production, le serveur Node expose `dist/` et les endpoints API (`/api/*`).

Vérification : `npm run deploy:check`

Préparation Hostinger (bundle prêt à uploader) :

```bash
npm run hostinger:prepare
```

## Compte admin (dev)

- Email : `admin@sowcoder.sn` (voir `.env` ou `server/.env`)
- Changer le mot de passe avant la mise en ligne.
