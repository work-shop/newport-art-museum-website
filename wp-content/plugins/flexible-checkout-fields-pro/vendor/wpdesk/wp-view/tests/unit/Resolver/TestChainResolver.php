<?php

use WPDesk\View\Resolver\ChainResolver;
use WPDesk\View\Resolver\Exception\CanNotResolve;
use WPDesk\View\Resolver\NullResolver;

class TestChainResolver extends \PHPUnit\Framework\TestCase
{
    const RESPONSE_OF_RESOLVER = 'response';

    const RESOLVE_METHOD_NAME = 'resolve';

    public function setUp()
    {
        \WP_Mock::setUp();
    }

    public function tearDown()
    {
        \WP_Mock::tearDown();
    }

    public function testUseSecondResolverWhenFirstFailed()
    {
        $validResolver = Mockery::mock(NullResolver::class);
        $validResolver
            ->shouldReceive(self::RESOLVE_METHOD_NAME)
            ->andReturn(self::RESPONSE_OF_RESOLVER);

        $resolver = new ChainResolver(new NullResolver(), new NullResolver(), $validResolver);
        $this->assertEquals(self::RESPONSE_OF_RESOLVER, $resolver->resolve('whatever.php'));
    }

    public function testUseFirstResolverFirst()
    {
        $validResolver = Mockery::mock(NullResolver::class);
        $validResolver
            ->shouldReceive(self::RESOLVE_METHOD_NAME)
            ->andReturn(self::RESPONSE_OF_RESOLVER);

        $resolver = new ChainResolver($validResolver, new NullResolver(), new NullResolver());
        $this->assertEquals(self::RESPONSE_OF_RESOLVER, $resolver->resolve('whatever.php'));
    }

    public function testThrowExceptionWhenBothCannotFind()
    {
        $this->expectException(CanNotResolve::class);

        $resolver = new ChainResolver(new NullResolver(), new NullResolver());

        $resolver->resolve('whatever2');
    }
}