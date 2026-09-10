<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class BuildTest extends TestCase
{
    public function testBuildExclusionsArePresent(): void
    {
        // The build.xml is in the root directory, three levels up from featurenavigator/tests/Unit/
        $buildXml = __DIR__ . '/../../../build.xml';
        $this->assertFileExists($buildXml);

        $content = file_get_contents($buildXml);

        // Verify that the build script explicitly excludes test-related artifacts using the ${folder} variable.
        $this->assertStringContainsString('exclude name="${folder}/tests/**"', $content, 'The tests directory must be excluded from the production package.');
        $this->assertStringContainsString('exclude name="${folder}/phpunit.xml.dist"', $content, 'The phpunit configuration file must be excluded from the production package.');
        $this->assertStringContainsString('exclude name="${folder}/.phpunit.result.cache"', $content, 'The PHPUnit result cache must be excluded from the production package.');
    }

    /**
     * This test checks if there are any "test" files in the source directory that might have been 
     * accidentally left behind (not part of the intended tests folder).
     */
    public function testNoTestFilesInSourceDirectory(): void
    {
        $sourceDir = __DIR__ . '/../../src';
        $this->assertDirectoryExists($sourceDir);
        
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sourceDir));

        foreach ($files as $file) {
            if ($file->isDir()) continue;
            
            $fileName = $file->getFilename();
            $this->assertStringNotContainsString('Test', $fileName, "Production source file '{$fileName}' appears to be a test file and should not be in the src directory.");
        }
    }
}
