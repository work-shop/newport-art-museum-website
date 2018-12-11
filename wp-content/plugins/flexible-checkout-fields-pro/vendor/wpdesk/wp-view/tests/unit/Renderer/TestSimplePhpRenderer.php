<?php

use WPDesk\View\Resolver\ChainResolver;
use WPDesk\View\Resolver\Exception\CanNotResolve;
use WPDesk\View\Resolver\NullResolver;

class TestSimplePhpRenderer extends \PHPUnit\Framework\TestCase
{
    const TEXT_IN_TEMPLATE = 'outputText';

    const TEMPLATE_NAME = 'some_template';

    const TEMPLATE_DIR = '/templates';

    public function testRenderWithDirResolver()
    {
        $renderer = new \WPDesk\View\Renderer\SimplePhpRenderer(new \WPDesk\View\Resolver\DirResolver(__DIR__ . self::TEMPLATE_DIR));
        $this->assertEquals(self::TEXT_IN_TEMPLATE, $renderer->render(self::TEMPLATE_NAME));
    }
}