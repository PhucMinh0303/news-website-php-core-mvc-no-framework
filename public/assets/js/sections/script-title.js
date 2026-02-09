/**
 * ============================================================================
 * SCRIPT.JS - Main Application JavaScript
 * ============================================================================
 *
 * PHP-JAVASCRIPT CONNECTION DOCUMENTATION
 * =========================================
 *
 * FILE LOCATION: public/assets/js/script.js
 * LOADED FROM: app/views/pages/include/scripts-root.php
 * SCRIPT TAG: <script src="<?php echo View::asset('js/script.js'); ?>"></script>
 *
 * PURPOSE: Client-side JavaScript for homepage. Fetches dynamic content from
 * PHP views and initializes interactive components.
 *
 * CONNECTION FLOW:
 * 1. User visits / → Router → HomeController@index()
 * 2. PHP renders app/views/homepage/homepage.php
 * 3. homepage.php includes scripts-root.php (at END)
 * 4. scripts-root.php loads this script.js
 * 5. script.js fetches content and initializes
 *
 * KEY DOM CONTAINERS (created by homepage.php):
 * - #header    → app/views/pages/include/header.php
 * - #section1  → /introduce/section1 → section1.php
 * - #section3  → /introduce/section3-2 → section3-2.php
 * - #section4  → /introduce/section4 → section4.php
 * - #section5  → /introduce/section5 → section5.php
 * - #footer    → app/views/pages/include/footer.php
 *
 * VIEW::ASSET() HELPER (PHP):
 * Generates correct asset paths from PHP views
 * View::asset('js/script.js') → /public/assets/js/script.js
 *
 * FETCH PATTERNS:
 *
 * Relative Path (from JS location):
 *   fetch("../../app/views/pages/include/header.php")
 *
 * Router Path (through application):
 *   fetch("/introduce/section1")
 *   → Router routes to PageController@section1
 *   → Renders app/views/pages/introduce/section1.php
 *
 * DOM INJECTION PATTERN:
 *   1. fetch() → get content
 *   2. .innerHTML = content → inject into DOM
 *   3. setTimeout(() => {}, 100) → wait for render
 *   4. initEvents() → attach listeners
 *
 * WHY TIMEOUT? Browser rendering is async. Setting innerHTML is sync.
 * 100ms allows browser to render before code queries elements.
 *
 * ============================================================================
 */
