# PHP-JavaScript Connection Quick Reference

## 🔗 Connection Overview

This application uses a **two-phase rendering model**:

1. **PHP Phase** (Server) - Renders homepage.php with containers
2. **JavaScript Phase** (Browser) - Fetches content and initializes components

---

## 📁 Key Files

### Server-Side (PHP)

- **Entry**: `app/views/homepage/homepage.php` - Main view file
- **Script Loader**: `app/views/pages/include/scripts-root.php` - Loads JavaScript
- **Helper**: `app/core/View.php` - Contains View::asset() function
- **Router**: `app/core/Router.php` - Routes URLs to controllers

### Client-Side (JavaScript)

- **Main Script**: `public/assets/js/script.js` - Fetches & initializes
- **Config**: `public/assets/js/config.js` - Configuration (if used)

---

## 🔄 Connection Flow Summary

```
User Request
    ↓
PHP renders homepage.php (creates containers)
    ↓
Browser loads script.js
    ↓
script.js fetches PHP views
    ↓
Content injected into containers
    ↓
JavaScript initializes components
    ↓
✅ Page Interactive
```

---

## 📌 DOM Containers Reference

| Container ID | Source                             | Content           |
| ------------ | ---------------------------------- | ----------------- |
| #header      | app/views/pages/include/header.php | Navigation, logo  |
| #section1    | /introduce/section1                | Hero slider       |
| #section3    | /introduce/section3-2              | Services tabs     |
| #section4    | /introduce/section4                | Partners carousel |
| #section5    | /introduce/section5                | Content section   |
| #footer      | app/views/pages/include/footer.php | Footer            |

---

## 🔗 Asset Loading Pattern

```php
// In PHP (scripts-root.php)
<script src="<?php echo View::asset('js/script.js'); ?>"></script>

// Becomes (HTML)
<script src="/public/assets/js/script.js"></script>

// Serves (File)
d:\xampp\htdocs\capitalam2-mvc\public\assets\js\script.js
```

---

## 📡 Fetch Patterns

### Relative Path (Filesystem)

```javascript
fetch("../../app/views/pages/include/header.php");
// From: public/assets/js/script.js
// To: app/views/pages/include/header.php
```

### Router Path (Application)

```javascript
fetch("/introduce/section1");
// Router: GET /introduce/section1 → PageController@section1
// Serves: app/views/pages/introduce/section1.php
```

---

## ⏱️ Critical Initialization Pattern

```javascript
// ✅ CORRECT - Waits for DOM render
fetch("/introduce/section1")
  .then((res) => res.text())
  .then((html) => {
    document.getElementById("section1").innerHTML = html;
    setTimeout(() => {
      initSection1Events(); // Safe to initialize
    }, 100);
  });

// ❌ WRONG - No timeout, elements not ready
fetch("/introduce/section1")
  .then((res) => res.text())
  .then((html) => {
    document.getElementById("section1").innerHTML = html;
    initSection1Events(); // Error: elements don't exist yet
  });
```

---

## 🔍 Debugging Quick Checklist

- [ ] Is `public/assets/js/script.js` file present?
- [ ] Does `View::asset('js/script.js')` return correct path? (Check HTML source)
- [ ] Are container divs (#section1, etc) in homepage.php?
- [ ] Does browser Network tab show script.js loading?
- [ ] Do fetched URLs exist? (Check Network → XHR/fetch)
- [ ] Are external libraries (Swiper, AOS) loaded? (Check Network)
- [ ] Does browser console show any JavaScript errors? (F12 → Console)
- [ ] Are there 100ms delays before initialization? (Check code)

---

## 🛠️ Adding New Content Section

1. **Create PHP View**

   ```
   app/views/pages/introduce/newsection.php
   ```

2. **Add Container in Homepage**

   ```php
   <!-- app/views/homepage/homepage.php -->
   <div id="newsection"></div>
   ```

3. **Add Fetch in script.js**

   ```javascript
   fetch("/introduce/newsection")
     .then((res) => res.text())
     .then((html) => {
       document.getElementById("newsection").innerHTML = html;
       setTimeout(() => {
         initNewSectionEvents();
       }, 100);
     });
   ```

4. **Define Route** (if using router path)
   ```php
   // routes/web_routes.php
   'GET /introduce/newsection' => 'Page@newsection'
   ```

---

## 📚 Documentation Files

| File                                                         | Purpose                                    |
| ------------------------------------------------------------ | ------------------------------------------ |
| [PHP_TO_JS_CONNECTION_MAP.md](PHP_TO_JS_CONNECTION_MAP.md)   | Detailed connection documentation          |
| [PHP_JS_CONNECTION_DIAGRAM.md](PHP_JS_CONNECTION_DIAGRAM.md) | Visual diagrams and flow charts            |
| ARCHITECTURE_FLOW.md                                         | Overall application architecture (updated) |
| [JS_UPDATES_SUMMARY.md](JS_UPDATES_SUMMARY.md)               | JavaScript updates and improvements        |

---

## 🎯 Key Concepts

### View::asset() Helper

- **Location**: app/core/View.php
- **Purpose**: Generate correct asset URLs
- **Usage**: `View::asset('js/script.js')` → `/public/assets/js/script.js`
- **Benefit**: Centralized path management

### DOM Injection Pattern

1. Fetch content from PHP view
2. Wait for browser to render (100ms)
3. Query elements that were injected
4. Attach event listeners
5. Initialize libraries

### Why 100ms Timeout?

- Setting innerHTML is synchronous (immediate)
- Browser rendering is asynchronous (takes time)
- Event listeners need real, rendered elements
- 100ms is safe margin for render cycle

### Router vs Relative Paths

- **Router**: `/introduce/section1` - Better practice, uses application routing
- **Relative**: `../../app/views/include/header.php` - Direct filesystem access
- **Prefer**: Router paths when possible (more maintainable)

---

## 🔗 Quick Links

**When you need to...**

| Task                        | File to Check                                    |
| --------------------------- | ------------------------------------------------ |
| Check where script.js loads | `app/views/pages/include/scripts-root.php`       |
| See DOM containers          | `app/views/homepage/homepage.php`                |
| Understand fetch patterns   | `public/assets/js/script.js` (top comment block) |
| Find section content        | `app/views/pages/introduce/section*.php`         |
| Add/modify routes           | `routes/web_routes.php`                          |
| Trace controller flow       | `app/controllers/HomeController.php`             |
| Debug asset paths           | Browser → F12 → Network tab                      |

---

## 💡 Best Practices

✅ **DO**

- Use View::asset() for all asset paths
- Keep container IDs consistent between PHP & JS
- Use setTimeout(fn, 100) before initializing
- Check Network tab when debugging
- Use absolute router paths (/introduce/...) over relative

❌ **DON'T**

- Hardcode asset paths in views
- Initialize components before fetch completes
- Load scripts in <head> section
- Change element IDs without updating JavaScript
- Ignore async operations and timing issues

---

## 📋 View::asset() Examples

```php
// CSS
<link rel="stylesheet" href="<?php echo View::asset('css/styles.css'); ?>">

// JavaScript
<script src="<?php echo View::asset('js/script.js'); ?>"></script>

// Images
<img src="<?php echo View::asset('img/logo.png'); ?>" alt="Logo">

// All resolve to /public/assets/...
```

---

## 🎓 Learning Path

1. **Start**: Read this file (quick overview)
2. **Understand**: Read ARCHITECTURE_FLOW.md (section 12)
3. **Visualize**: Read PHP_JS_CONNECTION_DIAGRAM.md (visual flows)
4. **Deep Dive**: Read PHP_TO_JS_CONNECTION_MAP.md (detailed explanations)
5. **Reference**: Check script.js header comments (implementation details)

---

## 🚀 Performance Notes

- All fetches run in parallel (not sequential) → Faster loading
- Content injected after fetch completes (lazy loading) → Efficient
- Event listeners attached after DOM update (safe initialization) → No errors
- External libraries preload before script.js → Library ready

---

## 🆘 Common Error Messages

| Error                                     | Solution                                         |
| ----------------------------------------- | ------------------------------------------------ |
| "Cannot set property 'innerHTML' of null" | Container #id doesn't exist in homepage.php      |
| "Swiper is not defined"                   | Swiper library not loaded before script.js       |
| XHR failed "404 Not Found"                | Fetch path incorrect or route not defined        |
| "Cannot read property '...' of undefined" | Trying to access elements before fetch completes |
| "script.js 404"                           | View::asset('js/script.js') path incorrect       |

---

## 📞 Support Resources

- Browser DevTools (F12) → Network tab for request debugging
- Browser Console (F12 → Console) for JavaScript errors
- PHP error logs for server-side issues
- Router paths in routes/web_routes.php for URL mapping
- HTML source code to verify asset paths are generated correctly

---

**Last Updated**: February 2026  
**Version**: 1.0  
**Architecture**: PHP MVC with JavaScript Enhancement
