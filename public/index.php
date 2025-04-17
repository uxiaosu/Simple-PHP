<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 主入口文件
 */

// 记录开始时间（用于性能监控）
$startTime = microtime(true);

// 设置PHP配置项
ini_set('display_errors', 0);
error_reporting(E_ALL);

// 定义根目录常量
define('ROOT_PATH', dirname(__DIR__));

// 加载Composer自动加载器（如果存在）
if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require ROOT_PATH . '/vendor/autoload.php';
}

// 加载框架核心
require_once ROOT_PATH . '/core/Bootstrap.php';

// 运行应用
$app = new SimplePHP\Core\Bootstrap();
$app->run();

// 记录执行时间（仅在调试模式启用时）
if (SimplePHP\Core\Config::get('app.debug', false)) {
    $endTime = microtime(true);
    $executionTime = number_format(($endTime - $startTime) * 1000, 2);
    
    // 添加性能指标注释
    echo "<!-- 页面生成时间: {$executionTime}ms -->";
} 