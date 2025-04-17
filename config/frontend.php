<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 前端集成配置文件
 */

return [
    // 资源路径配置
    'assets_path' => '/assets',
    
    // 前端框架集成
    'framework' => 'none', // 可选: 'vue', 'react', 'none'
    
    // 关键CSS配置
    'critical_css' => [
        'enabled' => true,
        'home' => 'home-critical',
        'docs' => 'docs-critical'
    ],
    
    // 关键脚本（不会被延迟加载）
    'critical_scripts' => [
        'app-core'
    ],
    
    // 资源优化配置
    'optimization' => [
        'defer_js' => true,
        'lazy_load_css' => true,
        'inline_small_css' => true,
        'inline_small_js' => false,
        'image_dimensions' => true,
    ],
    
    // 预加载资源
    'preload' => [
        'fonts' => [
            'font-awesome.woff2'
        ],
        'images' => [
            'logo.png'
        ]
    ],
    
    // SPA路由配置
    'spa_routes' => [
        'admin' => [
            'path' => '/admin/{path?}',
            'controller' => 'AdminController@index',
            'where' => ['path' => '.*']
        ]
    ],
    
    // 组件配置
    'components' => [
        'path' => '/resources/components',
        'auto_register' => true
    ],
    
    // 资源编译配置
    'build' => [
        'manifest_path' => '/public/assets/manifest.json',
        'output_path' => '/public/assets'
    ],
    
    // 开发环境配置
    'dev' => [
        'hot_reload' => true,
        'proxy' => [
            'enabled' => true,
            'url' => 'http://localhost:3000'
        ]
    ]
]; 