<?php
/**
 * Copyright (c) 2011-2018 Guilherme Valentim
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */
namespace FCMPHP\Url;

/**
 * Class FCMPHPUrlManipulator
 *
 * @package FCMPHP
 */
class FCMPHPUrlManipulator
{

    /**
     * Gracefully appends params to the URL.
     *
     * @param string $url       The URL that will receive the params.
     * @param array  $newParams The params to append to the URL.
     *
     * @return string
     */
    public static function appendParamsToUrl($url, array $newParams = array())
    {
        if (empty($newParams)) {
            return $url;
        }

        if (strpos($url, '?') === false) {
            return $url . '?' . http_build_query($newParams, null, '&');
        }

        list($path, $query) = explode('?', $url, 2);
        $existingParams = array();
        parse_str($query, $existingParams);

        // Favor params from the original URL over $newParams
        $newParams = array_merge($newParams, $existingParams);

        // Sort for a predicable order
        ksort($newParams);

        return $path . '?' . http_build_query($newParams, null, '&');
    }

    /**
     * Returns the params from a URL in the form of an array.
     *
     * @param string $url The URL to parse the params from.
     *
     * @return array
     */
    public static function getParamsAsArray($url)
    {
        $query = parse_url($url, PHP_URL_QUERY);
        if (!$query) {
            return array();
        }
        $params = array();
        parse_str($query, $params);

        return $params;
    }

    /**
     * Check for a "/" prefix and prepend it if not exists.
     *
     * @param string|null $string
     *
     * @return string|null
     */
    public static function forceSlashPrefix($string)
    {
        if (!$string) {
            return $string;
        }

        return strpos($string, '/') === 0 ? $string : '/' . $string;
    }
}
