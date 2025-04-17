<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 路由类 - 负责处理URL路由
 */

namespace SimplePHP\Core\Router;

use SimplePHP\Core\Config;

class Router
{
    /**
     * @var array 注册的路由
     */
    private $routes = [];
    
    /**
     * @var string 当前URI
     */
    private $currentUri;
    
    /**
     * @var string 当前HTTP方法
     */
    private $requestMethod;
    
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->currentUri = $this->parseUri();
        $this->requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        // 加载路由配置
        $this->loadRoutes();
    }
    
    /**
     * 解析当前URI
     *
     * @return string
     */
    private function parseUri()
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        error_log("原始请求URI: " . $uri);
        
        // 去除查询字符串
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        // 获取应用基础路径
        $basePath = Config::get('app.base_path', '');
        error_log("配置的基础路径: " . $basePath);
        
        // 如果URI以基础路径开头，则去除基础路径部分
        if (!empty($basePath) && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
            error_log("移除基础路径后的URI: " . $uri);
        }
        
        // 去除重复的斜杠并规范化
        $uri = '/' . trim($uri, '/');
        
        // 移除public目录路径（如果存在）
        $uri = preg_replace('/^\/public/', '', $uri);
        
        error_log("最终解析后的URI: " . $uri);
        
        return $uri;
    }
    
    /**
     * 加载路由配置
     */
    private function loadRoutes()
    {
        // 如果存在路由配置文件，则加载
        $routeFile = ROOT_PATH . '/config/routes.php';
        if (file_exists($routeFile)) {
            $routeDefinitions = require $routeFile;
            if (is_array($routeDefinitions)) {
                foreach ($routeDefinitions as $route) {
                    if (is_array($route) && count($route) >= 3) {
                        $method = strtoupper($route[0]);
                        $uri = $route[1];
                        $handler = $route[2];
                        $this->addRoute($method, $uri, $handler);
                    }
                }
            }
        }
    }
    
    /**
     * 添加GET路由
     *
     * @param string $uri URI模式
     * @param string|array $handler 控制器@方法 或 回调函数
     * @return Router
     */
    public function get($uri, $handler)
    {
        return $this->addRoute('GET', $uri, $handler);
    }
    
    /**
     * 添加POST路由
     *
     * @param string $uri URI模式
     * @param string|array $handler 控制器@方法 或 回调函数
     * @return Router
     */
    public function post($uri, $handler)
    {
        return $this->addRoute('POST', $uri, $handler);
    }
    
    /**
     * 添加PUT路由
     *
     * @param string $uri URI模式
     * @param string|array $handler 控制器@方法 或 回调函数
     * @return Router
     */
    public function put($uri, $handler)
    {
        return $this->addRoute('PUT', $uri, $handler);
    }
    
    /**
     * 添加DELETE路由
     *
     * @param string $uri URI模式
     * @param string|array $handler 控制器@方法 或 回调函数
     * @return Router
     */
    public function delete($uri, $handler)
    {
        return $this->addRoute('DELETE', $uri, $handler);
    }
    
    /**
     * 添加任意HTTP方法的路由
     *
     * @param string $method HTTP方法
     * @param string $uri URI模式
     * @param string|array $handler 控制器@方法 或 回调函数
     * @return Router
     */
    public function addRoute($method, $uri, $handler)
    {
        $uri = '/' . trim($uri, '/');
        $this->routes[$method][$uri] = $handler;
        
        return $this;
    }
    
    /**
     * 分发请求到相应的控制器/处理器
     *
     * @return mixed
     */
    public function dispatch()
    {
        error_log("开始分发请求，HTTP方法: {$this->requestMethod}, URI: {$this->currentUri}");
        
        // 检查是否有匹配的路由
        if (isset($this->routes[$this->requestMethod])) {
            error_log("找到 " . count($this->routes[$this->requestMethod]) . " 个 {$this->requestMethod} 路由");
            
            // 精确匹配
            if (isset($this->routes[$this->requestMethod][$this->currentUri])) {
                error_log("找到精确匹配路由: {$this->currentUri}");
                return $this->executeHandler($this->routes[$this->requestMethod][$this->currentUri]);
            }
            
            // 模式匹配
            foreach ($this->routes[$this->requestMethod] as $pattern => $handler) {
                error_log("尝试匹配路由模式: {$pattern}");
                if ($this->matchPattern($pattern)) {
                    error_log("找到匹配的路由模式: {$pattern}");
                    return $this->executeHandler($handler, $this->extractParams($pattern));
                }
            }
            
            error_log("没有找到匹配的 {$this->requestMethod} 路由，尝试使用默认路由");
        } else {
            error_log("没有 {$this->requestMethod} 路由配置");
        }
        
        // 没有匹配的路由，尝试使用默认的控制器/方法
        return $this->useDefaultRoute();
    }
    
    /**
     * 匹配URI模式
     *
     * @param string $pattern 路由模式
     * @return bool
     */
    private function matchPattern($pattern)
    {
        // 将路由模式转换为正则表达式
        $routePattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $pattern);
        $routePattern = str_replace('/', '\/', $routePattern);
        $routePattern = '/^' . $routePattern . '$/';
        
        $result = preg_match($routePattern, $this->currentUri);
        
        // 调试输出
        error_log("路由匹配: 模式=$pattern, 转换后=$routePattern, URI={$this->currentUri}, 结果=" . ($result ? "匹配" : "不匹配"));
        
        return $result;
    }
    
    /**
     * 从URI中提取参数
     *
     * @param string $pattern 路由模式
     * @return array
     */
    private function extractParams($pattern)
    {
        $params = [];
        
        // 从模式中提取参数名
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $pattern, $paramNames);
        $paramNames = $paramNames[1];
        
        // 从URI中提取参数值
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $pattern);
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = '/^' . $pattern . '$/';
        
        preg_match($pattern, $this->currentUri, $matches);
        
        // 移除第一个元素（完整匹配）
        array_shift($matches);
        
        // 将参数名与值对应
        foreach ($paramNames as $index => $name) {
            $params[$name] = $matches[$index] ?? null;
        }
        
        return $params;
    }
    
    /**
     * 执行路由处理器
     *
     * @param string|array $handler 处理器
     * @param array $params 参数
     * @return mixed
     */
    private function executeHandler($handler, array $params = [])
    {
        // 如果处理器是回调函数
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }
        
        // 如果处理器是字符串（控制器@方法）
        if (is_string($handler) && strpos($handler, '@') !== false) {
            list($controller, $action) = explode('@', $handler);
            
            // 添加命名空间
            $controller = 'SimplePHP\\App\\Controllers\\' . $controller;
            
            if (class_exists($controller)) {
                $controllerInstance = new $controller();
                
                if (method_exists($controllerInstance, $action)) {
                    return call_user_func_array([$controllerInstance, $action], $params);
                }
            }
        }
        
        // 处理器无效
        throw new \Exception("无效的路由处理器");
    }
    
    /**
     * 使用默认的控制器和方法
     *
     * @return mixed
     */
    private function useDefaultRoute()
    {
        // 获取URI段
        $segments = explode('/', trim($this->currentUri, '/'));
        error_log("URI段: " . json_encode($segments));
        
        // 确定控制器和方法
        $controllerName = !empty($segments[0]) ? ucfirst($segments[0]) : Config::get('app.default_controller');
        $controllerName = $controllerName . 'Controller'; // 确保添加Controller后缀
        
        $actionName = isset($segments[1]) ? $segments[1] : Config::get('app.default_action');
        // 确保动作方法名以Action结尾
        if (substr($actionName, -6) !== 'Action') {
            $actionName .= 'Action';
        }
        
        error_log("尝试使用默认路由: 控制器={$controllerName}, 方法={$actionName}");
        
        // 构建控制器类名
        $controllerClass = 'SimplePHP\\App\\Controllers\\' . $controllerName;
        
        // 检查控制器是否存在
        if (class_exists($controllerClass)) {
            error_log("控制器类存在: {$controllerClass}");
            $controller = new $controllerClass();
            
            // 检查方法是否存在
            if (method_exists($controller, $actionName)) {
                error_log("控制器方法存在: {$actionName}");
                // 提取参数
                $params = array_slice($segments, 2);
                
                // 执行控制器方法
                return call_user_func_array([$controller, $actionName], $params);
            } else {
                error_log("控制器方法不存在: {$actionName}");
            }
        } else {
            error_log("控制器类不存在: {$controllerClass}");
        }
        
        // 控制器或方法不存在，显示404错误页面
        header("HTTP/1.0 404 Not Found");
        include ROOT_PATH . '/app/views/errors/404.php';
        return null;
    }
} 