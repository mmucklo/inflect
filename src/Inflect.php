<?php

declare(strict_types=1);

namespace Inflect;

use Inflect\Locale\En;
use Inflect\Locale\Locale;

final class Inflect
{
    private Locale $locale;

    private static ?Locale $defaultLocale = null;

    /** @var array<string, class-string<Locale>|Locale> */
    private static array $registry = [
        'en' => En::class,
    ];

    public function __construct(Locale|string $locale = 'en')
    {
        $this->locale = $locale instanceof Locale ? $locale : self::resolve($locale);
    }

    // -- Static back-compat API (delegates to shared default En instance) --

    public static function pluralize(string $string): string
    {
        return self::defaultLocale()->pluralize($string);
    }

    public static function singularize(string $string): string
    {
        return self::defaultLocale()->singularize($string);
    }

    public static function pluralizeIf(int $count, string $string): string
    {
        if ($count === 1) {
            return "1 $string";
        }

        return "$count " . self::pluralize($string);
    }

    public static function addIrregular(string $singular, string $plural): void
    {
        self::defaultLocale()->addIrregular($singular, $plural);
    }

    public static function addUncountable(string $word): void
    {
        self::defaultLocale()->addUncountable($word);
    }

    public static function addPluralRule(string $pattern, string $replacement): void
    {
        self::defaultLocale()->addPluralRule($pattern, $replacement);
    }

    public static function addSingularRule(string $pattern, string $replacement): void
    {
        self::defaultLocale()->addSingularRule($pattern, $replacement);
    }

    /**
     * @param class-string<Locale>|Locale $localeOrClass
     */
    public static function registerLocale(string $name, Locale|string $localeOrClass): void
    {
        self::$registry[strtolower($name)] = $localeOrClass;
    }

    // -- Instance API --

    public function plural(string $string): string
    {
        return $this->locale->pluralize($string);
    }

    public function singular(string $string): string
    {
        return $this->locale->singularize($string);
    }

    public function pluralIf(int $count, string $string): string
    {
        if ($count === 1) {
            return "1 $string";
        }

        return "$count " . $this->locale->pluralize($string);
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    // -- Internal --

    private static function defaultLocale(): Locale
    {
        return self::$defaultLocale ??= new En();
    }

    private static function resolve(string $name): Locale
    {
        $key = strtolower($name);

        if (!isset(self::$registry[$key])) {
            throw new \InvalidArgumentException("Unknown locale: $name. Register it with Inflect::registerLocale().");
        }

        $entry = self::$registry[$key];

        if ($entry instanceof Locale) {
            return $entry;
        }

        $locale = new $entry();
        self::$registry[$key] = $locale;

        return $locale;
    }
}
