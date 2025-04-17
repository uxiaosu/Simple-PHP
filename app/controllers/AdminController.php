<?php
namespace SimplePHP\App\Controllers;

use SimplePHP\Core\Controller;
use SimplePHP\Core\Config;
use SimplePHP\Core\Frontend;

/**
 * 管理后台控制器
 * 支持SPA应用加载
 */
class AdminController extends Controller
{
    /**
     * 初始化方法
     */
    public function __construct()
    {
        parent::__construct();
        
        // 设置页面标题
        $this->title = '管理后台 - SimplePHP框架';
    }
    
    /**
     * 管理后台首页
     * 作为SPA应用的加载点
     * 
     * @return string
     */
    public function index()
    {
        // 准备传递给前端的初始数据
        $initialData = [
            'user' => $this->getCurrentUser(),
            'config' => $this->getClientConfig(),
            'routes' => $this->getClientRoutes(),
            'meta' => [
                'title' => $this->title,
                'version' => Config::get('app.version', '1.0.0'),
                'timestamp' => time()
            ]
        ];
        
        // 渲染SPA模板
        return $this->view('spa', [
            'title' => $this->title,
            'initialData' => $initialData,
            'cssBundle' => 'admin',
            'jsBundle' => 'admin'
        ]);
    }
    
    /**
     * 获取当前用户信息
     * 
     * @return array 用户信息
     */
    private function getCurrentUser()
    {
        // 这里应该从认证系统获取实际用户
        // 示例演示返回模拟数据
        return [
            'id' => 1,
            'username' => 'admin',
            'email' => 'admin@example.com',
            'role' => 'administrator',
            'permissions' => ['dashboard', 'users', 'settings']
        ];
    }
    
    /**
     * 获取客户端配置
     * 
     * @return array 配置信息
     */
    private function getClientConfig()
    {
        return [
            'apiBaseUrl' => '/api',
            'debug' => Config::get('app.debug', false),
            'defaultLocale' => Config::get('app.locale', 'zh_CN'),
            'features' => [
                'darkMode' => true,
                'notifications' => true
            ]
        ];
    }
    
    /**
     * 获取客户端路由
     * 
     * @return array 路由信息
     */
    private function getClientRoutes()
    {
        return [
            ['path' => '/', 'name' => 'dashboard', 'title' => '控制面板'],
            ['path' => '/users', 'name' => 'users', 'title' => '用户管理'],
            ['path' => '/settings', 'name' => 'settings', 'title' => '系统设置'],
            ['path' => '/profile', 'name' => 'profile', 'title' => '个人资料']
        ];
    }
} 