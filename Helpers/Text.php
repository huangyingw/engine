<?php

/**
 * Text Helpers
 *
 * @author emi
 */

namespace Minds\Helpers;

class Text
{
    /**
     * @param string $text
     * @param int $charLimit
     * @return false|string|null
     */
    public static function slug($text, $charLimit = 0)
    {
        if (!$text) {
            return '';
        }

        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $transliteratedText = @iconv('utf-8', 'us-ascii//TRANSLIT//IGNORE', $text);

        if ($transliteratedText) {
            $text = $transliteratedText;
        }

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // apply character limit (if any) and trim
        if ($charLimit > 0) {
            $text = trim(substr($text, 0, $charLimit), '-');
        }

        // lowercase
        $text = strtolower($text);

        return $text;
    }

    /**
     * @param string $text
     * @return string
     */
    public static function camel($text)
    {
        return lcfirst(str_replace(['_', ':'], '', ucwords($text, '_:')));
    }

    /**
     * @param string $text
     * @return string
     */
    public static function snake($text)
    {
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $text));
    }

    /**
     * @param mixed $value
     * @return string[]
     */
    public static function buildArray($value)
    {
        if (is_array($value)) {
            return array_map('static::_buildArrayElement', $value);
        }

        return [ static::_buildArrayElement($value) ];
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected static function _buildArrayElement($value)
    {
        return (string) $value;
    }

    /**
     * Runs through a body of text, checking it for values.
     *
     * @param [type] $haystack - Body of text.
     * @param [type] $needles - Array of values to be searched for.
     * @param integer $offset - offset to start.
     * @return boolean|string - The matching value.
     */
    public static function strposa($haystack, $needles, $offset = 0)
    {
        if (!is_array($needles)) {
            $needles = [$needles];
        }
        foreach ($needles as $query) {
            if (stripos($haystack, $query, $offset) !== false) {
                // stop on first true result
                return $query;
            }
        }
        return false;
    }

    /**
     * Extracts hashtags from a string and returns in array
     * @param string $fullText
     * @return string[]
     */
    public static function getHashtags($fullText): array
    {
        $htRe = '/(^|\s||)#(\pL+)/u';
        $matches = [];

        preg_match_all($htRe, $fullText, $matches);

        return array_filter($matches[2] ?? [], function ($tag) {
            return is_string($tag);
        });
    }

    /**
     * Truncate a string
     * @param string $str - the input
     * @param int $maxLength - the max length of your output
     * @return string
     */
    public static function truncate(string $str, int $maxLength = 140): string
    {
        if (strlen($str) > $maxLength) {
            return substr($str, 0, $maxLength - 3) . "...";
        }
        return $str;
    }
}
