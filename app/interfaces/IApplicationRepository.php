<?php
/**
 * IApplicationRepository Interface
 * Defines contract for application data access
 */

interface IApplicationRepository
{
    /**
     * Save application
     *
     * @param array $data
     * @return bool
     */
    public function save($data);

    /**
     * Check if already applied
     *
     * @param int $recruitmentId
     * @param string $email
     * @param string $ipAddress
     * @return bool
     */
    public function hasApplied($recruitmentId, $email, $ipAddress);

    /**
     * Get applications by recruitment ID
     *
     * @param int $recruitmentId
     * @return array
     */
    public function getByRecruitmentId($recruitmentId);
}

