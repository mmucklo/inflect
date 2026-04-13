# Changelog

All notable changes to this project are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- PHPStan level 8 and PHP-CS-Fixer wired into CI. ([#18])
- Expanded README, Keep-a-Changelog `CHANGELOG.md`, and `CONTRIBUTING.md`.

### Fixed
- Type safety gaps found by PHPStan level 8: `@var` generics on all static rule tables and caches; fall back to input string when `preg_replace` returns `null`. ([#18])

## [2.0.0] — 2026-04-13

Major release. Modernizes the PHP baseline, ports tests to PHPUnit 10, moves to PSR-4, adds GitHub Actions CI with Codecov and Scrutinizer, tightens types, and expands correctness.

### ⚠️ Breaking
- **PHP 8.1+ required** (was PHP 5.3.17+). Stay on the [`php5.3`](https://github.com/mmucklo/inflect/tree/php5.3) branch for back-ported fixes to the legacy baseline. ([#12])
- `src/Inflect/Inflect.php` moved to `src/Inflect.php`. Autoload changed from PSR-0 to PSR-4. `autoload.php.dist` removed. ([#12])
- `Inflect` class is now `final`. ([#14])
- Static rule tables (`$plural`, `$singular`, `$irregular`, `$uncountable`) and memoization caches are now `private`. External code reading or mutating them will break. ([#14])
- `pluralize(null)` and `singularize(null)` now throw `TypeError`. Empty strings still return `''`. ([#14])
- `pluralizeIf` uses strict comparison (`===`) for the count check. ([#14])
- `phpunit.xml-dist` renamed to `phpunit.xml.dist`. ([#12])

### Added
- GitHub Actions CI matrix on PHP **8.1 / 8.2 / 8.3 / 8.4**. ([#13])
- Codecov integration (98.07% coverage on master). ([#13], [#16])
- Scrutinizer integration. ([#13], [#17])
- 9 new irregular inflections: `datum/data`, `criterion/criteria`, `phenomenon/phenomena`, `cactus/cacti`, `nucleus/nuclei`, `syllabus/syllabi`, `curriculum/curricula`, `medium/media`, `bacterium/bacteria`. ([#9])
- 10 new uncountables: `news`, `aircraft`, `software`, `hardware`, `luggage`, `advice`, `traffic`, `furniture`, `metadata`, `multimedia`. ([#9])
- Case preservation on irregular transforms: `pluralize('Man') === 'Men'`, `singularize('Children') === 'Child'`. ([#9])
- Double-inflection guard: `pluralize('people') === 'people'`, `singularize('datum') === 'datum'`. ([#9])
- Case-insensitive uncountable lookup in `pluralize()` (matches `singularize()` behavior). ([#9])
- `declare(strict_types=1)`, typed parameters and return types on all public methods. ([#14])
- Typed static properties (PHP 7.4+ syntax). ([#14])
- `ROADMAP.md` documenting planned improvements and the locale-support path. ([#8], [#11], [#15])

### Changed
- PHPUnit upgraded from `3.7.*` to `^10.5`. Tests ported to namespaced `PHPUnit\Framework\TestCase`, `#[DataProvider]` attributes, strict types. ([#12])
- Composer autoload switched from PSR-0 to PSR-4. Tests live in the `Inflect\Tests` namespace. ([#12])
- Short array syntax (`[]`) throughout the class. ([#14])
- Issue #4 fix (singularization of `-ss` words like `business`, `wellness`) carried forward.

### Removed
- `autoload.php.dist` (obsoleted by Composer PSR-4 autoload).
- `.travis.yml` (replaced by GitHub Actions).

## Pre-2.0

Prior changes were tracked informally in the freeform `CHANGELOG` file (now removed). Selected history:

- **2013-02-12** — First draft of unit tests for singularize; update some expressions.
- **2013-02-08** — Update inflection singular, plural and non-inflectable rules from RoR.
- **2009-05-22** — Add static caching (memoization).
- **2008-12-18** — Forked from [Sho Kuwamoto's pluralizer](http://kuwamoto.org/2007/12/17/improved-pluralizing-in-php-actionscript-and-ror/) (MIT).

Thanks to [eval.ca's php-pluralize](http://www.eval.ca/articles/php-pluralize), [Ruby on Rails ActiveSupport::Inflections](https://github.com/rails/rails/blob/main/activesupport/lib/active_support/inflections.rb), and reference works on English pluralization.

<!-- PR links -->
[#8]: https://github.com/mmucklo/inflect/pull/8
[#9]: https://github.com/mmucklo/inflect/pull/9
[#11]: https://github.com/mmucklo/inflect/pull/11
[#12]: https://github.com/mmucklo/inflect/pull/12
[#13]: https://github.com/mmucklo/inflect/pull/13
[#14]: https://github.com/mmucklo/inflect/pull/14
[#15]: https://github.com/mmucklo/inflect/pull/15
[#16]: https://github.com/mmucklo/inflect/pull/16
[#17]: https://github.com/mmucklo/inflect/pull/17
[#18]: https://github.com/mmucklo/inflect/pull/18

<!-- Version diffs -->
[Unreleased]: https://github.com/mmucklo/inflect/compare/v2.0.0...HEAD
[2.0.0]: https://github.com/mmucklo/inflect/releases/tag/v2.0.0
