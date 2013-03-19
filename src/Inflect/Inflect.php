<?php

namespace Inflect;

class Inflect
{
    static $plural = array(
        '/(quiz)$/i'               => "$1zes",
        '/^(oxen)$/i'              => "$1",
        '/^(ox)$/i'                => "$1en",
        '/([m|l])ice$/i'           => "$1ice",
        '/([m|l])ouse$/i'          => "$1ice",
        '/(matr|vert|ind)ix|ex$/i' => "$1ices",
        '/(x|ch|ss|sh)$/i'         => "$1es",
        '/([^aeiouy]|qu)y$/i'      => "$1ies",
        '/(hive)$/i'               => "$1s",
        '/(?:([^f])fe|([lr])f)$/i' => "$1$2ves",
        '/(shea|lea|loa|thie)f$/i' => "$1ves",
        '/sis$/i'                  => "ses",
        '/([ti])a$/i'              => "$1a",
        '/([ti])um$/i'             => "$1a",
        '/(buffal|tomat|potat|ech|her|vet)o$/i'=> "$1oes",
        '/(bu)s$/i'                => "$1ses",
        '/(alias|status)$/i'       => "$1es",
        '/(octop|vir)i$/i'         => "$1i",
        '/(octop|vir)us$/i'        => "$1i",
        '/(ax|test)is$/i'          => "$1es",
        '/(us)$/i'                 => "$1es",
        '/s$/i'                    => "s",
        '/$/'                      => "s"
    );

    static $singular = array(
        '/(database)s$/i'           => "$1",
        '/(quiz)zes$/i'             => "$1",
        '/(matr)ices$/i'            => "$1ix",
        '/(vert|ind)ices$/i'        => "$1ex",
        '/^(ox)en$/i'               => "$1",
        '/(alias|status)(es)?$/i'   => "$1",
        '/(octop|vir)i$/i'          => "$1us",
        '/^(a)x[ie]s$/i'            => "$1xis",
        '/(cris|ax|test)es$/i'      => "$1is",
        '/(cris|ax|test)is$/i'      => "$1is",
        '/(shoe|foe)s$/i'           => "$1",
        '/(bus)es$/i'               => "$1",
        '/^(toe)s$/i'               => "$1",
        '/(o)es$/i'                 => "$1",
        '/([m|l])ice$/i'            => "$1ouse",
        '/(x|ch|ss|sh)es$/i'        => "$1",
        '/(m)ovies$/i'              => "$1ovie",
        '/(s)eries$/i'              => "$1eries",
        '/([^aeiouy]|qu)ies$/i'     => "$1y",
        '/([lr])ves$/i'             => "$1f",
        '/(tive)s$/i'               => "$1",
        '/(hive)s$/i'               => "$1",
        '/(li|wi|kni)ves$/i'        => "$1fe",
        '/([^f])ves$/i'             => "$1fe",
        '/(shea|loa|lea|thie)ves$/i'=> "$1f",
        '/(^analy)(sis|ses)$/i'     => "$1sis",
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)(sis|ses)$/i'  => "$1$2sis",
        '/([ti])a$/i'               => "$1um",
        '/(n)ews$/i'                => "$1ews",
        '/(h|bl)ouses$/i'           => "$1ouse",
        '/(corpse)s$/i'             => "$1",
        '/(use)s$/i'                => "$1",
        '/s$/i'                     => ""
    );

    static $irregular = array(
        'zombie' => 'zombies',
        'move'   => 'moves',
        'cow'    => 'kine',
        'foot'   => 'feet',
        'goose'  => 'geese',
        'sex'    => 'sexes',
        'child'  => 'children',
        'man'    => 'men',
        'tooth'  => 'teeth',
        'person' => 'people'
    );

    static $uncountable = array(
        'sheep'       => true,
        'fish'        => true,
        'deer'        => true,
        'series'      => true,
        'species'     => true,
        'money'       => true,
        'rice'        => true,
        'information' => true,
        'equipment'   => true,
        'jeans'       => true,
        'police'      => true
    );

    private static $pluralCache = array();
    private static $singularCache = array();

    public static function pluralize($string)
    {
        if (!$string)
            return;

        if (!isset(self::$pluralCache[$string]))
        {
            // save some time in the case that singular and plural are the same
            if (isset(self::$uncountable[$string]))
            {
                self::$pluralCache[$string] = $string;
                return $string;
            }

            // check for irregular singular forms
            foreach (self::$irregular as $pattern => $result)
            {
                $pattern = '/' . $pattern . '$/i';

                if (preg_match($pattern, $string))
                {
                    self::$pluralCache[$string] = preg_replace($pattern, $result, $string);
                    return self::$pluralCache[$string];
                }
            }

            // check for matches using regular expressions
            foreach (self::$plural as $pattern => $result)
            {
                if (preg_match($pattern, $string))
                {
                    self::$pluralCache[$string] = $result = preg_replace($pattern, $result, $string);
                    return self::$pluralCache[$string];
                }
            }

            self::$pluralCache[$string] = $string;
        }
        return self::$pluralCache[$string];
    }

    public static function singularize($string)
    {
        if (!$string)
            return;

        if (!isset(self::$singularCache[$string]))
        {
            // save some time in the case that singular and plural are the same
            if (isset(self::$uncountable[strtolower($string)]))
            {
                self::$singularCache[$string] = $string;
                return $string;
            }
            // check for irregular plural forms
            foreach (self::$irregular as $result => $pattern)
            {
                $pattern = '/' . $pattern . '$/i';

                if (preg_match($pattern, $string))
                {
                    self::$singularCache[$string] = preg_replace($pattern, $result, $string);
                    return self::$singularCache[$string];
                }
            }

            // check for matches using regular expressions
            foreach (self::$singular as $pattern => $result)
            {
                if (preg_match($pattern, $string))
                {
                    self::$singularCache[$string] = preg_replace($pattern, $result, $string);
                    return self::$singularCache[$string];
                }
            }

            self::$singularCache[$string] = $string;
        }

        return self::$singularCache[$string];
    }

    public static function pluralizeIf($count, $string)
    {
        if ($count == 1)
            return "1 $string";
        else
            return "$count " . self::pluralize($string);
    }

    /**
     * Creates a url-safe verison of the string
     * @param $string The string to transform
     * @param $separator The separator to use to transform non word characters into (default '-')
     * @param $ampersand Transform '&' to this (e.g. could set this to 'and') (default null)
     */
    public static function urlify($string, $separator = '-', $ampersand = null)
    {
        // Romanization of alphabet
        // $string = strtr(...)
        $pairs = array(
"À" => 'A',
"Á" => 'A',
"Â" => 'A',
"Ã" => 'A',
"Ä" => 'A',
"Å" => 'A',
"Æ" => 'AE',
"Ç" => 'C',
"È" => 'E',
"É" => 'E',
"Ê" => 'E',
"Ë" => 'E',
"Ì" => 'I',
"Í" => 'I',
"Î" => 'I',
"Ï" => 'I',
"Ğ" => 'ETH',
"Ñ" => 'N',
"Ò" => 'O',
"Ó" => 'O',
"Ô" => 'O',
"Õ" => 'O',
"Ö" => 'O',
"Ø" => 'O',
"Ù" => 'U',
"Ú" => 'U',
"Û" => 'U',
"Ü" => 'U',
"İ" => 'Y',
"Ş" => 'THORN',
"ß" => 'sz',
"à" => 'a',
"á" => 'a',
"â" => 'a',
"ã" => 'a',
"ä" => 'a',
"å" => 'a',
"æ" => 'ae',
"ç" => 'c',
"è" => 'e',
"é" => 'e',
"ê" => 'e',
"ë" => 'e',
"ì" => 'i',
"í" => 'i',
"î" => 'i',
"ï" => 'i',
"ğ" => 'eth',
"ñ" => 'n',
"ò" => 'o',
"ó" => 'o',
"ô" => 'o',
"õ" => 'o',
"ö" => 'o',
"ø" => 'o',
"ù" => 'u',
"ú" => 'u',
"û" => 'u',
"ü" => 'u',
"ı" => 'y',
"ş" => 'thorn',
"ÿ" => 'y',
  );
        $string = strtr($string, $pairs);
        $string = trim($string);
        $string = strtolower($string);
        if ($and)
        { 
            $and = $separator . $and;
            $string = str_replace(array('\'', '&'), array('', $separator . $and . $separator), $string);
        }
        $string = preg_replace('/[^\w]+/', $separator, $string);
        $string = str_replace(array('_',$separator.$separator.$separator.$separator, $separator.$separator.$separator, $separator.$separator), $separator, $string);
        if ($and)
        { 
            $string = str_replace(array($and.$and.$and.$and.$separator, $and.$and.$and.$separator, $and.$and.$separator, $and.$separator.$and.$separator), $and.$separator, $string); 
        }

        if (!$string)
            $string = $separator;

        return $string;
    }      
}

