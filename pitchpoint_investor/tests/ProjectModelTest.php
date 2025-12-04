<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for Project Model
 */
class ProjectModelTest extends TestCase
{
    /**
     * Test that Project class exists
     */
    public function testProjectClassExists(): void
    {
        $modelFile = __DIR__ . '/../app/models/project.php';
        
        $this->assertFileExists($modelFile, 'project.php file should exist');
        
        $content = file_get_contents($modelFile);
        $this->assertStringContainsString('class Project', $content, 'File should contain Project class');
        $this->assertStringContainsString('extends Model', $content, 'Project should extend Model');
    }

    /**
     * Test that Project has required methods
     */
    public function testProjectHasRequiredMethods(): void
    {
        $modelFile = __DIR__ . '/../app/models/project.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('function categories', $content, 'Project should have categories method');
        $this->assertStringContainsString('function explore', $content, 'Project should have explore method');
        $this->assertStringContainsString('function find', $content, 'Project should have find method');
    }

    /**
     * Test categories method query structure
     */
    public function testCategoriesQueryStructure(): void
    {
        $modelFile = __DIR__ . '/../app/models/project.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('SELECT category_id, category_name', $content, 'categories should select category fields');
        $this->assertStringContainsString('FROM categories', $content, 'categories should query categories table');
        $this->assertStringContainsString('ORDER BY category_name', $content, 'categories should order by name');
    }

    /**
     * Test explore method query structure
     */
    public function testExploreQueryStructure(): void
    {
        $modelFile = __DIR__ . '/../app/models/project.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('FROM projects p', $content, 'explore should query projects table');
        $this->assertStringContainsString("status = 'published'", $content, 'explore should filter published projects');
        $this->assertStringContainsString("visibility = 'public'", $content, 'explore should filter public projects');
        $this->assertStringContainsString('LIKE :q', $content, 'explore should support search with LIKE');
        $this->assertStringContainsString('category_id = :cat', $content, 'explore should support category filter');
    }

    /**
     * Test find method query structure
     */
    public function testFindQueryStructure(): void
    {
        $modelFile = __DIR__ . '/../app/models/project.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('SELECT p.*', $content, 'find should select project fields');
        $this->assertStringContainsString('FROM projects p', $content, 'find should query projects table');
        $this->assertStringContainsString('LEFT JOIN categories', $content, 'find should join categories');
        $this->assertStringContainsString('LEFT JOIN entrepreneurs', $content, 'find should join entrepreneurs');
        $this->assertStringContainsString('project_id = :id', $content, 'find should filter by project_id');
    }

    /**
     * Test explore method supports search functionality
     */
    public function testExploreSupportsSearch(): void
    {
        $modelFile = __DIR__ . '/../app/models/project.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('title   LIKE', $content, 'explore should search in title');
        $this->assertStringContainsString('summary LIKE', $content, 'explore should search in summary');
        $this->assertStringContainsString('problem LIKE', $content, 'explore should search in problem');
        $this->assertStringContainsString('solution LIKE', $content, 'explore should search in solution');
    }
}

