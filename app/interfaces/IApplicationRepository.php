<?php
// app/interfaces/IRecruitmentRepository.php

interface IRecruitmentRepository
{
    public function getActiveRecruitmentsPaginated($limit, $offset);
    public function getDetail($slug);
    public function incrementViews($id);
    public function countActive();
    public function getByPosition($position, $limit);
    public function getFeaturedRecruitments($limit);
    public function getAllPositions();
    public function search($keyword, $limit, $offset);
}