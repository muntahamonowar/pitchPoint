<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for Base Model Class
 */
class BaseModelTest extends TestCase
{
    /**
     * Test that Model base class exists
     */
    public function testModelClassExists(): void
    {
        $modelFile = __DIR__ . '/../app/core/model.php';
        
        $this->assertFileExists($modelFile, 'model.php file should exist');
        
        $content = file_get_contents($modelFile);
        $this->assertStringContainsString('abstract class Model', $content, 'File should contain abstract Model class');
    }

    /**
     * Test that Model has database property
     */
    public function testModelHasDatabaseProperty(): void
    {
        $modelFile = __DIR__ . '/../app/core/model.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('protected PDO $db', $content, 'Model should have protected db property');
    }

    /**
     * Test that Model has constructor
     */
    public function testModelHasConstructor(): void
    {
        $modelFile = __DIR__ . '/../app/core/model.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('__construct', $content, 'Model should have constructor');
        $this->assertStringContainsString('db()', $content, 'Model constructor should call db() function');
    }
}

