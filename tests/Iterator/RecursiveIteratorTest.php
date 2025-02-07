<?php

declare(strict_types=1);

namespace Kcs\ClassFinder\Tests\Iterator;

use Kcs\ClassFinder\Fixtures\Recursive\Bar;
use Kcs\ClassFinder\Fixtures\Recursive\Foo;
use Kcs\ClassFinder\Fixtures\Recursive\FooBar;
use Kcs\ClassFinder\Iterator\RecursiveIterator;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use SplFileInfo;
use function iterator_to_array;

class RecursiveIteratorTest extends TestCase
{
    public function testRecursiveIteratorShouldSearchInDirectory(): void
    {
        $iterator = new RecursiveIterator(__DIR__ . '/../../data/Recursive');

        self::assertEquals([
            Bar::class => new ReflectionClass(Bar::class),
            FooBar::class => new ReflectionClass(FooBar::class),
            Foo::class => new ReflectionClass(Foo::class),
        ], iterator_to_array($iterator));
    }

    public function testRecursiveIteratorShouldSkipFilesThatDoNotMatchFilter(): void
    {
        $iterator = new RecursiveIterator(
            __DIR__ . '/../../data/Recursive',
            0,
            static function ( string $path, SplFileInfo $info ): bool {
                return str_starts_with( $info->getFilename(), 'class-' );
            });

        self::assertEquals([
            FooBar::class => new ReflectionClass(FooBar::class),
        ], iterator_to_array($iterator));
    }
}
