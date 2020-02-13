<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$g_hostname = 'localhost';
$g_db_type = 'mysqli';
$g_database_name = 'hr4u';
$g_db_username = 'root';
$g_db_password = 'password';
$g_db_table_plugin_prefix = 'table';
$g_default_timezone = 'Africa/Abidjan';
$g_crypto_master_salt = 'f2okp//uxnS4RyJyhcA0BycXpzuvtqAN1ZFN/ZOmq6Q=';
$g_phpMailer_method = PHPMAILER_METHOD_SMTP;
// $g_smtp_host = 'smtp.mailtrap.io';            # used with
$g_smtp_host = 'mail.iprosonic.com';
$g_smtp_username = 'support@iprosonic.com';
$g_smtp_password = 'abcd_1234';
$g_stop_on_errors = ON;
$g_smtp_connection_mode = '';
$g_smtp_port = 25;
$g_webmaster_email = 'hemant@iprosonic.com';
$g_administrator_email = 'hemant@iprosonic.com';
$g_from_email = 'support@iprosonic.com';    # the "From: " field in emails
$g_return_path_email = 'hem587ant@gmail.com';    # the return address for bounced mail
$g_enable_email_notification = ON;
$g_email_recieve_own = ON;
$g_email_login_enabled = ON;
$g_antispam_max_event_count = 0;
$g_status_enum_string = '10:new,50:assigned,90:closed';
$g_file_upload_method = DISK;
//$g_absolute_path_default_upload_folder = '/var/www/html/mantisbt/attachments/';
$g_ticket_status = 'closed';
$g_bug_report_page_fields = array(
    'category_id',
    'handler',
    'priority',
    'summary',
    'description',
    'additional_info',
    'attachments',
    'due_date',
);
$g_history_order = 'DESC';
$g_log_level = LOG_ALL & LOG_DATABASE;
//$g_log_destination = 'file:/var/www/html/mantisbt/log/mantis.log';
$g_show_attachments = ON;
$g_preset_duedate = ON;
$g_display_errors = array(
    E_WARNING => 'halt',
    E_NOTICE => 'halt',
    E_ERROR => 'halt',
    E_USER_ERROR => 'halt',
    E_WARNING => 'inline',
    E_USER_WARNING => 'inline',
    E_USER_NOTICE => 'halt'
);
$g_duedate_days_default = 'today';
$g_show_detailed_errors = ON;
$g_notify_new_user_created_threshold_min = 91;
?>
