<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 配置类 - 管理框架配置
 */

namespace SimplePHP\Core;

class Config
{
    /**
     * @var array 配置数据
     */
    private static $config = [];
    
    /**
     * 加载配置文件
     */
    public static function load()
    {
        // 应用基本配置
        self::$config['app'] = require ROOT_PATH . '/config/app.php';
        
        // 数据库配置
        self::$config['database'] = require ROOT_PATH . '/config/database.php';
        
        // 安全配置
        self::$config['security'] = require ROOT_PATH . '/config/security.php';
    }
    
    /**
     * 获取配置项
     *
     * @param string $key 配置键，格式为 "节点.键名"
     * @param mixed $default 默认值
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        if (empty(self::$config)) {
            self::load();
        }
        
        $keys = explode('.', $key);
        
        if (count($keys) < 2) {
            return isset(self::$config[$key]) ? self::$config[$key] : $default;
        }
        
        $section = $keys[0];
        $configKey = $keys[1];
        
        if (!isset(self::$config[$section])) {
            return $default;
        }
        
        return isset(self::$config[$section][$configKey]) ? self::$config[$section][$configKey] : $default;
    }
    
    /**
     * 设置配置项
     *
     * @param string $key 配置键
     * @param mixed $value 配置值
     */
    public static function set($key, $value)
    {
        $keys = explode('.', $key);
        
        if (count($keys) < 2) {
            self::$config[$key] = $value;
            return;
        }
        
        $section = $keys[0];
        $configKey = $keys[1];
        
        if (!isset(self::$config[$section])) {
            self::$config[$section] = [];
        }
        
        self::$config[$section][$configKey] = $value;
    }
} 