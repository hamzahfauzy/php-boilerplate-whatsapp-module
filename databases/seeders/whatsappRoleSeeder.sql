INSERT INTO roles(name) VALUES ('Whatsapp Client');

INSERT INTO `role_routes`(role_id,route_path) VALUES 
    ((SELECT id FROM roles WHERE `name` = 'Whatsapp Client'),'whatsapp/*'),
    ((SELECT id FROM roles WHERE `name` = 'Whatsapp Client'),'crud/index?table=wa_devices'),
    ((SELECT id FROM roles WHERE `name` = 'Whatsapp Client'),'crud/create?table=wa_devices'),
    ((SELECT id FROM roles WHERE `name` = 'Whatsapp Client'),'crud/edit?table=wa_devices'),
    ((SELECT id FROM roles WHERE `name` = 'Whatsapp Client'),'crud/delete?table=wa_devices'),
    ((SELECT id FROM roles WHERE `name` = 'Whatsapp Client'),'crud/index?table=wa_contacts'),
    ((SELECT id FROM roles WHERE `name` = 'Whatsapp Client'),'crud/create?table=wa_contacts'),
    ((SELECT id FROM roles WHERE `name` = 'Whatsapp Client'),'crud/edit?table=wa_contacts'),
    ((SELECT id FROM roles WHERE `name` = 'Whatsapp Client'),'crud/delete?table=wa_contacts'),
    ((SELECT id FROM roles WHERE `name` = 'Whatsapp Client'),'crud/index?table=wa_templates'),
    ((SELECT id FROM roles WHERE `name` = 'Whatsapp Client'),'crud/edit?table=wa_templates'),
    ((SELECT id FROM roles WHERE `name` = 'Whatsapp Client'),'crud/create?table=wa_templates'),
    ((SELECT id FROM roles WHERE `name` = 'Whatsapp Client'),'crud/delete?table=wa_templates'),
    ((SELECT id FROM roles WHERE `name` = 'Whatsapp Client'),'default/profile');
