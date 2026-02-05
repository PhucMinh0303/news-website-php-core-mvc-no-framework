<?php
/**
 * Job Model - Handles job listing data
 */

class Job extends Model {
    
    protected static $data = [
        [
            'id' => 1,
            'title' => 'Senior Developer',
            'position' => 'Senior',
            'department' => 'Technology',
            'location' => 'Ho Chi Minh City',
            'salary' => '20-30 Million VND',
            'description' => 'We are looking for experienced developers...',
        ],
        [
            'id' => 2,
            'title' => 'Project Manager',
            'position' => 'Manager',
            'department' => 'Management',
            'location' => 'Hanoi',
            'salary' => '15-25 Million VND',
            'description' => 'Lead our projects to success...',
        ],
        [
            'id' => 3,
            'title' => 'Financial Consultant',
            'position' => 'Consultant',
            'department' => 'Consulting',
            'location' => 'Ho Chi Minh City',
            'salary' => '18-28 Million VND',
            'description' => 'Expert financial consultation...',
        ],
    ];
    
    /**
     * Get jobs by department
     */
    public static function getByDepartment($department) {
        return static::getBy('department', $department);
    }
}

?>
