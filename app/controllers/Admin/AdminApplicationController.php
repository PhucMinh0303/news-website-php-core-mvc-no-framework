<?php

/**
 * Admin Application Controller - cập nhật trạng thái đơn ứng tuyển (AJAX)
 */
class AdminApplicationController extends Controller
{
    private $applicationModel;

    public function __construct()
    {
        require_once __DIR__ . '/../../models/ApplicationModel.php';
        $this->applicationModel = new ApplicationModel();
    }

    public function update()
    {
        if (!$this->isPost()) {
            $this->redirect('/admin/application');
        }

        $id = (int) $this->post('id', 0);
        $status = $this->post('status', '');
        $allowedStatuses = ['pending', 'reviewed', 'interviewed', 'accepted', 'rejected', 'archived', 'deleted'];

        if (!$id || !in_array($status, $allowedStatuses, true)) {
            $this->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ'], 422);
        }

        $success = $this->applicationModel->updateStatus($id, $status);
        $this->json(
            ['success' => (bool) $success, 'status' => $status],
            $success ? 200 : 500
        );
    }
}