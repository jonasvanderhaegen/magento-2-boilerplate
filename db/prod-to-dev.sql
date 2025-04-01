# noinspection SqlNoDataSourceInspectionForFile

-- URL fixes
UPDATE core_config_data SET VALUE = REPLACE(VALUE,'example.production.com','m2.test');
UPDATE core_config_data SET VALUE = REPLACE(VALUE,'webshop2.prod.com','webshop2.test');

-- Store email addresses
UPDATE core_config_data SET value='x.local@dispostable.com' WHERE path like 'trans_email/%/email';
UPDATE core_config_data SET value='x.local@dispostable.com' WHERE path like 'sales_email/%/copy_to';
UPDATE core_config_data SET value='x.local@dispostable.com' WHERE path='contact/email/recipient_email';
UPDATE core_config_data SET value='0' WHERE path='smtp/general/enabled';
UPDATE customer_group SET group_manage_email='order@approval.com' WHERE group_manage_email IS NOT NULL;
UPDATE customer_group SET email_customer_approvers='customer@approval' WHERE group_manage_email IS NOT NULL;

-- Robots
UPDATE core_config_data SET value='NOINDEX,NOFOLLOW' WHERE path='design/search_engine_robots/default_robots';

-- Increment prefixes
UPDATE sales_sequence_profile SET prefix=CONCAT('DEV', prefix);

-- Analytics
UPDATE core_config_data SET value = '0' WHERE path = 'googletagmanager/general/active';

-- Search
UPDATE core_config_data SET value = 'opensearch' WHERE path = 'catalog/search/elasticsearch7_server_hostname';

-- Google Recaptcha
UPDATE core_config_data SET value = '0' WHERE path = 'msp_securitysuite_recaptcha/frontend/enabled';

-- Emails to mailhog
UPDATE core_config_data SET value = '1025' WHERE path = 'system/smtp/port';
UPDATE core_config_data SET value = '0' WHERE path = 'amsmtp/general/enable';

-- Disable Sentry
UPDATE core_config_data SET value = '0' WHERE path = 'sentry/general/enable_php_tracking';
UPDATE core_config_data SET value = '0' WHERE path = 'sentry/general/enable_script_tag';
UPDATE core_config_data SET value = '0' WHERE path = 'sentry/general/use_logrocket';