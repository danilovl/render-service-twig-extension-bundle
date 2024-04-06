<?php declare(strict_types=1);

namespace Danilovl\RenderServiceTwigExtensionBundle\Helper;

class FormatNameHelper
{
    public static function camelCaseToSnakeCase(string $input): string
    {
        $snake_case = strtolower($input[0]);

        for ($i = 1; $i < strlen($input); $i++) {
            if (ctype_upper($input[$i])) {
                $snake_case .= '_' . strtolower($input[$i]);
            } else {
                $snake_case .= $input[$i];
            }
        }

        return $snake_case;
    }
}
