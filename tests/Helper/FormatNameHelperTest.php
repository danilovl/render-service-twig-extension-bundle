<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\Tests\Helper;

use Danilovl\RenderServiceTwigExtensionBundle\Helper\FormatNameHelper;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FormatNameHelperTest extends TestCase
{
    #[DataProvider('camelCaseProvider')]
    public function testCamelCaseToSnakeCase(string $input, string $expected): void
    {
        $this->assertSame($expected, FormatNameHelper::camelCaseToSnakeCase($input));
    }

    public static function camelCaseProvider(): Generator
    {
        yield ['camelCase', 'camel_case'];
        yield ['HelloWorld', 'hello_world'];
        yield ['testCase', 'test_case'];
        yield ['anotherTest', 'another_test'];
    }
}
