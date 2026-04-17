# Recruitment OOP Structure Documentation

## Architecture Overview

The recruitment module has been refactored to follow Clean Architecture principles with clear separation of concerns:

```
PDO Database Connection (config/Database.php)
    ↓
Core Database Model (app/core/Model.php)
    ↓
Repository Layer (app/repositories/)
├── IRecruitmentRepository (Interface)
├── RecruitmentRepository (Implementation)
├── IApplicationRepository (Interface)
└── ApplicationRepository (Implementation)
    ↓
Service Layer (app/services/)
├── RecruitmentService (Business Logic)
├── ApplicationService (Application Logic)
├── ApplicationValidator (Validation)
└── FileUploadService (File Handling)
    ↓
Controller Layer (app/controllers/)
└── RecruitmentController (Request Handling)
    ↓
View Layer (app/views/Recruitment/)
├── Recruitment.php (List View)
└── Recruitment-title.php (Detail View)
```

## Component Breakdown

### 1. Database Layer (PDO)

**File:** `config/Database.php` & `app/core/Database.php`

- Configures PDO connection to MySQL
- Provides database credentials
- Connection is initialized in `Model` base class

```php
// PDO initialized with:
$pdo = new PDO(
    'mysql:host=localhost;dbname=quanly_tintuc;charset=utf8mb4',
    'capitalam',
    '123456'
);
```

### 2. Model Layer (Core Base Class)

**File:** `app/core/Model.php`

- Provides database access methods
- Implements basic CRUD operations
- Handles prepared statements for security
- Methods:
  - `findAll()` - Get all records
  - `findById($id)` - Get by ID
  - `findBySlug($slug)` - Get by slug
  - `create($data)` - Insert new record
  - `update($id, $data)` - Update record
  - `delete($id)` - Delete record
  - `where($conditions, $params)` - Query with conditions
  - `paginate($limit, $offset, $conditions)` - Pagination support

### 3. Repository Layer

**Purpose:** Abstracts data access logic from business logic

#### 3.1 IRecruitmentRepository (Interface)

**File:** `app/interfaces/IRecruitmentRepository.php`

Defines contract for recruitment data operations:
- `getActiveRecruitmentsPaginated($limit, $offset)` - Get paginated list
- `getActiveRecruitments($limit)` - Get all active
- `getDetail($slug)` - Get single recruitment
- `getFeaturedRecruitments($limit)` - Get featured items
- `getByPosition($position, $limit)` - Filter by position
- `search($keyword, $limit, $offset)` - Search functionality
- `countActive()` - Count total active
- `incrementViews($id)` - Track views
- `getAllPositions()` - Get position list

#### 3.2 RecruitmentRepository (Implementation)

**File:** `app/repositories/RecruitmentRepository.php`

Implements `IRecruitmentRepository`:
- Extends `Model` for database access
- Works with `recruitment_title` table
- Implements all interface methods
- Uses prepared statements for SQL injection prevention
- Implements filtering by status and deadline

#### 3.3 IApplicationRepository (Interface)

**File:** `app/interfaces/IApplicationRepository.php`

Defines contract for application data operations:
- `save($data)` - Save application
- `hasApplied($recruitmentId, $email, $ipAddress)` - Check duplicates
- `getByRecruitmentId($recruitmentId)` - Get applications

#### 3.4 ApplicationRepository (Implementation)

**File:** `app/repositories/ApplicationRepository.php`

Implements `IApplicationRepository`:
- Works with `applications` table
- Handles duplicate checks
- Manages application records

### 4. Service Layer

**Purpose:** Contains business logic and coordinates multiple operations

#### 4.1 RecruitmentService

**File:** `app/services/RecruitmentService.php`

Orchestrates recruitment operations:

```php
public function getRecruitmentsList($page, $limit)
// Returns paginated list with page info

public function getRecruitmentDetail($slug)
// Gets detail and increments view count

public function getRelatedRecruitments($position, $recruitmentId, $limit)
// Gets similar positions excluding current

public function getFeaturedRecruitments($limit)
// Gets featured items

public function getAllPositions()
// Gets distinct positions

public function searchRecruitments($keyword, $position, $limit)
// Searches by keyword or position
```

**Dependency Injection:**
```php
public function __construct(RecruitmentRepository $recruitmentRepository = null)
{
    $this->recruitmentRepository = $recruitmentRepository ?? new RecruitmentRepository();
}
```

#### 4.2 ApplicationService

**File:** `app/services/ApplicationService.php`

Manages application workflow:

```php
public function processApplication($recruitmentId, $postData, $files, $ipAddress)
// Main method: validates, uploads CV, saves application
// Returns: ['success' => bool, 'errors' => array, 'message' => string]

public function hasApplied($recruitmentId, $email, $ipAddress)
// Checks for duplicate applications
```

**Dependencies:**
- `ApplicationRepository` - Data access
- `ApplicationValidator` - Form validation
- `FileUploadService` - File handling

#### 4.3 ApplicationValidator

**File:** `app/services/ApplicationValidator.php`

Handles form validation:

```php
public function validate($data, $files)
// Main validation method

private function validateFullname($fullname)
private function validatePhone($phone)
private function validateEmail($email)
private function validateCV($file)
```

**Constants:**
- `MAX_CV_SIZE` = 5MB
- `ALLOWED_CV_TYPES` = PDF, Word documents

#### 4.4 FileUploadService

**File:** `app/services/FileUploadService.php`

Manages file operations:

```php
public function uploadCV($file, $fullname)
// Uploads CV to public/uploads/cv/
// Returns: ['success' => bool, 'path' => string, 'error' => string]

public function deleteFile($filePath)
// Deletes uploaded file
```

### 5. Controller Layer

**File:** `app/controllers/RecruitmentController.php`

Thin controller pattern - delegates to services:

```php
class RecruitmentController extends Controller
{
    private $recruitmentService;
    private $applicationService;
    
    // Dependency injection in constructor
    public function __construct()
    {
        $this->recruitmentService = new RecruitmentService();
        $this->applicationService = new ApplicationService();
    }
```

**Actions:**

1. `index()` - List page
   - Calls `RecruitmentService::getRecruitmentsList()`
   - Gets featured items and positions
   - Renders `recruitment/recruitment` view

2. `detail($slug)` - Detail page
   - Calls `RecruitmentService::getRecruitmentDetail()`
   - Gets related recruitments
   - Handles session messages
   - Renders `recruitment/recruitment-title` view

3. `apply()` - Process application
   - Calls `ApplicationService::processApplication()`
   - Sets session messages
   - Redirects back to detail page

4. `search()` - Search functionality
   - Calls `RecruitmentService::searchRecruitments()`
   - Supports AJAX and regular requests
   - Returns JSON for AJAX

5. `apiList()` - API endpoint
   - Returns JSON list of recruitments

### 6. View Layer

**Files:**
- `app/views/Recruitment/Recruitment.php` - List page
- `app/views/Recruitment/Recruitment-title.php` - Detail page

Views receive data from controller:
```php
// In controller
$data = [
    'recruitments' => $recruitments,
    'currentPage' => $page,
    'totalPages' => $totalPages,
    // ... more data
];
$this->view('recruitment/recruitment', $data);

// In view - variables are extracted
// $recruitments, $currentPage, $totalPages available directly
```

## Data Flow Example: Getting Recruitment List

```
1. Browser Request
   ↓
2. RecruitmentController::index()
   ├── $page = $_GET['page'] ?? 1
   ├── $limit = 10
   ├── $this->recruitmentService->getRecruitmentsList($page, $limit)
   │
3. RecruitmentService::getRecruitmentsList()
   ├── $offset = ($page - 1) * $limit
   ├── $this->recruitmentRepository->getActiveRecruitmentsPaginated($limit, $offset)
   ├── $this->recruitmentRepository->countActive()
   ├── Calculates $totalPages
   └── Returns array with pagination data
   │
4. RecruitmentRepository::getActiveRecruitmentsPaginated()
   ├── Builds SQL query
   ├── Prepares statement
   ├── Binds parameters
   ├── Executes query
   └── Fetches results from PDO
   │
5. PDO
   └── Executes SQL against MySQL database
   │
6. Data returned back through chain
   ├── Repository → Service → Controller
   │
7. Controller passes data to view
   └── $this->view('recruitment/recruitment', $data)
   │
8. View renders HTML
   ├── Iterates through $recruitments
   ├── Displays pagination links
   └── Returns HTML to browser
   │
9. Browser displays page
```

## Data Flow Example: Submitting Application

```
1. Form Submission
   └── POST /recruitment/apply
   │
2. RecruitmentController::apply()
   ├── Extracts POST data: $recruitmentId, $fullname, etc.
   ├── Gets IP address
   ├── Calls ApplicationService::processApplication()
   │
3. ApplicationService::processApplication()
   ├── Validates data with ApplicationValidator
   │   └── Checks fullname, phone, email, CV file
   ├── Checks duplicate with ApplicationRepository
   ├── Uploads CV with FileUploadService
   ├── Saves application with ApplicationRepository
   └── Returns result array
   │
4. If success:
   ├── Sets session message
   ├── Sends notification email (optional)
   ├── Redirects to recruitment detail page
   │
5. Detail page loads
   ├── Displays success message from session
   └── Clears session message
```

## Benefits of This Architecture

### 1. Separation of Concerns
- **Repository**: Data access logic only
- **Service**: Business logic only
- **Controller**: Request handling only
- **View**: Presentation only

### 2. Testability
- Services can be tested independently
- Mock repositories can be injected
- No global dependencies

### 3. Reusability
- Services can be used by multiple controllers
- Repositories can be swapped for different implementations
- Validators are standalone

### 4. Maintainability
- Changes to one layer don't affect others
- Easy to locate and fix bugs
- Code is self-documenting through interfaces

### 5. Scalability
- Easy to add new features
- Services can be extended without modifying existing code
- Interfaces allow for multiple implementations

## Migration from Old Structure

### Old Model-Based Approach
```php
class RecruitmentController {
    private $recruitmentTitleModel;
    
    public function __construct() {
        $this->recruitmentTitleModel = new RecruitmentTitleModel();
    }
    
    public function detail($slug) {
        $recruitment = $this->recruitmentTitleModel->getDetail($slug);
        // Validation, business logic, view rendering all mixed
    }
}
```

### New Service-Based Approach
```php
class RecruitmentController {
    private $recruitmentService;
    
    public function __construct() {
        $this->recruitmentService = new RecruitmentService();
    }
    
    public function detail($slug) {
        $recruitment = $this->recruitmentService->getRecruitmentDetail($slug);
        // Only handles request and view rendering
    }
}
```

## Deprecated Files

The following old files can be deprecated:
- `app/models/RecruitmentModel.php` - Replaced by RecruitmentRepository
- `app/models/RecruitmentTitleModel.php` - Replaced by RecruitmentRepository
- `app/models/ApplicationModel.php` - Replaced by ApplicationRepository

## Future Improvements

1. **Dependency Injection Container**
   - Create a service container for dependency management
   - Centralize service instantiation

2. **Event System**
   - Fire events on application submit
   - Allow listeners for notifications

3. **Queue System**
   - Queue email notifications
   - Async file processing

4. **Caching Layer**
   - Cache featured recruitments
   - Cache position list

5. **Logging**
   - Log all application submissions
   - Track errors in services

6. **Unit Tests**
   - Test each service independently
   - Test repositories with mock database
   - Test validators with various inputs

