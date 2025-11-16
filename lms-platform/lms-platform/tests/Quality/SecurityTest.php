<?php

namespace Tests\Quality;

use PHPUnit\Framework\TestCase;

class SecurityTest extends TestCase
{
    /**
     * Test that sensitive information is not exposed.
     */
    public function test_sensitive_information_not_exposed(): void
    {
        $sensitivePatterns = [
            '/password/i',
            '/secret/i',
            '/key/i',
            '/token/i',
            '/api_key/i',
            '/private_key/i',
            '/secret_key/i',
            '/access_token/i',
            '/refresh_token/i'
        ];
        
        $files = $this->getPhpFiles();
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            foreach ($sensitivePatterns as $pattern) {
                $this->assertDoesNotMatchRegularExpression(
                    $pattern,
                    $content,
                    "File {$file} may contain sensitive information"
                );
            }
        }
    }

    /**
     * Test that SQL injection vulnerabilities are not present.
     */
    public function test_sql_injection_vulnerabilities(): void
    {
        $sqlInjectionPatterns = [
            '/\$_(GET|POST|REQUEST)\[.*\]\s*\.\s*["\']\s*SELECT/i',
            '/\$_(GET|POST|REQUEST)\[.*\]\s*\.\s*["\']\s*INSERT/i',
            '/\$_(GET|POST|REQUEST)\[.*\]\s*\.\s*["\']\s*UPDATE/i',
            '/\$_(GET|POST|REQUEST)\[.*\]\s*\.\s*["\']\s*DELETE/i',
            '/\$_(GET|POST|REQUEST)\[.*\]\s*\.\s*["\']\s*DROP/i'
        ];
        
        $files = $this->getPhpFiles();
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            foreach ($sqlInjectionPatterns as $pattern) {
                $this->assertDoesNotMatchRegularExpression(
                    $pattern,
                    $content,
                    "File {$file} may have SQL injection vulnerability"
                );
            }
        }
    }

    /**
     * Test that XSS vulnerabilities are not present.
     */
    public function test_xss_vulnerabilities(): void
    {
        $xssPatterns = [
            '/echo\s+\$_(GET|POST|REQUEST)\[.*\]/i',
            '/print\s+\$_(GET|POST|REQUEST)\[.*\]/i',
            '/\$_(GET|POST|REQUEST)\[.*\]\s*\.\s*["\']\s*<script/i',
            '/\$_(GET|POST|REQUEST)\[.*\]\s*\.\s*["\']\s*<iframe/i',
            '/\$_(GET|POST|REQUEST)\[.*\]\s*\.\s*["\']\s*<object/i'
        ];
        
        $files = $this->getPhpFiles();
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            foreach ($xssPatterns as $pattern) {
                $this->assertDoesNotMatchRegularExpression(
                    $pattern,
                    $content,
                    "File {$file} may have XSS vulnerability"
                );
            }
        }
    }

    /**
     * Test that file upload vulnerabilities are not present.
     */
    public function test_file_upload_vulnerabilities(): void
    {
        $fileUploadPatterns = [
            '/move_uploaded_file\s*\(\s*\$_(FILES|POST)\[.*\]/i',
            '/copy\s*\(\s*\$_(FILES|POST)\[.*\]/i',
            '/file_get_contents\s*\(\s*\$_(FILES|POST)\[.*\]/i'
        ];
        
        $files = $this->getPhpFiles();
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            foreach ($fileUploadPatterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    // Check if proper validation is present
                    $this->assertStringContainsString(
                        'pathinfo',
                        $content,
                        "File {$file} has file upload without proper validation"
                    );
                }
            }
        }
    }

    /**
     * Test that authentication is properly implemented.
     */
    public function test_authentication_implementation(): void
    {
        $authFiles = [
            'app/Http/Controllers/Auth/LoginController.php',
            'app/Http/Controllers/Auth/RegisterController.php',
            'app/Http/Controllers/Auth/LogoutController.php'
        ];
        
        foreach ($authFiles as $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                
                $this->assertStringContainsString(
                    'Hash::check',
                    $content,
                    "File {$file} should use Hash::check for password verification"
                );
            }
        }
    }

    /**
     * Get all PHP files in the project.
     */
    private function getPhpFiles(): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator('.')
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                // Skip vendor directory
                if (strpos($file->getPathname(), 'vendor/') !== false) {
                    continue;
                }
                
                // Skip test files
                if (strpos($file->getPathname(), 'tests/') !== false) {
                    continue;
                }
                
                $files[] = $file->getPathname();
            }
        }
        
        return $files;
    }
}





