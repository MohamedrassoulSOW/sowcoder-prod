export function errorHandler(err, _req, res) {
  console.error("[api error]", err);
  res.status(err.status || 500).json({
    success: false,
    error: err.message || "Erreur serveur interne",
  });
}
