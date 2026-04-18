<?php

declare(strict_types=1);

namespace Inflect\Locale;

abstract class Locale
{
    /** @var array<string, string> */
    protected array $plural = [];

    /** @var array<string, string> */
    protected array $singular = [];

    /** @var array<string, string> */
    protected array $irregular = [];

    /** @var array<string, true> */
    protected array $uncountable = [];

    /** @var array<string, string> */
    private array $pluralCache = [];

    /** @var array<string, string> */
    private array $singularCache = [];

    public function pluralize(string $string): string
    {
        if ($string === '') {
            return '';
        }

        if (!isset($this->pluralCache[$string])) {
            if (isset($this->uncountable[strtolower($string)])) {
                $this->pluralCache[$string] = $string;
                return $string;
            }

            foreach ($this->irregular as $plural) {
                if (strcasecmp($string, $plural) === 0) {
                    $this->pluralCache[$string] = $string;
                    return $string;
                }
            }

            foreach ($this->irregular as $pattern => $result) {
                $regex = '/' . $pattern . '$/i';

                if (preg_match($regex, $string)) {
                    $this->pluralCache[$string] = self::preserveFirstCase($string, preg_replace($regex, $result, $string) ?? $string);
                    return $this->pluralCache[$string];
                }
            }

            foreach ($this->plural as $pattern => $result) {
                if (preg_match($pattern, $string)) {
                    $this->pluralCache[$string] = preg_replace($pattern, $result, $string) ?? $string;
                    return $this->pluralCache[$string];
                }
            }

            $this->pluralCache[$string] = $string;
        }

        return $this->pluralCache[$string];
    }

    public function singularize(string $string): string
    {
        if ($string === '') {
            return '';
        }

        if (!isset($this->singularCache[$string])) {
            if (isset($this->uncountable[strtolower($string)])) {
                $this->singularCache[$string] = $string;
                return $string;
            }

            foreach ($this->irregular as $singular => $_plural) {
                if (strcasecmp($string, $singular) === 0) {
                    $this->singularCache[$string] = $string;
                    return $string;
                }
            }

            foreach ($this->irregular as $result => $pattern) {
                $regex = '/' . $pattern . '$/i';

                if (preg_match($regex, $string)) {
                    $this->singularCache[$string] = self::preserveFirstCase($string, preg_replace($regex, $result, $string) ?? $string);
                    return $this->singularCache[$string];
                }
            }

            foreach ($this->singular as $pattern => $result) {
                if (preg_match($pattern, $string)) {
                    $this->singularCache[$string] = preg_replace($pattern, $result, $string) ?? $string;
                    return $this->singularCache[$string];
                }
            }

            $this->singularCache[$string] = $string;
        }

        return $this->singularCache[$string];
    }

    public function addIrregular(string $singular, string $plural): void
    {
        $this->irregular[$singular] = $plural;
        $this->clearCache();
    }

    public function addUncountable(string $word): void
    {
        $this->uncountable[strtolower($word)] = true;
        $this->clearCache();
    }

    public function addPluralRule(string $pattern, string $replacement): void
    {
        $this->plural = [$pattern => $replacement] + $this->plural;
        $this->clearCache();
    }

    public function addSingularRule(string $pattern, string $replacement): void
    {
        $this->singular = [$pattern => $replacement] + $this->singular;
        $this->clearCache();
    }

    private function clearCache(): void
    {
        $this->pluralCache = [];
        $this->singularCache = [];
    }

    private static function preserveFirstCase(string $source, string $replaced): string
    {
        if ($source !== '' && $replaced !== '' && ctype_upper($source[0]) && ctype_lower($replaced[0])) {
            return ucfirst($replaced);
        }

        return $replaced;
    }
}
