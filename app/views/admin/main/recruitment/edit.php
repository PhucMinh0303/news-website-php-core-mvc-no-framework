<?php

/**
 * Edit recruitment view
 */
$job = $data['job'] ?? null;
$old_input = $_SESSION['old_input'] ?? [];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
unset($_SESSION['old_input']);

if (!$job) {
    header('Location: /admin/recruitment');
    exit;
}
?>

<main class="main">
    <div class="main-header">
        <h1>Edit Job: <?php echo htmlspecialchars($job['title']); ?></h1>
        <a href="?page=recruitment" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error" style="background: #fee2e2; color: #991b1b; padding: 12px; margin: 10px 0; border-radius: 6px;">
            <?php foreach ($errors as $err): ?>
                <div><?php echo htmlspecialchars($err); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="?page=update-recruitment&id=<?php echo $job['id']; ?>" enctype="multipart/form-data" class="recruitment-form">
        <div class="form-group">
            <label for="title">Job Title <span class="required">*</span></label>
            <input type="text" id="title" name="title" required
                value="<?php echo htmlspecialchars($old_input['title'] ?? $job['title']); ?>"
                placeholder="Enter job title">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="work_location">Work Location <span class="required">*</span></label>
                <input type="text" id="work_location" name="work_location" required
                    value="<?php echo htmlspecialchars($old_input['work_location'] ?? $job['work_location']); ?>"
                    placeholder="e.g., Ho Chi Minh City, Ha Noi">
            </div>

            <div class="form-group">
                <label for="degree">Degree Requirement</label>
                <select id="degree" name="degree">
                    <option value="Cao Đẳng - Đại Học" <?php echo (($old_input['degree'] ?? $job['degree']) == 'Cao Đẳng - Đại Học') ? 'selected' : ''; ?>>Cao Đẳng - Đại Học</option>
                    <option value="Đại Học trở lên" <?php echo (($old_input['degree'] ?? $job['degree']) == 'Đại Học trở lên') ? 'selected' : ''; ?>>Đại Học trở lên</option>
                    <option value="Cao Học" <?php echo (($old_input['degree'] ?? $job['degree']) == 'Cao Học') ? 'selected' : ''; ?>>Cao Học</option>
                    <option value="Trung Cấp" <?php echo (($old_input['degree'] ?? $job['degree']) == 'Trung Cấp') ? 'selected' : ''; ?>>Trung Cấp</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="quantity">Quantity <span class="required">*</span></label>
                <input type="number" id="quantity" name="quantity" required min="1"
                    value="<?php echo htmlspecialchars($old_input['quantity'] ?? $job['quantity']); ?>">
            </div>

            <div class="form-group">
                <label for="salary_range">Salary Range</label>
                <input type="text" id="salary_range" name="salary_range"
                    value="<?php echo htmlspecialchars($old_input['salary_range'] ?? $job['salary_range']); ?>"
                    placeholder="e.g., $1000 - $2000 or Negotiable">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="deadline">Application Deadline <span class="required">*</span></label>
                <input type="date" id="deadline" name="deadline" required
                    value="<?php echo htmlspecialchars($old_input['deadline'] ?? $job['deadline']); ?>"
                    min="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="1" <?php echo (($old_input['status'] ?? $job['status']) == 1) ? 'selected' : ''; ?>>Published (Open)</option>
                    <option value="0" <?php echo (($old_input['status'] ?? $job['status']) == 0) ? 'selected' : ''; ?>>Draft</option>
                    <option value="2" <?php echo (($old_input['status'] ?? $job['status']) == 2) ? 'selected' : ''; ?>>Closed</option>
                </select>
            </div>
        </div>

        <?php if ($job['image'] && $job['image'] != 'default-job.webp'): ?>
            <div class="form-group">
                <label>Current Image</label>
                <div class="current-image">
                    <img src="/uploads/recruitments/<?php echo htmlspecialchars($job['image']); ?>"
                        alt="Current job image" style="max-width: 200px; border-radius: 8px;">
                </div>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="image">Change Image (optional)</label>
            <input type="file" id="image" name="image" accept="image/*">
            <small class="form-text">Accepted formats: JPG, PNG, WEBP. Max size: 2MB</small>
        </div>

        <div class="form-group">
            <label for="description">Job Description</label>
            <textarea id="description" name="description" rows="5"
                placeholder="Describe the job responsibilities, daily tasks..."><?php echo htmlspecialchars($old_input['description'] ?? $job['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="requirements">Requirements</label>
            <textarea id="requirements" name="requirements" rows="5"
                placeholder="List the requirements: Education, Experience, Skills..."><?php echo htmlspecialchars($old_input['requirements'] ?? $job['requirements']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="benefits">Benefits</label>
            <textarea id="benefits" name="benefits" rows="4"
                placeholder="What benefits do you offer? Salary, insurance, vacation..."><?php echo htmlspecialchars($old_input['benefits'] ?? $job['benefits']); ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-save"></i> Update Job Post
            </button>
            <a href="?page=recruitment" class="btn-secondary">Cancel</a>
        </div>
    </form>
</main>

<style>
    .recruitment-form {
        background: white;
        padding: 24px;
        border-radius: 12px;
        margin-top: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #374151;
    }

    .required {
        color: #ef4444;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.2s;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-text {
        display: block;
        margin-top: 5px;
        font-size: 12px;
        color: #6b7280;
    }

    .current-image {
        margin-top: 8px;
    }

    .current-image img {
        border: 1px solid #e5e7eb;
        padding: 4px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }
</style>