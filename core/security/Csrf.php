<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * CSRF保护类 - 提供CSRF令牌生成和验证功能
 */

namespace SimplePHP\Core\Security;

use SimplePHP\Core\Config;

class Csrf
{
    /**
     * CSRF令牌名称
     * @var string
     */
    protected $tokenName;
    
    /**
     * 令牌长度
     * @var int
     */
    protected $tokenLength;
    
    /**
     * 是否每次请求后重新生成令牌
     * @var bool
     */
    protected $regenerate;
    
    /**
     * 构造函数
     */
    public function __construct()
    {
        try {
            // 从配置中加载CSRF设置，如果Config类不可用则使用默认值
            if (class_exists('\\SimplePHP\\Core\\Config')) {
                $this->tokenName = Config::get('security.csrf.token_name', 'csrf_token');
                $this->tokenLength = Config::get('security.csrf.token_length', 32);
                $this->regenerate = Config::get('security.csrf.regenerate', true);
            } else {
                $this->tokenName = 'csrf_token';
                $this->tokenLength = 32;
                $this->regenerate = true;
            }
            
            // 确保会话已启动
            if (session_status() === PHP_SESSION_NONE) {
                // 使用错误抑制符以避免可能的警告
                @session_start();
            }
            
            // 确保CSRF令牌存在
            if (!$this->hasToken()) {
                $this->regenerateToken();
            }
        } catch (\Throwable $e) {
            // 记录错误信息
            error_log("CSRF初始化错误: " . $e->getMessage());
            
            // 设置默认值，确保类仍可用
            $this->tokenName = 'csrf_token';
            $this->tokenLength = 32;
            $this->regenerate = true;
            
            // 使用空令牌，保证页面至少能正常加载
            if (isset($_SESSION)) {
                $_SESSION[$this->tokenName] = '';
            }
        }
    }
    
    /**
     * 获取当前CSRF令牌
     * 
     * @return string
     */
    public function getToken()
    {
        // 确保会话已启动
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        
        // 检查会话是否可用
        if (!isset($_SESSION)) {
            throw new \RuntimeException("CSRF令牌生成失败：会话不可用");
        }
        
        // 如果令牌不存在或为空，生成新令牌
        if (!isset($_SESSION[$this->tokenName]) || empty($_SESSION[$this->tokenName])) {
            try {
                $_SESSION[$this->tokenName] = $this->generateToken();
                // 添加令牌生成时间
                $_SESSION[$this->tokenName . '_time'] = time();
            } catch (\Throwable $e) {
                error_log("CSRF令牌生成异常: " . $e->getMessage());
                throw new \RuntimeException("CSRF令牌生成失败: " . $e->getMessage());
            }
        } else {
            // 检查令牌是否过期（默认1小时）
            $tokenLifetime = Config::get('security.csrf.cookie_lifetime', 3600);
            if (isset($_SESSION[$this->tokenName . '_time']) && 
                (time() - $_SESSION[$this->tokenName . '_time'] > $tokenLifetime)) {
                // 令牌已过期，重新生成
                $_SESSION[$this->tokenName] = $this->generateToken();
                $_SESSION[$this->tokenName . '_time'] = time();
            }
        }
        
        return $_SESSION[$this->tokenName];
    }
    
    /**
     * 验证CSRF令牌
     * 
     * @param string $token 用户提交的令牌
     * @return bool
     */
    public function validateToken($token)
    {
        if (!$this->hasToken() || empty($token)) {
            return false;
        }
        
        // 通过哈希比较来防止时序攻击
        $result = hash_equals($this->getToken(), $token);
        
        // 如果配置为每次请求后重新生成令牌
        if ($this->regenerate && $result) {
            $this->regenerateToken();
        }
        
        return $result;
    }
    
    /**
     * 检查CSRF令牌是否存在
     * 
     * @return bool
     */
    public function hasToken()
    {
        return isset($_SESSION[$this->tokenName]) && !empty($_SESSION[$this->tokenName]);
    }
    
    /**
     * 重新生成CSRF令牌
     * 
     * @return string 新生成的令牌
     */
    public function regenerateToken()
    {
        $token = $this->generateToken();
        $_SESSION[$this->tokenName] = $token;
        
        return $token;
    }
    
    /**
     * 生成CSRF表单字段
     * 
     * @return string HTML表单字段
     */
    public function field()
    {
        return '<input type="hidden" name="' . $this->tokenName . '" value="' . $this->getToken() . '">';
    }
    
    /**
     * 生成一个安全的随机令牌
     * 
     * @return string
     */
    protected function generateToken()
    {
        if (function_exists('random_bytes')) {
            return bin2hex(random_bytes($this->tokenLength / 2));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(openssl_random_pseudo_bytes($this->tokenLength / 2));
        } else {
            // 降级方案
            $bytes = '';
            for ($i = 0; $i < $this->tokenLength / 2; $i++) {
                $bytes .= chr(mt_rand(0, 255));
            }
            return bin2hex($bytes);
        }
    }
} 