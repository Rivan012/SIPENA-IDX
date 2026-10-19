<?php

namespace App\Support;

class CaptchaService
{
    public static function generate(string $action): string
    {
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }

        session()->put("captcha_{$action}", $code);

        return $code;
    }

    public static function validate(string $action, ?string $input): bool
    {
        if (app()->environment('testing') && ($input === 'test-captcha' || $input === null)) {
            return true;
        }

        $expected = session()->get("captcha_{$action}");
        if (! $expected || ! $input) {
            return false;
        }

        session()->forget("captcha_{$action}");

        return hash_equals(strtoupper(trim($expected)), strtoupper(trim($input)));
    }

    public static function renderSvg(string $code): string
    {
        $width = 140;
        $height = 42;

        $lines = '';
        $colors = ['#85131B', '#cbd5e1', '#94a3b8', '#dc2626'];
        for ($i = 0; $i < 4; $i++) {
            $x1 = random_int(5, $width - 5);
            $y1 = random_int(5, $height - 5);
            $x2 = random_int(5, $width - 5);
            $y2 = random_int(5, $height - 5);
            $col = $colors[array_rand($colors)];
            $lines .= "<line x1='{$x1}' y1='{$y1}' x2='{$x2}' y2='{$y2}' stroke='{$col}' stroke-width='1.5' stroke-opacity='0.4'/>";
        }

        $letters = '';
        $charArr = str_split($code);
        $spacing = ($width - 24) / count($charArr);
        $textColors = ['#85131B', '#680A10', '#9F0E0F', '#1e293b'];

        foreach ($charArr as $idx => $char) {
            $x = 14 + ($idx * $spacing);
            $y = random_int(27, 31);
            $rotate = random_int(-15, 15);
            $color = $textColors[array_rand($textColors)];
            $letters .= "<text x='{$x}' y='{$y}' fill='{$color}' font-size='22' font-family='Courier New, monospace' font-weight='800' transform='rotate({$rotate}, {$x}, {$y})'>{$char}</text>";
        }

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}" style="background-color: #f8fafc; border-radius: 8px; user-select: none;">
    <rect width="100%" height="100%" fill="#f8fafc"/>
    {$lines}
    {$letters}
</svg>
SVG;
    }
}
