<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CAPTCHA Disabled
    |--------------------------------------------------------------------------
    |
    | Set to true to disable captcha globally. Useful for testing or local dev.
    |
    */
    'disable' => env('CAPTCHA_DISABLE', false),

    /*
    |--------------------------------------------------------------------------
    | Characters used in CAPTCHA
    |--------------------------------------------------------------------------
    */
    'characters' => [
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z',
        'a',
        'b',
        'c',
        'd',
        'e',
        'f',
        'g',
        'h',
        'i',
        'j',
        'k',
        'l',
        'm',
        'n',
        'o',
        'p',
        'q',
        'r',
        's',
        't',
        'u',
        'v',
        'w',
        'x',
        'y',
        'z',
        0,
        1,
        2,
        3,
        4,
        5,
        6,
        7,
        8,
        9
    ],

    /*
    |--------------------------------------------------------------------------
    | Directories (optional)
    |--------------------------------------------------------------------------
    | You can leave fontsDirectory and bgsDirectory as default if you don't
    | have custom fonts or backgrounds.
    */

    /*
    |--------------------------------------------------------------------------
    | Default CAPTCHA configuration
    |--------------------------------------------------------------------------
    */
    'default' => [
        'length' => 5,
        'width' => 120,
        'height' => 36,
        'quality' => 90,
        'math' => false,
        'bgImage' => false,
        'bgColor' => '#ffffff',
        'fontColors' => ['#000000'],
        'contrast' => 0,
        'encrypt' => false,
        'expire' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Flat CAPTCHA
    |--------------------------------------------------------------------------
    | Modern flat style captcha
    */
    'flat' => [
        'length' => 5,
        'width' => 120,
        'height' => 36,
        'quality' => 100,
        'math' => false,
        'bgImage' => false,
        'bgColor' => '#f2f2f2',
        'fontColors' => ['#2c3e50', '#c0392b', '#16a085', '#8e44ad', '#303f9f'],
        'lines' => 7,
        'contrast' => 0,
    ],

    /*
    |--------------------------------------------------------------------------
    | Mini CAPTCHA (small size)
    |--------------------------------------------------------------------------
    */
    'mini' => [
        'length' => 3,
        'width' => 60,
        'height' => 32,
        'quality' => 90,
        'bgImage' => false,
        'fontColors' => ['#000000'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Inverse CAPTCHA
    |--------------------------------------------------------------------------
    */
    'inverse' => [
        'length' => 5,
        'width' => 120,
        'height' => 36,
        'quality' => 90,
        'math' => false,
        'sensitive' => true,
        'angle' => 10,
        'sharpen' => 0,
        'blur' => 0,
        'invert' => false,
        'contrast' => 0,
        'bgImage' => false,
        'fontColors' => ['#000000'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Math CAPTCHA (arithmetic)
    |--------------------------------------------------------------------------
    */
    'math' => [
        'length' => 6,
        'width' => 120,
        'height' => 36,
        'quality' => 90,
        'math' => true,
        'encrypt' => false,
    ],

];
