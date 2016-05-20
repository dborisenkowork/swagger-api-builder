<?php
namespace Transportir\SwaggerAPIBuilder\Tests;

use Transportir\SwaggerAPIBuilder\LocalSwaggerAPIBuilder;

class LocalSwaggerAPIBuilderTest extends \PHPUnit_Framework_TestCase
{
    const FIXTURES_DIR = '/Resources/fixtures';

    public function testLocalSwaggerAPIBuilder() {
        $compare = json_decode(file_get_contents(__DIR__.'/Resources/fixtures/compare.json'), true);

        $this->assertTrue(is_array($compare));

        $fixturesDir = __DIR__.'/'.self::FIXTURES_DIR;
        $fixtures = [
            [
                sprintf('%s/01-no-subdirectory', $fixturesDir)
            ],
            [
                sprintf('%s/02-with-subdirectory', $fixturesDir)
            ],
            [
                sprintf('%s/03-with-subdirectories', $fixturesDir)
            ],
            [
                sprintf('%s/04-multiple-sources/source-global', $fixturesDir),
                sprintf('%s/04-multiple-sources/source-bundles', $fixturesDir),
            ]
        ];

        foreach($fixtures as $paths) {
            $builder = new LocalSwaggerAPIBuilder($paths);
            $result = $builder->build();

            $this->assertEquals($compare, $result);
        }
    }
}