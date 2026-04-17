# Recruitment Module - Architecture Diagram

## Complete OOP Structure Flow

```
┌─────────────────────────────────────────────────────────────────────────┐
│                           USER BROWSER                                   │
└────────────────────┬────────────────────────────────────────────────────┘
                     │
                     │ HTTP Request
                     ↓
┌─────────────────────────────────────────────────────────────────────────┐
│                      ROUTING LAYER                                       │
│  (routes/web_routes.php)                                               │
│  - /recruitment -> index()                                              │
│  - /recruitment/{slug} -> detail($slug)                                │
│  - POST /recruitment/apply -> apply()                                  │
│  - /recruitment/search -> search()                                     │
└────────────────────┬────────────────────────────────────────────────────┘
                     │
                     │ Route Match
                     ↓
┌─────────────────────────────────────────────────────────────────────────┐
│                    CONTROLLER LAYER                                      │
│  (app/controllers/RecruitmentController.php)                            │
│  ┌────────────────────────────────────────────────────────────────────┐ │
│  │ - RecruitmentController                                            │ │
│  │   ├─ __construct()                                                 │ │
│  │   │  ├─ $recruitmentService = new RecruitmentService()           │ │
│  │   │  └─ $applicationService = new ApplicationService()           │ │
│  │   │                                                                │ │
│  │   ├─ index()          ──→ calls RecruitmentService                 │ │
│  │   ├─ detail($slug)    ──→ calls RecruitmentService                 │ │
│  │   ├─ apply()          ──→ calls ApplicationService                 │ │
│  │   ├─ search()         ──→ calls RecruitmentService                 │ │
│  │   └─ apiList()        ──→ calls RecruitmentService                 │ │
│  └────────────────────────────────────────────────────────────────────┘ │
└────────────────────┬────────────────────────────────────────────────────┘
                     │
        ┌────────────┴────────────┐
        │                         │
        ↓                         ↓
┌──────────────────────┐  ┌──────────────────────────────────────────┐
│   SERVICE LAYER      │  │    SERVICE LAYER (Continued)            │
│                      │  │                                          │
│ RecruitmentService   │  │ ApplicationService                       │
│ ┌──────────────────┐ │  │ ┌──────────────────────────────────────┐ │
│ │ Dependencies:    │ │  │ │ Dependencies:                        │ │
│ │ - RecruitmentRep │ │  │ │ - ApplicationRepository              │ │
│ │                  │ │  │ │ - ApplicationValidator               │ │
│ │ Methods:         │ │  │ │ - FileUploadService                  │ │
│ │ - getRecr...List │ │  │ │                                      │ │
│ │ - getRecr...Detail│ │  │ │ Methods:                             │ │
│ │ - getRelated     │ │  │ │ - processApplication()               │ │
│ │ - getFeatured    │ │  │ │ - getApplicationsByRecId()           │ │
│ │ - getAllPos...   │ │  │ │ - hasApplied()                       │ │
│ │ - searchRecr...  │ │  │ │                                      │ │
│ └──────────────────┘ │  │ └──────────────────────────────────────┘ │
└──────────────────────┘  │                                          │
                          │ Supporting Services:                     │
                          │ ┌──────────────────────────────────────┐ │
                          │ │ ApplicationValidator                 │ │
                          │ │ - validate()                         │ │
                          │ │ - validateFullname()                 │ │
                          │ │ - validatePhone()                    │ │
                          │ │ - validateEmail()                    │ │
                          │ │ - validateCV()                       │ │
                          │ └──────────────────────────────────────┘ │
                          │                                          │
                          │ ┌──────────────────────────────────────┐ │
                          │ │ FileUploadService                    │ │
                          │ │ - uploadCV()                         │ │
                          │ │ - deleteFile()                       │ │
                          │ └──────────────────────────────────────┘ │
                          └──────────────────────────────────────────┘
        │
        └─────────────────────┬──────────────────────┐
                              │                      │
        ┌─────────────────────┴──────────┐          │
        │                                 │          │
        ↓                                 ↓          ↓
┌──────────────────────────┐  ┌──────────────────────────────────────┐
│   REPOSITORY LAYER       │  │   REPOSITORY LAYER (Continued)       │
│                          │  │                                      │
│ IRecruitmentRepository   │  │ IApplicationRepository               │
│ (Interface)              │  │ (Interface)                          │
│ ┌──────────────────────┐ │  │ ┌──────────────────────────────────┐ │
│ │ Abstract methods:    │ │  │ │ Abstract methods:                │ │
│ │ - getActiveRec...P   │ │  │ │ - save()                         │ │
│ │ - getActiveRec...    │ │  │ │ - hasApplied()                   │ │
│ │ - getDetail()        │ │  │ │ - getByRecruitmentId()           │ │
│ │ - getFeatured()      │ │  │ │                                  │ │
│ │ - getByPosition()    │ │  │ │ RecruitmentRepository            │
│ │ - search()           │ │  │ │ (Implements)                     │
│ │ - countActive()      │ │  │ │                                  │ │
│ │ - incrementViews()   │ │  │ │ ApplicationRepository            │
│ │ - getAllPositions()  │ │  │ │ (Implements)                     │
│ └──────────────────────┘ │  │ └──────────────────────────────────┘ │
│                          │  │                                      │
│ RecruitmentRepository    │  │ Table: applications                  │
│ (Implements)             │  │ - id                                 │
│ Extends: Model           │  │ - recruitment_id (FK)                │
│ Table: recruitment_title │  │ - fullname                           │
│ - id                     │  │ - phone                              │
│ - title                  │  │ - email                              │
│ - slug                   │  │ - content                            │
│ - position               │  │ - cv_file                            │
│ - work_location          │  │ - ip_address                         │
│ - description            │  │ - created_at                         │
│ - status                 │  │                                      │
│ - featured               │  │                                      │
│ - deadline               │  │                                      │
│ - views                  │  │                                      │
│ - created_at             │  │                                      │
│ - updated_at             │  │                                      │
└──────────────────────────┘  └──────────────────────────────────────┘
        │                                    │
        │ Extends Model                      │
        │                                    │
        └──────────────┬─────────────────────┘
                       │
                       ↓
            ┌──────────────────────┐
            │   CORE MODEL CLASS   │
            │ (app/core/Model.php) │
            │                      │
            │ Methods:             │
            │ - findAll()          │
            │ - findById()         │
            │ - findBySlug()       │
            │ - create()           │
            │ - update()           │
            │ - delete()           │
            │ - where()            │
            │ - paginate()         │
            │ - query()            │
            │ - execute()          │
            │ - beginTransaction() │
            │ - commit()           │
            │ - rollback()         │
            └──────────┬───────────┘
                       │
                       │ Uses PDO Connection
                       ↓
            ┌──────────────────────┐
            │    DATABASE CLASS    │
            │(app/core/Database.php)
            │                      │
            │ Creates PDO:         │
            │ - Host: localhost    │
            │ - DB: quanly_tintuc  │
            │ - User: capitalam    │
            │ - Charset: utf8mb4   │
            └──────────┬───────────┘
                       │
                       ↓
            ┌──────────────────────┐
            │  PDO CONNECTION      │
            │ (MySQL Driver)       │
            └──────────┬───────────┘
                       │
                       ↓
            ┌──────────────────────┐
            │   MYSQL DATABASE     │
            │                      │
            │ Tables:              │
            │ - recruitment_title  │
            │ - applications       │
            │ - (other tables)     │
            └──────────────────────┘
```

## Data Flow Layers

```
REQUEST → CONTROLLER → SERVICE → REPOSITORY → MODEL → PDO → DATABASE
                                                      ↓
RESPONSE ← VIEW ← CONTROLLER ← SERVICE ← REPOSITORY ← DATA

┌─────────────────────────────────────────────────────────┐
│ LAYER 1: REQUEST HANDLING                               │
│ - Parse URL parameters                                  │
│ - Validate request method (GET/POST)                    │
│ - Extract form data                                     │
└─────────────────────────────────────────────────────────┘
           ↓
┌─────────────────────────────────────────────────────────┐
│ LAYER 2: BUSINESS LOGIC                                 │
│ - Service processes data                                │
│ - Coordinates multiple operations                       │
│ - Handles transactions                                  │
└─────────────────────────────────────────────────────────┘
           ↓
┌─────────────────────────────────────────────────────────┐
│ LAYER 3: DATA ACCESS                                    │
│ - Repository builds queries                             │
│ - Prepares statements                                   │
│ - Binds parameters                                      │
└─────────────────────────────────────────────────────────┘
           ↓
┌─────────────────────────────────────────────────────────┐
│ LAYER 4: DATABASE                                       │
│ - Execute SQL queries                                   │
│ - Return result sets                                    │
│ - Handle database errors                                │
└─────────────────────────────────────────────────────────┘
           ↓
┌─────────────────────────────────────────────────────────┐
│ RETURN PATH: Data bubbles back up                       │
│ Database → Repository → Service → Controller → View     │
└─────────────────────────────────────────────────────────┘
```

## Dependency Injection Pattern

```
┌─────────────────────────────────────┐
│ RecruitmentController               │
│                                     │
│ __construct() {                     │
│   // Constructor Injection          │
│   $this->service =                  │
│     new RecruitmentService();       │
│ }                                   │
│                                     │
│ Uses: $this->service->method()      │
└──────────────┬──────────────────────┘
               │ Depends on
               ↓
┌─────────────────────────────────────┐
│ RecruitmentService                  │
│                                     │
│ __construct(                        │
│   RecruitmentRepository $repo = null│
│ ) {                                 │
│   // Dependency Injection           │
│   $this->repo = $repo ??            │
│     new RecruitmentRepository();    │
│ }                                   │
│                                     │
│ Uses: $this->repo->method()         │
└──────────────┬──────────────────────┘
               │ Depends on
               ↓
┌─────────────────────────────────────┐
│ IRecruitmentRepository (Interface)  │
│                                     │
│ Can be:                             │
│ - RecruitmentRepository             │
│ - MockRepository (for testing)      │
│ - CachedRepository (for performance)│
└─────────────────────────────────────┘
```

## Method Call Chain Example: Getting Recruitment List

```
Browser
  ↓
GET /recruitment?page=1
  ↓
RecruitmentController::index()
  │
  ├─ $page = $_GET['page'] ?? 1  // = 1
  ├─ $limit = 10
  │
  └─ $this->recruitmentService->getRecruitmentsList(1, 10)
     │
     RecruitmentService::getRecruitmentsList(1, 10)
     │
     ├─ $offset = (1 - 1) * 10  // = 0
     │
     ├─ $this->recruitmentRepository->getActiveRecruitmentsPaginated(10, 0)
     │  │
     │  RecruitmentRepository::getActiveRecruitmentsPaginated(10, 0)
     │  │
     │  ├─ $sql = "SELECT * FROM recruitment_title WHERE status = 1
     │  │          AND deadline >= CURDATE()
     │  │          ORDER BY created_at DESC
     │  │          LIMIT :limit OFFSET :offset"
     │  │
     │  ├─ $stmt = $this->connection->prepare($sql)  // PDO Statement
     │  │
     │  ├─ $stmt->bindValue(':limit', 10, PDO::PARAM_INT)
     │  ├─ $stmt->bindValue(':offset', 0, PDO::PARAM_INT)
     │  │
     │  ├─ $stmt->execute()  // Sends to MySQL
     │  │
     │  └─ return $stmt->fetchAll(PDO::FETCH_ASSOC)
     │     │
     │     ↓ MySQL
     │     [
     │       ['id' => 1, 'title' => 'Job 1', ...],
     │       ['id' => 2, 'title' => 'Job 2', ...],
     │       ...
     │     ]
     │
     ├─ $total = $this->recruitmentRepository->countActive()
     │  │
     │  └─ SELECT COUNT(*) as total... → MySQL → 25
     │
     ├─ $totalPages = ceil(25 / 10)  // = 3
     │
     └─ return [
          'recruitments' => [...],
          'currentPage' => 1,
          'totalPages' => 3,
          'total' => 25,
          'limit' => 10
        ]
     │
     ↓ Returns to Controller
     │
     ├─ $listData = [...]
     ├─ $recruitments = $listData['recruitments']
     ├─ $totalPages = $listData['totalPages']
     │
     └─ $this->view('recruitment/recruitment', $data)
        │
        ↓ Renders View
        │
        app/views/Recruitment/Recruitment.php
        │
        ├─ extract($data)  // Makes variables available
        ├─ $recruitments, $totalPages, $currentPage, etc.
        │
        ├─ foreach ($recruitments as $rec) {
        │   echo $rec['title'];
        │   echo $rec['position'];
        │   // ... render HTML
        │ }
        │
        ├─ Pagination links
        │
        └─ HTML output
           │
           ↓ Browser
           │
           Display page with 10 job listings and pagination
```

## File Upload Flow Example

```
Form Submit (POST)
├─ recruitment_id = 1
├─ ten = "John Doe"
├─ dt = "0909123456"
├─ email = "john@example.com"
├─ noidung = "I'm interested"
└─ filechon = <file object>
  │
  ↓
RecruitmentController::apply()
  │
  ├─ $recruitmentId = 1
  ├─ $slug = "job-title-slug"
  ├─ $ipAddress = "192.168.1.1"
  │
  └─ $result = $this->applicationService->processApplication(
       1,
       $_POST,    // Contains: ten, dt, email, noidung
       $_FILES,   // Contains: filechon
       "192.168.1.1"
     )
     │
     ApplicationService::processApplication()
     │
     ├─ Validate with ApplicationValidator
     │  │
     │  ├─ $validator->validate($_POST, $_FILES)
     │  │  ├─ validateFullname("John Doe")  → true
     │  │  ├─ validatePhone("0909123456")   → null (valid)
     │  │  ├─ validateEmail("john@example.com")  → null (valid)
     │  │  └─ validateCV($_FILES['filechon'])   → null (valid)
     │  │
     │  └─ $errors = []  // No errors
     │
     ├─ Check duplicate
     │  │
     │  └─ $this->applicationRepository->hasApplied(1, "john@example.com", "192.168.1.1")
     │     │
     │     SELECT COUNT(*) FROM applications
     │     WHERE recruitment_id = 1
     │     AND (email = "john@example.com" OR ip = "192.168.1.1")
     │     │
     │     ↓ MySQL
     │     COUNT = 0 (not applied before)
     │
     ├─ Upload file
     │  │
     │  └─ $this->fileUploadService->uploadCV(file, "John Doe")
     │     │
     │     ├─ Validate file type (PDF/Word)
     │     ├─ Create upload dir if needed
     │     ├─ Generate safe filename
     │     │  └─ "1701234567_John_Doe.pdf"
     │     ├─ Move file to public/uploads/cv/
     │     │
     │     └─ return [
     │          'success' => true,
     │          'path' => 'uploads/cv/1701234567_John_Doe.pdf'
     │        ]
     │
     ├─ Prepare application data
     │  │
     │  └─ $data = [
     │       'recruitment_id' => 1,
     │       'fullname' => "John Doe",
     │       'phone' => "0909123456",
     │       'email' => "john@example.com",
     │       'content' => "I'm interested",
     │       'cv_file' => "uploads/cv/1701234567_John_Doe.pdf",
     │       'ip_address' => "192.168.1.1"
     │     ]
     │
     ├─ Save to database
     │  │
     │  └─ $this->applicationRepository->save($data)
     │     │
     │     INSERT INTO applications (...)
     │     VALUES (...) 
     │     │
     │     ↓ MySQL
     │     ✓ Inserted successfully
     │
     └─ return [
          'success' => true,
          'errors' => [],
          'message' => 'Cảm ơn bạn đã ứng tuyển...'
        ]
     │
     ↓ Returns to Controller
     │
     ├─ Check if success
     ├─ Set session['apply_success']
     ├─ Send notification email (optional)
     │
     └─ redirect('/recruitment/job-title-slug')
        │
        ↓
     Detail page loads
     │
     ├─ Check session['apply_success']
     ├─ Display success message
     ├─ Clear session
     │
     └─ Browser shows confirmation
```

## Component Interaction Summary

```
┌─────────────────────────────────────────────────────────────┐
│                    REQUEST COMES IN                         │
└────────────────────┬────────────────────────────────────────┘
                     ↓
         ┌───────────────────────────┐
         │ RecruitmentController     │
         │ (Thin Controller)         │
         │ - Handles HTTP request    │
         │ - Delegates to services   │
         │ - Returns response        │
         └────────┬──────────────────┘
                  ↓
      ┌────────────────────────────────────┐
      │ RecruitmentService                 │
      │ (Business Logic)                   │
      │ - Coordinates operations           │
      │ - Handles pagination               │
      │ - Manages view counts              │
      │ - Searches data                    │
      └────────┬───────────────────────────┘
               ↓
      ┌────────────────────────────────────┐
      │ RecruitmentRepository              │
      │ (Data Access)                      │
      │ - Builds queries                   │
      │ - Executes prepared statements     │
      │ - Returns results                  │
      └────────┬───────────────────────────┘
               ↓
      ┌────────────────────────────────────┐
      │ Model (Base Class)                 │
      │ - PDO connection management        │
      │ - SQL execution                    │
      │ - Transaction handling             │
      └────────┬───────────────────────────┘
               ↓
      ┌────────────────────────────────────┐
      │ PDO / MySQL Database               │
      │ - Stores and retrieves data        │
      └────────────────────────────────────┘
               ↓
    (Data flows back up the chain)
               ↓
      ┌────────────────────────────────────┐
      │ View (Presentation)                │
      │ - Renders HTML                     │
      │ - Displays data to user            │
      └────────────────────────────────────┘
               ↓
      ┌────────────────────────────────────┐
      │ Browser / Response                 │
      │ - User sees result                 │
      └────────────────────────────────────┘
```

This is the complete OOP structure for the recruitment module!

