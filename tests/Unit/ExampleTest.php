<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }

    /**
     * Test string operations.
     */
    public function test_string_operations(): void
    {
        $string = 'Hello World';
        
        $this->assertEquals('Hello World', $string);
        $this->assertStringContainsString('World', $string);
        $this->assertStringStartsWith('Hello', $string);
    }

    /**
     * Test array operations.
     */
    public function test_array_operations(): void
    {
        $array = [1, 2, 3, 4, 5];
        
        $this->assertCount(5, $array);
        $this->assertContains(3, $array);
        $this->assertIsArray($array);
    }

    /**
     * Test mathematical operations.
     */
    public function test_mathematical_operations(): void
    {
        $this->assertEquals(4, 2 + 2);
        $this->assertEquals(6, 2 * 3);
        $this->assertEquals(2, 4 / 2);
        $this->assertEquals(1, 3 % 2);
    }
}