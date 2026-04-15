<?php
/**
 * IRecruitmentRepository Interface
 * Defines contract for recruitment data access
 */

interface IRecruitmentRepository
{
    /**
     * Get active recruitments with pagination
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getActiveRecruitmentsPaginated($limit, $offset);

    /**
     * Get all active recruitments
     *
     * @param int $limit
     * @return array
     */
    public function getActiveRecruitments($limit);

    /**
     * Get recruitment detail by slug
     *
     * @param string $slug
     * @return array|null
     */
    public function getDetail($slug);

    /**
     * Get featured recruitments
     *
     * @param int $limit
     * @return array
     */
    public function getFeaturedRecruitments($limit);

    /**
     * Get recruitments by position
     *
     * @param string $position
     * @param int $limit
     * @return array
     */
    public function getByPosition($position, $limit);

    /**
     * Search recruitments
     *
     * @param string $keyword
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function search($keyword, $limit, $offset);

    /**
     * Count active recruitments
     *
     * @return int
     */
    public function countActive();

    /**
     * Increment view count
     *
     * @param int $id
     * @return bool
     */
    public function incrementViews($id);

    /**
     * Get all positions
     *
     * @return array
     */
    public function getAllPositions();
}

