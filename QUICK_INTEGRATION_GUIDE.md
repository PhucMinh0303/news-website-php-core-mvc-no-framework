# CapitalA M2 MVC - Quick Integration Reference

A quick reference guide for connecting PHP files, API endpoints, and JavaScript in your MVC application.

---

## Quick Start: Add a New Feature

### **Scenario: Display "Products" with dynamic loading**

### **1️⃣ Step 1: Create API endpoints (routes/api_routes.php)**

```php
// Get all products
$router->get('/products', [ProductApiController::class, 'index']);

// Get single product
$router->get('/products/{id:\d+}', [ProductApiController::class, 'show']);
```

**Location:** `routes/api_routes.php`

---

### **2️⃣ Step 2: Create API Controller (app/controllers/API_controllers/)**

**File:** `app/controllers/API_controllers/ProductApiController.php`

```php
<?php
namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Product;

class ProductApiController extends Controller
{
    public function index() {
        $productModel = new Product();
        $products = $productModel->getAll();

        return $this->json([
            'status' => true,
            'data'   => $products
        ]);
    }

    public function show($id) {
        $productModel = new Product();
        $product = $productModel->findById($id);

        if ($product) {
            return $this->json([
                'status' => true,
                'data'   => $product
            ]);
        }

        return $this->json([
            'status'  => false,
            'message' => 'Product not found'
        ], 404);
    }
}
?>
```

---

### **3️⃣ Step 3: Create or Use Model (app/models/)**

**File:** `app/models/Product_model.php`

```php
<?php
namespace App\Models;

use App\Core\Model;

class Product extends Model
{
    protected $table = 'products';

    public function getAll() {
        return $this->db->table($this->table)->get();
    }

    public function findById($id) {
        return $this->db->table($this->table)
            ->where('id', $id)
            ->first();
    }
}
?>
```

---

### **4️⃣ Step 4: Create Web Controller (app/controllers/)**

**File:** `app/controllers/ProductController.php`

```php
<?php
class ProductController extends Controller {

    public function index() {
        $this->setPageTitle('Products & Services');
        $this->render('product&service/product-service');
    }
}
?>
```

---

### **5️⃣ Step 5: Create View (app/views/)**

**File:** `app/views/product&service/product-service.php`

```php
<?php include VIEWS_PATH . 'pages/include/head-root.php'; ?>
<?php include VIEWS_PATH . 'pages/include/header.php'; ?>

<div class="container">
    <h1><?php echo $this->data['page_title']; ?></h1>

    <!-- Products will load here -->
    <div id="products-container">
        <p>Loading products...</p>
    </div>
</div>

<?php include VIEWS_PATH . 'pages/include/footer.php'; ?>
<?php include VIEWS_PATH . 'pages/include/scripts-root.php'; ?>

<script>
    // Load products when page is ready
    document.addEventListener('DOMContentLoaded', loadProducts);
</script>
```

---

### **6️⃣ Step 6: JavaScript Handler (public/assets/js/script.js)**

Add this function to your `script.js`:

```javascript
/**
 * Load products from API
 */
function loadProducts() {
  fetch(APP_CONFIG.apiBase + "/products")
    .then((response) => response.json())
    .then((data) => {
      if (data.status) {
        renderProducts(data.data);
      } else {
        showError("Failed to load products");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showError("Error loading products");
    });
}

/**
 * Render products to DOM
 */
function renderProducts(products) {
  const container = document.getElementById("products-container");
  container.innerHTML = ""; // Clear loading message

  products.forEach((product) => {
    const productHTML = `
            <div class="product-card">
                <h3>${product.name}</h3>
                <p>${product.description}</p>
                <a href="/product/${product.id}" class="btn">View Details</a>
            </div>
        `;
    container.innerHTML += productHTML;
  });
}

/**
 * Show error message
 */
function showError(message) {
  document.getElementById("products-container").innerHTML =
    `<p class="error">${message}</p>`;
}
```

---

## Connection Flow Checklist

```
✅ Create API Route
   Location: routes/api_routes.php
   Pattern: $router->get('/endpoint', [ControllerClass::class, 'method']);

✅ Create API Controller
   Location: app/controllers/API_controllers/XxxApiController.php
   Methods: index(), show($id), store(), update($id), delete($id)

✅ Use/Create Model
   Location: app/models/Xxx_model.php
   Methods: getAll(), findById($id), create($data), update($id, $data)

✅ Create Web Controller (optional, for rendering page)
   Location: app/controllers/XxxController.php
   Method: Use setData() and render()

✅ Create View Template
   Location: app/views/section/page.php
   Include: head-root, header, footer, scripts-root
   Add: Container div with ID (for JavaScript to target)

✅ Add JavaScript Handler
   Location: public/assets/js/script.js
   Pattern: fetch() → parse response → render to DOM
   Use: APP_CONFIG.apiBase for consistent URLs
```

---

## File Locations Quick Reference

```
API Route Definition
└─ routes/api_routes.php

API Controller
└─ app/controllers/API_controllers/XxxApiController.php

Data Model
└─ app/models/Xxx_model.php

Web View
└─ app/views/section/page.php

JavaScript Logic
└─ public/assets/js/script.js
```

---

## Common API Patterns

### **GET - Retrieve Data**

```javascript
fetch(APP_CONFIG.apiBase + "/products")
  .then((res) => res.json())
  .then((data) => console.log(data));
```

### **GET with ID - Retrieve Single Item**

```javascript
fetch(APP_CONFIG.apiBase + "/products/5")
  .then((res) => res.json())
  .then((data) => console.log(data));
```

### **POST - Create New Data**

```javascript
fetch(APP_CONFIG.apiBase + "/products", {
  method: "POST",
  headers: APP_CONFIG.headers,
  body: JSON.stringify({
    name: "Product Name",
    description: "Product Description",
  }),
})
  .then((res) => res.json())
  .then((data) => console.log(data));
```

### **PUT - Update Data**

```javascript
fetch(APP_CONFIG.apiBase + "/products/5", {
  method: "PUT",
  headers: APP_CONFIG.headers,
  body: JSON.stringify({
    name: "Updated Name",
  }),
})
  .then((res) => res.json())
  .then((data) => console.log(data));
```

### **DELETE - Remove Data**

```javascript
fetch(APP_CONFIG.apiBase + "/products/5", {
  method: "DELETE",
  headers: APP_CONFIG.headers,
})
  .then((res) => res.json())
  .then((data) => console.log(data));
```

---

## Response Format Standard

All API endpoints should return JSON in this format:

```json
{
  "status": true,
  "data": {
    "id": 1,
    "name": "Item Name",
    "description": "Item Description"
  },
  "message": "Success message (optional)"
}
```

For errors:

```json
{
  "status": false,
  "message": "Error description",
  "code": 400
}
```

---

## JavaScript → API Communication Pattern

```javascript
// 1. Define fetch request
const apiUrl = APP_CONFIG.apiBase + "/your-endpoint";
const options = {
  method: "GET", // or POST, PUT, DELETE
  headers: APP_CONFIG.headers,
  body: JSON.stringify(data), // only for POST/PUT
};

// 2. Make request
fetch(apiUrl, options)
  // 3. Parse response
  .then((response) => response.json())

  // 4. Handle data
  .then((data) => {
    if (data.status) {
      // Success - use data.data
      updateUI(data.data);
    } else {
      // Error - use data.message
      showError(data.message);
    }
  })

  // 5. Handle network errors
  .catch((error) => {
    console.error("Network error:", error);
    showError("Failed to load data");
  });
```

---

## Common Elements in Views

```php
<!-- Container for dynamic content -->
<div id="content-container">
    <p>Loading...</p>
</div>

<!-- Or for lists -->
<div id="items-list" class="list-container">
    <!-- Items will be inserted here -->
</div>

<!-- Script block at end of view -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Call your fetch function
        loadYourData();
    });
</script>
```

---

## Configuration (config.js)

Update `public/assets/js/config.js` to include your new endpoints:

```javascript
const APP_CONFIG = {
  baseUrl: window.location.origin,
  apiBase: "/api",

  // Add your endpoints here
  endpoints: {
    products: "/api/products",
    news: "/api/news",
    categories: "/api/categories",
    // add new endpoints
  },
};
```

Use in JavaScript:

```javascript
fetch(APP_CONFIG.endpoints.products);
// or
fetch(APP_CONFIG.apiBase + "/products");
```

---

## Testing Your Integration

### **1. Test API Endpoint in Browser**

```
https://your-site.com/api/products
```

Should return JSON like:

```json
{
  "status": true,
  "data": [...]
}
```

### **2. Test JavaScript Function in Console**

```javascript
loadProducts(); // or your function name
```

Check browser console (F12) for errors.

### **3. Check Network Tab**

- Open Developer Tools (F12)
- Go to Network tab
- Refresh page
- Look for API calls (XHR/Fetch)
- Check response data

---

## Troubleshooting Guide

| Problem                                      | Solution                                                              |
| -------------------------------------------- | --------------------------------------------------------------------- |
| **API route not found**                      | Check `routes/api_routes.php` - verify route matches                  |
| **Controller not found**                     | Check file exists and namespace is correct                            |
| **Cannot read property 'data' of undefined** | API returned error - check `data.status` before accessing `data.data` |
| **CORS error**                               | API must return proper headers (usually not an issue for same domain) |
| **JavaScript function not defined**          | Ensure script.js is loaded, function is before it's called            |
| **Page shows "Loading..." forever**          | Check browser console (F12) for fetch errors                          |

---

## Next Steps After Integration

1. ✅ Test API endpoint works in browser
2. ✅ Test JavaScript fetch call in console
3. ✅ Add error handling
4. ✅ Add loading states/spinner
5. ✅ Add pagination if needed
6. ✅ Style the rendered content
7. ✅ Test on different browsers

---

This quick reference helps you consistently integrate PHP backend, API layer, and JavaScript frontend across your entire application.
