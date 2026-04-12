# Roadmap

Planned improvements to `mmucklo/inflect`. Items are grouped by theme and phased into releases at the bottom.

The `php5.3` branch is maintained for back-porting critical fixes to environments still on the legacy PHP baseline. All forward-looking work happens on `master`.

## 1. Modernize the PHP baseline

- Bump `composer.json` `require` to `"php": ">=8.1"`.
- Migrate autoloading from PSR-0 to **PSR-4**; drop `autoload.php.dist`.
- Upgrade PHPUnit from `3.7.*` to `^10`.
- Port tests to namespaced `PHPUnit\Framework\TestCase` with data providers (replace `print` + `foreach` loops).

## 2. Replace Travis with GitHub Actions

- Delete `.travis.yml`.
- Add `.github/workflows/ci.yml` running a matrix of PHP 8.1 / 8.2 / 8.3 / 8.4.
- Run tests, static analysis, and style checks on every push and PR.

## 3. Type safety & API

- Add `string` param/return types and `declare(strict_types=1)`.
- Mark `Inflect` as `final`.
- Decide on instance API vs. all-static. Static caches (`$pluralCache`, `$singularCache`) grow unbounded in long-running processes — consider per-instance cache with an optional size cap (LRU or count-based eviction).
- `pluralize(null)` currently returns `null` silently — prefer nullable types or throwing.

## 4. Correctness gaps

- Expand irregulars: `mouse→mice`, `louse→lice`, `die→dice`, `datum→data`, `criterion→criteria`, `phenomenon→phenomena`, `index→indices`, `appendix→appendices`, `cactus→cacti`, `nucleus→nuclei`, `syllabus→syllabi`, `curriculum→curricula`, `medium→media`, `bacterium→bacteria`.
- Expand uncountables: `news`, `aircraft`, `software`, `hardware`, `luggage`, `advice`, `traffic`, `furniture`.
- Preserve case: `Business` currently normalizes to `business`; the original capitalization should be retained.
- Fix `pluralizeIf(0, ...)` — today it pluralizes irregularly (e.g. "0 mouses").

## 5. Extensibility

- Add runtime registration APIs: `addIrregular()`, `addUncountable()`, `addPluralRule()`, `addSingularRule()`. Users currently cannot extend rules without editing the class.
- Consider a locale-hook so rule sets can be swapped (English-only today, but leave room in the API).

## 6. Documentation

- Expand `README.md`: installation, full API surface, examples, extension hooks, supported PHP versions, and badges (Packagist, CI, license).
- Add `CONTRIBUTING.md`.
- Convert freeform `CHANGELOG` to Keep-a-Changelog format (`CHANGELOG.md`).

## 7. Tooling

- `phpstan` at level 8.
- `php-cs-fixer` or `pint` with PSR-12.
- `infection` for mutation testing — brittle regex rules benefit from mutation coverage.
- `phpbench` benchmarks — this is a *memoizing* inflector, so performance is part of the pitch.

## 8. Release hygiene

- Tag `2.0.0` after the PHP 8 bump (breaking change).
- Keep a `1.x` branch for legacy bug fixes in addition to the `php5.3` branch.
- Update Packagist metadata accordingly.

## Branching

- `master` — active development, targets modern PHP.
- `php5.3` — back-port branch for critical fixes on the legacy PHP baseline. Do not merge forward-looking work here.

## Phasing

- **v2.0** — items 1, 2, 3 (breaking, one release).
- **v2.1** — items 4, 5 (additive).
- **ongoing** — items 6, 7, 8.
