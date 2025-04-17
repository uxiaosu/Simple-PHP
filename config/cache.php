<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 缓存配置文件
 */

return [
    // 是否启用缓存
    'enabled' => true,
    
    // 缓存目录
    'directory' => ROOT_PATH . '/storage/cache',
    
    // 默认缓存生存时间（秒）
    'default_ttl' => 3600,
    
    // 页面缓存设置
    'page_cache' => [
        'enabled' => true,
        'ttl' => 1800, // 30分钟
        'exclude_routes' => [
            '/admin',
            '/api'
        ],
        'cache_query_string' => false,
    ],
    
    // 数据缓存设置
    'data_cache' => [
        'enabled' => true,
        'ttl' => 3600 // 60分钟
    ],
    
    // API响应缓存
    'api_cache' => [
        'enabled' => true,
        'ttl' => 300, // 5分钟
        'exclude_endpoints' => [
            '/api/users', // 排除用户API
            '/api/auth'   // 排除认证API
        ]
    ],
    
    // 资源缓存配置（前端资源）
    'assets' => [
        'max_age' => 604800, // 一周
        'use_etags' => true,
        'gzip_types' => [
            'text/css',
            'text/javascript',
            'application/javascript',
            'application/json',
            'text/html',
            'text/plain'
        ]
    ]
]; 