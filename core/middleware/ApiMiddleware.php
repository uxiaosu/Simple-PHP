<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * API请求处理中间件
 */

namespace SimplePHP\Core\Middleware;

class ApiMiddleware
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
        // 设置API特定的头信息
        header('Content-Type: application/json');
        header('X-Content-Type-Options: nosniff');
        
        // 解析JSON请求体
        if ($this->isJsonRequest($request)) {
            $this->parseJsonBody($request);
        }
        
        // 继续处理请求
        return $next($request);
    }
    
    /**
     * 判断是否为JSON请求
     * 
     * @param object $request 请求对象
     * @return bool
     */
    private function isJsonRequest($request)
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        return strpos($contentType, 'application/json') !== false;
    }
    
    /**
     * 解析JSON请求体
     * 
     * @param object $request 请求对象
     * @return void
     */
    private function parseJsonBody($request)
    {
        $input = file_get_contents('php://input');
        if (!empty($input)) {
            $data = json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // 将JSON数据合并到请求中
                if (method_exists($request, 'setJsonData')) {
                    $request->setJsonData($data);
                } elseif (property_exists($request, 'jsonData')) {
                    $request->jsonData = $data;
                } else {
                    // 兼容处理：添加到全局变量
                    $_POST = array_merge($_POST, $data);
                }
            }
        }
    }
} 