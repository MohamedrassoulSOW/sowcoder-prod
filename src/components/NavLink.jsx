import { useLocation, useNavigate } from "react-router-dom";
import { useApp } from "../context/AppContext";
import { scrollToSection } from "../utils/scroll";

export default function NavLink({ href, children, className, onAfterNavigate }) {
  const { navigateTo } = useApp();
  const navigate = useNavigate();
  const location = useLocation();

  const handleClick = (e) => {
    e.preventDefault();
    const isHash = href.startsWith("#");

    if (isHash && location.pathname !== "/") {
      navigate("/");
      setTimeout(() => {
        scrollToSection(href);
        onAfterNavigate?.();
      }, 50);
      return;
    }

    if (isHash) {
      navigateTo(href);
    } else {
      navigate(href);
    }
    onAfterNavigate?.();
  };

  return (
    <a href={href} onClick={handleClick} className={className}>
      {children}
    </a>
  );
}
