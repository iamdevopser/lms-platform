<?php
/**
 * AWS Environment Prepend File
 * This file is automatically prepended to all PHP requests
 */

// Set AWS specific environment variables
if (!defined('AWS_REGION')) {
    define('AWS_REGION', $_ENV['AWS_REGION'] ?? 'us-east-1');
}

if (!defined('AWS_ACCOUNT_ID')) {
    define('AWS_ACCOUNT_ID', $_ENV['AWS_ACCOUNT_ID'] ?? '');
}

// Set CloudFront URL if available
if (!defined('CLOUDFRONT_URL')) {
    define('CLOUDFRONT_URL', $_ENV['CLOUDFRONT_URL'] ?? '');
}

// Set S3 bucket name
if (!defined('S3_BUCKET')) {
    define('S3_BUCKET', $_ENV['S3_BUCKET'] ?? '');
}

// AWS specific error handling
if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production') {
    // In production, log errors to CloudWatch
    ini_set('log_errors', 1);
    ini_set('error_log', '/var/log/php_errors.log');
}

// Set proper headers for AWS Load Balancer
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
}

if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
    $_SERVER['HTTPS'] = $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'on' : 'off';
    $_SERVER['SERVER_PORT'] = $_SERVER['HTTPS'] === 'on' ? 443 : 80;
}

// AWS CloudWatch Logs integration
if (function_exists('error_log')) {
    set_error_handler(function($severity, $message, $file, $line) {
        $logMessage = sprintf(
            '[%s] %s: %s in %s on line %d',
            date('Y-m-d H:i:s'),
            $severity,
            $message,
            $file,
            $line
        );
        error_log($logMessage);
    });
}





