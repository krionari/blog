<?php
/**
 * Created by PhpStorm.
 * User: Krionari
 * Date: 02/06/2019
 * Time: 21:17
 */

namespace App\Service;


class Slugify
{
    public function generate(string $text)
    {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);

        $text = mb_strtolower(preg_replace( '/[^a-zA-Z0-9\-\s]/', '', $text ));

        $text = str_replace(' ','-',trim($text));

        $text = preg_replace('/([-])\\1+/', '$1', $text);

        return $text;
    }
}
