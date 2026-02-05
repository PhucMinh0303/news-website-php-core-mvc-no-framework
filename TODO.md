# TODO: Implement Front Controller + Router Model for PHP Core MVC

## Completed Tasks

- [x] Scan 'app/views/' folders: admin/, Contact/, errors/, homepage/, Introduction/, investor&relations/, News/, pages/, product&service/, Recruitment/
- [x] Scan 'app/core/' files: App.php, Controller.php, Model.php, Router.php, View.php
- [x] Add routes for admin and errors in Router.php
- [x] Modify App.php to use Router for URL handling instead of manual parsing
- [x] Update TODO.md with completed tasks
- [x] Implement Front Controller pattern by integrating Router into App.php
- [x] Replace manual URL parsing in App.php with Router-based routing
- [x] Update App.php to instantiate Router and use route() method for controller, action, and params
- [x] Add require_once for Router.php in App.php
- [x] Implement fallback to NotFoundController if controller file does not exist
- [x] Update App.php constructor to use Router results and handle controller/method existence
- [x] Complete the integration of Router into App.php for Front Controller pattern

## Remaining Tasks

- [ ] Test the updated URL handling to ensure routes work correctly
- [ ] Verify that 404 errors are handled properly via NotFoundController
- [ ] Check if any additional routes need to be added for subfolders in 'pages/' (e.g., introduce/, Contact/, News/, product-service/, Recruitment/)
- [ ] Ensure all controllers exist and methods are properly defined
- [ ] Update any documentation or comments if necessary

## Notes

- The Front Controller pattern is now implemented with Router handling URL routing.
- App.php now delegates URL parsing to Router, which maps URLs to controllers and actions based on registered routes.
- Routes have been added for admin and errors sections.
- Fallback to NotFoundController if controller file does not exist.
- Router is instantiated in App.php and route() method is called to get controller, action, and params.
- Manual parseUrl method removed as it's no longer needed.
- App.php now requires Router.php at the top.
- Controller instantiation and method calling updated to use Router results.
- Added checks for controller file and method existence to ensure robustness.
- The MVC framework now uses centralized routing for URL handling.
