import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import api from "./api";

const LoginPage = () => {
  const [apiKey, setApiKey] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const navigate = useNavigate();

  const handleLogin = async (event) => {
    event.preventDefault(); setLoading(true); setError("");
    localStorage.setItem("adminApiKey", apiKey);
    try {
      await api.get("/api/blogs");
      localStorage.setItem("isLoggedIn", "true");
      navigate("/");
    } catch (err) {
      localStorage.removeItem("adminApiKey");
      setError(err.response?.data?.error || "Connection failed. Check the API URL and admin key.");
    } finally { setLoading(false); }
  };

  return <div className="login-page"><section className="login-story"><span className="brand-mark">M</span><span className="eyebrow">MULTISITE CONTENT OS</span><h1>One calm place for every website.</h1><p>Publish, optimize and maintain your digital properties without switching between tools.</p><div className="login-proof"><span>01</span>Central publishing <span>02</span>Technical SEO <span>03</span>Site controls</div></section><section className="login-form-wrap"><form onSubmit={handleLogin} className="login-card"><span className="eyebrow">SECURE WORKSPACE</span><h2>Welcome back</h2><p>Enter the admin API key configured on your server.</p>{error && <div className="notice error">{error}</div>}<label>Admin API key<input type="password" value={apiKey} onChange={(e) => setApiKey(e.target.value)} autoComplete="current-password" required placeholder="••••••••••••••••" /></label><button disabled={loading}>{loading ? "Checking…" : "Enter workspace"}</button><small>The key stays in this browser and is sent only to your configured API.</small></form></section></div>;
};

export default LoginPage;
