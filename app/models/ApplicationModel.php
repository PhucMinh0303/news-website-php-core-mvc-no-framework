<?php
// models/ApplicationModel.php

require_once '../core/Model.php';

class ApplicationModel extends Model
{
    protected $table = 'applications';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Lưu đơn ứng tuyển
     */
    public function save($data)
    {
        $sql = "INSERT INTO applications (recruitment_id, fullname, phone, email, content, cv_file, ip_address) 
                VALUES (:recruitment_id, :fullname, :phone, :email, :content, :cv_file, :ip_address)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Kiểm tra đã ứng tuyển chưa (tránh spam)
     */
    public function hasApplied($recruitmentId, $email, $ipAddress)
    {
        $sql = "SELECT COUNT(*) as count FROM applications 
                WHERE recruitment_id = :recruitment_id 
                AND (email = :email OR ip_address = :ip_address)
                AND DATE(applied_at) = CURDATE()";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'recruitment_id' => $recruitmentId,
            'email' => $email,
            'ip_address' => $ipAddress
        ]);

        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
}