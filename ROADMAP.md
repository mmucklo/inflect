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
- **Where the methods live**: on `Locale` instances (see §5a design — extension is per-locale, not global). `Inflect::addIrregular(...)` is provided as a back-compat proxy that mutates the shared default `En` instance.
- Each extension method must invalidate the instance's memoization cache to avoid stale lookups.
- §5 ships together with §5a in v2.1; they share the same API surface.

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

### Design (resolved)

Decisions below resolve the open questions and lock the Path A design before implementation.

**1. The `Locale` contract.**

`Inflect\Locale\Locale` is an **abstract class** (not a bare interface) that holds rule tables as `protected` instance state and provides a concrete regex-rule engine as its `pluralize()` / `singularize()` implementation. Subclasses override the rule tables; the engine is shared.

```php
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

    public function pluralize(string $string): string   { /* shared engine */ }
    public function singularize(string $string): string { /* shared engine */ }

    public function addIrregular(string $singular, string $plural): void;
    public function addUncountable(string $word): void;
    public function addPluralRule(string $pattern, string $replacement): void;
    public function addSingularRule(string $pattern, string $replacement): void;
}
```

An escape hatch — a `Locale` interface — can be introduced later for languages whose morphology doesn't fit the regex-rule-list model. Not in v2.1 scope.

**2. Rule-table visibility.**

`protected` on the abstract base. Not `private` — subclasses need to seed them. Not `public` — we moved the v2.0 class away from `public static` mutable state and aren't reintroducing it. The extension API (§5) is the supported mutation path.

**3. Seeding rules on subclasses.**

Subclasses populate their rule tables in the constructor, seeding from `protected const` class constants. This keeps the defaults introspectable without exposing mutable shared state:

```php
final class En extends Locale
{
    protected const PLURAL    = [/* regex => replacement */];
    protected const SINGULAR  = [/* ... */];
    protected const IRREGULAR = [/* singular => plural */];
    protected const UNCOUNTABLE = [/* word => true */];

    public function __construct()
    {
        $this->plural      = self::PLURAL;
        $this->singular    = self::SINGULAR;
        $this->irregular   = self::IRREGULAR;
        $this->uncountable = self::UNCOUNTABLE;
    }
}
```

Rule set seeded from `mmucklo/inflections/En` (substantially richer than current `Inflect` rules).

**4. Caching.**

Per-instance `$pluralCache` / `$singularCache` on each `Locale` instance. No shared global cache. Rationale: extension methods mutate instance state; a shared cache would have to be invalidated across unrelated instances. Per-instance caching makes ownership clean.

The static `Inflect::pluralize()` / `singularize()` uses a lazily-initialized **shared default `En` instance** (one per process) — so the common case still memoizes across calls.

**5. Default locale (static API).**

The static `Inflect::pluralize()` / `singularize()` always delegates to a shared `En` instance. **No global mutable default-locale setter.** Apps that want non-English use the instance API. Rationale: `Inflect::setDefaultLocale('fr')` at boot would change the meaning of every downstream `Inflect::pluralize()` call — a classic "action-at-a-distance" footgun we're choosing not to add.

**6. Instance API.**

```php
$en = new Inflect();                      // default 'en'
$fr = new Inflect('fr');                  // resolved via locale registry
$custom = new Inflect(new CustomLocale()); // pass-through

$en->pluralize('cat');                     // 'cats'
$en->addIrregular('platypus', 'platypuses');
```

Constructor signature: `public function __construct(Locale|string $locale = 'en')`.

Locale registry: `Inflect::registerLocale(string $name, Locale|class-string<Locale> $localeOrClass): void`. Ships with `'en'` pre-registered, mapped to `Inflect\Locale\En`. Accepts a class-string so registration is cheap (lazy instantiation — the instance is only created on first use).

**7. Locale resolution timing.**

**Lazy.** The locale registry maps names to class-strings; the instance is constructed on first `new Inflect('fr')` (or first static-API call, which forces `En`). Avoids pulling every registered locale into memory up-front.

**8. Back-compat.**

All v2.0 static methods (`pluralize`, `singularize`, `pluralizeIf`) keep their signatures. Internally they become:

```php
public static function pluralize(string $string): string
{
    return self::defaultLocale()->pluralize($string);
}

private static function defaultLocale(): Locale
{
    return self::$defaultLocale ??= new En();
}
```

Existing callers see zero behavior change. New callers can pick the instance API when they need isolation or a non-English locale.

Proxy extension methods (`Inflect::addIrregular(...)`) mutate the shared default `En` instance and invalidate its cache.

## 6. Documentation

Largely shipped in #19. Remaining:

- Document the instance API and the locale extension surface once §5a lands.
- Add an examples section with ten concrete pluralize/singularize/pluralizeIf snippets (current README has ~five).

## 7. Tooling

Shipped in #18:

- `phpstan` at level 8.
- `php-cs-fixer` with `@PSR12`.

Deferred (separate PRs):

- `infection` for mutation testing — brittle regex rules benefit from mutation coverage.
- `phpbench` benchmarks — this is a *memoizing* inflector, so performance is part of the pitch.

## 8. Release hygiene

- **v2.0.0 tagged** (2026-04-13). See the [release notes](https://github.com/mmucklo/inflect/releases/tag/v2.0.0).
- Maintain a `1.x` branch for legacy bug fixes alongside the `php5.3` branch if demand appears. (Not created yet — no 1.x users have surfaced.)
- Packagist auto-detects tags; metadata up to date.

## Branching

- `master` — active development, targets modern PHP.
- `php5.3` — back-port branch for critical fixes on the legacy PHP baseline. Do not merge forward-looking work here.

## Phasing

- **v2.0** — items 1, 2, 3 (breaking). **Shipped 2026-04-13.**
- **v2.1** — items 5 + 5a landed together (the extension API lives on `Locale`; splitting them would be churn). Adds the instance API, `Locale` abstract class, `En` as first-party locale, and proxy extension methods on `Inflect`. Additive, no breaking changes.
- **v2.2** — at least one non-English locale (candidate: `Es`, `Fr`, or `De`) as proof the `Locale` contract holds for non-trivial morphology. Possibly `infection` + `phpbench` tooling if not done earlier.
- **v3.x (conditional)** — Path B: extract `Inflect\Locale\*` into `mmucklo/inflections` as a sibling package. Triggers are listed in §5a.

## Beyond v3.x — strategic directions

Four themes for where the library could go once v3.x stabilizes. Not commitments — captured here so the decision space is explicit when we get there. Ranked roughly by how much each would change the library's identity.

### 9. Cross the binary-plural ceiling (CLDR plural categories)

The current API assumes **singular / plural is a binary**. It isn't in half the world's languages:

- Russian uses three countable forms (`1 книга`, `2 книги`, `5 книг`).
- Welsh uses four. Arabic uses six. Polish, Romanian, Lithuanian each have their own category rules.
- Unicode's [Common Locale Data Repository](https://cldr.unicode.org/) defines six categories (`zero`, `one`, `two`, `few`, `many`, `other`). PHP already ships `ext-intl` with CLDR plural rules built in.

**Concrete API direction:**

```php
// Today
Inflect::pluralizeIf(5, 'book');  // '5 books' — English-only assumption

// Future
$inflect->pluralForm(5, [
    'one' => 'book',
    'few' => 'books',
    'many' => 'books',
    'other' => 'books',
]);                               // returns 'books'; locale-aware category lookup
```

Locales delegate category resolution to `ext-intl` (which consults CLDR) rather than hand-maintained regex tables. This is the single move that turns the library from "English inflector with locale hooks" into a genuinely multilingual tool, while converging with a maintained external standard.

**Scope:** one new method, one category enum, locale implementations that call `MessageFormatter::formatMessage` or `NumberFormatter` for the category.

### 10. Expand from nouns to morphology

Today the library handles noun singular ↔ plural (plus `pluralizeIf` cosmetic prefixing). A full morphological toolkit would add:

- **Verb conjugation** — `conjugate('run', tense: 'past') === 'ran'`. Rails has this surface; irregular-verb tables are ~200 words per language.
- **Indefinite articles** — `indefiniteArticle('apple') === 'an'`. Locale-specific (French `à le → au`).
- **Ordinals** — `ordinalize(3) === '3rd'`. Bounded, per-locale.
- **Case / gender agreement** — required for real German / Slavic support. API needs `$gender`, `$case` parameters. Big cognitive-load bump; may not be worth it if the target audience is Rails-refugees rather than NLP users.

Where this theme stops determines whether the library stays a "small useful utility" or becomes a "morphological toolkit." Both are legitimate products; they attract different users.

### 11. Locale data quality

Regex-rule inflectors lose on unseen words — loanwords, coinages, compounds. Two ways to push the accuracy ceiling:

- **Test corpora per locale.** Ship `(lemma, form, features)` triples from a known-good source ([Wiktionary](https://en.wiktionary.org/) dumps, [UniMorph](https://unimorph.github.io/)) and run the inflector against them in CI with an accuracy metric. Rule additions become measurable — "this regex lifts English noun accuracy from 92.3% → 94.1% on UniMorph v1.2." Turns inflection from folklore into engineering.
- **Offline ML fallback.** When regex rules don't match, fall back to a small byte-level seq2seq model via ONNX Runtime + FFI. Heavy dependency story; probably a separate opt-in `mmucklo/inflect-neural` package. The accuracy ceiling jumps, at the cost of a binary artifact.

### 12. Ecosystem moves (zero new features, large adoption impact)

- **Symfony / Laravel bridges** — first-party integration packages (`mmucklo/inflect-bundle`, `mmucklo/inflect-laravel`) that register the inflector in each framework's service container with one `composer require`. Biggest adoption lift per hour of work — both frameworks ship their own inflectors today and users would otherwise have to wire Inflect in manually.
- **Composer-plugin locale discovery** — third-party locale packages (`someone/inflect-pl`, `acme/inflect-fr-quebec`) auto-register on install through a [composer-plugin](https://getcomposer.org/doc/articles/plugins.md). Adding a locale becomes a one-liner for consumers.
- **Benchmark-as-identity** — this library's pitch is "*memoizing* inflector." Publish concrete numbers (via `phpbench`, roadmap §7) vs Doctrine Inflector and Symfony String on the README, committed to never regressing them. Makes the performance claim verifiable instead of rhetorical.

### Headline recommendation

If we pick only one of these four: **§9 (CLDR plural categories).** Scoped, rides on a maintained external standard (Unicode CLDR), doesn't change the library's identity — but lifts its ceiling from "English-ish" to "genuinely multilingual." The other themes turn Inflect into a different product; §9 makes the current product complete.
