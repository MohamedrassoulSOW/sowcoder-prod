import { useRef, useState } from "react";
import { ImagePlus, Loader2, Trash2 } from "lucide-react";
import { api } from "../api/client";
import { mediaUrl } from "../utils/mediaUrl";

export default function ImageUpload({ value = "", onChange, label = "Photo" }) {
  const inputRef = useRef(null);
  const [uploading, setUploading] = useState(false);
  const [error, setError] = useState("");

  const handleFile = async (e) => {
    const file = e.target.files?.[0];
    if (!file) return;

    setUploading(true);
    setError("");
    try {
      const res = await api.uploadImage(file);
      onChange(res.data.url);
    } catch (err) {
      setError(err.message);
    } finally {
      setUploading(false);
      if (inputRef.current) inputRef.current.value = "";
    }
  };

  return (
    <div className="space-y-2">
      <span className="block text-xs font-medium text-slate-400">{label}</span>

      {value ? (
        <div className="relative overflow-hidden rounded-lg border border-white/10">
          <img
            src={mediaUrl(value)}
            alt=""
            className="h-36 w-full object-cover"
          />
          <div className="absolute right-2 top-2 flex gap-1">
            <button
              type="button"
              onClick={() => inputRef.current?.click()}
              disabled={uploading}
              className="rounded-lg bg-slate-900/90 px-2 py-1 text-xs text-white hover:bg-slate-800"
            >
              Remplacer
            </button>
            <button
              type="button"
              onClick={() => onChange("")}
              className="rounded-lg bg-red-500/90 p-1.5 text-white hover:bg-red-500"
              title="Supprimer la photo"
            >
              <Trash2 className="h-3.5 w-3.5" />
            </button>
          </div>
        </div>
      ) : (
        <button
          type="button"
          onClick={() => inputRef.current?.click()}
          disabled={uploading}
          className="flex h-28 w-full flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-cyan-500/30 bg-slate-950/50 text-sm text-slate-400 transition hover:border-cyan-500/50 hover:text-cyan-300 disabled:opacity-50"
        >
          {uploading ? (
            <Loader2 className="h-6 w-6 animate-spin text-cyan-400" />
          ) : (
            <>
              <ImagePlus className="h-6 w-6 text-cyan-500/70" />
              <span>Choisir une image (max 5 Mo)</span>
            </>
          )}
        </button>
      )}

      <input
        ref={inputRef}
        type="file"
        accept="image/jpeg,image/png,image/webp,image/gif"
        className="hidden"
        onChange={handleFile}
      />

      {error && <p className="text-xs text-red-400">{error}</p>}
    </div>
  );
}
