# Contributing

Thanks for taking the time to contribute. This document describes how to set up a development environment, run the test/quality suite locally, and submit changes.

## Development setup

```bash
git clone git@github.com:mmucklo/inflect.git
cd inflect
composer install
```

Requires **PHP 8.1+** for development. Release CI runs the full matrix (PHP 8.1 / 8.2 / 8.3 / 8.4).

## Running the suite

```bash
vendor/bin/phpunit                        # tests
vendor/bin/phpstan analyse                # static analysis (level 8)
vendor/bin/php-cs-fixer fix --dry-run     # style check
vendor/bin/php-cs-fixer fix               # apply style fixes
```

All four must pass before a PR can merge. CI runs PHPUnit on every PHP matrix leg and runs PHPStan + PHP-CS-Fixer on the 8.3 leg.

## Submitting changes

1. **Open a feature branch** off `master`. Do not commit directly to `master`.
2. **Write tests.** New inflections go in `singularizeProvider` / `pluralizeProvider` in `tests/InflectTest.php`. Edge cases (double-inflection guards, case preservation, uncountables) go inline as separate `#[DataProvider]` cases — see the existing patterns.
3. **Keep PRs focused.** One concern per PR. Roadmap items in `ROADMAP.md` are reasonable PR boundaries.
4. **Open a PR against `master`.** Reference the roadmap section it addresses (e.g. "Addresses §7").

### Branch conventions

- `master` — active development, PHP 8.1+.
- `php5.3` — back-port branch for critical fixes on the legacy PHP baseline. Do not land forward-looking work here. When a fix is applicable to both, cherry-pick to a sibling `foo-php5.3` branch and open a second PR targeting `php5.3`.

### Commit messages

Short imperative subject line, then a blank line, then a paragraph or two of context. Explain *why*, not just *what* — the diff shows the what.

### Writing inflection rules

Adding a new irregular or regex rule has sharp edges — a rule that "works for the word I tested" often breaks an existing test. Before adding one:

1. Confirm the word isn't already handled correctly by an existing regex.
2. Test both directions: if you add `X → Y` to `$irregular`, `singularize('Y')` and `pluralize('X')` must both round-trip, *and* unrelated words like `indie` must not accidentally match the suffix.
3. Add a test case with the input/expected in the appropriate provider.

## Reporting bugs

File an issue with:
- PHP version (`php -v`)
- Package version (`composer show mmucklo/inflect`)
- The input that produced the wrong output, the output you got, and the output you expected
- A short snippet that reproduces (ideally a failing test case)

## Roadmap

See `ROADMAP.md` for planned work. Items there are open for contribution — pick one, reply on the relevant issue or open a draft PR to start the conversation.
