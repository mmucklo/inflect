Inflect
=======

[![CI](https://github.com/mmucklo/inflect/actions/workflows/ci.yml/badge.svg)](https://github.com/mmucklo/inflect/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/mmucklo/inflect/branch/master/graph/badge.svg)](https://codecov.io/gh/mmucklo/inflect)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mmucklo/inflect/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mmucklo/inflect/?branch=master)

Inflect is an Inflector for PHP

Installation:
-------------
Add this line to your composer.json "require" section:

### composer.json
```json
    "require": {
       ...
       "mmucklo/inflect": "*"
```

Usage:
------

```php
use Inflect\Inflect;

echo Inflect::singularize('tests');
echo Inflect::pluralize('test');
```

Notes:
------

Many thanks to original author Sho Kuwamoto"
