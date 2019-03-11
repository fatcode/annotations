<?php declare(strict_types=1);

namespace IgniTest\Annotation;

use Igni\Annotation\Annotation;
use Igni\Annotation\Context;
use Igni\Annotation\Enum;
use Igni\Annotation\Parser;
use Igni\Annotation\Target;
use IgniTest\Annotation\Fixtures\Annotations\AnnotatedClass;
use IgniTest\Annotation\Fixtures\Annotations\MetaClass;
use IgniTest\Annotation\Fixtures\Annotations\MetaProperty;
use IgniTest\Annotation\Fixtures\Annotations\SimpleAnnotation;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class ParserTest extends TestCase
{
    public function testParseBuiltInAnnotations() : void
    {
        $reflection = new ReflectionClass(SimpleAnnotation::class);
        $parser = new Parser();
        $annotations = $parser->parse($reflection->getDocComment(), Context::fromReflectionClass($reflection));
        self::assertCount(3, $annotations);
        self::assertInstanceOf(Annotation::class, $annotations[0]);
        self::assertInstanceOf(Target::class, $annotations[1]);
        self::assertInstanceOf(Enum::class, $annotations[2]);
        self::assertSame([Target::TARGET_ALL], $annotations[1]->value);
        self::assertSame([1, 2, 3], $annotations[2]->value);
    }

    public function testParseCustomAnnotation() : void
    {
        $reflection = new ReflectionClass(AnnotatedClass::class);
        $parser = new Parser();
        $annotations = $parser->parse($reflection->getDocComment(), Context::fromReflectionClass($reflection));

        self::assertCount(1, $annotations);
        $annotation = $annotations[0];
        self::assertInstanceOf(MetaClass::class, $annotation);
        self::assertCount(2, $annotation->properties);
        self::assertInstanceOf(MetaProperty::class, $annotation->properties[0]);
        self::assertSame('testInt', $annotation->properties[0]->name);
        self::assertSame('int', $annotation->properties[0]->type);
        self::assertSame('test', $annotation->properties[0]->default);
        self::assertInstanceOf(MetaProperty::class, $annotation->properties[1]);
    }
}
