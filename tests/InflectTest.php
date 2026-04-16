<?php

declare(strict_types=1);

namespace Inflect\Tests;

use Inflect\Inflect;
use Inflect\Locale\En;
use Inflect\Locale\Locale;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class InflectTest extends TestCase
{
    #[DataProvider('singularizeProvider')]
    public function testSingularize(string $input, string $expected): void
    {
        $this->assertSame($expected, Inflect::singularize($input));
    }

    #[DataProvider('pluralizeProvider')]
    public function testPluralize(string $input, string $expected): void
    {
        $this->assertSame($expected, Inflect::pluralize($input));
    }

    public function testEmptyStringReturnsEmptyString(): void
    {
        $this->assertSame('', Inflect::pluralize(''));
        $this->assertSame('', Inflect::singularize(''));
    }

    #[DataProvider('pluralizeIfProvider')]
    public function testPluralizeIf(int $count, string $noun, string $expected): void
    {
        $this->assertSame($expected, Inflect::pluralizeIf($count, $noun));
    }

    /**
     * @return array<int, array{int, string, string}>
     */
    public static function pluralizeIfProvider(): array
    {
        return [
            [1, 'cat', '1 cat'],
            [2, 'cat', '2 cats'],
            [0, 'cat', '0 cats'],
            [1, 'person', '1 person'],
            [3, 'person', '3 people'],
            [0, 'person', '0 people'],
            [5, 'datum', '5 data'],
        ];
    }

    /**
     * @return array<int, array{string, string}>
     */
    public static function singularizeProvider(): array
    {
        return [
            ['ox', 'ox'],
            ['oxen', 'ox'],
            ['cats', 'cat'],
            ['purses', 'purse'],
            ['analyses', 'analysis'],
            ['houses', 'house'],
            ['sheep', 'sheep'],
            ['buses', 'bus'],
            ['uses', 'use'],
            ['databases', 'database'],
            ['quizzes', 'quiz'],
            ['matrices', 'matrix'],
            ['vertices', 'vertex'],
            ['alias', 'alias'],
            ['aliases', 'alias'],
            ['octopi', 'octopus'],
            ['axes', 'axis'],
            ['axis', 'axis'],
            ['crises', 'crisis'],
            ['crisis', 'crisis'],
            ['shoes', 'shoe'],
            ['foes', 'foe'],
            ['pianos', 'piano'],
            ['wierdos', 'wierdo'],
            ['toes', 'toe'],
            ['banjoes', 'banjo'],
            ['vetoes', 'veto'],
            ['cows', 'cow'],
            ['businesses', 'business'],
            ['business', 'business'],
            ['wellness', 'wellness'],
            ['data', 'datum'],
            ['criteria', 'criterion'],
            ['phenomena', 'phenomenon'],
            ['cacti', 'cactus'],
            ['nuclei', 'nucleus'],
            ['syllabi', 'syllabus'],
            ['curricula', 'curriculum'],
            ['media', 'medium'],
            ['bacteria', 'bacterium'],
            ['datum', 'datum'],
            ['criterion', 'criterion'],
            ['phenomenon', 'phenomenon'],
            ['cactus', 'cactus'],
            ['nucleus', 'nucleus'],
            ['syllabus', 'syllabus'],
            ['curriculum', 'curriculum'],
            ['medium', 'medium'],
            ['bacterium', 'bacterium'],
            ['news', 'news'],
            ['aircraft', 'aircraft'],
            ['software', 'software'],
            ['hardware', 'hardware'],
            ['luggage', 'luggage'],
            ['advice', 'advice'],
            ['traffic', 'traffic'],
            ['furniture', 'furniture'],
            ['metadata', 'metadata'],
            ['multimedia', 'multimedia'],
            ['Children', 'Child'],
            ['Men', 'Man'],
            ['People', 'Person'],
            ['Teeth', 'Tooth'],
        ];
    }

    /**
     * @return array<int, array{string, string}>
     */
    public static function pluralizeProvider(): array
    {
        return [
            ['ox', 'oxen'],
            ['cat', 'cats'],
            ['purse', 'purses'],
            ['analysis', 'analyses'],
            ['house', 'houses'],
            ['sheep', 'sheep'],
            ['bus', 'buses'],
            ['axis', 'axes'],
            ['use', 'uses'],
            ['database', 'databases'],
            ['quiz', 'quizzes'],
            ['matrix', 'matrices'],
            ['vertex', 'vertices'],
            ['alias', 'aliases'],
            ['octopus', 'octopi'],
            ['crisis', 'crises'],
            ['shoe', 'shoes'],
            ['foe', 'foes'],
            ['piano', 'pianos'],
            ['wierdo', 'wierdos'],
            ['toe', 'toes'],
            ['veto', 'vetoes'],
            ['cow', 'cows'],
            ['datum', 'data'],
            ['criterion', 'criteria'],
            ['phenomenon', 'phenomena'],
            ['cactus', 'cacti'],
            ['nucleus', 'nuclei'],
            ['syllabus', 'syllabi'],
            ['curriculum', 'curricula'],
            ['medium', 'media'],
            ['bacterium', 'bacteria'],
            ['data', 'data'],
            ['criteria', 'criteria'],
            ['phenomena', 'phenomena'],
            ['people', 'people'],
            ['men', 'men'],
            ['children', 'children'],
            ['news', 'news'],
            ['News', 'News'],
            ['aircraft', 'aircraft'],
            ['metadata', 'metadata'],
            ['Man', 'Men'],
            ['Child', 'Children'],
            ['Person', 'People'],
            ['Tooth', 'Teeth'],
        ];
    }

    // -- Instance API tests --

    public function testInstancePluralSingular(): void
    {
        $inflect = new Inflect('en');
        $this->assertSame('cats', $inflect->plural('cat'));
        $this->assertSame('cat', $inflect->singular('cats'));
        $this->assertSame('people', $inflect->plural('person'));
        $this->assertSame('person', $inflect->singular('people'));
    }

    public function testInstancePluralIf(): void
    {
        $inflect = new Inflect();
        $this->assertSame('1 cat', $inflect->pluralIf(1, 'cat'));
        $this->assertSame('3 cats', $inflect->pluralIf(3, 'cat'));
        $this->assertSame('0 people', $inflect->pluralIf(0, 'person'));
    }

    public function testInstanceConstructorWithLocaleObject(): void
    {
        $locale = new En();
        $inflect = new Inflect($locale);
        $this->assertSame('cats', $inflect->plural('cat'));
        $this->assertSame($locale, $inflect->getLocale());
    }

    public function testInstanceIsolation(): void
    {
        $a = new Inflect();
        $b = new Inflect();

        $a->getLocale()->addIrregular('platypus', 'platypuses');
        $this->assertSame('platypuses', $a->plural('platypus'));
        // b has its own En instance — not affected
        $this->assertSame('platypus', $b->singular('platypuses'));
    }

    // -- Extension API tests --

    public function testAddIrregular(): void
    {
        $locale = new En();
        $locale->addIrregular('formula', 'formulae');
        $this->assertSame('formulae', $locale->pluralize('formula'));
        $this->assertSame('formula', $locale->singularize('formulae'));
    }

    public function testAddUncountable(): void
    {
        $locale = new En();
        $locale->addUncountable('moose');
        $this->assertSame('moose', $locale->pluralize('moose'));
        $this->assertSame('moose', $locale->singularize('moose'));
    }

    public function testAddPluralRule(): void
    {
        $locale = new En();
        $locale->addPluralRule('/^(platypus)$/i', '$1es');
        $this->assertSame('platypuses', $locale->pluralize('platypus'));
    }

    public function testAddSingularRule(): void
    {
        $locale = new En();
        $locale->addSingularRule('/(platypus)es$/i', '$1');
        $this->assertSame('platypus', $locale->singularize('platypuses'));
    }

    public function testExtensionInvalidatesCache(): void
    {
        $locale = new En();
        $this->assertSame('formulae', $locale->singularize('formulae'));
        $locale->addIrregular('formula', 'formulae');
        $this->assertSame('formula', $locale->singularize('formulae'));
    }

    // -- Locale registration --

    public function testRegisterLocale(): void
    {
        Inflect::registerLocale('test-en', En::class);
        $inflect = new Inflect('test-en');
        $this->assertSame('cats', $inflect->plural('cat'));
    }

    public function testUnknownLocaleThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Inflect('nonexistent-locale');
    }

    // -- Static extension proxy --

    public function testStaticAddIrregularProxy(): void
    {
        Inflect::addIrregular('dwarf', 'dwarves');
        $this->assertSame('dwarves', Inflect::pluralize('dwarf'));
        $this->assertSame('dwarf', Inflect::singularize('dwarves'));
    }
}
