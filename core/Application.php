<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 应用类 - 框架的主要类
 */

namespace SimplePHP\Core;

use SimplePHP\Core\Router\Router;
use SimplePHP\Core\Security\Security;
use SimplePHP\Core\Database\Database;

class Application
{
    /**
     * @var Router 路由实例
     */
    private $router;
    
    /**
     * @var Security 安全组件实例
     */
    private $security;
    
    /**
     * @var Database 数据库实例
     */
    private $db;
    
    /**
     * 构造函数 - 初始化核心组件
     */
    public function __construct()
    {
        // 初始化安全组件
        $this->security = new Security();
        
        // 初始化数据库
        $this->db = new Database();
        
        // 初始化路由
        $this->router = new Router();
        
        // 设置错误处理器
        $this->setupErrorHandlers();
    }
    
    /**
     * 运行应用
     */
    public function run()
    {
        try {
            error_log("应用开始运行");
            
            // 应用安全措施
            $this->security->applySecurity();
            
            // 处理请求路由
            error_log("开始处理路由");
            $response = $this->router->dispatch();
            error_log("路由处理完成");
            
            // 输出响应
            if ($response !== null) {
                error_log("开始输出响应，响应长度: " . strlen($response));
                echo $response;
            } else {
                error_log("警告: 响应为空");
            }
        } catch (\Throwable $e) {
            error_log("捕获到异常: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            $this->handleException($e);
        } finally {
            // 清理资源
            $this->db->close();
            error_log("数据库连接已关闭");
            
            // 刷新并关闭输出缓冲
            if (ob_get_level() > 0) {
                ob_end_flush();
                error_log("输出缓冲已刷新并关闭");
            }
        }
    }
    
    /**
     * 设置错误和异常处理器
     */
    private function setupErrorHandlers()
    {
        // 设置异常处理函数 - 支持所有Throwable类型
        set_exception_handler(function(\Throwable $exception) {
            $this->handleException($exception);
        });
        
        // 设置错误处理函数 - 将错误转换为异常
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            // 检查是否启用了错误报告
            if (!(error_reporting() & $errno)) {
                // 错误被禁用，只记录不抛出异常
                error_log("错误 [$errno] $errstr - $errfile:$errline");
                return false;
            }
            
            // 将错误转换为异常
            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        });
        
        // 设置关闭处理函数，确保所有未捕获的错误都被记录
        register_shutdown_function(function() {
            $error = error_get_last();
            if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
                // 清除之前的所有输出
                if (ob_get_level() > 0) {
                    ob_clean();
                }
                $this->handleFatalError($error);
            }
        });
    }
    
    /**
     * 处理致命错误
     *
     * @param array $error 错误信息数组
     */
    public function handleFatalError($error)
    {
        // 记录错误到日志
        error_log("致命错误: {$error['message']} in {$error['file']} on line {$error['line']}");
        
        // 设置HTTP状态码
        http_response_code(500);
        
        // 显示友好的错误页面
        echo '<!DOCTYPE html>';
        echo '<html lang="zh">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        echo '<title>服务器错误</title>';
        echo '<style>';
        echo 'body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; padding: 20px; text-align: center; }';
        echo '.error-container { max-width: 600px; margin: 50px auto; background: #f8f8f8; padding: 20px; border-radius: 5px; }';
        echo 'h1 { color: #444; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        echo '<div class="error-container">';
        echo '<h1>服务器错误</h1>';
        echo '<p>很抱歉，处理您的请求时出现错误。</p>';
        echo '<p>请稍后再试或联系管理员。</p>';
        echo '</div>';
        echo '</body>';
        echo '</html>';
        
        exit(1);
    }
    
    /**
     * 处理异常
     *
     * @param \Throwable $exception 异常对象
     */
    public function handleException(\Throwable $exception)
    {
        // 记录错误到日志
        error_log("严重错误: " . $exception->getMessage() . ' in ' . $exception->getFile() . ' on line ' . $exception->getLine());
        
        // 清除之前的所有输出
        if (ob_get_level() > 0) {
            ob_clean();
        }
        
        // 设置HTTP状态码
        http_response_code(500);
        
        // 始终显示详细的错误信息用于调试
        echo '<!DOCTYPE html>';
        echo '<html lang="zh">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        echo '<title>应用程序错误</title>';
        echo '<style>';
        echo 'body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; padding: 20px; }';
        echo '.error-container { max-width: 1000px; margin: 0 auto; background: #f8f8f8; padding: 20px; border-radius: 5px; border-left: 5px solid #e74c3c; }';
        echo 'h1 { color: #e74c3c; }';
        echo 'pre { background: #f1f1f1; padding: 15px; overflow: auto; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        echo '<div class="error-container">';
        echo '<h1>应用程序错误</h1>';
        echo '<p><strong>错误信息:</strong> ' . htmlspecialchars($exception->getMessage()) . '</p>';
        echo '<p><strong>文件:</strong> ' . htmlspecialchars($exception->getFile()) . '</p>';
        echo '<p><strong>行号:</strong> ' . $exception->getLine() . '</p>';
        echo '<h2>堆栈跟踪</h2>';
        echo '<pre>' . htmlspecialchars($exception->getTraceAsString()) . '</pre>';
        echo '</div>';
        echo '</body>';
        echo '</html>';
        
        // 终止脚本执行
        exit(1);
    }
} 