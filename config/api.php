<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * API配置文件
 */

return [
    // API版本
    'version' => 'v1',
    
    // 默认返回格式
    'default_format' => 'json',
    
    // 速率限制配置
    'rate_limit' => [
        'enabled' => true,
        'max_requests' => 60, // 每分钟最大请求数
        'window' => 60, // 时间窗口（秒）
        'throttle_callback' => null // 限流回调函数
    ],
    
    // CORS配置
    'cors' => [
        'allowed_origins' => ['*'], // 允许的域名，* 代表所有
        'allowed_methods' => 'GET, POST, PUT, DELETE, OPTIONS',
        'allowed_headers' => 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN',
        'allow_credentials' => true,
        'max_age' => 86400 // 预检请求缓存时间（秒）
    ],
    
    // 认证配置
    'auth' => [
        'enabled' => true,
        'driver' => 'jwt', // 认证驱动: 'jwt', 'oauth', 'token'
        'jwt' => [
            'secret' => 'your-jwt-secret-key-change-this', // JWT密钥，生产环境需更改
            'algorithm' => 'HS256', // 签名算法
            'ttl' => 3600, // token有效期（秒）
            'refresh_ttl' => 604800, // 刷新token有效期（秒）
        ]
    ],
    
    // 文档配置
    'docs' => [
        'enabled' => true,
        'path' => '/api/docs',
        'title' => 'SimplePHP API文档',
        'description' => 'SimplePHP框架API接口文档',
        'version' => '1.0.0'
    ],
    
    // 中间件配置
    'middleware' => [
        'global' => ['cors', 'api'], // 全局中间件
        'api' => [] // API专用中间件
    ],
    
    // 响应配置
    'response' => [
        'wrap' => true, // 是否包装响应
        'wrap_format' => [
            'success' => true,
            'code' => 200,
            'message' => '',
            'data' => null
        ]
    ]
]; 