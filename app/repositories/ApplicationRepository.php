<?php
/**
 * ApplicationRepository
 * Implementation of IApplicationRepository for applications table
 */

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../interfaces/IApplicationRepository.php';

class ApplicationRepository extends Model implements IApplicationRepository
{
    protected $table = 'applications';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Save application
     */
    public function save($data)
    {
        $sql = "INSERT INTO {$this->table} (recruitment_id, fullname, phone, email, content, cv_file, ip_address, created_at) 
                VALUES (:recruitment_id, :fullname, :phone, :email, :content, :cv_file, :ip_address, NOW())";

        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([
            'recruitment_id' => $data['recruitment_id'],
            'fullname' => $data['fullname'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'content' => $data['content'] ?? null,
            'cv_file' => $data['cv_file'],
            'ip_address' => $data['ip_address']
        ]);
    }

    /**
     * Check if already applied
     */
    public function hasApplied($recruitmentId, $email, $ipAddress)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE recruitment_id = :recruitment_id 
                AND (email = :email OR ip_address = :ip_address)";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'recruitment_id' => $recruitmentId,
            'email' => $email,
            'ip_address' => $ipAddress
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['total'] ?? 0) > 0;
    }

    /**
     * Get applications by recruitment ID
     */
    public function getByRecruitmentId($recruitmentId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE recruitment_id = :recruitment_id ORDER BY created_at DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['recruitment_id' => $recruitmentId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

