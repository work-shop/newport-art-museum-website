<?php


use WPDesk\View\Resolver\Exception\CanNotResolve;

class TestDirResolver extends \PHPUnit\Framework\TestCase
{
    const TEMPLATE_NAME = 'some_template.php';
    const TEMPLATE_FILE = 'some_template.php';
    const TEMPLATE_SUBDIR = 'templates';


    public function testCanFindInDirPath()
    {
        $dir = __DIR__ . '/' . self::TEMPLATE_SUBDIR;
        $resolver           = new \WPDesk\View\Resolver\DirResolver($dir);

        $this->assertStringEndsWith(self::TEMPLATE_FILE, $resolver->resolve(self::TEMPLATE_NAME),
            'Template should be found in dir');
    }

    public function testThrowExceptionWhenCannotFind()
    {
        $this->expectException(CanNotResolve::class);

        $resolver = new \WPDesk\View\Resolver\DirResolver('whatever');
        $resolver->resolve('whatever2');
    }
}