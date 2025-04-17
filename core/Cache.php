<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 缓存系统 - 提供页面缓存和数据缓存功能
 */

namespace SimplePHP\Core;

class Cache
{
    /**
     * 缓存目录
     * @var string
     */
    private $cacheDir;

    /**
     * 缓存是否启用
     * @var bool
     */
    private $enabled;

    /**
     * 默认缓存时间（秒）
     * @var int
     */
    private $defaultTtl;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->cacheDir = Config::get('cache.directory', ROOT_PATH . '/storage/cache');
        $this->enabled = Config::get('cache.enabled', false);
        $this->defaultTtl = Config::get('cache.default_ttl', 3600);

        // 确保缓存目录存在
        if ($this->enabled && !is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * 生成缓存键
     *
     * @param string $key 原始键名
     * @return string 处理后的缓存键
     */
    private function generateKey($key)
    {
        return md5($key);
    }

    /**
     * 获取缓存文件路径
     *
     * @param string $key 缓存键
     * @return string 缓存文件路径
     */
    private function getCacheFilePath($key)
    {
        $hashedKey = $this->generateKey($key);
        return $this->cacheDir . '/' . $hashedKey . '.cache';
    }

    /**
     * 检查缓存是否存在且有效
     *
     * @param string $key 缓存键
     * @return bool 是否存在有效缓存
     */
    public function has($key)
    {
        if (!$this->enabled) {
            return false;
        }

        $cacheFile = $this->getCacheFilePath($key);
        
        if (!file_exists($cacheFile)) {
            return false;
        }

        $content = file_get_contents($cacheFile);
        $data = unserialize($content);

        // 检查缓存是否过期
        if ($data['expires'] < time()) {
            // 过期缓存，删除文件
            unlink($cacheFile);
            return false;
        }

        return true;
    }

    /**
     * 获取缓存数据
     *
     * @param string $key 缓存键
     * @param mixed $default 默认值
     * @return mixed 缓存数据或默认值
     */
    public function get($key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }

        $cacheFile = $this->getCacheFilePath($key);
        $content = file_get_contents($cacheFile);
        $data = unserialize($content);

        return $data['content'];
    }

    /**
     * 设置缓存数据
     *
     * @param string $key 缓存键
     * @param mixed $value 缓存内容
     * @param int $ttl 过期时间（秒）
     * @return bool 操作是否成功
     */
    public function set($key, $value, $ttl = null)
    {
        if (!$this->enabled) {
            return false;
        }

        if ($ttl === null) {
            $ttl = $this->defaultTtl;
        }

        $data = [
            'content' => $value,
            'expires' => time() + $ttl,
            'created' => time()
        ];

        $cacheFile = $this->getCacheFilePath($key);
        return file_put_contents($cacheFile, serialize($data)) !== false;
    }

    /**
     * 删除指定缓存
     *
     * @param string $key 缓存键
     * @return bool 操作是否成功
     */
    public function delete($key)
    {
        if (!$this->enabled) {
            return false;
        }

        $cacheFile = $this->getCacheFilePath($key);
        
        if (file_exists($cacheFile)) {
            return unlink($cacheFile);
        }

        return true;
    }

    /**
     * 清除所有缓存
     *
     * @return bool 操作是否成功
     */
    public function clear()
    {
        if (!$this->enabled || !is_dir($this->cacheDir)) {
            return false;
        }

        $files = glob($this->cacheDir . '/*.cache');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        return true;
    }

    /**
     * 记忆函数结果（如果缓存存在，返回缓存；否则执行函数并缓存结果）
     *
     * @param string $key 缓存键
     * @param callable $callback 回调函数
     * @param int $ttl 过期时间（秒）
     * @return mixed 函数结果
     */
    public function remember($key, callable $callback, $ttl = null)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $result = $callback();
        $this->set($key, $result, $ttl);
        
        return $result;
    }

    /**
     * 缓存页面内容
     *
     * @param string $url 请求URL
     * @param string $content 页面内容
     * @param int $ttl 过期时间（秒）
     * @return bool 操作是否成功
     */
    public function cachePage($url, $content, $ttl = null)
    {
        return $this->set('page_' . $url, $content, $ttl);
    }

    /**
     * 获取缓存的页面内容
     *
     * @param string $url 请求URL
     * @return string|null 页面内容或null
     */
    public function getPage($url)
    {
        return $this->get('page_' . $url);
    }

    /**
     * 检查页面缓存是否存在
     *
     * @param string $url 请求URL
     * @return bool 是否存在缓存
     */
    public function hasPageCache($url)
    {
        return $this->has('page_' . $url);
    }
} 