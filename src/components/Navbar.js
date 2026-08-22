import React from "react";
import { NavLink, useLocation, useNavigate } from "react-router-dom";

const links = [
  ["/", "Overview", "OV"],
  ["/websites", "Websites & SEO", "WS"],
  ["/add-blog", "Create blog", "CB"],
  ["/edit-blog", "Blog library", "BL"],
  ["/categories", "Categories", "CT"],
  ["/products", "Products", "PR"],
  ["/Accessory", "Pricing", "PC"],
];

const Navbar = () => {
  const location = useLocation();
  const navigate = useNavigate();
  if (location.pathname === "/login") return null;

  const logout = () => {
    localStorage.removeItem("isLoggedIn");
    localStorage.removeItem("adminApiKey");
    navigate("/login");
  };

  return (
    <aside className="sidebar">
      <div className="brand"><span className="brand-mark">M</span><div><strong>MultiSite</strong><small>Content OS</small></div></div>
      <nav className="side-nav">
        <span className="nav-label">Workspace</span>
        {links.map(([to, label, icon]) => (
          <NavLink key={to} to={to} end={to === "/"} className={({ isActive }) => `side-link ${isActive ? "active" : ""}`}>
            <span className="nav-icon">{icon}</span>{label}
          </NavLink>
        ))}
      </nav>
      <div className="sidebar-foot">
        <div className="admin-avatar">AD</div><div className="admin-meta"><strong>Administrator</strong><small>SEO workspace</small></div>
        <button onClick={logout} className="logout-btn" aria-label="Log out">↗</button>
      </div>
    </aside>
  );
};

export default Navbar;
