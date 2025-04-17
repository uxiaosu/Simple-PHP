<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 安全配置文件
 */

// 确定当前环境
$environment = isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] !== '127.0.0.1' && $_SERVER['SERVER_ADDR'] !== '::1' ? 'production' : 'development';

return [
    // 调试安全设置
    'debug_key' => 'YOUR_SECURE_DEBUG_KEY_CHANGE_THIS', // 请修改为复杂的随机字符串
    
    // CSRF保护
    'csrf' => [
        'enable' => true,
        'token_name' => 'csrf_token',
        'token_length' => 32,
        'regenerate' => true, // 每次请求后重新生成令牌
        'cookie_name' => 'csrf_cookie',
        'cookie_lifetime' => 3600 // 1小时
    ],
    
    // XSS防护
    'xss' => [
        'enable' => true,
        'auto_clean' => true,
        'allowed_tags' => '<p><br><a><strong><em><ul><ol><li><h1><h2><h3><h4><h5><blockquote><pre><code>' // 允许更多HTML标签
    ],
    
    // SQL注入防护
    'sql_injection' => [
        'enable' => true,
        'auto_escape' => true
    ],
    
    // 会话安全
    'session' => [
        'secure' => $environment === 'production', // 只在生产环境启用
        'httponly' => true, // 阻止JavaScript访问会话Cookie
        'use_only_cookies' => true, // 只使用Cookie存储会话标识符
        'regenerate_id' => true,
        'regenerate_interval' => 300, // 每5分钟重新生成会话ID
        'idle_timeout' => 1800, // 闲置超时30分钟
        'absolute_timeout' => 14400, // 绝对超时4小时
        'validate_ip' => false, // 不验证IP，避免动态IP问题
        'validate_ua' => $environment === 'production', // 只在生产环境验证用户代理
        'samesite' => $environment === 'production' ? 'Strict' : 'Lax' // 本地开发使用Lax
    ],
    
    // HTTP安全头
    'headers' => [
        'enable' => true,
        'x_frame_options' => 'SAMEORIGIN', // 允许在同源站点嵌入
        'x_xss_protection' => '1; mode=block', 
        'x_content_type_options' => 'nosniff',
        'referrer_policy' => 'same-origin',
        'strict_transport_security' => [
            'max-age' => 31536000, // 1年
            'includeSubDomains' => true,
            'preload' => $environment === 'production' // 只在生产环境开启预加载
        ],
        'content_security_policy' => [
            'default-src' => "'self'",
            'script-src' => "'self' 'unsafe-inline'", // 允许内联脚本
            'style-src' => "'self' 'unsafe-inline'", // 允许内联样式
            'img-src' => "'self' data:", // 允许data URL图片
            'font-src' => "'self'",
            'connect-src' => "'self'",
            'frame-src' => $environment === 'production' ? "'none'" : "'self'", // 开发环境允许框架
            'object-src' => "'none'"
        ],
        'feature_policy' => [
            'geolocation' => "'self'",
            'camera' => "'none'",
            'microphone' => "'none'",
            'payment' => "'self'"
        ]
    ],
    
    // 文件上传安全
    'file_upload' => [
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'], // 添加更多类型
        'max_size' => 5 * 1024 * 1024, // 5MB
        'validate_content' => true,
        'randomize_name' => true,
        'upload_dir' => ROOT_PATH . '/storage/uploads'
    ],
    
    // 密码策略
    'password' => [
        'min_length' => $environment === 'production' ? 10 : 8, // 开发环境较短
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_number' => true,
        'require_special' => $environment === 'production', // 只在生产环境要求特殊字符
        'hash_algo' => PASSWORD_ARGON2ID,
        'hash_options' => [
            'memory_cost' => 1024 * 16, // 16MB
            'time_cost' => 4,
            'threads' => 2
        ]
    ],
    
    // 请求频率限制
    'rate_limiting' => [
        'enable' => $environment === 'production', // 只在生产环境启用
        'storage' => 'file',
        'rules' => [
            // 登录页面限制
            [
                'interval' => 60,
                'limit' => 10, // 增加允许次数
                'paths' => ['/login', '/auth/login']
            ],
            // 敏感操作限制
            [
                'interval' => 60,
                'limit' => 20, // 增加允许次数
                'paths' => ['/admin/*', '/password/reset', '/register']
            ],
            // API请求限制
            [
                'interval' => 60,
                'limit' => 120, // 增加允许次数
                'paths' => ['/api/*']
            ],
            // 全局限制
            [
                'interval' => 60,
                'limit' => 180, // 增加允许次数
                'paths' => ['*']
            ]
        ]
    ],
    
    // 安全监控
    'monitoring' => [
        'enable' => true,
        'log_all_requests' => false, // 避免过多日志
        'block_suspicious' => $environment === 'production', // 只在生产环境阻止可疑请求
        'block_suspicious_headers' => $environment === 'production', // 只在生产环境阻止可疑的HTTP头
        'alert_types' => ['sql_injection', 'xss', 'command_injection', 'lfi'],
        'alert_threshold' => $environment === 'production' ? 'medium' : 'high', // 开发环境提高阈值
        'log_dir' => ROOT_PATH . '/storage/logs/security',
        'max_get_params' => 30, // 增加允许的GET参数数量
        'max_post_params' => 100, // 增加允许的POST参数数量
        'max_uri_length' => 3000, // 增加允许的URI长度
        'max_cookies' => 50 // 增加允许的Cookie数量
    ],
    
    // 访问控制
    'access_control' => [
        'enable' => true,
        'guest_redirect' => '/login',
        'default_role' => 'user',
        'admin_roles' => ['admin', 'superadmin'],
        'ip_whitelist' => ['127.0.0.1', '::1'], // 将本地IP加入白名单
        'ip_blacklist' => [],
        'role_hierarchy' => [
            'guest' => [],
            'user' => ['guest'],
            'editor' => ['user'],
            'admin' => ['editor'],
            'superadmin' => ['admin']
        ]
    ]
]; 