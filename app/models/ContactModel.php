<?php
// models/ContactModel.php
require_once __DIR__ . '/../core/Model.php';

class ContactModel extends Model
{
    protected $table = 'contacts';

    public function getAllContacts()
    {
        return $this->fetchAll(
            "SELECT * FROM contacts ORDER BY created_at DESC"
        );
    }

    public function getSimpleStats()
    {
        $rows = $this->fetchAll("SELECT status, COUNT(*) AS total FROM contacts GROUP BY status");
        $stats = ['total' => 0];
        foreach ($rows as $row) {
            $stats[$row['status']] = (int)$row['total'];
            $stats['total'] += (int)$row['total'];
        }
        return $stats;
    }

    public function getContactById($id)
    {
        return $this->findById((int)$id);
    }

    public function getNotes($contactId)
    {
        return $this->fetchAll(
            "SELECT * FROM contact_histories WHERE contact_id = ? AND action = 'note_added' ORDER BY created_at DESC",
            [(int)$contactId]
        );
    }

    public function moveToTrash($id)
    {
        return $this->update((int)$id, ['status' => 'spam']);
    }

    public function restoreFromTrash($id)
    {
        return $this->update((int)$id, ['status' => 'read']);
    }

    public function forceDelete($id)
    {
        return $this->delete((int)$id);
    }

    public function addNote($contactId, $note)
    {
        return $this->insertHistory((int)$contactId, $note);
    }

    private function insertHistory($contactId, $note)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO contact_histories (contact_id, action, note) VALUES (?, 'note_added', ?)"
        );
        return $stmt->execute([$contactId, $note]);
    }

    public function addContact($data)
    {
        return $this->insert([
            'customer_name' => $data['customer_name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'content' => $data['content'],
            'contact_type' => $data['contact_type'] ?? 'general',
            'category_id' => $data['category_id'] ?? null,
            'source' => $data['source'] ?? 'website',
            'ip_address' => $data['ip_address'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
            'page_url' => $data['page_url'] ?? null,
            'referrer_url' => $data['referrer_url'] ?? null,
        ]);
    }

    public function getPendingContacts()
    {
        $sql = "SELECT c.*, cat.name as category_name, a.username as assigned_to_name
                FROM contacts c
                LEFT JOIN contact_categories cat ON c.category_id = cat.id
                LEFT JOIN authors a ON c.assigned_to = a.id
                WHERE c.status IN ('new', 'read', 'processing')
                ORDER BY FIELD(c.priority, 'urgent', 'high', 'medium', 'low'), c.created_at";

        return $this->fetchAll($sql);
    }

    public function updateContactStatus($id, $status, $responseContent = null, $responseBy = null)
    {
        $data = ['status' => $status];

        if ($responseContent) {
            $data['response_content'] = $responseContent;
            $data['response_at'] = date('Y-m-d H:i:s');
            $data['response_by'] = $responseBy;
        }

        return $this->update((int)$id, $data);
    }

    public function updateStatus($id, $status)
    {
        return $this->updateContactStatus($id, $status);
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

        return $this->fetchAll($sql);
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

        return $this->fetchAll($sql, [$days]);
    }

    public function getContactHistory($contactId)
    {
        $sql = "SELECT * FROM contact_histories 
                WHERE contact_id = ? 
                ORDER BY created_at DESC";

        return $this->fetchAll($sql, [$contactId]);
    }
}
