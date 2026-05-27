# Mise en ligne Hostinger — SowCoder (Node.js)

## 1) Préparer le bundle prêt à uploader (sur votre PC)

```bash
npm ci
npm --prefix server ci
npm run hostinger:prepare
```

Le script génère un dossier prêt à déployer:

```text
.deploy/hostinger/
```

## 2) Uploader sur Hostinger

Uploadez le **contenu** de `.deploy/hostinger/` dans le dossier de votre app Node
(par exemple `domains/ms.sowcoder.com/app/`).

Structure attendue après upload:

```text
app/
├── dist/
├── server/
│   ├── src/
│   ├── data/
│   │   ├── site-content.json
│   │   └── uploads/
│   ├── package.json
│   └── package-lock.json
└── .env.hostinger.example
```

## 3) Configurer l'app Node dans hPanel

Dans **Advanced > Node.js**:

- **Node version**: 20+ recommandé
- **Application root**: dossier `app` (celui qui contient `dist/` et `server/`)
- **Application URL**: votre domaine (ex. `https://ms.sowcoder.com`)
- **Application startup file**: `server/src/index.js`

Puis faites **Create** / **Restart**.

## 4) Installer les dépendances serveur

Via terminal SSH Hostinger (dans le dossier `app`):

```bash
npm --prefix server ci --omit=dev
```

## 5) Variables d'environnement production

Créez `.env` à la racine de l'app (ou renseignez les variables dans hPanel)
à partir de `.env.hostinger.example`.

Minimum requis:

```bash
NODE_ENV=production
PORT=3001
CORS_ORIGIN=https://ms.sowcoder.com,https://www.ms.sowcoder.com
JWT_SECRET=SECRET_LONG_ALEATOIRE
ADMIN_PASSWORD=MOT_DE_PASSE_FORT
```

Ensuite redémarrez l'app dans hPanel.

## 6) Droits d'écriture

Assurez-vous que `server/data/uploads/` est inscriptible.

## 7) Vérifications finales

| URL | Attendu |
|-----|---------|
| https://ms.sowcoder.com/ | Accueil OK |
| https://ms.sowcoder.com/blog | Blog OK |
| https://ms.sowcoder.com/api/health | JSON `success: true` |
| Dashboard login | Connexion admin OK |
