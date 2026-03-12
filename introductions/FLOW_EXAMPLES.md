# CapitalA M2 MVC - Practical Flow Examples

This document shows concrete code examples of how the PHP-API-JavaScript flow works in practice.

---

## Example 1: Displaying News Articles

### **Step 1: Define the Route (routes/api_routes.php)**

```php
<?php
use App\Controllers\Api\NewsApiController;

// API endpoint for getting news
$router->get('/news', [NewsApiController::class, 'index']);
$router->get('/news/{id:\d+}', [NewsApiController::class, 'show']);
```

---

### **Step 2: Create API Controller (app/controllers/API_controllers/NewsApiController.php)**

```php
<?php
namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\News;

class NewsApiController extends Controller
{
    /**
     * Get all news articles
     */
    public function index()
    {
        $newsModel = new News();
        $news = $newsModel->getLatest(5);

        // Return JSON response
        return $this->json([
            'status' => true,
            'data'   => $news
        ]);
    }

    /**
     * Get single news article
     */
    public function show($id)
    {
        $newsModel = new News();
        $article = $newsModel->findById($id);

        if ($article) {
            return $this->json([
                'status' => true,
                'data'   => $article
            ]);
        } else {
            return $this->json([
                'status'  => false,
                'message' => 'Article not found'
            ], 404);
        }
    }
}
?>
```

---

### **Step 3: Create News Model (app/models/News_model.php)**

```php
<?php
namespace App\Models;

use App\Core\Model;

class News extends Model
{
    protected $table = 'news';
    protected $fillable = ['title', 'content', 'author_id', 'category_id', 'created_at'];

    /**
     * Get latest news articles
     */
    public function getLatest($limit = 5)
    {
        return $this->db->table($this->table)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
    }

    /**
     * Find article by ID
     */
    public function findById($id)
    {
        return $this->db->table($this->table)
            ->where('id', $id)
            ->first();
    }
}
?>
```

---

### **Step 4: Create Web Controller (app/controllers/NewsController.php)**

```php
<?php
/**
 * News Controller - Handles news pages
 */

class NewsController extends Controller {

    public function index() {
        $this->setPageTitle('Tin tức');

        // Get from model or pass empty, will be fetched via API
        $newsModel = new News_model();
        $latestNews = $newsModel->getLatest(10);

        $this->setData('news', $latestNews);
        $this->render('News/News');
    }

    public function show($id = null) {
        if (!$id) {
            return $this->render('errors/404');
        }

        $newsModel = new News_model();
        $article = $newsModel->findById($id);

        if (!$article) {
            return $this->render('errors/404');
        }

        $this->setPageTitle($article['title']);
        $this->setData('article', $article);
        $this->render('News/News-title');
    }
}
?>
```

---

### **Step 5: Create View (app/views/News/News.php)**

```php
<?php
/**
 * News List View
 */
?>

<!-- Include header and sections -->
<?php include VIEWS_PATH . 'pages/include/head-root.php'; ?>
<?php include VIEWS_PATH . 'pages/include/header.php'; ?>

<!-- Main content -->
<div class="container">
    <h1><?php echo $this->data['page_title'] ?? 'News'; ?></h1>

    <!-- News List Container -->
    <div id="news-list" class="news-grid">
        <!-- News items will be loaded here via JavaScript -->
        <p>Loading news...</p>
    </div>
</div>

<!-- Include footer and scripts -->
<?php include VIEWS_PATH . 'pages/include/footer.php'; ?>
<?php include VIEWS_PATH . 'pages/include/scripts-root.php'; ?>

<!-- Inline script to load news via API -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch news from API
    fetch('/api/news')
        .then(response => response.json())
        .then(data => {
            if (data.status && data.data.length > 0) {
                displayNews(data.data);
            } else {
                document.getElementById('news-list').innerHTML = '<p>No news available</p>';
            }
        })
        .catch(error => {
            console.error('Error loading news:', error);
            document.getElementById('news-list').innerHTML = '<p>Error loading news</p>';
        });
});
</script>
```

---

### **Step 6: JavaScript (public/assets/js/script.js)**

```javascript
/**
 * Display news articles on the page
 */
function displayNews(newsArray) {
  const newsContainer = document.getElementById("news-list");

  // Clear loading message
  newsContainer.innerHTML = "";

  // Create news items
  newsArray.forEach((article) => {
    const newsItem = document.createElement("article");
    newsItem.className = "news-item";
    newsItem.innerHTML = `
            <div class="news-card">
                <h2>${article.title}</h2>
                <p class="news-excerpt">${article.content.substring(0, 150)}...</p>
                <p class="news-meta">Published: ${article.created_at}</p>
                <a href="/news/${article.id}" class="read-more">Read More →</a>
            </div>
        `;
    newsContainer.appendChild(newsItem);
  });

  // Initialize animations or event listeners if needed
  initializeNewsEventListeners();
}

/**
 * Initialize event listeners for news items
 */
function initializeNewsEventListeners() {
  const newsItems = document.querySelectorAll(".news-item");
  newsItems.forEach((item) => {
    item.addEventListener("mouseenter", function () {
      this.classList.add("hovered");
    });
    item.addEventListener("mouseleave", function () {
      this.classList.remove("hovered");
    });
  });
}
```

---

### **Step 7: Config (public/assets/js/config.js)**

```javascript
/**
 * Application Configuration
 */
const APP_CONFIG = {
  // Base URLs
  baseUrl: window.location.origin,
  apiBase: "/api",

  // API Endpoints
  endpoints: {
    news: "/api/news",
    newsById: "/api/news",
    products: "/api/products",
    categories: "/api/categories",
  },

  // Request Headers
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },

  // Paths
  paths: {
    newsView: "/news",
    productView: "/product-service",
  },

  // Helper function to build API URL
  getApiUrl: function (endpoint) {
    return this.baseUrl + endpoint;
  },
};

// Make it globally available
window.APP_CONFIG = APP_CONFIG;
```

---

## Complete Request/Response Flow Diagram

```
USER FLOW EXAMPLE: View News Articles

┌─────────────────────────────────────────────────────────────────┐
│                      1. Page Load                                │
│  User navigates to: https://capitalam.com/news                  │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                   2. Server Processing                           │
│  Request hits: index.php                                         │
│  ├─ Loaded: bootstrap.php                                        │
│  ├─ Initialized: Router, Controller, Model                       │
│  ├─ Matched route: 'news' → NewsController@index                 │
│  └─ Executed: NewsController::index()                            │
│     ├─ setPageTitle('Tin tức')                                   │
│     ├─ setData('news', $latestNews)                              │
│     └─ render('News/News')                                       │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                   3. HTML Generated                              │
│  News/News.php view rendered with:                               │
│  ├─ HTML structure                                               │
│  ├─ CSS styling                                                  │
│  ├─ JavaScript includes (script.js, config.js)                   │
│  └─ Inline initialization script                                 │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                  4. HTML Sent to Browser                         │
│  HTTP Response 200 OK with HTML + CSS + JS                       │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│              5. Browser Renders & Executes JS                    │
│  ├─ User sees: "Loading news..."                                 │
│  ├─ script.js runs DOMContentLoaded event                        │
│  └─ Makes fetch call: GET /api/news                              │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│               6. API Request to Backend                          │
│  Request: GET /api/news                                          │
│  ├─ Route: public/api.php                                        │
│  ├─ Bootstrap: Same as index.php                                 │
│  ├─ Router matches: /api/news                                    │
│  └─ Dispatch to: NewsApiController@index                         │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│           7. API Controller Processes Request                    │
│  NewsApiController::index()                                      │
│  ├─ Instantiate: News Model                                      │
│  ├─ Query: $newsModel->getLatest(5)                              │
│  ├─ Database returns: Array of articles                          │
│  └─ Format response with: $this->json([...])                     │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│               8. JSON Response to Browser                        │
│  HTTP Response 200 OK                                            │
│  Content-Type: application/json                                  │
│  {                                                               │
│    "status": true,                                               │
│    "data": [                                                     │
│      {                                                           │
│        "id": 1,                                                  │
│        "title": "Article Title",                                │
│        "content": "Article content...",                          │
│        "created_at": "2026-02-06"                               │
│      },                                                          │
│      { ... more articles ... }                                   │
│    ]                                                             │
│  }                                                               │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│           9. JavaScript Processes Response                       │
│  fetch().then(response => response.json())                       │
│  .then(data => {                                                 │
│    ├─ Parse JSON                                                 │
│    ├─ Validate: data.status === true                             │
│    ├─ Call: displayNews(data.data)                               │
│    └─ Function builds HTML and inserts into DOM                  │
│  })                                                              │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│              10. DOM Updated with News                           │
│  For each article received:                                      │
│  ├─ Create article element                                       │
│  ├─ Set title, excerpt, date                                     │
│  ├─ Add click handlers                                           │
│  └─ Insert into #news-list                                       │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│           11. User Sees Final Rendered Page                      │
│  ├─ Page title                                                   │
│  ├─ News article list                                            │
│  ├─ Links to individual articles                                 │
│  └─ All interactive features working                             │
└─────────────────────────────────────────────────────────────────┘
```

---

## Example 2: Form Submission (Contact Form)

### **HTML Form (View)**

```php
<form id="contact-form" method="POST">
    <input type="text" name="name" placeholder="Your Name" required>
    <input type="email" name="email" placeholder="Your Email" required>
    <textarea name="message" placeholder="Your Message" required></textarea>
    <button type="submit">Send Message</button>
</form>
```

### **JavaScript Handler**

```javascript
document
  .getElementById("contact-form")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData);

    // POST to API
    fetch("/api/contact", {
      method: "POST",
      headers: APP_CONFIG.headers,
      body: JSON.stringify(data),
    })
      .then((res) => res.json())
      .then((response) => {
        if (response.status) {
          alert("Message sent successfully!");
          document.getElementById("contact-form").reset();
        } else {
          alert("Error: " + response.message);
        }
      })
      .catch((error) => console.error("Error:", error));
  });
```

### **API Controller Handler**

```php
<?php
public function store() {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate
    if (!isset($data['name'], $data['email'], $data['message'])) {
        return $this->json(['status' => false, 'message' => 'Missing fields'], 400);
    }

    // Save to database
    $contactModel = new Contact();
    $result = $contactModel->create([
        'name' => $data['name'],
        'email' => $data['email'],
        'message' => $data['message'],
        'created_at' => date('Y-m-d H:i:s')
    ]);

    if ($result) {
        return $this->json(['status' => true, 'message' => 'Message sent']);
    } else {
        return $this->json(['status' => false, 'message' => 'Database error'], 500);
    }
}
?>
```

---

## Data Flow Summary

| Layer              | Files                                            | Purpose                                |
| ------------------ | ------------------------------------------------ | -------------------------------------- |
| **Entry Point**    | `index.php`, `public/api.php`                    | Accept HTTP requests                   |
| **Initialization** | `app/bootstrap.php`                              | Load configuration and core classes    |
| **Routing**        | `routes/web_routes.php`, `routes/api_routes.php` | Map URLs to controllers                |
| **Server Logic**   | `app/controllers/`                               | Process requests and prepare responses |
| **Data Layer**     | `app/models/`, Database                          | Query and manipulate data              |
| **Views**          | `app/views/`                                     | Generate HTML output                   |
| **Client Logic**   | `public/assets/js/`                              | Handle browser interactions            |
| **Client Config**  | `public/assets/js/config.js`                     | Configuration for API calls            |
| **Styling**        | `public/assets/css/`                             | Visual presentation                    |

---

This flow supports both traditional web page rendering and modern AJAX API calls, allowing for optimal user experience with fast, dynamic page updates.
