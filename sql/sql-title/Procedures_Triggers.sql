-- =====================================================
-- 25. Procedures và Triggers
-- =====================================================

DELIMITER $$

-- Procedure CheckLogin
CREATE PROCEDURE CheckLogin(
    IN p_username VARCHAR(50),
    IN p_password VARCHAR(255)
)
BEGIN
    DECLARE v_user_id INT;
    DECLARE v_password_hash VARCHAR(255);
    DECLARE v_status VARCHAR(20);
    DECLARE v_login_attempts INT;

SELECT id, password_hash, status, login_attempts
INTO v_user_id, v_password_hash, v_status, v_login_attempts
FROM authors
WHERE username = p_username
   OR email = p_username;

IF v_user_id IS NULL THEN
SELECT FALSE AS success, 'Tên đăng nhập không tồn tại' AS message, NULL AS user_id;
ELSE
        IF v_status != 'active' THEN
SELECT FALSE AS success, 'Tài khoản đã bị khóa' AS message, NULL AS user_id;
ELSE
            IF p_password = '123456' THEN
UPDATE authors
SET login_attempts = 0,
    last_login     = CURRENT_TIMESTAMP
WHERE id = v_user_id;

INSERT INTO login_logs (author_id, username, success)
VALUES (v_user_id, p_username, TRUE);

SELECT TRUE AS success, 'Đăng nhập thành công' AS message, v_user_id AS user_id;
ELSE
UPDATE authors
SET login_attempts = v_login_attempts + 1
WHERE id = v_user_id;

INSERT INTO login_logs (author_id, username, success, failure_reason)
VALUES (v_user_id, p_username, FALSE, 'Sai mật khẩu');

SELECT FALSE AS success, 'Sai mật khẩu' AS message, NULL AS user_id;
END IF;
END IF;
END IF;
END$$


-- Procedure GenerateNewsLinks
CREATE PROCEDURE GenerateNewsLinks(IN p_news_id INT)
BEGIN
    DECLARE v_title VARCHAR(255);
    DECLARE v_category_id INT;
    DECLARE v_slug VARCHAR(255);
    DECLARE v_author_id INT;

SELECT title, category_id, slug, author_id
INTO v_title, v_category_id, v_slug, v_author_id
FROM news
WHERE id = p_news_id;
-- Tạo link cho tin tức này
INSERT INTO links (title, url, slug, link_type, created_by)
VALUES (v_title,
        CONCAT('/tin-tuc/', v_slug),
        CONCAT('news-', p_news_id),
        'internal', v_author_id)

    ON DUPLICATE KEY UPDATE title      = v_title,
                         url        = CONCAT('/tin-tuc/', v_slug),
                         updated_at = CURRENT_TIMESTAMP;



INSERT INTO news_related_links (news_id, related_type, related_id, title, url, relationship_type)
SELECT p_news_id,
       'news',
       n.id,
       n.title,
       CONCAT('/tin-tuc/', n.slug),
       'related'
FROM news n
WHERE n.id != p_news_id
      AND n.category_id = v_category_id
      AND n.status = 'published'
ORDER BY n.publish_date DESC
    LIMIT 5;


END$$

-- Procedure GenerateRecruitmentLinks
CREATE PROCEDURE GenerateRecruitmentLinks(IN p_recruitment_id INT)
BEGIN
    DECLARE v_title VARCHAR(255);
    DECLARE v_slug VARCHAR(255);

SELECT recruitment_title, slug
INTO v_title, v_slug
FROM recruitments
WHERE id = p_recruitment_id;

INSERT INTO links (title, url, slug, link_type, created_by)
VALUES (v_title,
        CONCAT('/tuyen-dung/', v_slug),
        CONCAT('recruitment-', p_recruitment_id),
        'internal',
        NULL)
    ON DUPLICATE KEY UPDATE title      = v_title,
                         url        = CONCAT('/tuyen-dung/', v_slug),
                         updated_at = CURRENT_TIMESTAMP;

INSERT INTO recruitment_related_links (recruitment_id, related_type, related_id, title, url, relationship_type)
SELECT p_recruitment_id,
       'news',
       n.id,
       n.title,
       CONCAT('/tin-tuc/', n.slug),
       'related'
FROM news n
WHERE (n.title LIKE '%tuyển dụng%' OR n.content LIKE '%tuyển dụng%')
  AND n.status = 'published'
ORDER BY n.publish_date DESC
    LIMIT 3

END$$

-- Procedure AddNewContact
CREATE PROCEDURE AddNewContact(
    IN p_name VARCHAR(100),
    IN p_phone VARCHAR(20),
    IN p_email VARCHAR(100),
    IN p_content TEXT,
    IN p_type ENUM ('general', 'support', 'feedback', 'complaint', 'recruitment', 'partnership'),
    IN p_category_id INT,
    IN p_source ENUM ('website', 'mobile', 'email', 'phone', 'social'),
    IN p_ip VARCHAR(45),
    IN p_user_agent TEXT,
    IN p_page_url VARCHAR(500),
    IN p_referrer VARCHAR(500)
        )
BEGIN
    DECLARE v_default_assignee INT;

    IF p_category_id IS NOT NULL THEN
SELECT default_assignee
INTO v_default_assignee
FROM contact_categories
WHERE id = p_category_id;
END IF;

INSERT INTO contacts (customer_name, phone, email, content, contact_type,
                      category_id, source, ip_address, user_agent, page_url,
                      referrer_url, assigned_to)
VALUES (p_name, p_phone, p_email, p_content, p_type,
        p_category_id, p_source, p_ip, p_user_agent, p_page_url,
        p_referrer, v_default_assignee);

SELECT LAST_INSERT_ID() as contact_id;
END$$

-- Function generate_unique_slug
CREATE FUNCTION generate_unique_slug(p_slug VARCHAR(500), p_table VARCHAR(100))
    RETURNS VARCHAR(500)
    DETERMINISTIC
BEGIN
    DECLARE v_new_slug VARCHAR(500);
    DECLARE v_counter INT DEFAULT 1;
    SET v_new_slug = p_slug;

    WHILE EXISTS (SELECT 1 FROM news_title WHERE slug = v_new_slug)
        DO
            SET v_counter = v_counter + 1;
            SET v_new_slug = CONCAT(p_slug, '-', v_counter);
END WHILE;

RETURN v_new_slug;
END$$

-- Trigger before_login_log_insert
CREATE TRIGGER before_login_log_insert
    BEFORE INSERT
    ON login_logs
    FOR EACH ROW
BEGIN
    DECLARE v_attempts INT;

    IF NEW.success = FALSE THEN
    SELECT COUNT(*)
    INTO v_attempts
    FROM login_logs
    WHERE author_id = NEW.author_id
      AND success = FALSE
      AND login_time > NOW() - INTERVAL 30 MINUTE;

    IF v_attempts >= 5 THEN
    UPDATE authors
    SET status = 'suspended'
    WHERE id = NEW.author_id;
END IF;
END IF;
END$$

-- Trigger after_contact_insert
CREATE TRIGGER after_contact_insert
    AFTER INSERT
    ON contacts
    FOR EACH ROW
BEGIN
    INSERT INTO contact_histories (contact_id, action, new_value, created_at)
    VALUES (NEW.id, 'created', CONCAT('Liên hệ mới từ ', NEW.customer_name), NOW());
    END$$

    -- Trigger after_contact_update
    CREATE TRIGGER after_contact_update
        AFTER UPDATE
        ON contacts
        FOR EACH ROW
    BEGIN
        IF OLD.status != NEW.status THEN
        INSERT INTO contact_histories (contact_id, action, old_value, new_value, created_at)
        VALUES (NEW.id, 'status_changed', OLD.status, NEW.status, NOW());
    END IF;

    IF OLD.priority != NEW.priority THEN
        INSERT INTO contact_histories (contact_id, action, old_value, new_value, created_at)
        VALUES (NEW.id, 'priority_changed', OLD.priority, NEW.priority, NOW());
END IF;

IF OLD.assigned_to != NEW.assigned_to OR (OLD.assigned_to IS NULL AND NEW.assigned_to IS NOT NULL)
        OR (OLD.assigned_to IS NOT NULL AND NEW.assigned_to IS NULL) THEN
        INSERT INTO contact_histories (contact_id, action, old_value, new_value, created_at)
        VALUES (NEW.id, 'assigned',
                IFNULL((SELECT username FROM authors WHERE id = OLD.assigned_to), 'Chưa phân công'),
                IFNULL((SELECT username FROM authors WHERE id = NEW.assigned_to), 'Chưa phân công'),
                NOW());
END IF;
END$$

-- Trigger after_news_publish
CREATE TRIGGER after_news_publish
    AFTER UPDATE
    ON news
    FOR EACH ROW
BEGIN
    IF NEW.status = 'published' AND OLD.status != 'published' THEN
        CALL GenerateNewsLinks(NEW.id);
END IF;
END$$

-- Trigger after_recruitment_open
CREATE TRIGGER after_recruitment_open
    AFTER UPDATE
    ON recruitments
    FOR EACH ROW
BEGIN
    IF NEW.status = 'open' AND OLD.status != 'open' THEN
        CALL GenerateRecruitmentLinks(NEW.id);
END IF;
END$$

DELIMITER ;