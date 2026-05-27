import app from "./app.js";
import { config } from "./config.js";
import { setupDatabase } from "./db/init.js";

await setupDatabase();

app.listen(config.port, () => {
  console.log(`SowCoder API — http://localhost:${config.port}`);
  console.log(`Environnement: ${config.nodeEnv}`);
  console.log(`Base de données : ${config.sqlitePath}`);
  if (config.adminEmail && config.adminPassword) {
    console.log(`Admin : ${config.adminEmail}`);
  }
});
