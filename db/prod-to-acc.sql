# noinspection SqlNoDataSourceInspectionForFile

-- URL fixesdep

-- Berlin
UPDATE core_config_data SET VALUE = REPLACE(VALUE,'foobar.prod.x.com','foobar.staging.x.com');

-- Robots
UPDATE core_config_data SET value='NOINDEX,NOFOLLOW' WHERE path='design/search_engine_robots/default_robots';

-- Increment prefixes
UPDATE sales_sequence_profile SET prefix=CONCAT('ACC', prefix);

-- Analytics
UPDATE core_config_data SET value = '0' WHERE path = 'googletagmanager/general/active';

-- Store email addresses
UPDATE core_config_data SET value='no-reply@x.com' WHERE path like 'trans_email/%/email';
UPDATE core_config_data SET value='no-reply@x.com' WHERE path like 'sales_email/%/copy_to';
UPDATE core_config_data SET value='no-reply@x.com' WHERE path='contact/email/recipient_email';
UPDATE customer_group SET group_manage_email='no-reply@x.com' WHERE group_manage_email IS NOT NULL;
UPDATE customer_group SET email_customer_approvers='no-reply@x.com' WHERE group_manage_email IS NOT NULL;

-- mollie live to test modus
UPDATE core_config_data SET VALUE='test' WHERE PATH = 'payment/mollie_general/type';

-- xtento order import sources path prod to staging
UPDATE xtento_orderimport_source SET PATH = REPLACE(PATH, 'prod', 'staging') WHERE PATH LIKE '%prod';
