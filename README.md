Inflect
=======

[![CI](https://github.com/mmucklo/inflect/actions/workflows/ci.yml/badge.svg)](https://github.com/mmucklo/inflect/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/mmucklo/inflect/branch/master/graph/badge.svg)](https://codecov.io/gh/mmucklo/inflect)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mmucklo/inflect/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mmucklo/inflect/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mmucklo/inflect/v)](https://packagist.org/packages/mmucklo/inflect)
[![License](https://poser.pugx.org/mmucklo/inflect/license)](https://packagist.org/packages/mmucklo/inflect)

Inflect is a memoizing inflector for PHP — pluralize, singularize, and format counted nouns for English.

## Requirements

- PHP **8.1+** (the legacy PHP 5.3 baseline is maintained on the [`php5.3`](https://github.com/mmucklo/inflect/tree/php5.3) branch for back-ports)

## Installation

```bash
composer require mmucklo/inflect
```

## Usage

```php
use Inflect\Inflect;

Inflect::pluralize('test');        // 'tests'
Inflect::singularize('tests');     // 'test'
Inflect::pluralizeIf(1, 'cat');    // '1 cat'
Inflect::pluralizeIf(3, 'person'); // '3 people'
```

Irregulars, uncountables, and case are handled automatically:

```php
Inflect::pluralize('Man');         // 'Men'
Inflect::pluralize('datum');       // 'data'
Inflect::pluralize('news');        // 'news'
Inflect::singularize('criteria');  // 'criterion'
Inflect::singularize('Children');  // 'Child'

// Double-inflection is a no-op:
Inflect::pluralize('people');      // 'people'
Inflect::singularize('datum');     // 'datum'
```

Results are memoized in a process-local static cache.

## API

### `Inflect::pluralize(string $string): string`
Returns the plural form of the given word. Empty input returns `''`.

### `Inflect::singularize(string $string): string`
Returns the singular form of the given word. Empty input returns `''`.

### `Inflect::pluralizeIf(int $count, string $string): string`
Returns `"$count $word"` with the word pluralized when `$count !== 1`.

Passing `null` to any of these methods raises a `TypeError` — callers should handle null explicitly.

## Versioning

Semantic versioning. The currently supported line is **2.x** (PHP 8.1+).

See [CHANGELOG.md](CHANGELOG.md) for release history and [ROADMAP.md](ROADMAP.md) for planned work (locale support, extension APIs, etc.).

## Upgrading from 1.x

2.0.0 is a breaking release. Highlights:

- PHP 8.1+ required (was 5.3.17+).
- `src/Inflect/Inflect.php` moved to `src/Inflect.php` (PSR-0 → PSR-4).
- `Inflect` is `final`; rule tables are `private`.
- `pluralize(null)` / `singularize(null)` now throw `TypeError`.
- `phpunit.xml-dist` renamed to `phpunit.xml.dist` (PHPUnit 10 convention).

Full list in the [v2.0.0 release notes](https://github.com/mmucklo/inflect/releases/tag/v2.0.0).

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for dev setup, running the test / static analysis / style suite, and PR conventions.

## Credits

Originally forked from Sho Kuwamoto's [improved pluralizer](http://kuwamoto.org/2007/12/17/improved-pluralizing-in-php-actionscript-and-ror/). Many thanks to Sho, and to the Ruby on Rails and Doctrine inflector projects from which rule patterns continue to be borrowed.

## License

MIT — see [LICENSE](LICENSE).
