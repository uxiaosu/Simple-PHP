<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 响应类 - 处理HTTP响应和缓存
 */

namespace SimplePHP\Core\Http;

use SimplePHP\Core\Config;

class Response
{
    /**
     * HTTP状态码
     * @var int
     */
    private $statusCode = 200;
    
    /**
     * 响应头
     * @var array
     */
    private $headers = [];
    
    /**
     * 响应内容
     * @var string
     */
    private $content = '';
    
    /**
     * 内容类型
     * @var string
     */
    private $contentType = 'text/html';
    
    /**
     * 字符集
     * @var string
     */
    private $charset = 'utf-8';
    
    /**
     * 是否启用Gzip压缩
     * @var bool
     */
    private $enableCompression = true;
    
    /**
     * 是否启用缓存控制
     * @var bool
     */
    private $enableCaching = true;
    
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->enableCompression = Config::get('cache.assets.compression', true);
        $this->enableCaching = Config::get('cache.assets.enable_caching', true);
    }
    
    /**
     * 设置状态码
     *
     * @param int $statusCode HTTP状态码
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }
    
    /**
     * 添加HTTP头
     *
     * @param string $name 头名称
     * @param string $value 头值
     * @return $this
     */
    public function addHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }
    
    /**
     * 设置内容类型
     *
     * @param string $contentType 内容类型
     * @param string $charset 字符集
     * @return $this
     */
    public function setContentType($contentType, $charset = 'utf-8')
    {
        $this->contentType = $contentType;
        $this->charset = $charset;
        return $this;
    }
    
    /**
     * 设置响应内容
     *
     * @param string $content 响应内容
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
    
    /**
     * 添加缓存控制头
     *
     * @param int $maxAge 最大缓存时间（秒）
     * @param bool $isPublic 是否公共缓存
     * @return $this
     */
    public function addCacheControl($maxAge, $isPublic = true)
    {
        if (!$this->enableCaching) {
            $this->addHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $this->addHeader('Pragma', 'no-cache');
            $this->addHeader('Expires', '0');
            return $this;
        }
        
        $cacheControl = $isPublic ? 'public' : 'private';
        $cacheControl .= ', max-age=' . $maxAge;
        
        // 添加缓存控制头
        $this->addHeader('Cache-Control', $cacheControl);
        
        // 设置过期时间
        $expires = gmdate('D, d M Y H:i:s', time() + $maxAge) . ' GMT';
        $this->addHeader('Expires', $expires);
        
        return $this;
    }
    
    /**
     * 添加ETag头
     *
     * @param string $etag ETag值
     * @return $this
     */
    public function addETag($etag = null)
    {
        if (!$this->enableCaching) {
            return $this;
        }
        
        // 如果未提供ETag，基于内容生成
        if ($etag === null) {
            $etag = '"' . md5($this->content) . '"';
        }
        
        $this->addHeader('ETag', $etag);
        
        // 检查请求的If-None-Match头，如果匹配则返回304
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {
            $this->setStatusCode(304);
            $this->content = '';
        }
        
        return $this;
    }
    
    /**
     * 设置内容安全策略
     *
     * @param array $policies 策略配置
     * @return $this
     */
    public function setContentSecurityPolicy($policies = [])
    {
        $defaultPolicies = [
            'default-src' => "'self'",
            'script-src' => "'self'",
            'style-src' => "'self'",
            'img-src' => "'self'",
            'connect-src' => "'self'",
            'font-src' => "'self'",
            'object-src' => "'none'",
            'base-uri' => "'self'"
        ];
        
        $policies = array_merge($defaultPolicies, $policies);
        
        $cspHeader = '';
        foreach ($policies as $directive => $sources) {
            $cspHeader .= $directive . ' ' . $sources . '; ';
        }
        
        $this->addHeader('Content-Security-Policy', $cspHeader);
        return $this;
    }
    
    /**
     * 压缩响应内容
     *
     * @return void
     */
    private function compressContent()
    {
        // 检查是否启用压缩
        if (!$this->enableCompression) {
            return;
        }
        
        // 检查是否支持Gzip和客户端是否接受Gzip
        if (extension_loaded('zlib') && 
            isset($_SERVER['HTTP_ACCEPT_ENCODING']) && 
            strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
            
            // 检查内容类型是否可压缩
            $compressibleTypes = Config::get('cache.assets.gzip_types', [
                'text/html', 'text/css', 'text/plain', 'text/javascript',
                'application/javascript', 'application/json', 'application/xml'
            ]);
            
            if (in_array($this->contentType, $compressibleTypes)) {
                $this->content = gzencode($this->content, 9);
                $this->addHeader('Content-Encoding', 'gzip');
            }
        }
    }
    
    /**
     * 发送响应到客户端
     *
     * @return void
     */
    public function send()
    {
        // 设置HTTP状态码
        http_response_code($this->statusCode);
        
        // 设置内容类型
        header('Content-Type: ' . $this->contentType . '; charset=' . $this->charset);
        
        // 压缩内容
        $this->compressContent();
        
        // 设置内容长度
        $this->addHeader('Content-Length', strlen($this->content));
        
        // 添加服务器信息
        $this->addHeader('X-Powered-By', 'SimplePHP');
        
        // 设置所有响应头
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }
        
        // 输出内容
        echo $this->content;
        exit;
    }
    
    /**
     * 设置适当的资源缓存头（用于静态资源）
     *
     * @param string $filePath 文件路径
     * @return $this
     */
    public function setCacheHeadersForAsset($filePath)
    {
        if (!$this->enableCaching || !file_exists($filePath)) {
            return $this;
        }
        
        // 获取文件修改时间
        $lastModified = filemtime($filePath);
        $lastModifiedFormatted = gmdate('D, d M Y H:i:s', $lastModified) . ' GMT';
        
        // 设置Last-Modified头
        $this->addHeader('Last-Modified', $lastModifiedFormatted);
        
        // 检查If-Modified-Since头
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && 
            strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModified) {
            $this->setStatusCode(304);
            $this->content = '';
            return $this;
        }
        
        // 设置ETag
        $etag = '"' . md5($filePath . $lastModified) . '"';
        $this->addETag($etag);
        
        // 设置缓存时间
        $maxAge = Config::get('cache.assets.max_age', 604800); // 默认一周
        $this->addCacheControl($maxAge);
        
        return $this;
    }
} 