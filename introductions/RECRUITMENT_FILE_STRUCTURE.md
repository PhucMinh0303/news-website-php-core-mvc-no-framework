# Recruitment Module - File Structure and Dependencies

## Complete File Directory

```
D:\xampp\htdocs\capitalam2-mvc\
│
├── app/
│   │
│   ├── interfaces/  ← NEW FOLDER
│   │   ├── IRecruitmentRepository.php
│   │   └── IApplicationRepository.php
│   │
│   ├── repositories/  ← NEW FOLDER
│   │   ├── RecruitmentRepository.php
│   │   │   ├── Extends: Model
│   │   │   └── Implements: IRecruitmentRepository
│   │   └── ApplicationRepository.php
│   │       ├── Extends: Model
│   │       └── Implements: IApplicationRepository
│   │
│   ├── services/  ← NEW FOLDER
│   │   ├── RecruitmentService.php
│   │   │   └── Depends on: RecruitmentRepository
│   │   ├── ApplicationService.php
│   │   │   ├── Depends on: ApplicationRepository
│   │   │   ├── Depends on: ApplicationValidator
│   │   │   └── Depends on: FileUploadService
│   │   ├── ApplicationValidator.php
│   │   │   └── No external dependencies
│   │   └── FileUploadService.php
│   │       └── No external dependencies
│   │
│   ├── controllers/
│   │   └── RecruitmentController.php  ← MODIFIED
│   │       ├── Depends on: RecruitmentService
│   │       └── Depends on: ApplicationService
│   │
│   ├── core/
│   │   ├── Model.php  ← Base class (unchanged)
│   │   │   └── Uses: Database class
│   │   ├── Controller.php  ← Base class (unchanged)
│   │   └── Database.php  ← Database configuration (unchanged)
│   │
│   ├── models/  ← DEPRECATED (old models)
│   │   ├── RecruitmentModel.php
│   │   ├── RecruitmentTitleModel.php
│   │   ├── ApplicationModel.php
│   │   └── ... other models
│   │
│   └── views/
│       └── Recruitment/
│           ├── Recruitment.php
│           └── Recruitment-title.php
│
└── introductions/
    ├── RECRUITMENT_OOP_STRUCTURE.md
    ├── RECRUITMENT_IMPLEMENTATION_GUIDE.md
    ├── RECRUITMENT_ARCHITECTURE_DIAGRAM.md
    └── RECRUITMENT_REFACTORING_SUMMARY.md
```

## Dependency Graph

```
┌─────────────────────────────────────────────────────────┐
│          RecruitmentController                          │
│          (app/controllers/)                             │
└──────────┬────────────────────────────┬─────────────────┘
           │                            │
           │ depends on                 │ depends on
           ↓                            ↓
     ┌──────────────────────┐    ┌──────────────────────┐
     │ RecruitmentService   │    │ ApplicationService   │
     │ (app/services/)      │    │ (app/services/)      │
     └──────┬───────────────┘    └──────┬───────────────┘
            │                           │
            │ depends on                ├─ depends on ──────┐
            ↓                           ↓                   │
    ┌────────────────────┐  ┌──────────────────────┐      │
    │ RecruitmentRepo    │  │ ApplicationRepo      │      │
    │ (app/repositories/)│  │ (app/repositories/)  │      │
    └─────┬──────────────┘  └──────┬───────────────┘      │
          │                        │                       │
          │ implements            │ implements            │
          ↓                        ↓                       │
    ┌────────────────────┐  ┌──────────────────────┐      │
    │IRecruitmentRepo    │  │IApplicationRepo      │      │
    │(app/interfaces/)   │  │(app/interfaces/)     │      │
    └────────────────────┘  └──────────────────────┘      │
                                                           │
          ┌────────────────────────────────────────────────┘
          │
          ├─ depends on ──────────────────┐
          │                               │
          ↓                               ↓
    ┌─────────────────────────┐   ┌──────────────────────┐
    │ ApplicationValidator    │   │ FileUploadService    │
    │ (app/services/)         │   │ (app/services/)      │
    └─────────────────────────┘   └──────────────────────┘
    
Both repositories ↓

    ┌───────────────────────┐
    │ Model                 │
    │ (app/core/)           │
    │ - Base class          │
    │ - PDO methods         │
    │ - CRUD operations     │
    └───────┬───────────────┘
            │
            ↓
    ┌───────────────────────┐
    │ Database              │
    │ (app/config/)         │
    │ - PDO Configuration   │
    │ - Connection creation │
    └───────┬───────────────┘
            │
            ↓
    ┌───────────────────────┐
    │ MySQL Database        │
    │ - recruitment_title   │
    │ - applications        │
    └───────────────────────┘
```

## File Import Dependencies

### RecruitmentController.php
```php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../services/RecruitmentService.php';
require_once __DIR__ . '/../services/ApplicationService.php';
require_once __DIR__ . '/../repositories/RecruitmentRepository.php';
require_once __DIR__ . '/../repositories/ApplicationRepository.php';
```

### RecruitmentService.php
```php
require_once __DIR__ . '/../repositories/RecruitmentRepository.php';
```

### ApplicationService.php
```php
require_once __DIR__ . '/../repositories/ApplicationRepository.php';
require_once __DIR__ . '/ApplicationValidator.php';
require_once __DIR__ . '/FileUploadService.php';
```

### RecruitmentRepository.php
```php
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../interfaces/IRecruitmentRepository.php';
```

### ApplicationRepository.php
```php
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../interfaces/IApplicationRepository.php';
```

## Data Type Flow

```
HTTP Request
    ↓
    ├─ GET /recruitment
    │  └─ int $page, int $limit
    │     ↓
    │     RecruitmentService::getRecruitmentsList($page, $limit)
    │     ↓
    │     return [
    │       'recruitments' => array[Recruitment],
    │       'currentPage' => int,
    │       'totalPages' => int,
    │       'total' => int
    │     ]
    │
    └─ POST /recruitment/apply
       ├─ int $recruitmentId
       ├─ array $_POST
       ├─ array $_FILES
       └─ string $ipAddress
          ↓
          ApplicationService::processApplication(...)
          ↓
          return [
            'success' => bool,
            'errors' => array,
            'message' => string
          ]
```

## Method Signatures

### RecruitmentRepository
```php
public function getActiveRecruitmentsPaginated(int $limit, int $offset): array
public function getActiveRecruitments(?int $limit): array
public function getDetail(string $slug): ?array
public function getFeaturedRecruitments(int $limit): array
public function getByPosition(string $position, int $limit): array
public function search(string $keyword, ?int $limit, ?int $offset): array
public function countActive(): int
public function incrementViews(int $id): bool
public function getAllPositions(): array
```

### ApplicationRepository
```php
public function save(array $data): bool
public function hasApplied(int $recruitmentId, string $email, string $ipAddress): bool
public function getByRecruitmentId(int $recruitmentId): array
```

### RecruitmentService
```php
public function getRecruitmentsList(int $page, int $limit): array
public function getRecruitmentDetail(string $slug): ?array
public function getRelatedRecruitments(string $position, int $recruitmentId, int $limit): array
public function getFeaturedRecruitments(int $limit): array
public function getAllPositions(): array
public function searchRecruitments(string $keyword, string $position, int $limit): array
```

### ApplicationService
```php
public function processApplication(int $recruitmentId, array $postData, array $files, string $ipAddress): array
public function getApplicationsByRecruitmentId(int $recruitmentId): array
public function hasApplied(int $recruitmentId, string $email, string $ipAddress): bool
```

### ApplicationValidator
```php
public function validate(array $data, array $files): array
public function validateFullname(string $fullname): bool
public function validatePhone(string $phone): ?string
public function validateEmail(string $email): ?string
public function validateCV(?array $file): ?string
```

### FileUploadService
```php
public function uploadCV(array $file, string $fullname): array
public function deleteFile(string $filePath): bool
```

## Database Tables

### recruitment_title
```
Column Name      | Type         | Key | Constraints
─────────────────┼──────────────┼─────┼─────────────
id               | INT          | PK  | AUTO_INCREMENT
title            | VARCHAR(255) |     | 
slug             | VARCHAR(255) | UQ  |
position         | VARCHAR(100) |     |
work_location    | VARCHAR(100) |     |
description      | LONGTEXT     |     |
status           | TINYINT      |     | DEFAULT 1
featured         | TINYINT      |     | DEFAULT 0
deadline         | DATE         |     |
views            | INT          |     | DEFAULT 0
created_at       | TIMESTAMP    |     | DEFAULT CURRENT_TIMESTAMP
updated_at       | TIMESTAMP    |     |
```

### applications
```
Column Name      | Type         | Key | Constraints
─────────────────┼──────────────┼─────┼─────────────
id               | INT          | PK  | AUTO_INCREMENT
recruitment_id   | INT          | FK  | NOT NULL
fullname         | VARCHAR(100) |     |
phone            | VARCHAR(20)  |     |
email            | VARCHAR(100) |     |
content          | TEXT         |     |
cv_file          | VARCHAR(255) |     |
ip_address       | VARCHAR(45)  |     |
created_at       | TIMESTAMP    |     | DEFAULT CURRENT_TIMESTAMP
```

## Code Statistics

### New Code Created
| Component | File | Lines | Purpose |
|-----------|------|-------|---------|
| Interface | IRecruitmentRepository.php | 63 | Define contract |
| Interface | IApplicationRepository.php | 33 | Define contract |
| Repository | RecruitmentRepository.php | 182 | Data access |
| Repository | ApplicationRepository.php | 56 | Data access |
| Service | RecruitmentService.php | 110 | Business logic |
| Service | ApplicationService.php | 108 | Application logic |
| Service | ApplicationValidator.php | 105 | Validation |
| Service | FileUploadService.php | 68 | File handling |
| **Total** | **8 files** | **727 lines** | **New OOP structure** |

### Updated Files
| File | Changes | Purpose |
|------|---------|---------|
| RecruitmentController.php | Complete refactor | Thin controller pattern |

### Documentation Created
| File | Lines | Purpose |
|------|-------|---------|
| RECRUITMENT_OOP_STRUCTURE.md | 430 | Detailed architecture |
| RECRUITMENT_IMPLEMENTATION_GUIDE.md | 520 | Quick reference |
| RECRUITMENT_ARCHITECTURE_DIAGRAM.md | 380 | Visual diagrams |
| RECRUITMENT_REFACTORING_SUMMARY.md | 320 | Executive summary |
| **Total Documentation** | **1650 lines** | **Comprehensive guides** |

## Import Order (Important)

When loading files, follow this order to avoid dependency issues:

```
1. Load base classes (Model, Controller)
2. Load interfaces
3. Load services (they depend on repos and validators)
4. Load repositories (they depend on Model and interfaces)
5. Load controller (depends on services)
6. Load views (depend on controller data)
```

## Backward Compatibility

Old model classes can coexist:
- `RecruitmentModel.php` - Now deprecated, use RecruitmentRepository
- `RecruitmentTitleModel.php` - Now deprecated, use RecruitmentRepository
- `ApplicationModel.php` - Now deprecated, use ApplicationRepository

They can be removed after verification that all code uses new structure.

## Performance Considerations

### Database Queries
- Prepared statements prevent SQL injection
- Pagination prevents loading all records
- Indexed columns: slug, status, deadline

### File Operations
- Files stored outside web root recommended
- Upload directory: `/public/uploads/cv/`
- Max file size: 5MB (configurable)

### Caching Opportunities
- Cache featured recruitments (rarely changes)
- Cache position list (updated daily)
- Consider Redis for high traffic

## Security Layers

```
Input → Validation → Sanitization → Database
                ↓
          (All layers have security)
                
1. Validation Layer
   - Check format (email, phone, etc)
   - File type validation
   - File size validation

2. Sanitization Layer
   - Prepared statements prevent SQL injection
   - Filename sanitization
   - Input trimming

3. Database Layer
   - Parameterized queries
   - Foreign key constraints
   - Transaction support
```

## Testing Structure

```
Tests/
├── Unit/
│   ├── RecruitmentRepositoryTest.php
│   ├── ApplicationRepositoryTest.php
│   ├── RecruitmentServiceTest.php
│   ├── ApplicationServiceTest.php
│   ├── ApplicationValidatorTest.php
│   └── FileUploadServiceTest.php
├── Integration/
│   ├── RecruitmentControllerTest.php
│   └── ApplicationFlowTest.php
└── Features/
    ├── GetRecruitmentListTest.php
    ├── ApplyForPositionTest.php
    └── SearchRecruitmentTest.php
```

## How to Extend

### Add New Repository Method

1. **Add to interface**
```php
// IRecruitmentRepository.php
public function getExpiringSoon($days);
```

2. **Implement in repository**
```php
// RecruitmentRepository.php
public function getExpiringSoon($days) {
    // Implementation
}
```

3. **Add to service**
```php
// RecruitmentService.php
public function getExpiringSoon($days) {
    return $this->recruitmentRepository->getExpiringSoon($days);
}
```

4. **Use in controller**
```php
// RecruitmentController.php
$expiring = $this->recruitmentService->getExpiringSoon(7);
```

## Troubleshooting

### Common Issues

**Issue:** "Call to undefined method"
- **Solution:** Check if method exists in interface and implementation

**Issue:** "Class not found"
- **Solution:** Verify require_once statements in file

**Issue:** "Database connection failed"
- **Solution:** Check credentials in config/Database.php

**Issue:** "File upload failed"
- **Solution:** Check directory permissions (should be 755 or 777)

**Issue:** "Validation errors not showing"
- **Solution:** Verify session is started in controller constructor

## Next Steps

1. ✓ Review the OOP structure (DONE)
2. ✓ Understand data flow (DOCUMENTATION PROVIDED)
3. → Test all recruitment functionality
4. → Remove old model files (OPTIONAL)
5. → Add unit tests (RECOMMENDED)
6. → Implement caching (PERFORMANCE)
7. → Add more features using new structure

## Support Documentation

All documentation files are in: `introductions/`

- Start with: `RECRUITMENT_REFACTORING_SUMMARY.md` (this gives overview)
- For details: `RECRUITMENT_OOP_STRUCTURE.md` (complete guide)
- For coding: `RECRUITMENT_IMPLEMENTATION_GUIDE.md` (examples)
- For visuals: `RECRUITMENT_ARCHITECTURE_DIAGRAM.md` (diagrams)

**The OOP refactoring is complete and ready for use!**

