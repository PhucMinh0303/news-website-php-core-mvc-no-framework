<?php

/**
 * Admin Contact Controller - Simplified version
 * No authentication, no API - just view contacts
 */

class AdminContactController extends Controller
{

    private $contactModel;

    public function __construct()
    {
        $this->contactModel = new ContactModel();
    }

    /**
     * Hiển thị trang quản lý liên hệ (không cần login)
     */
    public function index()
    {
        $this->setPageTitle('Quản lý liên hệ');

        // Lấy tất cả danh sách liên hệ
        $contacts = $this->contactModel->getAllContacts();
        $stats = $this->contactModel->getSimpleStats();

        // Chuẩn bị dữ liệu cho view
        $data = [
            'contacts' => $contacts,
            'stats' => $stats,
            'pageTitle' => 'Quản lý liên hệ',
            'admin_page' => 'contact',
            'admin_view' => 'admin/main/contact_admin',
        ];

        $this->render('admin/admin', $data);
    }

    /**
     * Xem chi tiết một liên hệ
     */
    public function detail($id)
    {
        $id = (int) $id;
        if (!$id) {
            $this->redirect('/admin/contact');
        }

        $contact = $this->contactModel->getContactById($id);
        if (!$contact) {
            $_SESSION['error'] = 'Không tìm thấy liên hệ';
            $this->redirect('/admin/contact');
        }

        // Đánh dấu đã đọc
        if ($contact['status'] === 'new') {
            $this->contactModel->updateStatus($id, 'read');
            $contact['status'] = 'read';
        }

        $notes = $this->contactModel->getNotes($id);

        $data = [
            'contact' => $contact,
            'notes' => $notes,
            'pageTitle' => 'Chi tiết liên hệ'
        ];

        $this->render('admin/main/contact_admin', $data);
    }

    /**
     * Cập nhật trạng thái (submit form)
     */
    public function update()
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/contact');
        }

        $id = (int) $this->post('id', 0);
        $status = $this->post('status', '');
        $response = $this->post('response', '');
        $allowedStatuses = ['new', 'read', 'replied', 'processing', 'resolved', 'spam', 'archived'];

        if (!$id || !in_array($status, $allowedStatuses, true)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ'], 422);
            }

            $_SESSION['error'] = 'Dữ liệu không hợp lệ';
            $this->redirect('/admin/contact');
        }

        // Update status
        $result = $this->contactModel->updateContactStatus($id, $status, $response);

        if ($this->isAjax()) {
            $success = $result !== false;
            $this->json(
                ['success' => $success, 'status' => $status],
                $success ? 200 : 500
            );
        }

        if ($result) {
            $_SESSION['success'] = 'Cập nhật trạng thái thành công';
        } else {
            $_SESSION['error'] = 'Cập nhật thất bại';
        }

        $this->redirect('/admin/contact/view/' . $id);
    }

    /**
     * Xóa liên hệ (chuyển vào thùng rác)
     */
    public function delete($id)
    {
        $id = (int) $id;
        if (!$id) {
            $this->redirect('/admin/contact');
        }

        $result = $this->contactModel->moveToTrash($id);

        if ($result) {
            $_SESSION['success'] = 'Đã chuyển vào thùng rác';
        } else {
            $_SESSION['error'] = 'Xóa thất bại';
        }

        $this->redirect('/admin/contact');
    }

    /**
     * Khôi phục từ thùng rác
     */
    public function restore($id)
    {
        $id = (int) $id;
        if (!$id) {
            $this->redirect('/admin/contact');
        }

        $result = $this->contactModel->restoreFromTrash($id);

        if ($result) {
            $_SESSION['success'] = 'Đã khôi phục thành công';
        } else {
            $_SESSION['error'] = 'Khôi phục thất bại';
        }

        $this->redirect('/admin/contact');
    }

    /**
     * Xóa vĩnh viễn
     */
    public function forceDelete($id)
    {
        $id = (int) $id;
        if (!$id) {
            $this->redirect('/admin/contact');
        }

        $result = $this->contactModel->forceDelete($id);

        if ($result) {
            $_SESSION['success'] = 'Đã xóa vĩnh viễn';
        } else {
            $_SESSION['error'] = 'Xóa thất bại';
        }

        $this->redirect('/admin/contact');
    }

    /**
     * Thêm ghi chú (submit form)
     */
    public function addNote()
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/contact');
        }

        $contactId = (int) $this->post('contact_id', 0);
        $note = trim($this->post('note', ''));

        if (!$contactId || empty($note)) {
            $_SESSION['error'] = 'Thiếu thông tin ghi chú';
            $this->redirect('/admin/contact/view/' . $contactId);
        }

        $result = $this->contactModel->addNote($contactId, $note);

        if ($result) {
            $_SESSION['success'] = 'Đã thêm ghi chú';
        } else {
            $_SESSION['error'] = 'Thêm ghi chú thất bại';
        }

        $this->redirect('/admin/contact/view/' . $contactId);
    }
}
