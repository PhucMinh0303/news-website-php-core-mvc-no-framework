# Recruitment Module - Quick Implementation Guide

## Directory Structure

```
app/
├── interfaces/
│   ├── IRecruitmentRepository.php      (Interface)
│   └── IApplicationRepository.php       (Interface)
├── repositories/
│   ├── RecruitmentRepository.php       (Implements IRecruitmentRepository)
│   └── ApplicationRepository.php        (Implements IApplicationRepository)
├── services/
│   ├── RecruitmentService.php          (Business Logic - Recruitment)
│   ├── ApplicationService.php          (Business Logic - Applications)
│   ├── ApplicationValidator.php        (Form Validation)
│   └── FileUploadService.php           (File Handling)
├── controllers/
│   └── RecruitmentController.php       (HTTP Request Handler)
├── core/
│   ├── Model.php                       (Base Database Class)
│   └── Controller.php                  (Base Controller Class)
├── models/                             (DEPRECATED - Use repositories)
│   ├── RecruitmentModel.php
│   ├── RecruitmentTitleModel.php
│   └── ApplicationModel.php
└── views/
    └── Recruitment/
        ├── Recruitment.php             (List View)
        └── Recruitment-title.php       (Detail View)
```

## Step-by-Step Flow

### 1. User Visits Recruitment List
```
Browser
  ↓
/recruitment
  ↓
RecruitmentController::index()
  ↓
Gets page number from URL
  ↓
RecruitmentService::getRecruitmentsList()
  ↓
RecruitmentRepository::getActiveRecruitmentsPaginated()
  ↓
SQL Query → PDO → MySQL
  ↓
Data returned to view
  ↓
Render app/views/Recruitment/Recruitment.php
```

### 2. User Applies for Position
```
Form Submission (POST)
  ↓
RecruitmentController::apply()
  ↓
ApplicationService::processApplication()
  ├─ ApplicationValidator::validate()        (Check form data)
  ├─ ApplicationRepository::hasApplied()     (Check duplicates)
  ├─ FileUploadService::uploadCV()          (Handle file)
  └─ ApplicationRepository::save()           (Store in DB)
  ↓
Set session message
  ↓
Redirect to detail page
  ↓
Detail page displays success message
```

## Key Classes and Methods

### RecruitmentRepository

```php
$repo = new RecruitmentRepository();

// Get paginated list
$results = $repo->getActiveRecruitmentsPaginated(10, 0);

// Get single by slug
$recruitment = $repo->getDetail('job-title-slug');

// Get featured
$featured = $repo->getFeaturedRecruitments(5);

// Get by position
$positions = $repo->getByPosition('PHP Developer', 10);

// Search
$results = $repo->search('PHP', 20, 0);

// Count active
$count = $repo->countActive();

// Increment views
$repo->incrementViews($id);

// Get all positions
$positions = $repo->getAllPositions();
```

### RecruitmentService

```php
$service = new RecruitmentService();

// Get list with pagination info
$data = $service->getRecruitmentsList(1, 10);
// Returns: ['recruitments', 'totalPages', 'total', 'limit']

// Get single with view count
$recruitment = $service->getRecruitmentDetail('slug');

// Get related
$related = $service->getRelatedRecruitments('position', $id, 4);

// Get featured
$featured = $service->getFeaturedRecruitments(5);

// Get positions
$positions = $service->getAllPositions();

// Search
$results = $service->searchRecruitments('keyword', 'position', 20);
```

### ApplicationService

```php
$service = new ApplicationService();

// Process application
$result = $service->processApplication(
    $recruitmentId,  // int
    $_POST,          // array: ten, dt, email, noidung
    $_FILES,         // array: filechon
    $ipAddress       // string
);

// Result format
// ['success' => bool, 'errors' => array, 'message' => string]

if ($result['success']) {
    // Application saved
} else {
    // Check $result['errors'] for validation failures
}
```

### ApplicationValidator

```php
$validator = new ApplicationValidator();

// Validate all fields
$errors = $validator->validate($_POST, $_FILES);

// Validate individually
$validator->validateFullname($name);       // Returns bool
$validator->validatePhone($phone);         // Returns string|null
$validator->validateEmail($email);         // Returns string|null
$validator->validateCV($file);             // Returns string|null
```

### FileUploadService

```php
$service = new FileUploadService();

// Upload CV
$result = $service->uploadCV($_FILES['filechon'], 'fullname');
// Returns: ['success' => bool, 'path' => string, 'error' => string]

// Delete file
$deleted = $service->deleteFile('uploads/cv/filename.pdf');
```

## Controller Usage

```php
class RecruitmentController extends Controller
{
    private $recruitmentService;
    private $applicationService;

    public function __construct()
    {
        // Dependency injection
        $this->recruitmentService = new RecruitmentService();
        $this->applicationService = new ApplicationService();
    }

    // List page
    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $listData = $this->recruitmentService->getRecruitmentsList($page, 10);
        
        $data = [
            'recruitments' => $listData['recruitments'],
            'totalPages' => $listData['totalPages'],
            // ... more data
        ];
        
        $this->view('recruitment/recruitment', $data);
    }

    // Detail page
    public function detail($slug)
    {
        $recruitment = $this->recruitmentService->getRecruitmentDetail($slug);
        // Handles view increment automatically
        
        // ... rest of implementation
    }

    // Apply
    public function apply()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/recruitment');
            return;
        }
        
        $result = $this->applicationService->processApplication(
            $_POST['recruitment_id'],
            $_POST,
            $_FILES,
            $_SERVER['REMOTE_ADDR']
        );
        
        if ($result['success']) {
            $_SESSION['apply_success'] = $result['message'];
        } else {
            $_SESSION['apply_errors'] = $result['errors'];
        }
        
        $this->redirect('/recruitment/' . $_POST['slug']);
    }
}
```

## Database Schema

### recruitment_title table
```sql
CREATE TABLE recruitment_title (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255),
    slug VARCHAR(255) UNIQUE,
    position VARCHAR(100),
    work_location VARCHAR(100),
    description LONGTEXT,
    status TINYINT DEFAULT 1,
    featured TINYINT DEFAULT 0,
    deadline DATE,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP
);
```

### applications table
```sql
CREATE TABLE applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recruitment_id INT NOT NULL,
    fullname VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    content TEXT,
    cv_file VARCHAR(255),
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recruitment_id) REFERENCES recruitment_title(id)
);
```

## Extending the System

### Add New Repository Method

1. Add method to interface
```php
// IRecruitmentRepository.php
public function getExpiringSoon($days = 7);
```

2. Implement in repository
```php
// RecruitmentRepository.php
public function getExpiringSoon($days = 7)
{
    $sql = "SELECT * FROM {$this->table} 
            WHERE status = 1 
            AND deadline BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL {$days} DAY)
            ORDER BY deadline ASC";
    $stmt = $this->connection->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

3. Use in service
```php
// RecruitmentService.php
public function getExpiringRecruitments($days = 7)
{
    return $this->recruitmentRepository->getExpiringSoon($days);
}
```

4. Use in controller
```php
$expiring = $this->recruitmentService->getExpiringRecruitments(7);
```

## Best Practices

### 1. Always Use Dependency Injection
```php
// Good
public function __construct(RecruitmentRepository $repo = null)
{
    $this->repo = $repo ?? new RecruitmentRepository();
}

// Bad
public function __construct()
{
    $this->repo = new RecruitmentRepository();
}
```

### 2. Use Interfaces for Type Hinting
```php
// Good
public function __construct(IRecruitmentRepository $repo)

// Bad
public function __construct(RecruitmentRepository $repo)
```

### 3. Keep Controllers Thin
```php
// Good - All logic in service
$result = $this->service->process($data);

// Bad - Logic in controller
if ($data['email']) {
    // validation logic
    // business logic
    // file handling
}
```

### 4. Return Consistent Response Format
```php
// Service should return consistent format
return [
    'success' => bool,
    'data' => mixed,
    'errors' => array,
    'message' => string
];
```

### 5. Use Prepared Statements
```php
// Good - Prevents SQL injection
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);

// Bad - SQL injection vulnerability
$sql = "SELECT * FROM users WHERE id = $id";
```

## Testing

### Test Repository
```php
// Test with mock repository
class TestRecruitmentService {
    public function test_gets_paginated_list() {
        $mock = $this->createMock(IRecruitmentRepository::class);
        $mock->expects($this->once())
            ->method('getActiveRecruitmentsPaginated')
            ->with(10, 0)
            ->willReturn([...]);
        
        $service = new RecruitmentService($mock);
        $result = $service->getRecruitmentsList(1, 10);
        
        $this->assertTrue($result['success']);
    }
}
```

### Test Validator
```php
public function test_validates_phone() {
    $validator = new ApplicationValidator();
    
    $error = $validator->validatePhone('0123456789');
    $this->assertNull($error);
    
    $error = $validator->validatePhone('123');
    $this->assertNotNull($error);
}
```

## Common Issues and Solutions

### Issue: "Call to undefined method RecruitmentRepository"
**Solution:** Make sure you're calling the correct method name. Check IRecruitmentRepository interface.

### Issue: "Headers already sent" on redirect
**Solution:** Make sure no output is sent before `$this->redirect()`. Check for echo statements.

### Issue: File upload fails
**Solution:** Check upload directory permissions. Should be 755 or 777.

### Issue: Duplicate application check not working
**Solution:** Verify email and IP address are being captured correctly. Check ApplicationRepository::hasApplied().

## Performance Tips

1. **Cache featured recruitments** - Rarely changes
2. **Index slug and status columns** - Used in WHERE clause
3. **Use pagination** - Don't load all records
4. **Eager load related data** - If needed
5. **Consider database connection pooling** - For high traffic

## Security Checklist

- ✓ Use prepared statements (implemented)
- ✓ Validate file uploads (ApplicationValidator)
- ✓ Limit file size (ApplicationValidator::MAX_CV_SIZE)
- ✓ Check file MIME type (ApplicationValidator)
- ✓ Sanitize file names (FileUploadService)
- ✓ Check duplicate applications (ApplicationRepository)
- ✓ Validate email format (ApplicationValidator)
- ✓ Validate phone format (ApplicationValidator)
- ✓ Protect against CSRF (add CSRF token to forms)
- ✓ Rate limit applications (consider implementing)

