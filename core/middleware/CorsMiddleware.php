<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * CORS跨域支持中间件
 */

namespace SimplePHP\Core\Middleware;

use SimplePHP\Core\Config;

class CorsMiddleware
{
    /**
     * 处理请求
     * 
     * @param object $request 请求对象
     * @param callable $next 下一个中间件
     * @return mixed
     */
    public function handle($request, $next)
    {
        // 获取CORS配置
        $config = Config::get('api.cors', []);
        
        // 允许的域名
        $allowedOrigins = $config['allowed_origins'] ?? ['*'];
        
        // 允许的方法
        $allowedMethods = $config['allowed_methods'] ?? 'GET, POST, PUT, DELETE, OPTIONS';
        
        // 允许的头信息
        $allowedHeaders = $config['allowed_headers'] ?? 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN';
        
        // 是否允许携带凭证
        $allowCredentials = $config['allow_credentials'] ?? true;
        
        // 缓存时间
        $maxAge = $config['max_age'] ?? 86400;
        
        // 检查Origin头
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        
        // 设置CORS头
        if (in_array('*', $allowedOrigins) || in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: " . ($origin ?: '*'));
            header("Access-Control-Allow-Methods: {$allowedMethods}");
            header("Access-Control-Allow-Headers: {$allowedHeaders}");
            
            if ($allowCredentials) {
                header("Access-Control-Allow-Credentials: true");
            }
            
            header("Access-Control-Max-Age: {$maxAge}");
            
            // 预检请求处理
            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                http_response_code(204);
                exit(0);
            }
        }
        
        // 继续处理请求
        return $next($request);
    }
} 