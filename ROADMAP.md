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

### Prior art in this account

Two places in `mmucklo/*` have already reached for this design:

- **`mmucklo/inflect@feature/inflections`** (WIP, ~2014). Sketched `new Inflect($locale = 'en')`, a `src/Inflections/En.php` class holding rules as `protected static` arrays, and a PSR-4 layout change. Incomplete — tests were dropped and the branch predates all correctness fixes now on `master`. Design reference only, not a merge base.
- **`mmucklo/inflections`** (separate repo, last touched 2017-06-13). Purpose-built as a rule-data package for inflectors. Layout is `src/En/Plural.php`, `src/En/Singular.php`, `src/En/Uninflected.php`, each class exposing `public static $rules`, `$uninflected`, `$irregular`, and a `$version` stub. Rule set is substantially richer than what `inflect` currently carries, sourced from Doctrine Inflector + Rails. Stale and in need of modernization (PHP 5.6, PHPUnit 5.7, Travis) but the architecture is exactly the engine/data split this roadmap is moving toward.

### Two paths

**Path A — vendor the rule data into `inflect`.** Introduce `src/Inflections/En.php` inside this repo, implementing a `Locale` contract. Simpler: one repo, one release cadence, no external dependency. Loses the package-boundary benefits but keeps everything local while locale support is young.

**Path B — revive `mmucklo/inflections` as a sibling package.** Modernize that repo (PHP 8.1+, PHPUnit 10, GH Actions, private-or-read-only state), define a `Locale` contract it implements, and have `inflect` require it. This is the architecturally right answer:

- Rule data and engine version independently — a new irregular in English doesn't force an engine release, and an engine bugfix doesn't republish rule data.
- Third-party locale packages don't need to land in this repo. `someone/inflections-pl` implementing the `Locale` contract works out of the box.
- Rule data becomes reusable by other PHP inflectors (Doctrine, Symfony String port, etc. — today each duplicates the same lists).
- Cleaner test boundary: rule-data tests prove regexes are well-formed; engine tests prove inflection outcomes.

The cost is real: two CI pipelines, two changelogs, two Packagist pages, two Dependabot surfaces. Worth paying only if third-party locale packages actually materialize.

### Recommendation

Path A for v2.2 (ship faster, one repo to reason about). Re-evaluate the split for v3.x once there's evidence of external locale demand. Keep the internal `Locale` contract clean enough that moving from A to B later is a file relocation, not a rewrite.

### Concrete deliverables (Path A, v2.2)

- Introduce a `Locale` / `Inflections` contract (plural rules, singular rules, irregulars, uncountables).
- Ship `Inflect\Inflections\En` as the built-in implementation, seeded from the richer rule set in `mmucklo/inflections/En` rather than the minimal one currently in `Inflect`.
- Keep the static `Inflect::pluralize()` / `Inflect::singularize()` delegating to `En` for backwards compatibility.
- Add an instance API (`new Inflect('en')`, `$inflect->pluralize(...)`) so callers can pick a locale without touching globals.
- Add at least one additional locale as proof of concept — candidates: `Es` (Spanish), `Fr` (French), `De` (German). Each has distinct pluralization patterns (e.g. Spanish `-es` vs `-s` depending on final consonant, French silent-consonant rules, German umlaut + plural suffix classes).
- Document how to register a third-party locale (fits with the extensibility APIs in §5).

### Deferred to Path B (v3.x, conditional)

- Extract `Inflect\Inflections\*` into `mmucklo/inflections` (modernized) and add it as a `composer require` of `inflect`.
- Drop the bundled locales from `inflect` (or keep `En` as a fallback only).
- Publish the `Locale` contract as a PHP interface that sibling packages can implement.
- Trigger: either (a) a third party asks to publish a locale, (b) rule data starts changing on a cadence that diverges from engine releases, or (c) another inflector library expresses interest in consuming the rule data.

### Open design questions to resolve before implementing Path A

- Should locale resolution be lazy (load rule class on first call) or eager?
- How should the static API choose a default locale — class constant, env var, or setter? The `feature/inflections` constructor `new Inflect($locale = 'en')` only addresses the instance case.
- Does per-locale caching share a namespace, or is each locale's cache isolated?
- Should the rule-table properties on `En` be `private` (matches the §3 tightening in `Inflect`) or `public` (matches the `mmucklo/inflections` convention, designed for external extension)? Leaning `private` with explicit extension APIs per §5.

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
