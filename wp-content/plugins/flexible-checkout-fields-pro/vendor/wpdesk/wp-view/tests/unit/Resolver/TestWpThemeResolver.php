<?php


use WPDesk\View\Resolver\Exception\CanNotResolve;

class TestThemeResolver extends \PHPUnit\Framework\TestCase
{
    const TEMPLATE_NAME = 'some_template.php';
    const TEMPLATE_FILE = 'some_template.php';
    const TEMPLATE_SUBDIR = 'templates';

    public function setUp()
    {
        \WP_Mock::setUp();

        \WP_Mock::userFunction('locate_template', [
            'return' => function ($template_names, $load = false, $require_once = true) {
                $located = '';
                foreach ((array)$template_names as $template_name) {
                    if ( ! $template_name) {
                        continue;
                    }
                    if (file_exists(STYLESHEETPATH . '/' . $template_name)) {
                        $located = STYLESHEETPATH . '/' . $template_name;
                        break;
                    }
                }

                return $located;
            }
        ]);

        \WP_Mock::userFunction('trailingslashit', [
            'return' => function ($string) {
                return untrailingslashit($string) . '/';
            }
        ]);

        \WP_Mock::userFunction('untrailingslashit', [
            'return' => function ($string) {
                return rtrim($string, '/\\');
            }
        ]);
    }

    public function tearDown()
    {
        \WP_Mock::tearDown();
    }

    public function testCanFindInStyleSheetPath()
    {
        define('STYLESHEETPATH', __DIR__);

        $template_base_path = self::TEMPLATE_SUBDIR;
        $resolver           = new \WPDesk\View\Resolver\WPThemeResolver($template_base_path);

        $this->assertStringEndsWith(self::TEMPLATE_FILE, $resolver->resolve(self::TEMPLATE_NAME),
            'Template should be found in stylesheetpath');
    }

    public function testThrowExceptionWhenCannotFind()
    {
        $this->expectException(CanNotResolve::class);

        $resolver = new \WPDesk\View\Resolver\WPThemeResolver('whatever');
        $resolver->resolve('whatever2');
    }
}