# CapitalA M2 MVC - Documentation Index

Complete documentation for understanding and implementing the PHP-API-JavaScript flow in your CapitalA M2 MVC application.

## 📚 Documentation Files

### 1. **ARCHITECTURE_FLOW.md** - Comprehensive Architecture Guide

- Complete system overview
- Detailed web request flow
- Detailed API request flow
- Directory structure and responsibilities
- Key integration points
- Data flow examples
- Best practices for development

**Start here to understand** the overall architecture and how components connect.

### 2. **FLOW_EXAMPLES.md** - Practical Code Examples

- Complete working example: News article display
- Step-by-step code implementation
- Complete request/response flow diagram
- Form submission example (Contact form)
- Data flow summary table

**Start here to see real code** and understand how to implement features.

### 3. **QUICK_INTEGRATION_GUIDE.md** - Developer Quick Reference

- 6-step checklist for adding new features
- Quick start scenario (Products example)
- File location reference
- Common API patterns (GET, POST, PUT, DELETE)
- Standard response formats
- JavaScript communication patterns
- Testing guide
- Troubleshooting tips

**Start here when implementing** new features or integrating new endpoints.

---

## 🎯 Quick Navigation by Task

### **I want to understand the overall architecture**

→ Read: **ARCHITECTURE_FLOW.md** sections 1-6

### **I want to see a concrete example**

→ Read: **FLOW_EXAMPLES.md** - Example 1: Displaying News Articles

### **I'm adding a new feature**

→ Use: **QUICK_INTEGRATION_GUIDE.md** - 6-step checklist

### **I need to create an API endpoint**

→ Use: **QUICK_INTEGRATION_GUIDE.md** - File Locations Reference

### **I need to write JavaScript to call an API**

→ Use: **QUICK_INTEGRATION_GUIDE.md** - Common API Patterns

### **Something isn't working**

→ Use: **QUICK_INTEGRATION_GUIDE.md** - Troubleshooting Guide

---

## 🔄 The Three Main Flows

### **Flow 1: Web Page Request**

```
User visits URL → index.php → Router → Controller → View (HTML) → Browser
                                                         ↓
                                                    Loads JS Files
```

### **Flow 2: API Request**

```
JavaScript (fetch) → public/api.php → Router → API Controller → JSON Response → JavaScript
                                                      ↓
                                                   Model
                                                      ↓
                                                  Database
```

### **Flow 3: Dynamic Page Update**

```
Page loads with HTML → JavaScript runs → Fetch API → Update DOM → User sees new content
```

---

## 📁 Key Files Involved

### **Entry Points**

- `index.php` - Web requests
- `public/api.php` - API requests

### **Routing**

- `routes/web_routes.php` - Web page route definitions
- `routes/api_routes.php` - API endpoint definitions

### **Controllers**

- `app/controllers/` - Web controllers (return HTML views)
- `app/controllers/API_controllers/` - API controllers (return JSON)

### **Data Layer**

- `app/models/` - Data models (query database)
- `app/core/Database.php` - Database connection

### **Views**

- `app/views/` - HTML templates

### **Client Code**

- `public/assets/js/script.js` - Main JavaScript logic
- `public/assets/js/config.js` - Configuration for API calls
- `public/assets/css/` - Styling

---

## 🚀 Getting Started Steps

### **Step 1: Understand the Architecture**

Read: ARCHITECTURE_FLOW.md (sections 1-4)
Time: 10-15 minutes

### **Step 2: See a Real Example**

Read: FLOW_EXAMPLES.md - Example 1
Time: 5-10 minutes

### **Step 3: Create Your First Integration**

Use: QUICK_INTEGRATION_GUIDE.md
Time: 30-45 minutes

### **Step 4: Test & Debug**

Use: QUICK_INTEGRATION_GUIDE.md - Testing & Troubleshooting
Time: As needed

---

## 📊 Request/Response Patterns

### **Standard API Response (Success)**

```json
{
  "status": true,
  "data": {...},
  "message": "Optional message"
}
```

### **Standard API Response (Error)**

```json
{
  "status": false,
  "message": "Error description",
  "code": 400
}
```

### **JavaScript Fetch Pattern**

```javascript
fetch(url, options)
  .then((res) => res.json())
  .then((data) => {
    if (data.status) {
      // Handle success
    } else {
      // Handle error
    }
  })
  .catch((error) => console.error("Error:", error));
```

---

## 🔧 Common Tasks

### **Task 1: Add a new API endpoint**

1. Add route in `routes/api_routes.php`
2. Create controller in `app/controllers/API_controllers/`
3. Use or create model in `app/models/`
4. Test in browser: `/api/your-endpoint`

**Guide:** QUICK_INTEGRATION_GUIDE.md - Steps 1-3

### **Task 2: Create a new web page**

1. Add route in `routes/web_routes.php`
2. Create controller in `app/controllers/`
3. Create view in `app/views/`
4. Add styles in `public/assets/css/`

**Guide:** QUICK_INTEGRATION_GUIDE.md - Steps 1, 4-5

### **Task 3: Load data dynamically via JavaScript**

1. Ensure API endpoint exists (above)
2. Create fetch function in `public/assets/js/script.js`
3. Add container div in view
4. Call fetch function when DOM is ready

**Guide:** QUICK_INTEGRATION_GUIDE.md - Step 6

### **Task 4: Handle form submission**

1. Create form in view
2. Add event listener in JavaScript
3. POST data to API endpoint
4. Handle response and update UI

**Guide:** FLOW_EXAMPLES.md - Example 2

---

## 🎓 Learning Tips

1. **Start Simple**: Begin with GET requests before POST/PUT/DELETE
2. **Use Browser Tools**: F12 DevTools → Network tab to see all requests
3. **Check Console**: F12 DevTools → Console to see JavaScript errors
4. **Test APIs Directly**: Copy API URL into browser to see raw JSON response
5. **Read Error Messages**: They usually tell you exactly what's wrong
6. **Follow the Examples**: Study FLOW_EXAMPLES.md closely - it shows everything

---

## ✅ Verification Checklist

When implementing a new feature, verify:

- [ ] Route is defined in appropriate routes file
- [ ] Controller created and method exists
- [ ] Model created with necessary database methods
- [ ] View template created with proper includes
- [ ] JavaScript function created to fetch/handle data
- [ ] HTML container with ID for JavaScript targets
- [ ] API returns JSON with correct format
- [ ] JavaScript fetch call uses correct URL
- [ ] Browser Network tab shows requests/responses
- [ ] No JavaScript errors in Console
- [ ] Page displays data correctly

---

## 🆘 Help Resources

### **When stuck, check:**

1. Browser Console (F12) for JavaScript errors
2. Network Tab (F12) for failed requests
3. Server logs for PHP errors
4. QUICK_INTEGRATION_GUIDE.md - Troubleshooting section
5. FLOW_EXAMPLES.md for similar examples

### **Common Issues:**

- **Blank page** → Check console for errors
- **"Loading..." forever** → Check Network tab, API might be failing
- **JSON parse error** → API returned HTML instead of JSON (check route!)
- **Function not defined** → script.js not loaded or function not created

---

## 📞 Quick Reference

| What                 | Where                              | How                                    |
| -------------------- | ---------------------------------- | -------------------------------------- |
| Add API route        | `routes/api_routes.php`            | `$router->get('/endpoint', [...])`     |
| Create API handler   | `app/controllers/API_controllers/` | Extend Controller, use `$this->json()` |
| Query database       | `app/models/`                      | Extend Model, use `$this->db->table()` |
| Render HTML page     | `app/controllers/`                 | Use `$this->render('view/path')`       |
| Create HTML template | `app/views/`                       | Include head, header, footer, scripts  |
| Call API from JS     | `public/assets/js/script.js`       | Use `fetch()` with `APP_CONFIG`        |
| Configure API URL    | `public/assets/js/config.js`       | Update `APP_CONFIG.apiBase`            |

---

## 🎬 Next Steps

1. **Read:** ARCHITECTURE_FLOW.md to understand the system
2. **Study:** FLOW_EXAMPLES.md to see working code
3. **Reference:** QUICK_INTEGRATION_GUIDE.md when building features
4. **Test:** Use browser DevTools to verify requests/responses
5. **Implement:** Create your first integrated feature

Good luck! The system is well-designed and scalable. Follow the patterns shown in the examples and you'll be able to build features quickly and reliably.

---

**Last Updated:** February 6, 2026
**Framework:** CapitalA M2 MVC
**Version:** 1.0

For questions about specific files, see the documentation files linked above.
