<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 应用配置文件
 */

// 确定当前环境
// 可以通过环境变量、域名或其他方式判断
// 这里使用一个简单的方法：通过检查服务器IP来判断
$environment = isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] !== '127.0.0.1' && $_SERVER['SERVER_ADDR'] !== '::1' ? 'production' : 'development';

return [
    // 应用名称
    'name' => 'SimplePHP Application',
    
    // 应用URL
    'url' => 'http://localhost',
    
    // 应用基础路径 - 修改为空字符串，因为web根目录指向public
    'base_path' => '',
    
    // 环境设置 - 强制使用开发环境设置
    'environment' => 'development',
    
    // 调试模式 - 在开发环境启用
    'debug' => true,
    
    // 错误显示设置 - 在开发环境启用
    'display_errors' => true,
    
    // 错误日志设置 - 在所有环境中启用
    'log_errors' => true,
    
    // 默认时区
    'timezone' => 'Asia/Shanghai',
    
    // 默认控制器
    'default_controller' => 'Home',
    
    // 默认方法
    'default_action' => 'index',
    
    // 日志路径
    'log_path' => ROOT_PATH . '/storage/logs',
    
    // 视图路径
    'views_path' => ROOT_PATH . '/app/views',
    
    // 是否使用布局
    'use_layout' => true,
    
    // 默认布局
    'default_layout' => 'main'
]; 