# PHP to JavaScript Connection Map

## Overview

This document describes the complete flow of how PHP views connect to and initialize JavaScript in the CapitalA M2 MVC application, specifically focusing on the homepage and script initialization.

---

## 1. Connection Flow Diagram

```
┌────────────────────────────────────────────────────────────────────┐
│                   HOMEPAGE LOAD SEQUENCE                            │
└────────────────────────────────────────────────────────────────────┘

User Browser Request (/)
        ↓
   index.php (Entry Point)
        ↓
   app/bootstrap.php (Initialize App)
        ↓
   Router matches "/" → HomeController@index
        ↓
   HomeController::index()
   - Sets view data
   - Calls: $this->render('homepage/homepage')
        ↓
   app/views/homepage/homepage.php LOADS
   ├─ includes head-root.php (DOCTYPE, meta, stylesheets)
   ├─ includes header.php (navigation, logo)
   ├─ includes section1.php (hero slider)
   ├─ includes section2.php (content)
   ├─ includes section3-2.php (services)
   ├─ includes section4.php (partners carousel)
   ├─ includes section5.php (content)
   └─ includes scripts-root.php (SCRIPT LOADING POINT)
        ↓
   scripts-root.php LOADS
   ├─ <script src="View::asset('js/script.js')"></script>
   │   └─ Converts to: <script src="/public/assets/js/script.js"></script>
   ├─ <script src="View::asset('js/product-service.js')"></script>
   ├─ <script src="https://cdn.jsdelivr.net/npm/swiper@11"></script>
   ├─ <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
   └─ Inline animation data script (if available)
        ↓
   ✅ HTML fully parsed by browser
        ↓
   script.js EXECUTES
   ├─ Initializes all sections (1-5)
   ├─ Fetches dynamic content
   ├─ Sets up event listeners
   └─ Initializes libraries (Swiper, AOS)
        ↓
   🎨 Page fully interactive & styled
```

---

## 2. View Helper Function - View::asset()

**Location:** `app/core/View.php`

### How it works:

```php
// In scripts-root.php (PHP code):
<script src="<?php echo View::asset('js/script.js'); ?>"></script>

// This gets converted to (HTML output):
<script src="/public/assets/js/script.js"></script>
```

### Implementation details:

```php
// View::asset() function (in app/core/View.php)
public static function asset($path) {
    return '/public/assets/' . $path;
}

// This creates the correct path from:
// - app/views/pages/include/scripts-root.php
// to:
// - public/assets/js/script.js
```

---

## 3. PHP View Hierarchy & Script Loading

### File Structure:

```
app/
└── views/
    └── homepage/
        └── homepage.php (MAIN VIEW FILE)
             ├─ includes: pages/include/head-root.php
             ├─ includes: pages/include/header.php
             ├─ includes: pages/introduce/section1.php
             ├─ includes: pages/introduce/section2.php
             ├─ includes: pages/introduce/section3-2.php
             ├─ includes: pages/introduce/section4.php
             ├─ includes: pages/introduce/section5.php
             └─ includes: pages/include/scripts-root.php
                 ├─ script.js (MAIN CLIENT LOGIC)
                 ├─ product-service.js
                 ├─ Swiper library (CDN)
                 ├─ AOS library (CDN)
                 └─ Animation data (inline PHP)
```

### Load Order:

1. **Head Content** (`head-root.php`)
   - HTML structure, meta tags
   - CSS stylesheets
2. **HTML Content** (sections 1-5 + header/footer)
   - Creates DOM structure
   - Adds `id` attributes for JavaScript targeting
3. **Scripts** (`scripts-root.php`) - **MUST BE LAST**
   - External libraries (Swiper, AOS)
   - Main JavaScript file (script.js)
   - Inline PHP data

---

## 4. Script.js Initialization Flow

### Entry Point:

```javascript
// File: public/assets/js/script.js
// Line 1: Start of execution

// 1. FETCH HEADER (dynamic content)
fetch("../../app/views/pages/include/header.php")
  .then((res) => res.text())
  .then((data) => {
    document.getElementById("header").innerHTML = data;
    // Initialize header events (menu, etc)
  });

// 2. FETCH SECTION1 (hero slider)
fetch("/introduce/section1")
  .then((res) => res.text())
  .then((html) => {
    document.getElementById("section1").innerHTML = html;
    setTimeout(() => {
      initSection1Events(); // Initialize Swiper
    }, 100);
  });

// 3. FETCH SECTION3 (services tabs)
fetch("/introduce/section3-2")
  .then((res) => res.text())
  .then((html) => {
    document.getElementById("section3").innerHTML = html;
    setTimeout(() => {
      initSection3Events(); // Initialize hover effects
    }, 100);
  });

// 4. FETCH SECTION4 (partners carousel)
fetch("/introduce/section4")
  .then((res) => res.text())
  .then((html) => {
    document.getElementById("section4").innerHTML = html;
    setTimeout(() => {
      initSection4Carousel(); // Initialize Swiper
    }, 100);
  });

// 5. FETCH SECTION5 (content)
fetch("/introduce/section5")
  .then((res) => res.text())
  .then((data) => {
    document.getElementById("section5").innerHTML = data;
  });

// 6. FETCH FOOTER
fetch("../../app/views/pages/include/footer.php")
  .then((res) => res.text())
  .then((data) => {
    document.getElementById("footer").innerHTML = data;
  });

// 7. INITIALIZE SEARCH (built-in)
initSearchBoxToggle();
```

---

## 5. Logical Mind Map

```
┌─────────────────────────────────────────────────────────────────┐
│                    PHP-JS CONNECTION MIND MAP                    │
└─────────────────────────────────────────────────────────────────┘

                          USER VISITS /
                              ↓
                   ┌──────────────────────┐
                   │  index.php loaded    │
                   │  Router initialized  │
                   └──────────────────────┘
                              ↓
                   ┌──────────────────────┐
                   │ HomeController@index │
                   └──────────────────────┘
                              ↓
              ┌───────────────────────────────────┐
              │   homepage/homepage.php LOADED    │
              └───────────────────────────────────┘
                              ↓
        ┌─────────────────────────────────────────────────────┐
        │           PHP RENDERING PHASE                       │
        │  (Still on PHP side - NO JavaScript yet)           │
        └─────────────────────────────────────────────────────┘
        │
        ├─→ head-root.php (CSS, meta, DOCTYPE)
        ├─→ header.php (Navigation HTML)
        ├─→ section1.php (Hero Slider HTML)
        ├─→ section2.php (Content HTML)
        ├─→ section3-2.php (Services HTML)
        ├─→ section4.php (Partners HTML)
        ├─→ section5.php (Content HTML)
        └─→ scripts-root.php (SCRIPT LOADING)
            │
            ├─→ <script src="/public/assets/js/script.js"></script>
            ├─→ External libraries (Swiper, AOS)
            └─→ PHP inline data (if any)
                              ↓
        ┌─────────────────────────────────────────────────────┐
        │           JAVASCRIPT EXECUTION PHASE                │
        │  (Browser has full HTML DOM + external libs)       │
        └─────────────────────────────────────────────────────┘
        │
        ├─→ FETCH & SET DOM
        │   ├─ header.php → #header.innerHTML
        │   ├─ section1 → section1.innerHTML
        │   ├─ section3-2 → section3.innerHTML
        │   ├─ section4 → section4.innerHTML
        │   ├─ section5 → section5.innerHTML
        │   └─ footer.php → #footer.innerHTML
        │
        ├─→ INITIALIZE COMPONENTS
        │   ├─ Swiper (Hero slider)
        │   ├─ Swiper (Partners carousel)
        │   ├─ Hover effects (Services)
        │   ├─ Event listeners (Menu)
        │   └─ Custom animations
        │
        └─→ EVENT LISTENERS READY
            └─ Page interactive & fully functional
                              ↓
                        🎨 USER SEES LIVE PAGE
```

---

## 6. Key Entry Points & Connections

### Connection Point 1: View Helper

```
PHP Code:
├─ scripts-root.php
│  └─ View::asset('js/script.js')  ← Generates correct path

JavaScipt Path:
└─ public/assets/js/script.js      ← Loaded by browser
```

### Connection Point 2: DOM Target IDs

```
PHP View Structure:
├─ homepage.php
│  ├─ <div id="header">...</div>      ← JavaScript target
│  ├─ <div id="section1">...</div>    ← JavaScript target
│  ├─ <div id="section3">...</div>    ← JavaScript target
│  ├─ <div id="section4">...</div>    ← JavaScript target
│  ├─ <div id="section5">...</div>    ← JavaScript target
│  └─ <div id="footer">...</div>      ← JavaScript target

JavaScript Access:
├─ document.getElementById("header")   ← Finds element
├─ document.getElementById("section1")  ← Finds element
└─ .innerHTML = data                    ← Sets content
```

### Connection Point 3: Configuration

```
PHP Config:
└─ VIEWS_PATH = 'app/views/'

JavaScript Fetch Paths:
├─ fetch("../../app/views/pages/include/header.php")
├─ fetch("../../app/views/pages/introduce/section1.php")
└─ fetch("/introduce/section1")  ← Router-based paths
```

---

## 7. Data & State Flow

### Initialization Sequence:

```javascript
1. DOM READY
   ├─ All HTML elements exist
   ├─ All CSS styles loaded
   ├─ External libraries (Swiper, AOS) available
   └─ script.js begins execution

2. FETCH CONTENT
   ├─ Start loading header via fetch
   ├─ Start loading sections via fetch
   └─ Parallel requests for performance

3. DOM INJECTION
   ├─ Header content → #header
   ├─ Sections content → respective divs
   └─ Elements now in DOM

4. EVENT INITIALIZATION
   ├─ Query for newly injected elements
   ├─ Add event listeners
   ├─ Initialize libraries (Swiper, etc)
   └─ Setup animations & interactions

5. INTERACTIVE STATE
   ├─ User can interact with page
   ├─ Hover effects work
   ├─ Click handlers active
   ├─ Forms respond to input
   └─ Sliders auto-rotate/respond to user
```

---

## 8. Async Operations & Timing

### Critical Timing Issues Handled:

```javascript
// ❌ WRONG (causes errors):
function initSection1() {
  const swiper = new Swiper(".swiper"); // Element doesn't exist yet!
}

// ✅ RIGHT (waits for DOM):
fetch("/introduce/section1")
  .then((res) => res.text())
  .then((html) => {
    document.getElementById("section1").innerHTML = html;
    // Element now exists

    // Wait for browser to render
    setTimeout(() => {
      initSection1Events(); // Now safe to initialize
    }, 100);
  });
```

---

## 9. CSS & JavaScript Dependency Order

### Correct Load Order in scripts-root.php:

```html
<!-- 1. External CSS (in head-root.php) -->
<link rel="stylesheet" href="/public/assets/css/styles.css" />

<!-- 2. External Libraries -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

<!-- 3. Application JavaScript (must be last) -->
<script src="/public/assets/js/script.js"></script>
<!-- script.js can now safely reference Swiper, AOS, and all DOM elements -->
```

### Why This Order Matters:

```
✓ CSS loads first:      Prevents FOUC (Flash of Unstyled Content)
✓ Libraries load early: Available when script.js runs
✓ script.js loads last: Can safely use everything above it
```

---

## 10. Best Practices for Maintaining Connection

### When Adding New Sections:

1. **Create PHP View** (`app/views/pages/introduce/newsection.php`)

   ```php
   <div id="newsection-content">
       <!-- HTML structure -->
   </div>
   ```

2. **Add Container in Homepage** (`app/views/homepage/homepage.php`)

   ```php
   <div id="newsection"></div>
   ```

3. **Add Fetch in JavaScript** (`public/assets/js/script.js`)

   ```javascript
   fetch("../../app/views/pages/introduce/newsection.php")
     .then((res) => res.text())
     .then((html) => {
       document.getElementById("newsection").innerHTML = html;
       setTimeout(() => {
         initNewSectionEvents();
       }, 100);
     });
   ```

4. **Initialize Events** (same file)
   ```javascript
   function initNewSectionEvents() {
     // Query elements
     // Add listeners
     // Initialize libraries
   }
   ```

---

## 11. Debugging Connection Issues

### Checklist:

- [ ] Homepage loads without errors (check Network tab)
- [ ] script.js is being fetched (Network tab → script.js)
- [ ] DOM elements have correct `id` attributes
- [ ] Fetch paths are correct (check Network → XHR)
- [ ] Browser console shows no JavaScript errors
- [ ] Swiper & AOS libraries loaded (check Network)
- [ ] Elements exist before initialization (setTimeout helps)
- [ ] Event listeners are attached to correct elements

### Common Issues:

```
❌ Issue: script.js returns 404
   → Check: View::asset() path is correct
   → Check: File exists at public/assets/js/script.js

❌ Issue: Swiper undefined error
   → Check: Swiper library loaded before script.js
   → Check: script.js path in scripts-root.php is correct

❌ Issue: "Cannot set innerHTML of null"
   → Check: Container div exists with correct id
   → Check: homepage.php includes container
   → Check: JavaScript queries correct id

❌ Issue: Events not working
   → Check: Elements fetched successfully
   → Check: setTimeout delay is sufficient
   → Check: Event listeners attached after fetch completes
```

---

## 12. Complete Request/Response Cycle

### Example: Homepage Load to Interactive

```
USER REQUEST
  ↓
GET / HTTP/1.1
  ↓
HTTP/1.1 200 OK
Content-Type: text/html
Etag: ...
Date: ...

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="/public/assets/css/styles.css">
</head>
<body>
  <div id="header"></div>
  <div id="section1"></div>
  <div id="section3"></div>
  <div id="section4"></div>
  <div id="section5"></div>
  <div id="footer"></div>

  <script src="/public/assets/js/script.js"></script>
</body>
</html>
  ↓
BROWSER EXECUTES script.js
  ├─ fetch("../../app/views/pages/include/header.php")
  ├─ fetch("/introduce/section1")
  ├─ fetch("/introduce/section3-2")
  ├─ fetch("/introduce/section4")
  ├─ fetch("/introduce/section5")
  └─ fetch("../../app/views/pages/include/footer.php")
  ↓
FETCH RESPONSES
  ├─ header.php returns HTML → #header.innerHTML
  ├─ section1 returns HTML → #section1.innerHTML
  ├─ section3-2 returns HTML → #section3.innerHTML
  ├─ section4 returns HTML → #section4.innerHTML
  ├─ section5 returns HTML → #section5.innerHTML
  └─ footer.php returns HTML → #footer.innerHTML
  ↓
INITIALIZATION
  ├─ initSection1Events() → Swiper initialized
  ├─ initSection3Events() → Hover listeners added
  ├─ initSection4Carousel() → Carousel initialized
  └─ Event listeners ready
  ↓
✅ PAGE INTERACTIVE - USER SEES LIVE CONTENT
```

---

## Summary

The PHP-to-JavaScript connection in this MVC application works through:

1. **PHP View Rendering** - homepage.php includes all sections and loads scripts-root.php
2. **View Helper** - View::asset() generates correct paths for script linking
3. **Script Execution** - script.js fetches additional content and initializes components
4. **Event Listeners** - JavaScript attaches handlers to dynamically loaded elements
5. **User Interaction** - Page responds to user actions with pre-initialized handlers

This creates a seamless connection where PHP generates the initial HTML structure and JavaScript enhances it with interactivity and dynamic content loading.
