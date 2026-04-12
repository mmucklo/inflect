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

Largely shipped in #9 / #10. Remaining items:

- Case preservation on the regex path (only the irregular path preserves case today; regex backreferences happen to preserve case in practice, but there is no guarantee for all-caps input like `BUSINESSES`).
- Audit additional irregulars that are *suffix-safe* — `mouse/louse`, `index/appendix` are already handled correctly by regex; `die/dice` was rejected because its suffix match would break `indie → indice`.

## 5. Extensibility

- Add runtime registration APIs: `addIrregular()`, `addUncountable()`, `addPluralRule()`, `addSingularRule()`. Users currently cannot extend rules without editing the class.

## 5a. Locale-based inflections

First-class support for multiple languages, not just English. All current rules (`$plural`, `$singular`, `$irregular`, `$uncountable`) are hard-coded English — the API should let a caller pick a locale at construction time and load the matching rule set.

Prior exploration lives on the `feature/inflections` branch (WIP from 2014-ish). It sketched:

- A constructor `new Inflect($locale = 'en')` alongside the existing static API.
- A `src/Inflections/En.php` class holding the English rules as `protected static` arrays.
- Moving `Inflect` up from `src/Inflect/Inflect.php` to `src/Inflect.php` (PSR-4 layout change).

That branch is incomplete (tests were dropped, ROADMAP not yet present) and predates the correctness fixes now on `master`. Use it as design reference, not as a merge base.

Concrete deliverables:

- Introduce a `Locale` / `Inflections` contract (plural rules, singular rules, irregulars, uncountables).
- Ship `En` as the built-in implementation; keep the static `Inflect::pluralize()` / `Inflect::singularize()` delegating to `En` for backwards compatibility.
- Add an instance API (`new Inflect('en')`, `$inflect->pluralize(...)`) so callers can pick a locale without touching globals.
- Add at least one additional locale as proof of concept — candidates: `Es` (Spanish), `Fr` (French), `De` (German). Each has distinct pluralization patterns (e.g. Spanish `-es` vs `-s` depending on final consonant, French silent-consonant rules, German umlaut + plural suffix classes).
- Document how to register a third-party locale package (fits with the extensibility APIs in §5).

Open design questions to resolve before implementation:

- Should locale resolution be lazy (load rule class on first call) or eager?
- How should the static API choose a default locale — class constant, env var, or setter? The branch's `new Inflect($locale = 'en')` signature only addresses the instance case.
- Does per-locale caching share a namespace, or is each locale's cache isolated?

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
- **v2.1** — remaining items in 4, plus 5 (additive).
- **v2.2** — item 5a (locale-based inflections; introduces new API surface but is additive).
- **ongoing** — items 6, 7, 8.
