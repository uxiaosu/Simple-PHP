<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 安全类 - 提供各种安全功能
 */

namespace SimplePHP\Core\Security;

use SimplePHP\Core\Config;

class Security
{
    /**
     * 应用安全措施
     */
    public function applySecurity()
    {
        // 设置安全相关的HTTP头
        $this->setSecurityHeaders();
        
        // 设置安全的会话选项 - 使用严格模式
        $this->secureSession();
        
        // 启用CSRF保护 - 严格模式
        if (Config::get('security.csrf.enable', true)) {
            $this->protectAgainstCsrf();
        }
        
        // XSS过滤
        if (Config::get('security.xss.enable', true) && Config::get('security.xss.auto_clean', true)) {
            $this->cleanInput();
        }
        
        // 添加请求限速 - 强化限制
        if (Config::get('security.rate_limiting.enable', true)) {
            $this->applyRateLimiting();
        }
        
        // 安全监控 - 严格检测规则
        if (Config::get('security.monitoring.enable', true)) {
            $this->monitorSecurity();
        }
    }
    
    /**
     * 设置安全相关的HTTP头
     */
    private function setSecurityHeaders()
    {
        if (!Config::get('security.headers.enable', true)) {
            return;
        }
        
        // X-Frame-Options
        $xFrameOptions = Config::get('security.headers.x_frame_options', 'SAMEORIGIN');
        header("X-Frame-Options: " . (is_string($xFrameOptions) ? $xFrameOptions : 'SAMEORIGIN'));
        
        // X-XSS-Protection
        $xXssProtection = Config::get('security.headers.x_xss_protection', '1; mode=block');
        header("X-XSS-Protection: " . (is_string($xXssProtection) ? $xXssProtection : '1; mode=block'));
        
        // X-Content-Type-Options
        $xContentTypeOptions = Config::get('security.headers.x_content_type_options', 'nosniff');
        header("X-Content-Type-Options: " . (is_string($xContentTypeOptions) ? $xContentTypeOptions : 'nosniff'));
        
        // Content-Security-Policy
        $csp = Config::get('security.headers.content_security_policy');
        if ($csp) {
            if (is_array($csp)) {
                $cspString = '';
                foreach ($csp as $directive => $value) {
                    if (is_string($value)) {
                        $cspString .= $directive . ' ' . $value . '; ';
                    }
                }
                header("Content-Security-Policy: " . trim($cspString));
            } else if (is_string($csp)) {
                header("Content-Security-Policy: $csp");
            }
        }
        
        // Strict-Transport-Security
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $hsts = Config::get('security.headers.strict_transport_security');
            if ($hsts) {
                if (is_array($hsts)) {
                    $hstsString = '';
                    foreach ($hsts as $directive => $value) {
                        if (is_string($value) || is_numeric($value)) {
                            if (empty($value)) {
                                $hstsString .= $directive . '; ';
                            } else {
                                $hstsString .= $directive . '=' . $value . '; ';
                            }
                        }
                    }
                    header("Strict-Transport-Security: " . trim($hstsString));
                } else if (is_string($hsts)) {
                    header("Strict-Transport-Security: $hsts");
                }
            }
        }
        
        // Referrer-Policy
        $referrerPolicy = Config::get('security.headers.referrer_policy');
        if ($referrerPolicy) {
            header("Referrer-Policy: " . (is_string($referrerPolicy) ? $referrerPolicy : 'same-origin'));
        }
        
        // Feature-Policy
        $featurePolicy = Config::get('security.headers.feature_policy');
        if ($featurePolicy) {
            if (is_array($featurePolicy)) {
                $fpString = '';
                foreach ($featurePolicy as $feature => $value) {
                    if (is_string($value)) {
                        $fpString .= $feature . ' ' . $value . '; ';
                    }
                }
                header("Feature-Policy: " . trim($fpString));
            } else if (is_string($featurePolicy)) {
                header("Feature-Policy: $featurePolicy");
            }
        }
    }
    
    /**
     * 严格的会话安全设置
     */
    private function secureSession()
    {
        // 在设置cookie前调用
        if (session_status() === PHP_SESSION_NONE) {
            $sessionConfig = Config::get('security.session', []);
            
            // 设置会话cookie参数 - 强制安全设置
            session_set_cookie_params(
                0, // lifetime
                '/', // path
                '', // domain
                $sessionConfig['secure'] ?? true, // 强制启用secure
                $sessionConfig['httponly'] ?? true // httponly
            );
            
            // 使用仅cookie的会话
            ini_set('session.use_only_cookies', 1);
            
            // 设置会话名称
            session_name('SimplePHP_Session');
            
            // 启动会话
            session_start();
            
            // 会话固定攻击防护
            $this->preventSessionFixation();
            
            // 会话活动监控
            $this->monitorSessionActivity();
        }
    }
    
    /**
     * 防止会话固定攻击
     */
    private function preventSessionFixation()
    {
        $sessionConfig = Config::get('security.session', []);
        $regenerateInterval = $sessionConfig['regenerate_interval'] ?? 300; // 默认5分钟
        
        // 首次访问时初始化会话时间戳
        if (!isset($_SESSION['_security'])) {
            $_SESSION['_security'] = [
                'created_at' => time(),
                'last_regenerated' => time(),
                'client_ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
            ];
            
            // 首次访问立即重新生成ID
            session_regenerate_id(true);
            return;
        }
        
        // 定期重新生成会话ID
        if (time() - $_SESSION['_security']['last_regenerated'] > $regenerateInterval) {
            session_regenerate_id(true);
            $_SESSION['_security']['last_regenerated'] = time();
        }
        
        // 验证客户端指纹，防止会话劫持
        $currentIp = $_SERVER['REMOTE_ADDR'] ?? '';
        $currentUA = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // 检查客户端信息是否改变（可能的会话劫持）
        if ($sessionConfig['validate_ip'] ?? true) {
            if ($_SESSION['_security']['client_ip'] !== $currentIp) {
                // IP地址变更，销毁会话
                $this->destroySession();
                session_start();
                $_SESSION['_security_alert'] = 'possible_session_hijack_ip';
                return;
            }
        }
        
        // 检查用户代理是否改变
        if ($sessionConfig['validate_ua'] ?? true) {
            if ($_SESSION['_security']['user_agent'] !== $currentUA) {
                // 用户代理变更，销毁会话
                $this->destroySession();
                session_start();
                $_SESSION['_security_alert'] = 'possible_session_hijack_ua';
                return;
            }
        }
    }
    
    /**
     * 监控会话活动
     */
    private function monitorSessionActivity()
    {
        $sessionConfig = Config::get('security.session', []);
        $idleTimeout = $sessionConfig['idle_timeout'] ?? 1800; // 默认30分钟
        $absoluteTimeout = $sessionConfig['absolute_timeout'] ?? 14400; // 默认4小时
        
        $now = time();
        
        // 首次访问时初始化时间戳
        if (!isset($_SESSION['_last_activity'])) {
            $_SESSION['_last_activity'] = $now;
        }
        
        // 检查绝对超时（会话总时长限制）
        if (isset($_SESSION['_security']) && ($now - $_SESSION['_security']['created_at'] > $absoluteTimeout)) {
            // 会话已超过最大允许时间
            $this->destroySession();
            session_start();
            $_SESSION['_security_alert'] = 'session_expired';
            return;
        }
        
        // 检查闲置超时
        if ($now - $_SESSION['_last_activity'] > $idleTimeout) {
            // 会话闲置时间过长
            $this->destroySession();
            session_start();
            $_SESSION['_security_alert'] = 'session_idle_timeout';
            return;
        }
        
        // 更新最后活动时间
        $_SESSION['_last_activity'] = $now;
    }
    
    /**
     * 安全销毁会话
     */
    public function destroySession()
    {
        // 清空会话数据
        $_SESSION = [];
        
        // 删除会话cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        // 销毁会话
        session_destroy();
    }
    
    /**
     * 严格的CSRF保护
     */
    private function protectAgainstCsrf()
    {
        try {
            $csrfConfig = Config::get('security.csrf', []);
            $tokenName = $csrfConfig['token_name'] ?? 'csrf_token';
            
            // 确保会话已启动
            if (session_status() === PHP_SESSION_NONE) {
                @session_start();
            }
            
            // 确保每个会话都有一个CSRF令牌
            if (!isset($_SESSION[$tokenName]) || empty($_SESSION[$tokenName])) {
                $_SESSION[$tokenName] = $this->generateToken($csrfConfig['token_length'] ?? 32);
                $_SESSION[$tokenName . '_time'] = time();
            }
            
            // 检查令牌是否过期（默认1小时）
            $tokenLifetime = $csrfConfig['cookie_lifetime'] ?? 3600;
            if (isset($_SESSION[$tokenName . '_time']) && 
                (time() - $_SESSION[$tokenName . '_time'] > $tokenLifetime)) {
                // 令牌已过期，重新生成
                $_SESSION[$tokenName] = $this->generateToken($csrfConfig['token_length'] ?? 32);
                $_SESSION[$tokenName . '_time'] = time();
            }
            
            // 只验证POST/PUT/DELETE等非安全请求
            $requestMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
            if (in_array($requestMethod, ['GET', 'HEAD', 'OPTIONS'])) {
                return;
            }
            
            // 获取提交的令牌 (从表单或者HTTP头)
            $userToken = $_POST[$tokenName] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
            
            // 如果令牌为空则通过HTTP头再次尝试
            if (empty($userToken) && function_exists('getallheaders')) {
                $headers = getallheaders();
                $userToken = $headers['X-CSRF-TOKEN'] ?? $headers['X-Csrf-Token'] ?? null;
            }
            
            // 检查例外URL路径
            $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
            $csrfExceptions = $csrfConfig['exceptions'] ?? [];
            foreach ($csrfExceptions as $pattern) {
                if ($this->pathMatch($pattern, $currentPath)) {
                    return;
                }
            }
            
            // 验证CSRF令牌
            if (empty($userToken) || !$this->compareToken($userToken, $_SESSION[$tokenName])) {
                // CSRF验证失败，记录安全事件
                $this->logSecurityEvent('csrf_validation_failed', [
                    'user_token' => $userToken,
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    'path' => $currentPath,
                    'method' => $requestMethod
                ]);
                
                // 中止请求处理，返回403状态码
                http_response_code(403);
                
                // 显示自定义错误页面
                echo '<!DOCTYPE html>
                <html lang="zh-CN">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>安全验证失败</title>
                    <style>
                        body {
                            font-family: system-ui, -apple-system, sans-serif;
                            background-color: #f8f9fa;
                            color: #212529;
                            line-height: 1.5;
                            margin: 0;
                            padding: 0;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            min-height: 100vh;
                        }
                        .container {
                            max-width: 600px;
                            padding: 2rem;
                            background: white;
                            border-radius: 8px;
                            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
                            text-align: center;
                        }
                        h1 {
                            color: #dc3545;
                            margin-bottom: 1rem;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h1>安全验证失败</h1>
                        <p>CSRF令牌验证失败，为保障您的安全，系统已阻止此请求。</p>
                        <p>请返回上一页重试，或联系管理员寻求帮助。</p>
                    </div>
                </body>
                </html>';
                
                exit;
            }
            
            // 验证成功后，是否需要重新生成令牌
            if (isset($csrfConfig['regenerate']) && $csrfConfig['regenerate']) {
                $_SESSION[$tokenName] = $this->generateToken($csrfConfig['token_length'] ?? 32);
                $_SESSION[$tokenName . '_time'] = time();
            }
        } catch (\Throwable $e) {
            // 记录错误但继续执行
            error_log("CSRF保护异常: " . $e->getMessage());
            
            // 在开发环境可以重新抛出异常，生产环境则静默处理
            if (Config::get('app.debug', false)) {
                throw $e;
            }
        }
    }
    
    /**
     * 清理输入数据以防止XSS攻击
     */
    private function cleanInput()
    {
        $_GET = $this->sanitizeData($_GET);
        $_POST = $this->sanitizeData($_POST);
        $_COOKIE = $this->sanitizeData($_COOKIE);
        $_REQUEST = $this->sanitizeData($_REQUEST);
    }
    
    /**
     * 递归清理数据
     *
     * @param mixed $data 要清理的数据
     * @return mixed
     */
    public function sanitizeData($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitizeData($value);
            }
            return $data;
        }
        
        // 清理字符串
        if (is_string($data)) {
            // 移除空字符串
            $data = str_replace(chr(0), '', $data);
            
            // HTML特殊字符转换
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
        
        return $data;
    }
    
    /**
     * 根据配置的密码策略验证密码强度
     *
     * @param string $password 密码
     * @return bool|array 如果密码符合策略返回true，否则返回错误数组
     */
    public function validatePassword($password)
    {
        $errors = [];
        $passwordConfig = Config::get('security.password', []);
        
        // 检查长度
        $minLength = $passwordConfig['min_length'] ?? 8;
        if (strlen($password) < $minLength) {
            $errors[] = "密码长度必须至少为{$minLength}个字符";
        }
        
        // 检查是否包含大小写混合
        if (isset($passwordConfig['require_mixed_case']) && $passwordConfig['require_mixed_case']) {
            if (!preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password)) {
                $errors[] = "密码必须同时包含大写和小写字母";
            }
        }
        
        // 检查是否包含数字
        if (isset($passwordConfig['require_numbers']) && $passwordConfig['require_numbers']) {
            if (!preg_match('/[0-9]/', $password)) {
                $errors[] = "密码必须包含至少一个数字";
            }
        }
        
        // 检查是否包含特殊字符
        if (isset($passwordConfig['require_symbols']) && $passwordConfig['require_symbols']) {
            if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
                $errors[] = "密码必须包含至少一个特殊字符";
            }
        }
        
        return empty($errors) ? true : $errors;
    }
    
    /**
     * 使用安全的哈希算法哈希密码
     *
     * @param string $password 要哈希的密码
     * @return string
     */
    public function hashPassword($password)
    {
        $hashAlgo = Config::get('security.password.hash_algo', PASSWORD_ARGON2ID);
        return password_hash($password, $hashAlgo);
    }
    
    /**
     * 验证密码是否匹配哈希
     *
     * @param string $password 明文密码
     * @param string $hash 密码哈希
     * @return bool
     */
    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
    
    /**
     * 检查上传文件的安全性
     *
     * @param array $file 上传的文件数组($_FILES['file'])
     * @return bool|string 成功返回true，失败返回错误信息
     */
    public function validateUploadedFile($file)
    {
        $fileConfig = Config::get('security.file_upload', []);
        $allowedTypes = $fileConfig['allowed_types'] ?? [];
        $maxSize = $fileConfig['max_size'] ?? 5242880; // 默认5MB
        
        // 检查上传错误
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return "文件上传失败，错误代码：" . $file['error'];
        }
        
        // 检查文件大小
        if ($file['size'] > $maxSize) {
            return "文件大小超过限制";
        }
        
        // 检查文件类型
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!empty($allowedTypes) && !in_array($fileExt, $allowedTypes)) {
            return "不允许的文件类型";
        }
        
        // 检查文件内容
        if (isset($fileConfig['scan_files']) && $fileConfig['scan_files']) {
            // 简单的文件内容检查
            $content = file_get_contents($file['tmp_name']);
            
            // 检查PHP代码
            if (preg_match('/<\?php/i', $content)) {
                return "文件包含PHP代码，不允许上传";
            }
            
            // 检查可能的恶意JavaScript
            if (preg_match('/<script[^>]*>.*?<\/script>/is', $content)) {
                return "文件包含JavaScript代码，不允许上传";
            }
        }
        
        return true;
    }
    
    /**
     * 防止SQL注入（基本过滤，主要依赖于预编译语句）
     *
     * @param string $value 要过滤的值
     * @return string
     */
    public function preventSqlInjection($value)
    {
        if (!is_string($value)) {
            return $value;
        }
        
        // 移除危险的SQL字符
        $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
        $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
        
        return str_replace($search, $replace, $value);
    }
    
    /**
     * 获取当前CSRF令牌
     *
     * @return string
     */
    public function getCsrfToken()
    {
        // 确保会话已初始化
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // 获取令牌名称并确保它是字符串类型
        $tokenName = Config::get('security.csrf.token_name', 'csrf_token');
        if (!is_string($tokenName) && !is_int($tokenName)) {
            $tokenName = 'csrf_token'; // 使用默认值
        }
        
        // 检查令牌是否存在
        if (!isset($_SESSION) || !is_array($_SESSION) || !isset($_SESSION[$tokenName])) {
            // 如果令牌不存在，生成一个新的
            $_SESSION[$tokenName] = $this->generateToken(32);
        }
        
        return $_SESSION[$tokenName];
    }
    
    /**
     * 生成CSRF表单字段
     *
     * @return string HTML表单字段
     */
    public function csrfField()
    {
        $tokenName = Config::get('security.csrf.token_name', 'csrf_token');
        $token = $this->getCsrfToken();
        
        return '<input type="hidden" name="' . $tokenName . '" value="' . $token . '">';
    }
    
    /**
     * 验证密码是否符合安全策略
     *
     * @param string $password 密码
     * @return array [bool $valid, string $message]
     */
    public function validatePasswordStrength($password)
    {
        // ... existing code ...
    }
    
    /**
     * RBAC - 基于角色的访问控制系统
     */
    
    /**
     * 检查用户是否拥有指定权限
     *
     * @param string|array $permission 单个权限或权限数组
     * @param int|null $userId 用户ID，默认为当前用户
     * @return bool
     */
    public function hasPermission($permission, $userId = null)
    {
        // 获取当前用户ID
        if ($userId === null && isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
        }
        
        // 未登录用户没有任何权限
        if (!$userId) {
            return false;
        }
        
        // 获取用户的角色
        $userRoles = $this->getUserRoles($userId);
        
        // 超级管理员角色拥有所有权限
        if (in_array('admin', $userRoles)) {
            return true;
        }
        
        // 检查单个权限
        if (is_string($permission)) {
            return $this->checkSinglePermission($permission, $userRoles);
        }
        
        // 检查多个权限（所有权限都要满足）
        if (is_array($permission)) {
            foreach ($permission as $perm) {
                if (!$this->checkSinglePermission($perm, $userRoles)) {
                    return false;
                }
            }
            return true;
        }
        
        return false;
    }
    
    /**
     * 检查用户是否有指定角色
     *
     * @param string|array $role 角色名或角色数组
     * @param int|null $userId 用户ID，默认为当前用户
     * @return bool
     */
    public function hasRole($role, $userId = null)
    {
        // 获取当前用户ID
        if ($userId === null && isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
        }
        
        // 未登录用户没有任何角色
        if (!$userId) {
            return false;
        }
        
        $userRoles = $this->getUserRoles($userId);
        
        // 检查单个角色
        if (is_string($role)) {
            return in_array($role, $userRoles);
        }
        
        // 检查多个角色（满足其中之一即可）
        if (is_array($role)) {
            foreach ($role as $r) {
                if (in_array($r, $userRoles)) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * 获取用户的所有角色
     *
     * @param int $userId 用户ID
     * @return array 角色名数组
     */
    private function getUserRoles($userId)
    {
        // 这里应该是从数据库中获取用户角色
        // 为演示目的，这里使用模拟数据
        
        // 从缓存中获取
        static $userRolesCache = [];
        
        if (isset($userRolesCache[$userId])) {
            return $userRolesCache[$userId];
        }
        
        // 在实际应用中，这里应该查询数据库
        // 例如: SELECT r.name FROM roles r JOIN user_roles ur ON r.id = ur.role_id WHERE ur.user_id = ?
        
        // 这里仅供演示
        $roles = [];
        
        // 模拟数据库查询
        try {
            $db = new \SimplePHP\Core\Database\Database();
            $result = $db->query("SELECT role_name FROM user_roles WHERE user_id = ?", [$userId]);
            
            if ($result) {
                foreach ($result as $row) {
                    $roles[] = $row['role_name'];
                }
            }
        } catch (\Exception $e) {
            error_log("获取用户角色失败: " . $e->getMessage());
            // 失败时返回空角色数组
        }
        
        // 缓存结果
        $userRolesCache[$userId] = $roles;
        
        return $roles;
    }
    
    /**
     * 检查单个权限
     *
     * @param string $permission 权限名
     * @param array $userRoles 用户角色数组
     * @return bool
     */
    private function checkSinglePermission($permission, $userRoles)
    {
        // 在实际应用中，这里应该查询数据库获取角色对应的权限
        // 例如: SELECT 1 FROM role_permissions rp JOIN roles r ON rp.role_id = r.id 
        //       WHERE r.name IN (?) AND rp.permission = ? LIMIT 1
        
        // 这里仅供演示
        $rolePermissions = [
            'editor' => ['content.create', 'content.edit', 'content.view'],
            'viewer' => ['content.view'],
            'manager' => ['content.create', 'content.edit', 'content.delete', 'content.view', 'user.view'],
            'admin' => ['*']  // 通配符表示所有权限
        ];
        
        foreach ($userRoles as $role) {
            if (isset($rolePermissions[$role])) {
                // 通配符检查
                if (in_array('*', $rolePermissions[$role])) {
                    return true;
                }
                
                // 精确权限匹配
                if (in_array($permission, $rolePermissions[$role])) {
                    return true;
                }
                
                // 支持通配符权限 (例如: user.*)
                if (strpos($permission, '.') !== false) {
                    $permPrefix = substr($permission, 0, strrpos($permission, '.') + 1) . '*';
                    if (in_array($permPrefix, $rolePermissions[$role])) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * 严格的请求频率限制
     */
    private function applyRateLimiting()
    {
        $config = Config::get('security.rate_limiting', []);
        $storageMethod = $config['storage'] ?? 'file'; // 可选: file, session, database
        
        // 获取客户端标识符
        $identifier = $this->getClientIdentifier();
        
        // 读取配置的限制规则
        $rules = $config['rules'] ?? [
            // 默认规则 - 更严格的限制
            ['interval' => 60, 'limit' => 60, 'paths' => ['*']]
        ];
        
        // 获取当前路径
        $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        
        // 应用所有匹配的规则
        foreach ($rules as $rule) {
            $interval = $rule['interval'] ?? 60; // 默认60秒
            $limit = $rule['limit'] ?? 60; // 默认60个请求
            $paths = $rule['paths'] ?? ['*'];
            
            // 检查当前路径是否匹配规则
            $pathMatched = false;
            foreach ($paths as $path) {
                if ($path === '*' || (strpos($path, '*') !== false && $this->pathMatch($path, $currentPath)) || $path === $currentPath) {
                    $pathMatched = true;
                    break;
                }
            }
            
            if (!$pathMatched) {
                continue; // 不匹配此规则，继续下一个
            }
            
            // 获取请求计数
            $key = "rate_limit:{$identifier}:{$interval}";
            $requestCount = $this->getRateLimitCount($key, $storageMethod);
            
            // 增加请求计数
            $requestCount++;
            $this->setRateLimitCount($key, $requestCount, $interval, $storageMethod);
            
            // 如果请求次数超过限制，则返回429状态码
            if ($requestCount > $limit) {
                // 设置重试头
                header('Retry-After: ' . $interval);
                http_response_code(429); // Too Many Requests
                
                // 记录限流事件
                $this->logSecurityEvent('rate_limit_exceeded', [
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    'path' => $currentPath,
                    'limit' => $limit,
                    'interval' => $interval,
                    'count' => $requestCount
                ]);
                
                // 输出错误消息
                echo '<!DOCTYPE html>';
                echo '<html lang="zh">';
                echo '<head>';
                echo '<meta charset="UTF-8">';
                echo '<title>请求频率过高</title>';
                echo '<style>';
                echo 'body { font-family: Arial, sans-serif; margin: 30px; line-height: 1.6; }';
                echo '.container { max-width: 600px; margin: 0 auto; padding: 20px; border-radius: 5px; background: #f8f8f8; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }';
                echo 'h1 { color: #d35400; }';
                echo '</style>';
                echo '</head>';
                echo '<body>';
                echo '<div class="container">';
                echo '<h1>请求频率过高</h1>';
                echo '<p>您的请求频率超过了系统限制，请稍后再试。</p>';
                echo '<p>请在 ' . $interval . ' 秒后重试。</p>';
                echo '</div>';
                echo '</body>';
                echo '</html>';
                exit;
            }
        }
    }
    
    /**
     * 获取客户端标识符
     *
     * @return string
     */
    private function getClientIdentifier()
    {
        // 组合IP地址和用户代理
        $identifier = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        // 如果用户已登录，使用用户ID进一步识别
        if (isset($_SESSION['user_id'])) {
            $identifier .= '|' . $_SESSION['user_id'];
        }
        
        return md5($identifier);
    }
    
    /**
     * 获取请求计数
     *
     * @param string $key 缓存键
     * @param string $method 存储方法
     * @return int
     */
    private function getRateLimitCount($key, $method)
    {
        switch ($method) {
            case 'session':
                return isset($_SESSION[$key]) ? $_SESSION[$key] : 0;
                
            case 'database':
                try {
                    $db = new \SimplePHP\Core\Database\Database();
                    $result = $db->query("SELECT count, expires FROM rate_limits WHERE `key` = ?", [$key]);
                    if ($result && count($result) > 0) {
                        // 检查是否过期
                        if (time() > $result[0]['expires']) {
                            return 0; // 已过期
                        }
                        return (int)$result[0]['count'];
                    }
                } catch (\Exception $e) {
                    error_log("获取速率限制失败: " . $e->getMessage());
                }
                return 0;
                
            case 'file':
            default:
                $cacheDir = ROOT_PATH . '/storage/cache';
                if (!is_dir($cacheDir)) {
                    mkdir($cacheDir, 0755, true);
                }
                
                $file = $cacheDir . '/' . md5($key) . '.cache';
                if (file_exists($file)) {
                    $data = file_get_contents($file);
                    if ($data) {
                        $cacheData = json_decode($data, true);
                        if ($cacheData && isset($cacheData['count']) && isset($cacheData['expires'])) {
                            // 检查是否过期
                            if (time() > $cacheData['expires']) {
                                return 0; // 已过期
                            }
                            return (int)$cacheData['count'];
                        }
                    }
                }
                return 0;
        }
    }
    
    /**
     * 设置请求计数
     *
     * @param string $key 缓存键
     * @param int $count 计数
     * @param int $interval 过期时间(秒)
     * @param string $method 存储方法
     */
    private function setRateLimitCount($key, $count, $interval, $method)
    {
        $expires = time() + $interval;
        
        switch ($method) {
            case 'session':
                $_SESSION[$key] = $count;
                if (!isset($_SESSION["{$key}_expires"])) {
                    $_SESSION["{$key}_expires"] = $expires;
                }
                break;
                
            case 'database':
                try {
                    $db = new \SimplePHP\Core\Database\Database();
                    $db->query(
                        "INSERT INTO rate_limits (`key`, count, expires) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE count = ?, expires = ?",
                        [$key, $count, $expires, $count, $expires]
                    );
                } catch (\Exception $e) {
                    error_log("更新速率限制失败: " . $e->getMessage());
                }
                break;
                
            case 'file':
            default:
                $cacheDir = ROOT_PATH . '/storage/cache';
                if (!is_dir($cacheDir)) {
                    mkdir($cacheDir, 0755, true);
                }
                
                $file = $cacheDir . '/' . md5($key) . '.cache';
                $data = json_encode(['count' => $count, 'expires' => $expires]);
                file_put_contents($file, $data, LOCK_EX);
                break;
        }
    }
    
    /**
     * 检查路径是否匹配模式
     *
     * @param string $pattern 匹配模式
     * @param string $path 请求路径
     * @return bool
     */
    private function pathMatch($pattern, $path)
    {
        // 转义特殊字符，将*替换为正则表达式
        $pattern = str_replace(['/', '.', '*'], ['\/', '\.', '.*'], $pattern);
        $pattern = '/^' . $pattern . '$/';
        
        return preg_match($pattern, $path) === 1;
    }
    
    /**
     * 严格的安全监控系统
     */
    private function monitorSecurity()
    {
        $config = Config::get('security.monitoring', []);
        
        // 记录所有请求
        if ($config['log_all_requests'] ?? true) {
            $this->logRequest();
        }
        
        // 检测可疑活动
        $this->detectSuspiciousActivity();
    }
    
    /**
     * 检测可疑活动
     */
    private function detectSuspiciousActivity()
    {
        // 检查HTTP头部中的异常
        $this->checkSuspiciousHeaders();
        
        // 检查请求参数中的可疑输入
        $this->checkSuspiciousInput();
        
        // 检查异常的请求模式
        $this->checkAbnormalRequestPatterns();
    }
    
    /**
     * 检查可疑的HTTP头
     */
    private function checkSuspiciousHeaders()
    {
        // 判断当前环境
        $environment = Config::get('app.environment', 'production');
        $isLocalEnvironment = ($environment === 'development' || 
                            $_SERVER['REMOTE_ADDR'] === '127.0.0.1' || 
                            $_SERVER['REMOTE_ADDR'] === '::1');
        
        // 在本地环境中不阻止请求
        if ($isLocalEnvironment) {
            return;
        }
        
        // 检查常见的代理头以及可疑的HTTP头部
        $suspiciousHeaders = [];
        
        // 代理相关头部
        $proxyHeaders = [
            'HTTP_VIA', 
            'HTTP_X_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'HTTP_X_FORWARDED_HOST',
            'HTTP_X_FORWARDED_SERVER',
            'HTTP_FORWARDED_FOR',
            'HTTP_X_REAL_IP'
        ];
        
        // 本地环境不检查代理头部
        if (!$isLocalEnvironment) {
            foreach ($proxyHeaders as $header) {
                if (isset($_SERVER[$header]) && !empty($_SERVER[$header])) {
                    $suspiciousHeaders[$header] = $_SERVER[$header];
                }
            }
        }
        
        // 检查不匹配的内容类型
        if (isset($_SERVER['CONTENT_TYPE']) && 
            $_SERVER['REQUEST_METHOD'] === 'POST' && 
            !in_array($_SERVER['CONTENT_TYPE'], [
                'application/x-www-form-urlencoded',
                'multipart/form-data',
                'application/json',
                'text/plain', // 添加更多常见类型
                'application/xml',
                'text/xml'
            ])) {
            $suspiciousHeaders['CONTENT_TYPE'] = $_SERVER['CONTENT_TYPE'];
        }
        
        // 检查不常见的用户代理
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $ua = $_SERVER['HTTP_USER_AGENT'];
            
            // 检查空或异常短的UA
            if (empty($ua) || strlen($ua) < 5) {
                $suspiciousHeaders['HTTP_USER_AGENT'] = $ua;
            }
            
            // 检查包含已知扫描工具关键字的UA
            $suspiciousTerms = ['sqlmap', 'nikto', 'nessus', 'acunetix', 'burpsuite', 'nmap'];
            foreach ($suspiciousTerms as $term) {
                if (stripos($ua, $term) !== false) {
                    $suspiciousHeaders['HTTP_USER_AGENT'] = $ua;
                    break;
                }
            }
        }
        
        if (!empty($suspiciousHeaders)) {
            $this->logSecurityEvent('suspicious_headers', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
                'headers' => $suspiciousHeaders
            ]);
            
            // 根据配置决定是否阻止请求
            if (Config::get('security.monitoring.block_suspicious_headers', false)) {
                header('HTTP/1.1 403 Forbidden');
                echo '<!DOCTYPE html>';
                echo '<html lang="zh">';
                echo '<head>';
                echo '<meta charset="UTF-8">';
                echo '<title>访问被拒绝</title>';
                echo '<style>';
                echo 'body { font-family: Arial, sans-serif; margin: 30px; line-height: 1.6; }';
                echo '.container { max-width: 600px; margin: 0 auto; padding: 20px; border-radius: 5px; background: #f8f8f8; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }';
                echo 'h1 { color: #d35400; }';
                echo '</style>';
                echo '</head>';
                echo '<body>';
                echo '<div class="container">';
                echo '<h1>访问被拒绝</h1>';
                echo '<p>您的请求包含可疑的HTTP头信息，已被系统拦截。</p>';
                echo '<p>如果您认为这是误报，请联系网站管理员。</p>';
                echo '</div>';
                echo '</body>';
                echo '</html>';
                exit;
            }
        }
    }
    
    /**
     * 检查请求中的可疑输入
     */
    private function checkSuspiciousInput()
    {
        // 判断当前环境
        $environment = Config::get('app.environment', 'production');
        $isLocalEnvironment = ($environment === 'development' || 
                              $_SERVER['REMOTE_ADDR'] === '127.0.0.1' || 
                              $_SERVER['REMOTE_ADDR'] === '::1');
        
        // 在本地环境中放行白名单IP
        $ipWhitelist = Config::get('security.access_control.ip_whitelist', []);
        if ($isLocalEnvironment || in_array($_SERVER['REMOTE_ADDR'], $ipWhitelist)) {
            // 仅记录不拦截
            $blockRequest = false;
        } else {
            $blockRequest = Config::get('security.monitoring.block_suspicious', true);
        }
        
        // 合并所有输入
        $input = array_merge($_GET, $_POST, $_COOKIE);
        
        // 排除安全相关参数
        $excludeKeys = ['csrf_token', 'token', '_token', 'debug_key'];
        
        // 检查SQL注入模式 - 更精确的模式以减少误报
        $sqlPatterns = [
            '/\bUNION\s+ALL\s+SELECT\b.*?FROM/i',
            '/\bDROP\s+TABLE\s+[a-zA-Z0-9_]+/i',
            '/\bFROM\s+information_schema\./i',
            '/\bDELETE\s+FROM\s+[a-zA-Z0-9_]+/i',
            '/\bINSERT\s+INTO\s+[a-zA-Z0-9_]+\s*\(/i',
            '/\bSLEEP\s*\(\s*\d+\s*\)/i',
            '/\bBENCHMARK\s*\(\s*\d+\s*,/i',
            '/\bWAITFOR\s+DELAY\b/i'
        ];
        
        // 检查XSS模式 - 更精确的模式以减少误报
        $xssPatterns = [
            '/<script[^>]*>[^<]*<\/script>/i', 
            '/<iframe[^>]*>[^<]*<\/iframe>/i',
            '/javascript\s*:\s*[a-z0-9\s\(\)]+/i', 
            '/eval\s*\(\s*.*?\s*\)/i',
            '/document\.cookie.*?=/i',
            '/document\.location\s*=/i',
            '/onload\s*=\s*["\'][^"\']*["\']/i',
            '/onclick\s*=\s*["\'][^"\']*["\']/i',
            '/onerror\s*=\s*["\'][^"\']*["\']/i'
        ];
        
        // 检查命令注入模式 - 更精确的模式以减少误报
        $commandPatterns = [
            '/;\s*rm\s+-rf/i',
            '/;\s*del\s+[\/\\\\]/i',
            '/`(rm|del|chmod|wget|curl|bash|sh|cmd|powershell)/i',
            '/\|\s*(rm|del|chmod|wget|curl|bash|sh|cmd|powershell)/i',
            '/system\s*\(\s*["\'][^"\']*["\']\s*\)/i',
            '/exec\s*\(\s*["\'][^"\']*["\']\s*\)/i',
            '/shell_exec\s*\(\s*["\'][^"\']*["\']\s*\)/i'
        ];
        
        // 检查文件包含模式 - 更精确的模式以减少误报
        $lfiPatterns = [
            '/\.\.\/(etc|var|proc|sys)\/[a-z0-9]+/i',
            '/\.\.\\\\(windows|system32|boot)\\\\[a-z0-9]+/i',
            '/\/etc\/passwd/i',
            '/\/etc\/shadow/i',
            '/c:\\\\windows\\\\system32/i'
        ];
        
        $suspiciousInputs = [];
        
        // 检查每个输入
        foreach ($input as $key => $value) {
            // 排除安全相关键和二进制数据
            if (in_array($key, $excludeKeys) || !is_string($value)) {
                continue;
            }
            
            // 只检查有足够长度的字符串，避免短字符串误报
            if (strlen($value) < 8) {
                continue;
            }
            
            // 检查SQL注入
            foreach ($sqlPatterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    $suspiciousInputs['sql_injection'][$key] = $value;
                    break;
                }
            }
            
            // 检查XSS
            foreach ($xssPatterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    $suspiciousInputs['xss'][$key] = $value;
                    break;
                }
            }
            
            // 检查命令注入
            foreach ($commandPatterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    $suspiciousInputs['command_injection'][$key] = $value;
                    break;
                }
            }
            
            // 检查文件包含
            foreach ($lfiPatterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    $suspiciousInputs['lfi'][$key] = $value;
                    break;
                }
            }
        }
        
        if (!empty($suspiciousInputs)) {
            $this->logSecurityEvent('suspicious_input', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
                'inputs' => $suspiciousInputs
            ]);
            
            // 阻止请求
            if ($blockRequest) {
                header('HTTP/1.1 403 Forbidden');
                echo '<!DOCTYPE html>';
                echo '<html lang="zh">';
                echo '<head>';
                echo '<meta charset="UTF-8">';
                echo '<title>访问被拒绝</title>';
                echo '<style>';
                echo 'body { font-family: Arial, sans-serif; margin: 30px; line-height: 1.6; }';
                echo '.container { max-width: 600px; margin: 0 auto; padding: 20px; border-radius: 5px; background: #f8f8f8; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }';
                echo 'h1 { color: #d35400; }';
                echo '</style>';
                echo '</head>';
                echo '<body>';
                echo '<div class="container">';
                echo '<h1>访问被拒绝</h1>';
                echo '<p>您的请求包含可疑内容，已被系统拦截。</p>';
                echo '<p>如果您认为这是误报，请联系网站管理员。</p>';
                if ($isLocalEnvironment) {
                    echo '<div style="margin-top: 20px; padding: 10px; background-color: #f0f0f0; border-left: 5px solid #3498db;">';
                    echo '<p><strong>调试信息</strong>：检测到以下可疑内容</p>';
                    echo '<pre>' . htmlspecialchars(json_encode($suspiciousInputs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>';
                    echo '</div>';
                }
                echo '</div>';
                echo '</body>';
                echo '</html>';
                exit;
            }
        }
    }
    
    /**
     * 检查异常的请求模式
     */
    private function checkAbnormalRequestPatterns()
    {
        // 判断当前环境
        $environment = Config::get('app.environment', 'production');
        $isLocalEnvironment = ($environment === 'development' || 
                            $_SERVER['REMOTE_ADDR'] === '127.0.0.1' || 
                            $_SERVER['REMOTE_ADDR'] === '::1');
                            
        // 在本地环境中不检查异常请求模式
        if ($isLocalEnvironment) {
            return;
        }
        
        // 检查异常的请求方法
        $allowedMethods = ['GET', 'POST', 'HEAD', 'PUT', 'DELETE', 'OPTIONS'];
        if (!in_array($_SERVER['REQUEST_METHOD'], $allowedMethods)) {
            $this->logSecurityEvent('abnormal_request_method', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown'
            ]);
            
            header('HTTP/1.1 405 Method Not Allowed');
            header('Allow: GET, POST, HEAD, PUT, DELETE, OPTIONS');
            exit;
        }
        
        // 获取配置的限制值
        $maxGetParams = Config::get('security.monitoring.max_get_params', 20);
        $maxPostParams = Config::get('security.monitoring.max_post_params', 50);
        $maxUriLength = Config::get('security.monitoring.max_uri_length', 2000);
        $maxCookies = Config::get('security.monitoring.max_cookies', 30);
        
        // 检查异常的URL参数数量
        if (count($_GET) > $maxGetParams) {
            $this->logSecurityEvent('too_many_get_params', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
                'count' => count($_GET)
            ]);
        }
        
        // 检查异常的POST参数数量
        if (count($_POST) > $maxPostParams) {
            $this->logSecurityEvent('too_many_post_params', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
                'count' => count($_POST)
            ]);
        }
        
        // 检查请求URI长度
        if (isset($_SERVER['REQUEST_URI']) && strlen($_SERVER['REQUEST_URI']) > $maxUriLength) {
            $this->logSecurityEvent('uri_too_long', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'uri_length' => strlen($_SERVER['REQUEST_URI'])
            ]);
            
            header('HTTP/1.1 414 URI Too Long');
            exit;
        }
        
        // 检查Cookie数量
        if (count($_COOKIE) > $maxCookies) {
            $this->logSecurityEvent('too_many_cookies', [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'count' => count($_COOKIE)
            ]);
        }
    }
    
    /**
     * 记录请求信息
     */
    private function logRequest()
    {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
            'uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'referer' => $_SERVER['HTTP_REFERER'] ?? '',
            'user_id' => $_SESSION['user_id'] ?? 0
        ];
        
        $this->logSecurityEvent('request', $logData);
    }
    
    /**
     * 记录安全事件
     *
     * @param string $type 事件类型
     * @param array $data 事件数据
     */
    private function logSecurityEvent($type, $data)
    {
        $logDir = ROOT_PATH . '/storage/logs/security';
        
        // 确保日志目录存在
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // 创建日志文件名
        $date = date('Y-m-d');
        $logFile = $logDir . '/security-' . $date . '.log';
        
        // 格式化日志数据
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => $type,
            'data' => $data
        ];
        
        // 写入日志
        file_put_contents(
            $logFile,
            json_encode($logData, JSON_UNESCAPED_UNICODE) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
        
        // 可选：发送严重安全事件的警报
        $alertTypes = Config::get('security.monitoring.alert_types', []);
        $alertThreshold = Config::get('security.monitoring.alert_threshold', 'high');
        
        $severeTypes = [
            'sql_injection' => 'high',
            'xss' => 'medium',
            'command_injection' => 'high',
            'rate_limit_exceeded' => 'medium',
            'possible_session_hijack_ip' => 'high',
            'possible_session_hijack_ua' => 'medium'
        ];
        
        // 检查是否应该发送警报
        if (isset($severeTypes[$type]) && 
            in_array($type, $alertTypes) && 
            $this->isAlertSeverityAtOrAbove($severeTypes[$type], $alertThreshold)) {
            
            $this->sendSecurityAlert($type, $data);
        }
    }
    
    /**
     * 检查警报严重性是否达到或超过阈值
     *
     * @param string $severity 当前严重性
     * @param string $threshold 阈值
     * @return bool
     */
    private function isAlertSeverityAtOrAbove($severity, $threshold)
    {
        $levels = ['low' => 1, 'medium' => 2, 'high' => 3, 'critical' => 4];
        
        return ($levels[$severity] ?? 0) >= ($levels[$threshold] ?? 0);
    }
    
    /**
     * 发送安全警报
     *
     * @param string $type 事件类型
     * @param array $data 事件数据
     */
    private function sendSecurityAlert($type, $data)
    {
        // 此函数可以实现发送邮件、SMS或通过API通知管理员
        // 这里仅记录警报，实际实现应根据项目需求
        $alertLogDir = ROOT_PATH . '/storage/logs/alerts';
        
        // 确保警报日志目录存在
        if (!is_dir($alertLogDir)) {
            mkdir($alertLogDir, 0755, true);
        }
        
        // 创建警报日志文件名
        $date = date('Y-m-d');
        $alertFile = $alertLogDir . '/alert-' . $date . '.log';
        
        // 格式化警报数据
        $alertData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => $type,
            'data' => $data,
            'message' => '检测到安全威胁: ' . $type
        ];
        
        // 写入警报日志
        file_put_contents(
            $alertFile,
            json_encode($alertData, JSON_UNESCAPED_UNICODE) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
        
        // TODO: 实现实际的警报通知机制，如邮件或SMS
    }
    
    /**
     * 安全文件上传处理
     *
     * @param array $fileData $_FILES中的文件数据
     * @param array $options 上传选项
     * @return array [bool $success, string $message, string $path]
     */
    public function handleFileUpload($fileData, $options = [])
    {
        // 默认选项
        $defaultOptions = [
            'allowedTypes' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'],
            'maxSize' => 5 * 1024 * 1024, // 5MB
            'uploadDir' => ROOT_PATH . '/storage/uploads',
            'randomizeName' => true,
            'validateContent' => true,
            'scanForViruses' => false,
            'createDirIfNotExists' => true,
            'overwrite' => false
        ];
        
        // 合并选项
        $options = array_merge($defaultOptions, $options);
        
        // 文件上传错误检查
        if (!isset($fileData['error']) || is_array($fileData['error'])) {
            return [false, '无效的文件上传参数', ''];
        }
        
        // 检查上传错误
        switch ($fileData['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return [false, '文件大小超过限制', ''];
            case UPLOAD_ERR_PARTIAL:
                return [false, '文件仅部分上传', ''];
            case UPLOAD_ERR_NO_FILE:
                return [false, '没有文件被上传', ''];
            case UPLOAD_ERR_NO_TMP_DIR:
                return [false, '缺少临时文件夹', ''];
            case UPLOAD_ERR_CANT_WRITE:
                return [false, '文件写入失败', ''];
            case UPLOAD_ERR_EXTENSION:
                return [false, '文件上传被扩展停止', ''];
            default:
                return [false, '未知上传错误', ''];
        }
        
        // 检查文件大小
        if ($fileData['size'] > $options['maxSize']) {
            return [false, '文件大小超过限制: ' . $this->formatFileSize($options['maxSize']), ''];
        }
        
        // 检查MIME类型
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($fileData['tmp_name']);
        
        // 获取文件扩展名
        $extension = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));
        
        // 验证文件类型
        if (!in_array($extension, $options['allowedTypes'])) {
            return [false, '不允许的文件类型: ' . $extension, ''];
        }
        
        // 进一步验证文件MIME类型与扩展名是否匹配
        if ($options['validateContent']) {
            $validMimeTypes = $this->getValidMimeTypes($extension);
            if (!in_array($mimeType, $validMimeTypes)) {
                return [false, '文件内容与扩展名不匹配', ''];
            }
            
            // 检查图片文件的内容
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                if (!$this->isValidImage($fileData['tmp_name'], $extension)) {
                    return [false, '无效的图片文件', ''];
                }
            }
        }
        
        // 创建上传目录
        if ($options['createDirIfNotExists'] && !is_dir($options['uploadDir'])) {
            if (!mkdir($options['uploadDir'], 0755, true)) {
                return [false, '无法创建上传目录', ''];
            }
        }
        
        // 生成安全的文件名
        $newFileName = $this->generateSecureFileName($fileData['name'], $options['randomizeName']);
        $uploadFilePath = $options['uploadDir'] . '/' . $newFileName;
        
        // 检查文件是否已存在
        if (file_exists($uploadFilePath) && !$options['overwrite']) {
            return [false, '文件已存在', ''];
        }
        
        // 移动上传的文件
        if (!move_uploaded_file($fileData['tmp_name'], $uploadFilePath)) {
            return [false, '无法保存上传的文件', ''];
        }
        
        // 设置安全的文件权限
        chmod($uploadFilePath, 0644);
        
        // 记录上传事件
        $this->logSecurityEvent('file_upload', [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'filename' => $newFileName,
            'original_name' => $fileData['name'],
            'size' => $fileData['size'],
            'mime_type' => $mimeType
        ]);
        
        return [true, '文件上传成功', $uploadFilePath];
    }
    
    /**
     * 格式化文件大小
     *
     * @param int $bytes 文件大小（字节）
     * @return string
     */
    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
    
    /**
     * 获取扩展名对应的有效MIME类型
     *
     * @param string $extension 文件扩展名
     * @return array
     */
    private function getValidMimeTypes($extension)
    {
        $mimeMap = [
            'jpg' => ['image/jpeg', 'image/pjpeg'],
            'jpeg' => ['image/jpeg', 'image/pjpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'xls' => ['application/vnd.ms-excel'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'txt' => ['text/plain'],
            'zip' => ['application/zip', 'application/x-zip-compressed'],
            'rar' => ['application/x-rar-compressed'],
            'mp4' => ['video/mp4'],
            'mp3' => ['audio/mpeg']
        ];
        
        return isset($mimeMap[$extension]) ? $mimeMap[$extension] : [];
    }
    
    /**
     * 验证图片文件
     *
     * @param string $filePath 文件路径
     * @param string $extension 文件扩展名
     * @return bool
     */
    private function isValidImage($filePath, $extension)
    {
        // 检查文件头部字节签名
        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            return false;
        }
        
        $header = fread($handle, 12);
        fclose($handle);
        
        $hexHeader = bin2hex(substr($header, 0, 8));
        
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                // JPEG文件以FFD8开头
                return strpos($hexHeader, 'ffd8') === 0;
                
            case 'png':
                // PNG文件头: 89 50 4E 47 0D 0A 1A 0A
                return $hexHeader === '89504e470d0a1a0a';
                
            case 'gif':
                // GIF文件头: GIF87a 或 GIF89a
                return substr($header, 0, 6) === 'GIF87a' || substr($header, 0, 6) === 'GIF89a';
                
            default:
                return false;
        }
    }
    
    /**
     * 生成安全的文件名
     *
     * @param string $originalName 原始文件名
     * @param bool $randomize 是否随机化文件名
     * @return string
     */
    private function generateSecureFileName($originalName, $randomize = true)
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        
        if ($randomize) {
            // 生成基于时间和随机字符串的唯一文件名
            return time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        } else {
            // 清理文件名并保留原始名称
            $filename = pathinfo($originalName, PATHINFO_FILENAME);
            // 移除特殊字符
            $cleanName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $filename);
            // 移除多余的下划线
            $cleanName = preg_replace('/_+/', '_', $cleanName);
            // 截断长文件名
            $cleanName = substr($cleanName, 0, 100);
            
            return $cleanName . '_' . time() . '.' . $extension;
        }
    }
    
    /**
     * 生成安全的随机令牌
     *
     * @param int $length 令牌长度
     * @return string
     */
    public function generateToken($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }
    
    /**
     * 安全比较两个令牌（防止时序攻击）
     *
     * @param string $userToken 用户提供的令牌
     * @param string $storedToken 存储的令牌
     * @return bool
     */
    private function compareToken($userToken, $storedToken)
    {
        return hash_equals($storedToken, $userToken);
    }
} 