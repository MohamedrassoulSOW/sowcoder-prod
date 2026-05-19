import { mediaUrl } from "../utils/mediaUrl";

export default function ContentImage({
  src,
  alt = "",
  className = "",
  fallbackClassName = "",
}) {
  if (!src) {
    if (!fallbackClassName) return null;
    return <div className={fallbackClassName} aria-hidden />;
  }

  return (
    <img
      src={mediaUrl(src)}
      alt={alt}
      className={className}
      loading="lazy"
      decoding="async"
    />
  );
}
