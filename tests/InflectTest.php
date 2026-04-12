<?php

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../src/Inflect/Inflect.php');

use Inflect\Inflect;

class InflectTest extends PHPUnit_Framework_TestCase
{
    public function testSingularize()
    {
        $inflections = array('ox' => 'ox',
                'cats' => 'cat',
                'oxen' => 'ox',
                'cats' => 'cat',
                'purses' => 'purse',
                'analyses' => 'analysis',
                'houses' => 'house',
                'sheep' => 'sheep',
                'buses' => 'bus',
                'uses' => 'use',
                'databases' => 'database',
                'quizzes' => 'quiz',
                'matrices' => 'matrix',
                'vertices' => 'vertex',
                'alias' => 'alias',
                'aliases' => 'alias',
                'octopi' => 'octopus',
                'axes' => 'axis',
                'axis' => 'axis',
                'crises' => 'crisis',
                'crisis' => 'crisis',
                'shoes' => 'shoe',
                'foes' => 'foe',
                'pianos' => 'piano',
                'wierdos' => 'wierdo',
                'toes' => 'toe',
                'banjoes' => 'banjo',
                'vetoes' => 'veto',
                'cows' => 'cow',
                'businesses' => 'business',
                'business' => 'business',
                'wellness' => 'wellness',
            );

        foreach ($inflections as $key => $value)
        {
            print "Testing $key singularizes to: $value\n";
            $this->assertEquals($value, Inflect::singularize($key));
        }

	print "\n";
    }

    public function testPluralize()
    {
        $inflections = array('oxen' => 'ox',
                'cats' => 'cat',
                'cats' => 'cat',
                'purses' => 'purse',
                'analyses' => 'analysis',
                'houses' => 'house',
                'sheep' => 'sheep',
                'buses' => 'bus',
                'axes' => 'axis',
                'uses' => 'use',
                'databases' => 'database',
                'quizzes' => 'quiz',
                'matrices' => 'matrix',
                'vertices' => 'vertex',
                'aliases' => 'aliases',
                'aliases' => 'alias',
                'octopi' => 'octopus',
                'axes' => 'axis',
                'crises' => 'crisis',
                'crises' => 'crises',
                'shoes' => 'shoe',
                'foes' => 'foe',
                'pianos' => 'piano',
                'wierdos' => 'wierdo',
                'toes' => 'toe',
                'banjos' => 'banjo',
                'vetoes' => 'veto',
                'cows' => 'cow',
                );
        foreach ($inflections as $key => $value)
        {
            print "Testing $value pluralizes to: $key\n";
            $this->assertEquals($key, Inflect::pluralize($value));
        }
	print "\n";
    }

    // Uses a list of [input, expected] pairs to avoid duplicate-key dedup.
    public function testNewIrregularsAndGuards()
    {
        $singularizeCases = array(
            // new irregulars: plural -> singular
            array('data', 'datum'),
            array('criteria', 'criterion'),
            array('phenomena', 'phenomenon'),
            array('cacti', 'cactus'),
            array('nuclei', 'nucleus'),
            array('syllabi', 'syllabus'),
            array('curricula', 'curriculum'),
            array('media', 'medium'),
            array('bacteria', 'bacterium'),
            // already-singular guard: singular -> singular
            array('datum', 'datum'),
            array('criterion', 'criterion'),
            array('phenomenon', 'phenomenon'),
            array('cactus', 'cactus'),
            array('nucleus', 'nucleus'),
            array('syllabus', 'syllabus'),
            array('curriculum', 'curriculum'),
            array('medium', 'medium'),
            array('bacterium', 'bacterium'),
            // new uncountables
            array('news', 'news'),
            array('aircraft', 'aircraft'),
            array('software', 'software'),
            array('hardware', 'hardware'),
            array('luggage', 'luggage'),
            array('advice', 'advice'),
            array('traffic', 'traffic'),
            array('furniture', 'furniture'),
            array('metadata', 'metadata'),
            array('multimedia', 'multimedia'),
            // case preservation on irregulars
            array('Children', 'Child'),
            array('Men', 'Man'),
            array('People', 'Person'),
            array('Teeth', 'Tooth'),
        );

        foreach ($singularizeCases as $case)
        {
            list($input, $expected) = $case;
            print "Testing $input singularizes to: $expected\n";
            $this->assertEquals($expected, Inflect::singularize($input));
        }

        $pluralizeCases = array(
            // new irregulars: singular -> plural
            array('datum', 'data'),
            array('criterion', 'criteria'),
            array('phenomenon', 'phenomena'),
            array('cactus', 'cacti'),
            array('nucleus', 'nuclei'),
            array('syllabus', 'syllabi'),
            array('curriculum', 'curricula'),
            array('medium', 'media'),
            array('bacterium', 'bacteria'),
            // already-plural guard
            array('data', 'data'),
            array('criteria', 'criteria'),
            array('phenomena', 'phenomena'),
            array('people', 'people'),
            array('men', 'men'),
            array('children', 'children'),
            // uncountables
            array('news', 'news'),
            array('News', 'News'),
            array('aircraft', 'aircraft'),
            array('metadata', 'metadata'),
            // case preservation on irregulars
            array('Man', 'Men'),
            array('Child', 'Children'),
            array('Person', 'People'),
            array('Tooth', 'Teeth'),
        );

        foreach ($pluralizeCases as $case)
        {
            list($input, $expected) = $case;
            print "Testing $input pluralizes to: $expected\n";
            $this->assertEquals($expected, Inflect::pluralize($input));
        }
	print "\n";
    }
}