-- =====================================================
-- 26. Views
-- =====================================================

-- View vw_contact_summary
CREATE OR REPLACE VIEW vw_contact_summary AS
SELECT c.id,
       c.customer_name,
       c.phone,
       c.email,
       SUBSTRING(c.content, 1, 100)  as content_preview,
       c.contact_type,
       cat.name                      as category_name,
       c.status,
       c.priority,
       c.created_at,
       c.response_at,
       a.username                    as assigned_to,
       DATEDIFF(NOW(), c.created_at) as days_old,
       COUNT(DISTINCT att.id)        as attachment_count,
       COUNT(DISTINCT h.id)          as history_count
FROM contacts c
         LEFT JOIN contact_categories cat ON c.category_id = cat.id
         LEFT JOIN authors a ON c.assigned_to = a.id
         LEFT JOIN contact_attachments att ON c.id = att.contact_id
         LEFT JOIN contact_histories h ON c.id = h.contact_id
GROUP BY c.id
ORDER BY c.created_at DESC;

-- View vw_news_links
CREATE OR REPLACE VIEW vw_news_links AS
SELECT n.id    as news_id,
       n.title as news_title,
       n.slug  as news_slug,
       l.id    as link_id,
       l.title as link_title,
       l.url   as link_url,
       l.link_type,
       l.target,
       nrl.relationship_type,
       nrl.link_order
FROM news n
         LEFT JOIN news_related_links nrl ON n.id = nrl.news_id
         LEFT JOIN links l ON (
    (nrl.related_type = 'link' AND nrl.related_id = l.id) OR
    (nrl.related_type = 'news' AND l.slug = CONCAT('news-', nrl.related_id)) OR
    (nrl.related_type = 'recruitment' AND l.slug = CONCAT('recruitment-', nrl.related_id))
    )
WHERE n.status = 'published'
  AND (l.id IS NULL OR l.is_active = 1)
ORDER BY n.id, nrl.link_order;

-- View vw_recruitment_links
CREATE OR REPLACE VIEW vw_recruitment_links AS
SELECT r.id    as recruitment_id,
       r.recruitment_title,
       r.slug  as recruitment_slug,
       l.id    as link_id,
       l.title as link_title,
       l.url   as link_url,
       l.link_type,
       l.target,
       rrl.relationship_type,
       rrl.link_order
FROM recruitments r
         LEFT JOIN recruitment_related_links rrl ON r.id = rrl.recruitment_id
         LEFT JOIN links l ON (
    (rrl.related_type = 'link' AND rrl.related_id = l.id) OR
    (rrl.related_type = 'news' AND l.slug = CONCAT('news-', rrl.related_id)) OR
    (rrl.related_type = 'recruitment' AND l.slug = CONCAT('recruitment-', rrl.related_id))
    )
WHERE r.status = 'open'
  AND (l.id IS NULL OR l.is_active = 1)
ORDER BY r.id, rrl.link_order;

-- =====================================================
-- 27. Các câu truy vấn mẫu (đã được comment)
-- =====================================================

-- Lấy tất cả tin tức đã xuất bản
-- SELECT n.*, c.name as category_name
-- FROM news n
-- LEFT JOIN categories c ON n.category_id = c.id
-- WHERE n.status = 'published'
-- ORDER BY n.publish_date DESC;

-- Lấy tin tuyển dụng đang mở
-- SELECT * FROM recruitments
-- WHERE status = 'open'
-- AND (deadline IS NULL OR deadline >= CURDATE())
-- ORDER BY created_at DESC;

-- Tìm kiếm tin tức
-- SELECT * FROM news
-- WHERE (title LIKE '%công nghệ%' OR content LIKE '%công nghệ%')
-- AND status = 'published';

-- Thống kê số lượng tin theo thể loại
-- SELECT c.name, COUNT(n.id) as news_count
-- FROM categories c
-- LEFT JOIN news n ON c.id = n.category_id
-- GROUP BY c.id;

-- Xem lịch sử đăng nhập
-- SELECT
--     ll.login_time,
--     a.username,
--     a.full_name,
--     ll.ip_address,
--     CASE WHEN ll.success THEN 'Thành công' ELSE 'Thất bại' END as status,
--     ll.failure_reason
-- FROM login_logs ll
-- LEFT JOIN authors a ON ll.author_id = a.id
-- ORDER BY ll.login_time DESC
-- LIMIT 50;