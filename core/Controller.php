<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 控制器基类 - 所有控制器的父类
 */

namespace SimplePHP\Core;

use SimplePHP\Core\Security\Security;

abstract class Controller
{
    /**
     * @var Security 安全组件实例
     */
    protected $security;
    
    /**
     * 应用实例
     * @var Application
     */
    protected $app;

    /**
     * 请求对象
     * @var Request
     */
    protected $request;

    /**
     * 响应对象
     * @var Response
     */
    protected $response;

    /**
     * 视图数据
     * @var array
     */
    protected $viewData = [];

    /**
     * CSRF保护对象
     * @var \SimplePHP\Core\Security\Csrf
     */
    protected $csrf;
    
    /**
     * 当前路由路径
     * @var string
     */
    protected $currentRoute = '';

    /**
     * 缓存实例
     * @var Cache
     */
    protected $cache;

    /**
     * 构造函数
     */
    public function __construct()
    {
        try {
            // 设置当前路由路径(从请求URI中提取)
            $uri = $_SERVER['REQUEST_URI'] ?? '';
            $basePath = Config::get('app.base_path', '');
            
            // 如果URI以基础路径开头，则去除基础路径部分
            if (!empty($basePath) && strpos($uri, $basePath) === 0) {
                $uri = substr($uri, strlen($basePath));
            }
            
            // 去除查询字符串和前导斜杠
            if (($pos = strpos($uri, '?')) !== false) {
                $uri = substr($uri, 0, $pos);
            }
            $this->currentRoute = trim($uri, '/');
            
            // 初始化安全组件
            if (class_exists('\\SimplePHP\\Core\\Security\\Security')) {
                $this->security = new Security();
            }
            
            // 获取应用实例
            if (class_exists('\\SimplePHP\\Core\\Application')) {
                $this->app = Application::getInstance();
                
                // 确保请求和响应对象存在
                if (isset($this->app->request)) {
                    $this->request = $this->app->request;
                }
                
                if (isset($this->app->response)) {
                    $this->response = $this->app->response;
                }
            }
            
            // 初始化缓存
            if (class_exists('\\SimplePHP\\Core\\Cache')) {
                $this->cache = new Cache();
            }
            
            // 初始化视图数据
            $this->viewData = [];
            
            // 安全地初始化CSRF保护
            try {
                // 只有在类存在且全部依赖可用时才初始化CSRF
                if (class_exists('\\SimplePHP\\Core\\Security\\Csrf') && 
                    class_exists('\\SimplePHP\\Core\\Config') && 
                    function_exists('session_start')) {
                    $this->csrf = new \SimplePHP\Core\Security\Csrf();
                }
            } catch (\Throwable $e) {
                // 记录错误但不阻止控制器初始化
                error_log("CSRF初始化错误: " . $e->getMessage());
            }
        } catch (\Throwable $e) {
            // 记录整体初始化错误
            error_log("控制器初始化错误: " . $e->getMessage());
        }
    }
    
    /**
     * 渲染视图
     *
     * @param string $view 视图文件名
     * @param array $data 传递给视图的数据
     * @param string $layout 布局文件名
     * @return string
     */
    protected function view($view, $data = [], $layout = null)
    {
        try {
            // 检查是否启用了页面缓存且当前路由不被排除
            $cacheEnabled = Config::get('cache.page_cache.enabled', false);
            $excludeRoutes = Config::get('cache.page_cache.exclude_routes', []);
            
            // 生成缓存键
            $cacheKey = 'page_' . $this->currentRoute;
            if (Config::get('cache.page_cache.cache_query_string', false) && !empty($_SERVER['QUERY_STRING'])) {
                $cacheKey .= '_' . $_SERVER['QUERY_STRING'];
            }
            
            // 如果启用了缓存且不在排除列表中，检查缓存
            if ($cacheEnabled && 
                !in_array('/' . $this->currentRoute, $excludeRoutes) && 
                !$this->isMethod('POST') && 
                isset($this->cache)) {
                
                // 如果存在缓存，直接返回
                if ($this->cache->hasPageCache($cacheKey)) {
                    return $this->cache->getPage($cacheKey);
                }
            }
            
            error_log("开始渲染视图: {$view}");
            
            // 设置默认布局
            if ($layout === null && Config::get('app.use_layout', true)) {
                $layout = Config::get('app.default_layout', 'main');
            }
            
            // 确保currentRoute传递给视图
            $this->viewData['currentRoute'] = $this->currentRoute;
            
            // 合并数据
            $data = array_merge($this->viewData, $data);
            
            // 视图文件路径
            $viewPath = Config::get('app.views_path', ROOT_PATH . '/app/views');
            $viewFile = $viewPath . '/' . $view . '.php';
            
            // 检查视图文件是否存在
            if (!file_exists($viewFile)) {
                error_log("视图文件不存在: {$viewFile}");
                throw new \Exception("视图文件不存在: {$viewFile}");
            }
            
            // 开始输出缓冲
            ob_start();
            
            // 导出变量到视图
            extract($data);
            
            // 包含视图文件
            error_log("正在包含视图文件: {$viewFile}");
            include $viewFile;
            
            // 获取视图内容
            $content = ob_get_clean();
            error_log("视图内容获取成功，长度: " . strlen($content));
            
            // 如果不使用布局，直接返回视图内容
            if ($layout === false) {
                error_log("不使用布局，直接返回视图内容");
                
                // 如果启用了缓存且不在排除列表中，缓存页面内容
                if ($cacheEnabled && 
                    !in_array('/' . $this->currentRoute, $excludeRoutes) && 
                    !$this->isMethod('POST') && 
                    isset($this->cache)) {
                    $this->cache->cachePage($cacheKey, $content, Config::get('cache.page_cache.ttl', 1800));
                }
                
                return $content;
            }
            
            // 布局文件路径
            $layoutFile = $viewPath . '/layouts/' . $layout . '.php';
            
            // 检查布局文件是否存在
            if (!file_exists($layoutFile)) {
                error_log("布局文件不存在: {$layoutFile}");
                throw new \Exception("布局文件不存在: {$layoutFile}");
            }
            
            // 开始输出缓冲
            ob_start();
            
            // 导出变量到布局
            $layoutData = array_merge($data, ['content' => $content]);
            extract($layoutData);
            
            // 包含布局文件
            error_log("正在包含布局文件: {$layoutFile}");
            include $layoutFile;
            
            // 返回完整的HTML
            $finalHtml = ob_get_clean();
            error_log("布局渲染完成，最终HTML长度: " . strlen($finalHtml));
            
            // 如果启用了缓存且不在排除列表中，缓存页面内容
            if ($cacheEnabled && 
                !in_array('/' . $this->currentRoute, $excludeRoutes) && 
                !$this->isMethod('POST') && 
                isset($this->cache)) {
                $this->cache->cachePage($cacheKey, $finalHtml, Config::get('cache.page_cache.ttl', 1800));
            }
            
            return $finalHtml;
        } catch (\Throwable $e) {
            // 记录错误到日志
            error_log("视图渲染错误: " . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            
            // 清除之前的所有输出
            if (ob_get_level() > 0) {
                ob_clean();
            }
            
            // 简化错误显示，不使用复杂的安全检查
            header('HTTP/1.1 500 Internal Server Error');
            echo '<!DOCTYPE html>';
            echo '<html lang="zh">';
            echo '<head><title>错误</title></head>';
            echo '<body>';
            echo '<h1>服务器错误</h1>';
            echo '<p>渲染视图时出错: ' . htmlspecialchars($e->getMessage()) . '</p>';
            if (Config::get('app.debug', false)) {
                echo '<p>文件: ' . htmlspecialchars($e->getFile()) . ' (行 ' . $e->getLine() . ')</p>';
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            }
            echo '</body>';
            echo '</html>';
            exit;
        }
    }
    
    /**
     * 返回JSON响应
     *
     * @param mixed $data 数据
     * @param int $status HTTP状态码
     * @return string
     */
    protected function json($data, $status = 200)
    {
        // 设置HTTP状态码
        http_response_code($status);
        
        // 设置内容类型为JSON
        header('Content-Type: application/json; charset=utf-8');
        
        // 检查是否启用了API缓存
        $apiCacheEnabled = Config::get('cache.api_cache.enabled', false);
        $excludeEndpoints = Config::get('cache.api_cache.exclude_endpoints', []);
        
        // 如果是GET请求，且启用了API缓存，且不在排除列表中
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && 
            $apiCacheEnabled && 
            !in_array('/' . $this->currentRoute, $excludeEndpoints) && 
            isset($this->cache)) {
            
            // 生成缓存键
            $cacheKey = 'api_' . $this->currentRoute;
            if (!empty($_SERVER['QUERY_STRING'])) {
                $cacheKey .= '_' . $_SERVER['QUERY_STRING'];
            }
            
            // 缓存API响应
            $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
            $this->cache->set($cacheKey, $jsonData, Config::get('cache.api_cache.ttl', 300));
            
            return $jsonData;
        }
        
        // 返回JSON
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * 重定向到指定URL
     *
     * @param string $url 目标URL
     * @param int $status HTTP状态码
     */
    protected function redirect($url, $status = 302)
    {
        // 设置HTTP状态码
        http_response_code($status);
        
        // 设置Location头
        header("Location: {$url}");
        exit;
    }
    
    /**
     * 生成应用内URL
     * 
     * @param string $path 相对路径
     * @return string 完整URL
     */
    protected function url($path = '')
    {
        $basePath = Config::get('app.base_path', '');
        $path = trim($path, '/');
        
        if (empty($path)) {
            return $basePath ?: '/';
        }
        
        return rtrim($basePath, '/') . '/' . $path;
    }
    
    /**
     * 获取请求参数
     *
     * @param string $key 参数名
     * @param mixed $default 默认值
     * @return mixed
     */
    protected function input($key = null, $default = null)
    {
        $input = array_merge($_GET, $_POST);
        
        if ($key === null) {
            return $input;
        }
        
        return isset($input[$key]) ? $input[$key] : $default;
    }
    
    /**
     * 获取URL参数
     *
     * @param string $key 参数名
     * @param mixed $default 默认值
     * @return mixed
     */
    protected function query($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }
    
    /**
     * 获取POST参数
     *
     * @param string $key 参数名
     * @param mixed $default 默认值
     * @return mixed
     */
    protected function post($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }
        
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    
    /**
     * 检查请求方法
     *
     * @param string $method 请求方法
     * @return bool
     */
    protected function isMethod($method)
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) === strtoupper($method);
    }
    
    /**
     * 检查是否为AJAX请求
     *
     * @return bool
     */
    protected function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * 验证输入数据
     *
     * @param array $data 要验证的数据
     * @param array $rules 验证规则
     * @return array 错误信息数组，为空表示验证通过
     */
    protected function validate(array $data, array $rules)
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $ruleItems = explode('|', $rule);
            
            foreach ($ruleItems as $ruleItem) {
                // 带参数的规则
                if (strpos($ruleItem, ':') !== false) {
                    list($ruleName, $ruleValue) = explode(':', $ruleItem, 2);
                } else {
                    $ruleName = $ruleItem;
                    $ruleValue = null;
                }
                
                // 必填字段
                if ($ruleName === 'required' && (!isset($data[$field]) || trim($data[$field]) === '')) {
                    $errors[$field][] = "{$field}字段是必填的";
                    break; // 如果必填验证失败，则不再验证其他规则
                }
                
                // 如果字段不存在且不是必填字段，则跳过验证
                if (!isset($data[$field]) || $data[$field] === '') {
                    continue;
                }
                
                // 验证长度
                if ($ruleName === 'min' && strlen($data[$field]) < (int)$ruleValue) {
                    $errors[$field][] = "{$field}字段长度不能小于{$ruleValue}个字符";
                }
                
                if ($ruleName === 'max' && strlen($data[$field]) > (int)$ruleValue) {
                    $errors[$field][] = "{$field}字段长度不能大于{$ruleValue}个字符";
                }
                
                // 验证邮箱
                if ($ruleName === 'email' && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "{$field}字段必须是有效的电子邮件地址";
                }
                
                // 验证数字
                if ($ruleName === 'numeric' && !is_numeric($data[$field])) {
                    $errors[$field][] = "{$field}字段必须是数字";
                }
                
                // 验证整数
                if ($ruleName === 'integer' && !filter_var($data[$field], FILTER_VALIDATE_INT)) {
                    $errors[$field][] = "{$field}字段必须是整数";
                }
                
                // 验证字母数字
                if ($ruleName === 'alpha_num' && !ctype_alnum($data[$field])) {
                    $errors[$field][] = "{$field}字段只能包含字母和数字";
                }
                
                // 验证URL
                if ($ruleName === 'url' && !filter_var($data[$field], FILTER_VALIDATE_URL)) {
                    $errors[$field][] = "{$field}字段必须是有效的URL";
                }
                
                // 验证日期
                if ($ruleName === 'date' && !strtotime($data[$field])) {
                    $errors[$field][] = "{$field}字段必须是有效的日期";
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * 生成CSRF表单字段
     *
     * @return string
     */
    protected function csrfField()
    {
        return $this->security->csrfField();
    }

    /**
     * 获取或缓存数据
     *
     * @param string $key 缓存键
     * @param callable $callback 回调函数，用于生成数据
     * @param int $ttl 过期时间（秒）
     * @return mixed 缓存或新生成的数据
     */
    protected function cached($key, callable $callback, $ttl = null)
    {
        if (!isset($this->cache) || !Config::get('cache.data_cache.enabled', false)) {
            return $callback();
        }
        
        if ($ttl === null) {
            $ttl = Config::get('cache.data_cache.ttl', 3600);
        }
        
        return $this->cache->remember($key, $callback, $ttl);
    }
} 