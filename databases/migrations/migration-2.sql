CREATE TABLE wa_reply_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    expiration_time INT DEFAULT NULL,
    expiration_message LONGTEXT DEFAULT NULL,
    reply_status ENUM("ACTIVE","INACTIVE") DEFAULT "INACTIVE",
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    created_by INT NOT NULL,
    deleted_by INT DEFAULT NULL,
    CONSTRAINT fk_wa_reply_settings_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_wa_reply_settings_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_wa_reply_settings_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE wa_reply_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_id INT NOT NULL,
    contact_id INT NOT NULL,
    session_data LONGTEXT DEFAULT NULL,
    status ENUM("ACTIVE","EXPIRED") DEFAULT "ACTIVE",
    expired_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_wa_reply_sessions_contact_id FOREIGN KEY (contact_id) REFERENCES wa_contacts(id) ON DELETE CASCADE,
    CONSTRAINT fk_wa_reply_sessions_device_id FOREIGN KEY (device_id) REFERENCES wa_devices(id) ON DELETE CASCADE
);

CREATE TABLE wa_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    device_id INT NOT NULL,
    keyword LONGTEXT NOT NULL,
    content LONGTEXT NOT NULL,
    reply_type ENUM("TEXT","WEBHOOK") DEFAULT "TEXT",
    action_after ENUM("STAY","NEXT","BACK","EXIT") DEFAULT "EXIT",
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    created_by INT NOT NULL,
    deleted_by INT DEFAULT NULL,
    CONSTRAINT fk_wa_replies_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_wa_replies_device_id FOREIGN KEY (device_id) REFERENCES wa_devices(id) ON DELETE CASCADE,
    CONSTRAINT fk_wa_replies_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_wa_replies_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL
);