import React, { useEffect, useState } from "react";
import api from "../api";
import { Link } from "react-router-dom";

const API_BASE = "/api";

const BlogList = () => {
  const [blogs, setBlogs] = useState([]);
  const [filteredBlogs, setFilteredBlogs] = useState([]);

  const [websites, setWebsites] = useState([]);
  const [selectedWebsite, setSelectedWebsite] = useState("");
  const [searchText, setSearchText] = useState("");


  // Load blogs + websites
  useEffect(() => {
    fetchBlogs();
    fetchWebsites();
  }, []);

  const fetchBlogs = () => {
    api
      .get(`${API_BASE}/blogs`)
      .then((res) => {
        // Keep ONLY allowed blogs
        setBlogs(res.data);
        setFilteredBlogs(res.data);
      })
      .catch((err) => console.error(err));
  };

  const fetchWebsites = () => {
    api
      .get(`${API_BASE}/websites/all`)
      .then((res) => {
        // Keep ONLY allowed websites
        const allowed = res.data.websites;

        setWebsites(allowed);

        // Auto-select the first allowed website
        if (allowed.length > 0) {
          setSelectedWebsite(allowed[0]._id);
        }
      })
      .catch((err) => console.error(err));
  };

  // Filter blogs based on website + search
  useEffect(() => {
    let data = blogs;

    // Filter by website
    if (selectedWebsite) {
      data = data.filter((b) => (b.websiteId?._id || b.websiteId) === selectedWebsite);
    }

    // Search filter
    if (searchText.trim() !== "") {
      data = data.filter((b) =>
        b.title.toLowerCase().includes(searchText.toLowerCase())
      );
    }

    setFilteredBlogs(data);
  }, [selectedWebsite, searchText, blogs]);

  return (
    <div className="container mt-4">
      <h2>Blogs</h2>

      {/* Filters */}
      <div className="row mt-3 mb-4">
        {/* Website Filter */}
        <div className="col-md-3">
          <label>Filter by Website</label>
          <select
            className="form-control"
            value={selectedWebsite}
            onChange={(e) => setSelectedWebsite(e.target.value)}
          >
            {websites.map((w) => (
              <option key={w._id} value={w._id}>
                {w.name}
              </option>
            ))}
          </select>
        </div>

        {/* Search Filter */}
        <div className="col-md-3">
          <label>Search Blog</label>
          <input
            className="form-control"
            placeholder="Search by blog title..."
            value={searchText}
            onChange={(e) => setSearchText(e.target.value)}
          />
        </div>

        {/* Reset */}
        <div className="col-md-3 d-flex align-items-end">
          <button
            className="btn btn-secondary w-100"
            onClick={() => {
              if (websites.length > 0) {
                setSelectedWebsite(websites[0]._id); // Reset to first allowed website
              }
              setSearchText("");
            }}
          >
            Reset Filters
          </button>
        </div>
      </div>

      {/* Blog Table */}
      <table className="table table-bordered">
        <thead>
          <tr>
            <th>Title</th>
            <th>Slug</th>
            <th>Website</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
          {filteredBlogs.length === 0 ? (
            <tr>
              <td colSpan="5" className="text-center">No blogs found</td>
            </tr>
          ) : (
            filteredBlogs.map((blog) => (
              <tr key={blog._id}>
                <td>{blog.title}</td>
                <td>{blog.slug}</td>
                <td>{blog.websiteId?.name || websites.find((w) => w._id === blog.websiteId)?.name}</td>
                <td>{new Date(blog.createdAt).toLocaleDateString()}</td>
                <td>
                  <Link
                    to={`/edit-blog/${blog._id}`}
                    className="btn btn-primary btn-sm"
                  >
                    Edit
                  </Link>
                </td>
              </tr>
            ))
          )}
        </tbody>
      </table>
    </div>
  );
};

export default BlogList;
