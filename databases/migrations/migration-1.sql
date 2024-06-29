CREATE TABLE wa_devices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(100) DEFAULT NULL,
    status ENUM("CONNECTED","NOT CONNECTED","LOGOUT") DEFAULT "NOT CONNECTED",
    qrcode TEXT DEFAULT NULL,
    webhook_url TEXT DEFAULT NULL,
    record_status ENUM("PUBLISH","DRAFT","DELETED") DEFAULT "PUBLISH",
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT NOT NULL,
    deleted_by INT DEFAULT NULL,
    CONSTRAINT fk_wa_devices_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_wa_devices_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_wa_devices_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE wa_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(100) NOT NULL,
    record_status ENUM("PUBLISH","DRAFT","DELETED") DEFAULT "PUBLISH",
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT NOT NULL,
    deleted_by INT DEFAULT NULL,
    CONSTRAINT fk_wa_contacts_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_wa_contacts_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_wa_contacts_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE wa_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    content LONGTEXT NOT NULL,
    record_type VARCHAR(100) DEFAULT NULL,
    record_status ENUM("PUBLISH","DRAFT","DELETED") DEFAULT "PUBLISH",
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT NOT NULL,
    deleted_by INT DEFAULT NULL,
    CONSTRAINT fk_wa_templates_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_wa_templates_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_wa_templates_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE wa_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_id INT DEFAULT NULL,
    device_id INT NOT NULL,
    contact_id INT NOT NULL,
    content LONGTEXT NOT NULL,
    response JSON DEFAULT NULL,
    status ENUM("WAITING","SENT","ERROR") DEFAULT "WAITING",
    record_type ENUM("MESSAGE_IN","MESSAGE_OUT") DEFAULT "MESSAGE_OUT",
    record_status ENUM("PUBLISH","DRAFT","DELETED") DEFAULT "PUBLISH",
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL,
    scheduled_at DATETIME DEFAULT NULL,
    created_by INT NOT NULL,
    deleted_by INT DEFAULT NULL,
    CONSTRAINT fk_wa_messages_template_id FOREIGN KEY (template_id) REFERENCES wa_templates(id) ON DELETE SET NULL,
    CONSTRAINT fk_wa_messages_contact_id FOREIGN KEY (contact_id) REFERENCES wa_contacts(id) ON DELETE CASCADE,
    CONSTRAINT fk_wa_messages_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_wa_messages_deleted_by FOREIGN KEY (deleted_by) REFERENCES users(id) ON DELETE SET NULL
);