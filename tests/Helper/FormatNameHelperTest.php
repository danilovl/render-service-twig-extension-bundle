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
        yield ['MultipleUpperCASELetters', 'multiple_upper_case_letters'];
        yield ['withNumbers123', 'with_numbers123'];
        yield ['with456Numbers', 'with456_numbers'];
        yield ['singleLetterA', 'single_letter_a'];
        yield ['ABCabc', 'ab_cabc'];
        yield ['XMLHttpRequest', 'xml_http_request'];
        yield ['iOS8Device', 'i_os8_device'];
        yield ['UserIDAndName', 'user_id_and_name'];
        yield ['already_snake_case', 'already_snake_case'];
        yield ['mixedCASEpattern', 'mixed_cas_epattern'];
    }
}
