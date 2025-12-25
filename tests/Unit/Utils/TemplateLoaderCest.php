<?php
namespace Tests\Unit\Utils;

use ReflectionClass;
use ReflectionException;
use RobertWP\WebPConverterLite\Utils\TemplateLoader;
use Tests\Support\UnitTester;

class TemplateLoaderCest
{
    protected string $basePath;

    public function _before(UnitTester $I): void
    {
        // 模拟插件基础路径
        $this->basePath = codecept_data_dir() . 'templates/';

        // 全局模板目录
        if (!is_dir($this->basePath . 'includes/templates')) {
            mkdir($this->basePath . 'includes/templates', 0777, true);
        }

        // Admin 页面模板目录
        if (!is_dir($this->basePath . 'includes/admin/views')) {
            mkdir($this->basePath . 'includes/admin/views', 0777, true);
        }

        // 模块模板目录
        if (!is_dir($this->basePath . 'includes/modules/testmodule/templates')) {
            mkdir($this->basePath . 'includes/modules/testmodule/templates', 0777, true);
        }

        TemplateLoader::init($this->basePath);
    }

    /**
     * 测试：定位全局模板
     *
     * @throws ReflectionException
     */
    public function testLocateGlobalTemplate(UnitTester $I): void
    {
        $globalTemplate = $this->basePath . 'includes/templates/global.php';
        file_put_contents($globalTemplate, '<?php echo "GLOBAL"; ?>');

        $result = $this->invokeLocate('global');
        $I->assertEquals($globalTemplate, $result);
    }

    /**
     * 测试：定位模块模板（优先级最高）
     *
     * @throws ReflectionException
     */
    public function testLocateModuleTemplate(UnitTester $I): void
    {
        $moduleTemplate = $this->basePath . 'includes/modules/testmodule/templates/module.php';
        file_put_contents($moduleTemplate, '<?php echo "MODULE"; ?>');

        $result = $this->invokeLocate('module', 'testmodule');
        $I->assertEquals($moduleTemplate, $result);
    }

    /**
     * ✅ 新增：测试 Admin 页面模板定位
     *
     * @throws ReflectionException
     */
    public function testLocateAdminTemplate(UnitTester $I): void
    {
        $adminTemplate = $this->basePath . 'includes/admin/views/admin-page.php';
        file_put_contents($adminTemplate, '<?php echo "ADMIN"; ?>');

        $result = $this->invokeLocate('admin-page');
        $I->assertEquals($adminTemplate, $result);
    }

    /**
     * 测试：render() 返回模板输出
     */
    public function testRenderReturnsTemplateOutput(UnitTester $I): void
    {
        $file = $this->basePath . 'includes/templates/render_test.php';
        file_put_contents($file, '<?php echo "Hello $name"; ?>');

        $output = TemplateLoader::render('render_test', ['name' => 'Codeception']);
        $I->assertEquals('Hello Codeception', $output);
    }

    /**
     * 测试：render() 在模板不存在时返回空字符串
     */
    public function testRenderReturnsEmptyForMissingTemplate(UnitTester $I): void
    {
        $output = TemplateLoader::render('nonexistent');
        $I->assertEquals('', $output);
    }

    /**
     * 测试：exists() 方法
     */
    public function testExists(UnitTester $I): void
    {
        $file = $this->basePath . 'includes/templates/existing.php';
        file_put_contents($file, '<?php echo "OK"; ?>');

        $I->assertTrue(TemplateLoader::exists('existing'));
        $I->assertFalse(TemplateLoader::exists('missing'));
    }

    /**
     * 辅助方法：通过反射调用 protected locate()
     *
     * @throws ReflectionException
     */
    protected function invokeLocate(string $template, string $module = ''): ?string
    {
        $ref = new ReflectionClass(TemplateLoader::class);
        $method = $ref->getMethod('locate');

        return $method->invoke(null, $template, $module);
    }

    public function testAdminTemplateOverridesGlobal(UnitTester $I): void
    {
        file_put_contents(
            $this->basePath . 'includes/templates/same.php',
            '<?php echo "GLOBAL"; ?>'
        );

        file_put_contents(
            $this->basePath . 'includes/admin/views/same.php',
            '<?php echo "ADMIN"; ?>'
        );

        $result = TemplateLoader::render('same');
        $I->assertEquals('ADMIN', $result);
    }

}

