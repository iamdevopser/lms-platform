<?php

namespace Tests\Quality;

use PHPUnit\Framework\TestCase;

class CodeQualityTest extends TestCase
{
    /**
     * Test that all PHP files have proper syntax.
     */
    public function test_php_syntax(): void
    {
        $files = $this->getPhpFiles();
        
        foreach ($files as $file) {
            $output = [];
            $returnCode = 0;
            
            exec("php -l {$file} 2>&1", $output, $returnCode);
            
            $this->assertEquals(0, $returnCode, "Syntax error in {$file}: " . implode("\n", $output));
        }
    }

    /**
     * Test that all PHP files have proper opening tags.
     */
    public function test_php_opening_tags(): void
    {
        $files = $this->getPhpFiles();
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            $this->assertStringStartsWith('<?php', $content, "File {$file} should start with <?php");
        }
    }

    /**
     * Test that all PHP files have proper closing tags.
     */
    public function test_php_closing_tags(): void
    {
        $files = $this->getPhpFiles();
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            // Skip files that should not have closing tags
            if (strpos($file, 'index.php') !== false || strpos($file, 'artisan') !== false) {
                continue;
            }
            
            $this->assertStringEndsWith('?>', $content, "File {$file} should end with ?>");
        }
    }

    /**
     * Test that all PHP files have proper indentation.
     */
    public function test_php_indentation(): void
    {
        $files = $this->getPhpFiles();
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $lines = explode("\n", $content);
            
            foreach ($lines as $lineNumber => $line) {
                if (trim($line) === '') {
                    continue;
                }
                
                // Check for mixed tabs and spaces
                $this->assertFalse(
                    strpos($line, "\t") !== false && strpos($line, '    ') !== false,
                    "File {$file} line " . ($lineNumber + 1) . " has mixed tabs and spaces"
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





