<?php

declare(strict_types=1);

namespace Inflect\Tests;

use Inflect\Inflect;
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
}
