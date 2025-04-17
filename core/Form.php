/**
 * 生成CSRF表单字段
 * 
 * @return string HTML表单字段
 */
public function csrfField()
{
    // 确保会话已启动
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
    
    // 获取CSRF令牌名称
    $tokenName = \SimplePHP\Core\Config::get('security.csrf.token_name', 'csrf_token');
    
    // 确保令牌存在
    if (!isset($_SESSION[$tokenName]) || empty($_SESSION[$tokenName])) {
        // 生成新令牌
        if (function_exists('random_bytes')) {
            $token = bin2hex(random_bytes(16));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $token = bin2hex(openssl_random_pseudo_bytes(16));
        } else {
            $token = md5(uniqid(mt_rand(), true));
        }
        
        $_SESSION[$tokenName] = $token;
        $_SESSION[$tokenName . '_time'] = time();
    }
    
    // 返回表单字段HTML
    return '<input type="hidden" name="' . htmlspecialchars($tokenName) . '" value="' . 
           htmlspecialchars($_SESSION[$tokenName]) . '">';
}

/**
 * 开始生成一个表单
 * 
 * @param string $action 表单提交地址
 * @param string $method 表单提交方法
 * @param array $attributes 表单其他属性
 * @param bool $withCsrf 是否自动添加CSRF保护字段
 * @return string
 */
public function open($action = '', $method = 'post', $attributes = [], $withCsrf = true)
{
    $attr = '';
    foreach ($attributes as $key => $value) {
        $attr .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
    }
    
    $form = '<form action="' . htmlspecialchars($action) . '" method="' . htmlspecialchars(strtolower($method)) . '"' . $attr . '>';
    
    // 如果是POST/PUT/DELETE表单且需要CSRF保护，自动添加令牌字段
    if ($withCsrf && in_array(strtolower($method), ['post', 'put', 'delete'])) {
        $form .= $this->csrfField();
    }
    
    return $form;
} 