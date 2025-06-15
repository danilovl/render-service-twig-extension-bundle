<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\Helper;

use function Symfony\Component\String\u;

class FormatNameHelper
{
    public static function camelCaseToSnakeCase(string $input): string
    {
        return u($input)->snake()->toString();
    }
}
