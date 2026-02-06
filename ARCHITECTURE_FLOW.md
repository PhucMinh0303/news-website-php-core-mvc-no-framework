# CapitalA M2 MVC - Architecture & Data Flow

## System Architecture Overview

This document describes the complete flow of how PHP controllers, API endpoints, and JavaScript files work together in the CapitalA M2 MVC application.

---

## 1. Request Entry Points

The application has two main entry points:

### **Web Requests** (index.php)

```
User Browser → index.php → app/bootstrap.php → Router
```

### **API Requests** (public/api.php)

```
JavaScript (fetch) → public/api.php → app/bootstrap.php → API Router
```

---

## 2. Complete Web Request Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                    USER REQUEST FLOW (Web)                       │
└─────────────────────────────────────────────────────────────────┘

    1. Browser Request
    ↓
    2. index.php (Entry Point)
       └─ require bootstrap.php
    ↓
    3. app/bootstrap.php
       ├─ Load Configuration (APP_PATH, DB_PATH, etc.)
       ├─ Register Autoloader
       ├─ Load Core Classes:
       │  ├─ Router.php
       │  ├─ Controller.php
       │  ├─ Model.php
       │  └─ View.php
       └─ Initialize App
    ↓
    4. Router (app/core/Router.php)
       ├─ Parse URL path
       ├─ Match against web_routes.php
       └─ Dispatch to appropriate Controller
    ↓
    5. Controller (e.g., HomeController.php)
       ├─ setPageTitle('Trang chủ')
       ├─ setData('sections', [...])
       ├─ setData('animation_data', {...})
       └─ render('homepage/homepage')
    ↓
    6. View (app/views/homepage/homepage.php)
       ├─ Includes head-root.php
       ├─ Includes header.php
       ├─ Includes section1-5.php
       ├─ Includes footer.php
       └─ Includes scripts-root.php
    ↓
    7. HTML + JavaScript Loaded in Browser
       ├─ script.js (Main client-side logic)
       ├─ config.js (Configuration for API calls)
       └─ product-service.js (Product-specific logic)
    ↓
    8. Browser Renders Page + Initializes JS Event Listeners
```

---

## 3. Complete API Request Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                    API REQUEST FLOW (AJAX)                       │
└─────────────────────────────────────────────────────────────────┘

    1. JavaScript makes fetch() call
       Example: fetch('/api/news')
    ↓
    2. public/api.php (API Entry Point)
       └─ require app/bootstrap.php
    ↓
    3. app/bootstrap.php (Same as above)
       └─ Initialize core classes
    ↓
    4. routes/api_routes.php
       ├─ $router->get('/news', [NewsApiController::class, 'index'])
       ├─ $router->get('/news/{id}', [NewsApiController::class, 'show'])
       ├─ $router->post('/news', [NewsApiController::class, 'store'])
       └─ ... other API routes
    ↓
    5. API Router dispatches to API Controller
       Example: NewsApiController
    ↓
    6. API Controller (e.g., NewsApiController.php)
       ├─ Instantiate Model (News Model)
       ├─ Fetch data from Database
       ├─ Format response as JSON
       └─ Return: $this->json(['status' => true, 'data' => $news])
    ↓
    7. JSON Response sent to Browser
       Example:
       {
         "status": true,
         "data": [
           { "id": 1, "title": "Article 1", ... },
           { "id": 2, "title": "Article 2", ... }
         ]
       }
    ↓
    8. JavaScript receives response
       ├─ Parse JSON
       ├─ Update DOM with data
       └─ Trigger UI updates
```

---

## 4. JavaScript-API Integration

### **Configuration (config.js)**

```javascript
const APP_CONFIG = {
  baseUrl: window.location.origin,
  apiBase: "/api",
  paths: {
    section1: "/app/views/pages/introduce/section1.php",
    // ... other paths
  },
};
```

### **Main Script (script.js)**

#### Loading Dynamic Content with fetch:

```javascript
// Example 1: Load Header
fetch("../../app/views/pages/include/header.php")
  .then((res) => res.text())
  .then((data) => {
    document.getElementById("header").innerHTML = data;
    // Initialize event listeners
  });

// Example 2: Fetch News via API (using config)
fetch(APP_CONFIG.apiBase + "/news")
  .then((res) => res.json())
  .then((response) => {
    if (response.status) {
      displayNews(response.data);
    }
  })
  .catch((error) => console.error("Error:", error));
```

---

## 5. Directory Structure & Responsibilities

```
capitalam2-mvc/
│
├── index.php                          # Entry point for web requests
│
├── app/
│   ├── bootstrap.php                  # Initialization & autoloader
│   ├── config/
│   │   ├── config.php                 # App configuration
│   │   └── database.php               # Database configuration
│   │
│   ├── core/                          # MVC Framework Core
│   │   ├── Router.php                 # URL routing
│   │   ├── Controller.php             # Base controller class
│   │   ├── Model.php                  # Base model class
│   │   ├── View.php                   # View renderer
│   │   ├── Request.php                # Request handling
│   │   ├── Response.php               # Response handling
│   │   └── Database.php               # Database connection
│   │
│   ├── controllers/                   # Web Controllers
│   │   ├── HomeController.php         # Homepage logic
│   │   ├── NewsController.php         # News page logic
│   │   ├── ProductController.php      # Products page logic
│   │   ├── RecruitmentController.php  # Recruitment page logic
│   │   ├── ContactController.php      # Contact form logic
│   │   │
│   │   ├── API_controllers/           # API Controllers (return JSON)
│   │   │   ├── NewsApiController.php  # API: /api/news
│   │   │   ├── CategoryApiController.php
│   │   │   ├── AuthApiController.php
│   │   │   └── ApiController.php
│   │   │
│   │   └── Admin/                     # Admin Controllers
│   │       ├── DashboardController.php
│   │       ├── ProductController.php
│   │       └── UserController.php
│   │
│   ├── models/                        # Data Models
│   │   ├── BaseModel.php              # Base model class
│   │   ├── News_model.php             # News data model
│   │   ├── Product_model.php          # Product data model
│   │   ├── User_model.php             # User data model
│   │   ├── Category_model.php
│   │   └── ... other models
│   │
│   └── views/                         # HTML Templates
│       ├── homepage/
│       │   └── homepage.php
│       ├── pages/include/
│       │   ├── head-root.php
│       │   ├── header.php
│       │   └── footer.php
│       ├── pages/introduce/
│       │   ├── section1.php
│       │   ├── section2.php
│       │   └── ... sections
│       ├── News/
│       │   ├── News.php
│       │   └── News-title.php
│       └── errors/
│           └── 404.php
│
├── public/
│   ├── api.php                        # Entry point for API requests
│   └── assets/
│       ├── css/
│       │   ├── styles.css
│       │   ├── admin.css
│       │   └── responsive-mobile.css
│       ├── js/
│       │   ├── config.js              # API configuration
│       │   ├── script.js              # Main client logic
│       │   └── product-service.js     # Product-specific logic
│       └── img/
│           └── ... images
│
├── routes/
│   ├── web_routes.php                 # Web route definitions
│   └── api_routes.php                 # API route definitions
│
└── config/
    └── ... configuration files
```

---

## 6. Key Integration Points

### **Point 1: Controller → View → JavaScript**

```php
// In HomeController.php
$this->setData('animation_data', [
    'element' => '.some-element',
    'animation' => 'fadeIn',
    'duration' => 1000
]);
$this->render('homepage/homepage');
```

```php
<!-- In homepage.php view -->
<?php include VIEWS_PATH . 'pages/include/scripts-root.php'; ?>
<!-- scripts-root.php loads script.js and config.js -->
```

```javascript
// In script.js - access PHP data via DOM attributes or inline variables
const animationData = <?php echo json_encode($animation_data); ?>;
```

---

### **Point 2: JavaScript → API → Database**

```javascript
// script.js makes API request
fetch("/api/news")
  .then((res) => res.json())
  .then((data) => {
    // Update DOM with response data
    renderNews(data);
  });
```

```php
// API Router directs to NewsApiController
'GET /api/news' => 'Api\NewsApiController@index'

// NewsApiController fetches and returns JSON
public function index() {
    $newsModel = new News();
    $news = $newsModel->getLatest(5);
    return $this->json(['status' => true, 'data' => $news]);
}
```

---

### **Point 3: Dynamic Content Loading**

```javascript
// Load header dynamically
fetch("../../app/views/pages/include/header.php")
  .then((res) => res.text())
  .then((data) => {
    document.getElementById("header").innerHTML = data;
    // Re-initialize event listeners after DOM update
    initializeMenuMobile();
  });
```

---

## 7. Data Flow Examples

### **Example 1: Displaying News on Homepage**

```
1. User visits homepage /
   ↓
2. Router dispatches to HomeController@index
   ↓
3. HomeController renders homepage.php view
   ↓
4. homepage.php includes sections/includes header
   ↓
5. Browser loads script.js and config.js
   ↓
6. script.js runs JavaScript initialization code
   ↓
7. JavaScript executes: fetch('/api/news')
   ↓
8. public/api.php routes to NewsApiController@index
   ↓
9. NewsApiController queries News Model
   ↓
10. News Model returns data from database
    ↓
11. NewsApiController returns JSON response
    ↓
12. JavaScript receives JSON data
    ↓
13. JavaScript updates DOM with news articles
    ↓
14. User sees news articles on page
```

### **Example 2: Form Submission → API → Database**

```
1. User fills contact form on page
   ↓
2. script.js listens for form submit event
   ↓
3. JavaScript makes fetch POST request to /api/contact
   ↓
4. Request includes: { name, email, message, ... }
   ↓
5. public/api.php routes to ContactApiController
   ↓
6. ContactApiController validates data
   ↓
7. ContactApiController saves to database via Model
   ↓
8. ContactApiController returns JSON response
   ↓
9. JavaScript receives response
   ↓
10. JavaScript displays success/error message to user
```

---

## 8. Configuration Integration

### **App Configuration (app/config/config.php)**

- Defines APP_PATH, VIEWS_PATH, MODELS_PATH
- Database connection parameters
- Base URL settings

### **JavaScript Configuration (public/assets/js/config.js)**

- API_BASE: `/api`
- BASE_URL: `window.location.origin`
- Paths to various resources

---

## 9. Request/Response Pattern

### **Web Request → HTML Response**

```
Request:  GET /news
↓
Routed to: NewsController@index
↓
Response: HTML page with news list
```

### **API Request → JSON Response**

```
Request:  GET /api/news
Headers:  Accept: application/json
↓
Routed to: NewsApiController@index
↓
Response: {
  "status": true,
  "data": [
    { "id": 1, "title": "...", "content": "..." },
    ...
  ]
}
```

---

## 10. Best Practices for Future Development

### **When Adding New Features:**

1. **Create API Endpoint** (if data is needed)
   - Add route in `routes/api_routes.php`
   - Create API Controller in `app/controllers/API_controllers/`
   - Create/Use Model in `app/models/`

2. **Create Web Page** (if new page is needed)
   - Add route in `routes/web_routes.php`
   - Create Controller in `app/controllers/`
   - Create View in `app/views/`

3. **Update JavaScript** (if client-side logic needed)
   - Add functions in `public/assets/js/script.js` or create new file
   - Update `config.js` if new API endpoints are added
   - Load scripts in view via `scripts-root.php`

4. **Link them together**
   - Pass data from Controller to View via `setData()`
   - Access data in View
   - Call API from JavaScript when needed
   - Update DOM with API responses

---

## 11. Current Routes References

### **Web Routes File Structure**

- `routes/web_routes.php` - Defines web page routes
- Maps URLs to Controllers

### **API Routes File Structure**

- `routes/api_routes.php` - Defines API endpoint routes
- Maps `/api/...` URLs to API Controllers

---

This architecture enables clean separation of concerns with proper data flow between server-side logic (PHP/Controllers/Models) and client-side interactivity (JavaScript).
