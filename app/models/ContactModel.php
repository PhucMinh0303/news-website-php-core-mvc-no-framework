<?php
// models/ContactModel.php
require_once __DIR__ . '/../core/Model.php';

class ContactModel extends Model
{
    protected $table = 'contacts';

    public function addContact($data)
    {
        // Use the stored procedure
        $sql = "CALL AddNewContact(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $data['customer_name'],
            $data['phone'],
            $data['email'] ?? null,
            $data['content'],
            $data['contact_type'] ?? 'general',
            $data['category_id'] ?? null,
            $data['source'] ?? 'website',
            $data['ip_address'] ?? null,
            $data['user_agent'] ?? null,
            $data['page_url'] ?? null,
            $data['referrer_url'] ?? null
        ];

        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetch();
        return $result['contact_id'] ?? null;
    }

    public function getPendingContacts()
    {
        $sql = "SELECT c.*, cat.name as category_name, a.username as assigned_to_name
                FROM contacts c
                LEFT JOIN contact_categories cat ON c.category_id = cat.id
                LEFT JOIN authors a ON c.assigned_to = a.id
                WHERE c.status IN ('new', 'read', 'processing')
                ORDER BY FIELD(c.priority, 'urgent', 'high', 'medium', 'low'), c.created_at";

        return $this->db->fetchAll($sql);
    }

    public function updateContactStatus($id, $status, $responseContent = null, $responseBy = null)
    {
        $data = ['status' => $status];

        if ($responseContent) {
            $data['response_content'] = $responseContent;
            $data['response_at'] = date('Y-m-d H:i:s');
            $data['response_by'] = $responseBy;
        }

        return $this->updateById($id, $data);
    }

    public function getContactStats()
    {
        $sql = "SELECT 
                    status,
                    COUNT(*) as count,
                    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM contacts), 2) as percentage
                FROM contacts
                GROUP BY status
                ORDER BY FIELD(status, 'new', 'processing', 'read', 'replied', 'resolved')";

        return $this->db->fetchAll($sql);
    }

    public function getDailyStats($days = 30)
    {
        $sql = "SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as total_contacts,
                    SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_contacts,
                    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_contacts
                FROM contacts
                WHERE created_at >= CURDATE() - INTERVAL ? DAY
                GROUP BY DATE(created_at)
                ORDER BY date DESC";

        return $this->db->fetchAll($sql, [$days]);
    }

    public function getContactHistory($contactId)
    {
        $sql = "SELECT * FROM contact_histories 
                WHERE contact_id = ? 
                ORDER BY created_at DESC";

        return $this->db->fetchAll($sql, [$contactId]);
    }
}