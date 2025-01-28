CREATE TABLE wa_campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    device_id INT NOT NULL,
    template_id INT DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    record_status ENUM("PUBLISH","DRAFT","DELETED") DEFAULT "PUBLISH",
    start_at TIMESTAMP NULL DEFAULT NULL,
    finish_at TIMESTAMP NULL DEFAULT NULL,
    expiring_time INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    scheduled_at TIMESTAMP NULL DEFAULT NULL,
    created_by INT NOT NULL,
    deleted_by INT DEFAULT NULL,
    CONSTRAINT fk_wa_campaigns_template_id FOREIGN KEY (template_id) REFERENCES wa_templates(id) ON DELETE SET NULL,
    CONSTRAINT fk_wa_campaigns_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_wa_campaigns_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE wa_campaign_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT DEFAULT NULL,
    message_id INT DEFAULT NULL,
    session_id INT DEFAULT NULL,
    response TEXT DEFAULT NULL,
    item_status ENUM("WAITING","EXPIRED","REPLIED") DEFAULT "WAITING",
    expired_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    created_by INT NOT NULL,
    deleted_by INT DEFAULT NULL,
    CONSTRAINT fk_wa_campaign_items_campaign_id FOREIGN KEY (campaign_id) REFERENCES wa_campaigns(id) ON DELETE SET NULL,
    CONSTRAINT fk_wa_campaign_items_message_id FOREIGN KEY (message_id) REFERENCES wa_messages(id) ON DELETE SET NULL,
    CONSTRAINT fk_wa_campaign_items_session_id FOREIGN KEY (session_id) REFERENCES wa_reply_sessions(id) ON DELETE SET NULL,
    CONSTRAINT fk_wa_campaign_items_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_wa_campaign_items_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL
);