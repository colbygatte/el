<?php

namespace ColbyGatte\El\Tests;

use PHPUnit\Framework\TestCase;

class ElTest extends TestCase
{
    /** @test */
    public function can_make_single_tag()
    {
        $strong = el('Strong!')->tag('strong');

        $this->assertEquals('<strong>Strong!</strong>', $strong->str());
    }

    /** @test */
    public function can_tag_multiple_items()
    {
        $ul = el(['one', 'two', 'three'])->each('li')->tag('ul');

        $this->assertEquals('<ul><li>one</li><li>two</li><li>three</li></ul>', $ul->str());
    }

    /** @test */
    public function can_make_table()
    {
        $this->assertEquals(
            '<table><tr><th>name</th><th>hi</th></tr><tr><td>colby</td></tr></table>',
            (string) el([['name', 'hi'], ['colby']])
                ->at(0)->each('th')->top()
                ->at(1)->each('td')->top()
                ->each('tr')
                ->tag('table')
        );
    }

    /** @test */
    public function can_use_attributes()
    {
        $thing = (string) el('Foo')->tag('a')
            ->attr
            ->href('http://example.com')
            ->target('_blank');

        $this->assertEquals('<a href="http://example.com" target="_blank">Foo</a>', $thing);

        // Using the magic getter to create a tag will return the Attribute class
        $thing = (string) el('Foo')->tagA
            ->href('http://example.com')
            ->target('_blank');

        $this->assertEquals('<a href="http://example.com" target="_blank">Foo</a>', $thing);
    }

    /** @test */
    public function safe_mode()
    {
        $this->assertEquals('<a href="http://example.com">&lt;what&gt;</a>', elsafe('<what>')->tagA->href('http://example.com')->str());
    }

    /** @test */
    public function can_add()
    {
        $this->assertEquals(
            '<a>hi<b>there</b></a>',
            (string) el('hi')->tag('a')->add(el('there')->tag('b'))
        );
    }

    /** @test */
    public function can_after()
    {
        $this->assertEquals(
            '<a>hi</a><b>there</b>',
            (string) el('hi')->tag('a')->after(el('there')->tag('b'))
        );
    }

    /** @test */
    public function can_specifiy_depth()
    {
        $data = [$depth = [[[['a1', 'a2', 'a3'], ['b1', 'b2', 'b3'], ['c1', 'c2', 'c3'], ['d1', 'd2', 'd3'],], [['a1', 'a2', 'a3'], ['b1', 'b2', 'b3'], ['c1', 'c2', 'c3'], ['d1', 'd2', 'd3'],]]], $depth];

        $this->assertEquals(
            '<foo>a1</foo><foo>a2</foo><foo>a3</foo><foo>b1</foo><foo>b2</foo><foo>b3</foo><foo>c1</foo><foo>c2</foo><foo>c3</foo>'.
            '<foo>d1</foo><foo>d2</foo><foo>d3</foo><foo>a1</foo><foo>a2</foo><foo>a3</foo><foo>b1</foo><foo>b2</foo><foo>b3</foo>'.
            '<foo>c1</foo><foo>c2</foo><foo>c3</foo><foo>d1</foo><foo>d2</foo><foo>d3</foo><foo>a1</foo><foo>a2</foo><foo>a3</foo>'.
            '<foo>b1</foo><foo>b2</foo><foo>b3</foo><foo>c1</foo><foo>c2</foo><foo>c3</foo><foo>d1</foo><foo>d2</foo><foo>d3</foo>'.
            '<foo>a1</foo><foo>a2</foo><foo>a3</foo><foo>b1</foo><foo>b2</foo><foo>b3</foo><foo>c1</foo><foo>c2</foo><foo>c3</foo>'.
            '<foo>d1</foo><foo>d2</foo><foo>d3</foo>',
            el($data)->depth(5)->tag('foo')->str()
        );

        $this->assertEquals(
            '<foo>a1a2a3</foo><foo>b1b2b3</foo><foo>c1c2c3</foo><foo>d1d2d3</foo><foo>a1a2a3</foo><foo>b1b2b3</foo><foo>c1c2c3</foo>'.
            '<foo>d1d2d3</foo><foo>a1a2a3</foo><foo>b1b2b3</foo><foo>c1c2c3</foo><foo>d1d2d3</foo><foo>a1a2a3</foo><foo>b1b2b3</foo>'.
            '<foo>c1c2c3</foo><foo>d1d2d3</foo>',
            el($data)->depth(4)->tag('foo')->str()
        );

        $this->assertEquals(
            '<foo>a1a2a3b1b2b3c1c2c3d1d2d3</foo><foo>a1a2a3b1b2b3c1c2c3d1d2d3</foo><foo>a1a2a3b1b2b3c1c2c3d1d2d3</foo><foo>a1a2a3b1b2b3c1c2c3d1d2d3</foo>',
            el($data)->depth(3)->tag('foo')->str()
        );

        $this->assertEquals(
            '<foo>a1a2a3b1b2b3c1c2c3d1d2d3a1a2a3b1b2b3c1c2c3d1d2d3</foo><foo>a1a2a3b1b2b3c1c2c3d1d2d3a1a2a3b1b2b3c1c2c3d1d2d3</foo>',
            el($data)->depth(2)->tag('foo')->str()
        );

        $this->assertEquals(
            '<foo>a1a2a3b1b2b3c1c2c3d1d2d3a1a2a3b1b2b3c1c2c3d1d2d3</foo><foo>a1a2a3b1b2b3c1c2c3d1d2d3a1a2a3b1b2b3c1c2c3d1d2d3</foo>',
            el($data)->depth(1)->tag('foo')->str()
        );

        $this->assertEquals(
            '<foo>a1a2a3b1b2b3c1c2c3d1d2d3a1a2a3b1b2b3c1c2c3d1d2d3a1a2a3b1b2b3c1c2c3d1d2d3a1a2a3b1b2b3c1c2c3d1d2d3</foo>',
            el($data)->depth(0)->tag('foo')->str()
        );
    }

    /** @test */
    public function can_make_table_with_header()
    {
        $data = [
            ['h1', 'h2', 'h3'],
            ['a1', 'a2', 'a3'],
            ['b1', 'b2', 'b3'],
            ['c1', 'c2', 'c3'],
        ];

        $table = (string) el($data)
            ->at(0)->each('th')->top()
            ->slice(1)->depth(1)->each('td')->top()
            ->each('tr')
            ->tag('table');

        $this->assertEquals(
            '<table><tr><th>h1</th><th>h2</th><th>h3</th></tr><tr><td>a1</td><td>a2</td><td>a3</td></tr>'.
            '<tr><td>b1</td><td>b2</td><td>b3</td></tr><tr><td>c1</td><td>c2</td><td>c3</td></tr></table>',
            $table
        );
    }
}