<?php
/**
 * RecruitmentService
 * Business logic for recruitment operations
 */

require_once __DIR__ . '/../repositories/RecruitmentRepository.php';

class RecruitmentService
{
    private $recruitmentRepository;

    public function __construct(RecruitmentRepository $recruitmentRepository = null)
    {
        $this->recruitmentRepository = $recruitmentRepository ?? new RecruitmentRepository();
    }

    /**
     * Get recruitment list with pagination
     *
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getRecruitmentsList($page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;

        $recruitments = $this->recruitmentRepository->getActiveRecruitmentsPaginated($limit, $offset);
        $total = $this->recruitmentRepository->countActive();
        $totalPages = ceil($total / $limit);

        return [
            'recruitments' => $recruitments,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'limit' => $limit
        ];
    }

    /**
     * Get recruitment detail
     *
     * @param string $slug
     * @return array|null
     */
    public function getRecruitmentDetail($slug)
    {
        $recruitment = $this->recruitmentRepository->getDetail($slug);

        if ($recruitment) {
            // Increment view count
            $this->recruitmentRepository->incrementViews($recruitment['id']);
        }

        return $recruitment;
    }

    /**
     * Get related recruitments
     *
     * @param string $position
     * @param int $recruitmentId
     * @param int $limit
     * @return array
     */
    public function getRelatedRecruitments($position, $recruitmentId, $limit = 4)
    {
        $related = $this->recruitmentRepository->getByPosition($position, $limit);

        // Remove current recruitment
        return array_filter($related, function ($item) use ($recruitmentId) {
            return $item['id'] != $recruitmentId;
        });
    }

    /**
     * Get featured recruitments
     *
     * @param int $limit
     * @return array
     */
    public function getFeaturedRecruitments($limit = 5)
    {
        return $this->recruitmentRepository->getFeaturedRecruitments($limit);
    }

    /**
     * Get all positions
     *
     * @return array
     */
    public function getAllPositions()
    {
        return $this->recruitmentRepository->getAllPositions();
    }

    /**
     * Search recruitments
     *
     * @param string $keyword
     * @param string $position
     * @param int $limit
     * @return array
     */
    public function searchRecruitments($keyword = '', $position = '', $limit = 20)
    {
        if (!empty($keyword)) {
            return $this->recruitmentRepository->search($keyword, $limit, 0);
        } elseif (!empty($position)) {
            return $this->recruitmentRepository->getByPosition($position, $limit);
        } else {
            return $this->recruitmentRepository->getActiveRecruitments($limit);
        }
    }
}

