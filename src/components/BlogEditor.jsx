import React, { useState, useEffect } from "react";
import api from "../api";
import { Editor } from "@tinymce/tinymce-react";

const API_BASE = "/api";

const BlogEditor = () => {

  const [title, setTitle] = useState("");
  const [slug, setSlug] = useState("");
  const [content, setContent] = useState("");
  const [metaTitle, setMetaTitle] = useState("");
  const [metaDescription, setMetaDescription] = useState("");
  const [metaKeyword, setMetaKeyword] = useState("");

  const [websiteId, setWebsiteId] = useState("");
  const [websites, setWebsites] = useState([]);
  const [images, setImages] = useState([]);
// Website IDs allowed to create blogs

  // Fetch websites list
  useEffect(() => {
    api.get(`${API_BASE}/websites/all`)
        .then(res => setWebsites(res.data.websites , console.log(res.data.website)))
        
      .catch(err => console.log(err));
  }, []);

  // Auto-generate slug
  const generateSlug = (text) => {
    const newSlug = text
      .toLowerCase()
      .replace(/[^a-z0-9]+/g, "-")
      .replace(/(^-|-$)/g, "");

    setSlug(newSlug);
  };

  const handleTitleChange = (e) => {
    const newTitle = e.target.value;
    setTitle(newTitle);
    generateSlug(newTitle);
  };

  // Upload image handler
  const handleImageUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append("image", file);

    try {
      const res = await api.post(`${API_BASE}/blogs/upload-image`, formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });

      setImages((prev) => [...prev, res.data.url]);

      // Insert into editor
      setContent(content + `<img src="${res.data.url}" alt="Blog Image"/>`);

    } catch (err) {
      console.error(err);
    }
  };

  // Submit Blog
  const handleSubmit = async () => {
    if (!title || !websiteId || !content) {
      alert("Please fill required fields");
      return;
    }

    const payload = {
      title,
      slug,
      content,
      metaTitle,
      metaDescription,
      websiteId,
      images, 
      metaKeyword,
      status: "published"
    };

    try {
      const res = await api.post(`${API_BASE}/blogs/create`, payload);
      alert("Blog Created Successfully!");
      console.log(res.data);

      // Reset form
      setTitle("");
      setSlug("");
      setContent("");
      setMetaTitle("");
      setMetaDescription("");
      setMetaKeyword("");
      setWebsiteId("");
      setImages([]);

    } catch (err) {
      console.error(err);
      alert("Error creating blog");
    }
  };

  return (
    <div style={{ maxWidth: "900px", margin: "0 auto" }}>
      <h2>Create Blog</h2>

      {/* Title */}
      <label>Blog Title *</label>
      <input
        className="form-control mb-3"
        value={title}
        onChange={handleTitleChange}
        placeholder="Enter blog title"
      />

      {/* Slug */}
      <label>Slug (auto-generated)</label>
      <input
        className="form-control mb-3"
        value={slug}
        onChange={(e) => setSlug(e.target.value)}
      />

      {/* Website Selector */}
      <label>Select Website *</label>
      <select
        className="form-control mb-3"
        value={websiteId}
        onChange={(e) => setWebsiteId(e.target.value)}
      >
        <option value="">Choose Website</option>
        {websites.map((w) => (

          <option key={w._id} value={w._id}>
            {w.name}
          </option>
        ))}
      </select>

      {/* Meta Title */}
      <label>Meta Title</label>
      <input
        className="form-control mb-3"
        value={metaTitle}
        onChange={(e) => setMetaTitle(e.target.value)}
        placeholder="SEO Meta Title"
      />

      {/* Meta Description */}
      <label>Meta Description</label>
      <textarea
        className="form-control mb-3"
        value={metaDescription}
        onChange={(e) => setMetaDescription(e.target.value)}
        placeholder="SEO Meta Description"
      ></textarea>
<label>Meta Keyword</label>
      <textarea
        className="form-control mb-3"
        value={metaKeyword}
        onChange={(e) => setMetaKeyword(e.target.value)}
        placeholder="SEO Meta Description"
      ></textarea>

      {/* Image Upload */}
      <label>Upload Image</label>
      <input
        type="file"
        className="form-control mb-3"
        onChange={handleImageUpload}
      />

      {/* Rich Text Editor */}
      <label>Blog Content *</label>
      <Editor
        apiKey="e4yuc3ytrhl38jxs25ek9zh65kuk0llgp0nmj8olmzhocpnq"
         value={content}
  init={{
    height: 500,
    menubar: true,
    plugins: [
      "link",
      "lists",
      "image",
      "table",
      "code",
      "autolink",
      "preview",
      "anchor",
      "fullscreen",
      "searchreplace",
      "wordcount"
    ],
    toolbar:
      "undo redo | blocks | bold italic underline | " +
      "alignleft aligncenter alignright alignjustify | " +
      "bullist numlist outdent indent | link image table | code preview fullscreen",
    toolbar_sticky: true,
  }}
  onEditorChange={(newContent) => setContent(newContent)}
/>
      <button className="btn btn-primary mt-4" onClick={handleSubmit}>
        Publish Blog
      </button>
    </div>
  );
};

export default BlogEditor;
