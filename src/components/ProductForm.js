import React, { useState, useEffect } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { addProduct, getProducts } from '../features/products/ProductsSlice';
import { getCategories } from '../features/categories/CategoriesSlice';
import { getAccessories } from '../features/accessories/AccessoriesSlice'; // <-- NEW
import { Form, Button, Container, Row, Col } from 'react-bootstrap';
import api from "../api";

const ProductForm = () => {
  const [name, setName] = useState('');
  const [description, setDescription] = useState('');
  const [price, setPrice] = useState('');
  const [image, setImage] = useState(null);
  const [selectedCategory, setSelectedCategory] = useState('');
  const [categoryPath, setCategoryPath] = useState([]);
  const [selectedAccessories, setSelectedAccessories] = useState([]); // <-- NEW
const [availableAccessories, setAvailableAccessories] = useState([]); 

  const dispatch = useDispatch();
// Fetch all accessories from backend
useEffect(() => {
  const fetchAccessories = async () => {
    try {
      const res = await api.get("/accprice");
      setAvailableAccessories(res.data);
    } catch (err) {
      console.error("Failed to load accessories", err);
    }
  };

  fetchAccessories();
}, []);

  const { items: categories, status: categoriesStatus } = useSelector(
    (state) => state.categories
  );

  const { items: accessories } = useSelector(
    (state) => state.accessories
  ); // <-- NEW

  const { addStatus } = useSelector((state) => state.products);

  // Fetch categories
  useEffect(() => {
    if (categoriesStatus === 'idle') {
      dispatch(getCategories());
    }
  }, [categoriesStatus, dispatch]);

  // Fetch accessories on load
  useEffect(() => {
    dispatch(getAccessories());
  }, [dispatch]);

  // Success alert
  useEffect(() => {
    if (addStatus === 'succeeded') {
      alert('✅ Product added successfully!');
      dispatch(getProducts());
      resetForm();
    }
  }, [addStatus]);

  // Reset Form
  const resetForm = () => {
    setName('');
    setDescription('');
    setPrice('');
    setImage(null);
    setCategoryPath([]);
    setSelectedCategory('');
    setSelectedAccessories([]); // <-- NEW
  };

  // Filter subcategories
  const getSubcategories = (parentId) =>
    categories.filter((c) => c.parent && c.parent._id === parentId);

  const handleCategoryChange = (levelIndex, value) => {
    const updatedPath = [...categoryPath];
    updatedPath[levelIndex] = value;
    updatedPath.splice(levelIndex + 1);
    setCategoryPath(updatedPath);
    setSelectedCategory(value);
  };

  const hasSubcategories = selectedCategory
    ? getSubcategories(selectedCategory).length > 0
    : categories.some((c) => !c.parent);

  // Handle Accessories select
  const handleAccessoriesChange = (e) => {
    const options = [...e.target.options];
    const selected = options
      .filter((opt) => opt.selected)
      .map((opt) => opt.value);

    setSelectedAccessories(selected);
  };

  // Submit Form
  const handleSubmit = (e) => {
    e.preventDefault();

    if (!selectedCategory) {
      alert('Please select at least one category!');
      return;
    }

    const formData = new FormData();
    formData.append('name', name);
    formData.append('description', description);
    formData.append('price', price);
    formData.append('image', image);
    formData.append('category', selectedCategory);

    // NEW: add accessories as JSON string
    formData.append('accessories', JSON.stringify(selectedAccessories));

    dispatch(addProduct(formData));
  };

  // Top-level Categories
  const topCategories = categories.filter((c) => !c.parent);

  return (
    <Container className="mt-4">
      <h2 className="mb-4">Add Product</h2>

      <Form onSubmit={handleSubmit}>
        <Row className="mb-3">
          <Col md={6}>
            <Form.Group controlId="productName">
              <Form.Label>Product Name</Form.Label>
              <Form.Control
                type="text"
                value={name}
                onChange={(e) => setName(e.target.value)}
                required
              />
            </Form.Group>
          </Col>

          <Col md={6}>
            <Form.Group controlId="productPrice">
              <Form.Label>Price</Form.Label>
              <Form.Control
                type="number"
                value={price}
                onChange={(e) => setPrice(e.target.value)}
                required
              />
            </Form.Group>
          </Col>
        </Row>

        {/* Dynamic Categories */}
        <Row className="mb-3">
          <Col md={6}>
            <Form.Label>Category Selection</Form.Label>

            {[null, ...categoryPath].map((parentId, levelIndex) => {
              const options =
                levelIndex === 0
                  ? topCategories
                  : getSubcategories(categoryPath[levelIndex - 1]);

              if (options.length === 0) return null;

              return (
                <Form.Group
                  key={levelIndex}
                  controlId={`categoryLevel${levelIndex + 1}`}
                  className="mb-2"
                >
                  <Form.Select
                    value={categoryPath[levelIndex] || ''}
                    onChange={(e) =>
                      handleCategoryChange(levelIndex, e.target.value)
                    }
                  >
                    <option value="">
                      Select Category Level {levelIndex + 1}
                    </option>
                    {options.map((c) => (
                      <option key={c._id} value={c._id}>
                        {c.name}
                      </option>
                    ))}
                  </Form.Select>
                </Form.Group>
              );
            })}
          </Col>

          <Col md={6}>
            <Form.Group controlId="productImage">
              <Form.Label>Product Image</Form.Label>
              <Form.Control
                type="file"
                accept="image/*"
                onChange={(e) => setImage(e.target.files[0])}
                required
              />
            </Form.Group>
          </Col>
        </Row>

        {/* Accessories Multi-Select */}
       {/* Accessories Checkbox List */}
<Row className="mb-3">
  <Col md={12}>
    <Form.Group>
      <Form.Label>Select Accessories</Form.Label>

      {availableAccessories.length === 0 ? (
        <p>Loading accessories...</p>
      ) : (
        availableAccessories.map((acc) => (
          <div key={acc._id} className="mb-1">
            <input
              type="checkbox"
              value={acc._id}
              checked={selectedAccessories.includes(acc._id)}
              onChange={(e) => {
                const value = e.target.value;
                if (selectedAccessories.includes(value)) {
                  setSelectedAccessories(selectedAccessories.filter((id) => id !== value));
                } else {
                  setSelectedAccessories([...selectedAccessories, value]);
                }
              }}
            />
            <label className="ms-2">
              {acc.name} — ₹{acc.price}
            </label>
          </div>
        ))
      )}
    </Form.Group>
  </Col>
</Row>


        {/* Description */}
        <Row className="mb-3">
          <Col>
            <Form.Group controlId="productDescription">
              <Form.Label>Description</Form.Label>
              <Form.Control
                as="textarea"
                rows={3}
                value={description}
                onChange={(e) => setDescription(e.target.value)}
                required
              />
            </Form.Group>
          </Col>
        </Row>

        <Button
          variant="primary"
          type="submit"
          disabled={hasSubcategories && !selectedCategory}
        >
          Add Product
        </Button>
      </Form>
    </Container>
  );
};

export default ProductForm;
