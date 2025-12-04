<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for Base Controller Class
 */
class BaseControllerTest extends TestCase
{
    /**
     * Test that Controller base class exists
     */
    public function testControllerClassExists(): void
    {
        $controllerFile = __DIR__ . '/../app/core/controller.php';
        
        $this->assertFileExists($controllerFile, 'controller.php file should exist');
        
        $content = file_get_contents($controllerFile);
        $this->assertStringContainsString('abstract class Controller', $content, 'File should contain abstract Controller class');
    }

    /**
     * Test that Controller has view method
     */
    public function testControllerHasViewMethod(): void
    {
        $controllerFile = __DIR__ . '/../app/core/controller.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('function view', $content, 'Controller should have view method');
        $this->assertStringContainsString('protected function view', $content, 'view should be protected');
    }

    /**
     * Test view method structure
     */
    public function testViewMethodStructure(): void
    {
        $controllerFile = __DIR__ . '/../app/core/controller.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('extract($data)', $content, 'view should extract data array');
        $this->assertStringContainsString('header.php', $content, 'view should include header');
        $this->assertStringContainsString('footer.php', $content, 'view should include footer');
        $this->assertStringContainsString('views/', $content, 'view should load from views directory');
    }

    /**
     * Test that Controller has redirect method
     */
    public function testControllerHasRedirectMethod(): void
    {
        $controllerFile = __DIR__ . '/../app/core/controller.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('function redirect', $content, 'Controller should have redirect method');
        $this->assertStringContainsString('protected function redirect', $content, 'redirect should be protected');
    }

    /**
     * Test redirect method structure
     */
    public function testRedirectMethodStructure(): void
    {
        $controllerFile = __DIR__ . '/../app/core/controller.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('Location:', $content, 'redirect should set Location header');
        $this->assertStringContainsString('investorindex.php', $content, 'redirect should use investorindex.php');
        $this->assertStringContainsString('exit', $content, 'redirect should exit after redirecting');
    }
}

