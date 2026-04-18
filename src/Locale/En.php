<?php

declare(strict_types=1);

namespace Inflect\Locale;

final class En extends Locale
{
    /** @var array<string, string> */
    protected const PLURAL = [
        '/(quiz)$/i'               => '$1zes',
        '/^(oxen)$/i'              => '$1',
        '/^(ox)$/i'                => '$1en',
        '/([m|l])ice$/i'           => '$1ice',
        '/([m|l])ouse$/i'          => '$1ice',
        '/(matr|vert|ind)ix|ex$/i' => '$1ices',
        '/(x|ch|ss|sh)$/i'         => '$1es',
        '/([^aeiouy]|qu)y$/i'      => '$1ies',
        '/(hive)$/i'               => '$1s',
        '/(?:([^f])fe|([lr])f)$/i' => '$1$2ves',
        '/(shea|lea|loa|thie)f$/i' => '$1ves',
        '/sis$/i'                  => 'ses',
        '/([ti])a$/i'              => '$1a',
        '/([ti])um$/i'             => '$1a',
        '/(buffal|tomat|potat|ech|her|vet)o$/i' => '$1oes',
        '/(bu)s$/i'                => '$1ses',
        '/(alias|status)$/i'       => '$1es',
        '/(octop|vir)i$/i'         => '$1i',
        '/(octop|vir)us$/i'        => '$1i',
        '/(ax|test)is$/i'          => '$1es',
        '/(us)$/i'                 => '$1es',
        '/s$/i'                    => 's',
        '/$/'                      => 's',
    ];

    /** @var array<string, string> */
    protected const SINGULAR = [
        '/(ss)$/i'                  => '$1',
        '/(database)s$/i'           => '$1',
        '/(quiz)zes$/i'             => '$1',
        '/(matr)ices$/i'            => '$1ix',
        '/(vert|ind)ices$/i'        => '$1ex',
        '/^(ox)en$/i'               => '$1',
        '/(alias|status)(es)?$/i'   => '$1',
        '/(octop|vir)i$/i'          => '$1us',
        '/^(a)x[ie]s$/i'            => '$1xis',
        '/(cris|ax|test)es$/i'      => '$1is',
        '/(cris|ax|test)is$/i'      => '$1is',
        '/(shoe|foe)s$/i'           => '$1',
        '/(bus)es$/i'               => '$1',
        '/^(toe)s$/i'               => '$1',
        '/(o)es$/i'                 => '$1',
        '/([m|l])ice$/i'            => '$1ouse',
        '/(x|ch|ss|sh)es$/i'        => '$1',
        '/(m)ovies$/i'              => '$1ovie',
        '/(s)eries$/i'              => '$1eries',
        '/([^aeiouy]|qu)ies$/i'     => '$1y',
        '/([lr])ves$/i'             => '$1f',
        '/(tive)s$/i'               => '$1',
        '/(hive)s$/i'               => '$1',
        '/(li|wi|kni)ves$/i'        => '$1fe',
        '/([^f])ves$/i'             => '$1fe',
        '/(shea|loa|lea|thie)ves$/i' => '$1f',
        '/(^analy)(sis|ses)$/i'     => '$1sis',
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)(sis|ses)$/i' => '$1$2sis',
        '/([ti])a$/i'               => '$1um',
        '/(n)ews$/i'                => '$1ews',
        '/(h|bl)ouses$/i'           => '$1ouse',
        '/(corpse)s$/i'             => '$1',
        '/(use)s$/i'                => '$1',
        '/s$/i'                     => '',
    ];

    /** @var array<string, string> */
    protected const IRREGULAR = [
        'zombie'     => 'zombies',
        'move'       => 'moves',
        'foot'       => 'feet',
        'goose'      => 'geese',
        'sex'        => 'sexes',
        'child'      => 'children',
        'man'        => 'men',
        'tooth'      => 'teeth',
        'person'     => 'people',
        'datum'      => 'data',
        'criterion'  => 'criteria',
        'phenomenon' => 'phenomena',
        'cactus'     => 'cacti',
        'nucleus'    => 'nuclei',
        'syllabus'   => 'syllabi',
        'curriculum' => 'curricula',
        'medium'     => 'media',
        'bacterium'  => 'bacteria',
    ];

    /** @var array<string, true> */
    protected const UNCOUNTABLE = [
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
        'police'      => true,
        'news'        => true,
        'aircraft'    => true,
        'software'    => true,
        'hardware'    => true,
        'luggage'     => true,
        'advice'      => true,
        'traffic'     => true,
        'furniture'   => true,
        'metadata'    => true,
        'multimedia'  => true,
    ];

    public function __construct()
    {
        $this->plural = self::PLURAL;
        $this->singular = self::SINGULAR;
        $this->irregular = self::IRREGULAR;
        $this->uncountable = self::UNCOUNTABLE;
    }
}
