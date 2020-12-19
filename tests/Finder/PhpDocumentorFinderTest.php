<?php declare(strict_types=1);

namespace Kcs\ClassFinder\Tests\Finder;

use Kcs\ClassFinder\Finder\PhpDocumentorFinder;
use Kcs\ClassFinder\Fixtures\Psr0;
use Kcs\ClassFinder\Fixtures\Psr4;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\Interface_;
use phpDocumentor\Reflection\Php\Trait_;
use PHPUnit\Framework\TestCase;

class PhpDocumentorFinderTest extends TestCase
{
    public function testFinderShouldBeIterable(): void
    {
        $finder = new PhpDocumentorFinder(__DIR__);
        self::assertInstanceOf(\Traversable::class, $finder);
    }

    public function testFinderShouldFilterByNamespace(): void
    {
        $finder = new PhpDocumentorFinder(__DIR__.'/../../data');
        $finder->inNamespace(['Kcs\ClassFinder\Fixtures\Psr4']);

        $classes = \iterator_to_array($finder);

        self::assertArrayHasKey(Psr4\BarBar::class, $classes);
        self::assertInstanceOf(Class_::class, $classes[Psr4\BarBar::class]);
        self::assertArrayHasKey(Psr4\Foobar::class, $classes);
        self::assertInstanceOf(Class_::class, $classes[Psr4\Foobar::class]);
        self::assertArrayHasKey(Psr4\AbstractClass::class, $classes);
        self::assertInstanceOf(Class_::class, $classes[Psr4\AbstractClass::class]);
        self::assertArrayHasKey(Psr4\SubNs\FooBaz::class, $classes);
        self::assertInstanceOf(Class_::class, $classes[Psr4\SubNs\FooBaz::class]);
        self::assertArrayHasKey(Psr4\FooInterface::class, $classes);
        self::assertInstanceOf(Interface_::class, $classes[Psr4\FooInterface::class]);
        self::assertArrayHasKey(Psr4\FooTrait::class, $classes);
        self::assertInstanceOf(Trait_::class, $classes[Psr4\FooTrait::class]);
    }

    public function testFinderShouldFilterByDirectory(): void
    {
        $finder = new PhpDocumentorFinder(__DIR__.'/../../data');
        $finder->in([__DIR__.'/../../data/Composer/Psr0']);

        $classes = \iterator_to_array($finder);

        self::assertArrayHasKey(Psr0\BarBar::class, $classes);
        self::assertInstanceOf(Class_::class, $classes[Psr0\BarBar::class]);
        self::assertArrayHasKey(Psr0\Foobar::class, $classes);
        self::assertInstanceOf(Class_::class, $classes[Psr0\Foobar::class]);
        self::assertArrayHasKey(Psr0\SubNs\FooBaz::class, $classes);
        self::assertInstanceOf(Class_::class, $classes[Psr0\SubNs\FooBaz::class]);
    }

    public function testFinderShouldFilterByInterfaceImplementation(): void
    {
        $finder = new PhpDocumentorFinder(__DIR__.'/../../data');
        $finder->in([__DIR__.'/../../data']);
        $finder->implementationOf(Psr4\FooInterface::class);

        $classes = \iterator_to_array($finder);

        self::assertArrayHasKey(Psr4\BarBar::class, $classes);
        self::assertInstanceOf(Class_::class, $classes[Psr4\BarBar::class]);
    }

    public function testFinderShouldFilterBySuperClass(): void
    {
        $finder = new PhpDocumentorFinder(__DIR__.'/../../data');
        $finder->in([__DIR__.'/../../data']);
        $finder->subclassOf(Psr4\AbstractClass::class);

        $classes = \iterator_to_array($finder);

        self::assertArrayHasKey(Psr4\Foobar::class, $classes);
        self::assertInstanceOf(Class_::class, $classes[Psr4\Foobar::class]);
    }

    public function testFinderShouldFilterByAnnotation(): void
    {
        $finder = new PhpDocumentorFinder(__DIR__.'/../../data');
        $finder->in([__DIR__.'/../../data']);
        $finder->annotatedBy(Psr4\SubNs\FooBaz::class);

        $classes = \iterator_to_array($finder);

        self::assertArrayHasKey(Psr4\AbstractClass::class, $classes);
        self::assertInstanceOf(Class_::class, $classes[Psr4\AbstractClass::class]);
    }

    /**
     * @requires PHP >= 8.0
     */
    public function testFinderShouldFilterByAttribute(): void
    {
        $finder = new PhpDocumentorFinder(__DIR__.'/../../data');
        $finder->in([__DIR__.'/../../data']);
        $finder->withAttribute(Psr4\SubNs\FooBaz::class);

        // Not implemented yet
        $this->expectException(\RuntimeException::class);
        /* $classes = */ \iterator_to_array($finder);

//        self::assertArrayHasKey(Psr4\AbstractClass::class, $classes);
//        self::assertInstanceOf(Class_::class, $classes[Psr4\AbstractClass::class]);
    }

    public function testFinderShouldFilterByCallback(): void
    {
        $finder = new PhpDocumentorFinder(__DIR__.'/../../data');
        $finder->filter(function (Element $class) {
            return (string) $class->getFqsen() === '\\'.Psr4\AbstractClass::class;
        });

        $classes = \iterator_to_array($finder);

        self::assertCount(1, $classes);
        self::assertArrayHasKey(Psr4\AbstractClass::class, $classes);
        self::assertInstanceOf(Class_::class, $classes[Psr4\AbstractClass::class]);
    }
}
