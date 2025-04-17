 # SimplePHP框架服务器配置指南

## 简介

SimplePHP框架支持两种服务器环境进行开发和部署：PHP内置服务器（开发环境）和Nginx服务器（生产环境）。本文档说明这两种环境下可能遇到的问题及解决方案。

## 1. PHP内置服务器 (开发环境)

### 1.1 启动方法

```bash
# 在项目根目录执行
php -S localhost:8000 -t public
```

### 1.2 优点
- 无需额外配置
- 自动处理路由请求
- 错误直接显示在控制台
- 适合快速开发和测试

### 1.3 常见问题与解决方案

#### 问题：端口被占用
```
Error: Address already in use
```

**解决方案**：
```bash
# 使用其他端口
php -S localhost:8080 -t public
```

#### 问题：路由无法正确解析
**解决方案**：
在public目录下创建router.php文件：
```php
<?php
// router.php
if (preg_match('/\.(?:css|js|png|jpg|jpeg|gif)$/', $_SERVER["REQUEST_URI"])) {
    return false; // 静态文件由服务器直接处理
} else {
    include __DIR__ . '/index.php'; // 所有非静态请求交给框架处理
}
```

然后启动时指定路由文件：
```bash
php -S localhost:8000 -t public public/router.php
```

## 2. Nginx服务器 (生产环境)

### 2.1 基本配置

```nginx
server {
    listen 80;
    server_name example.com;
    
    root /path/to/SimplePHP/public;
    index index.php index.html;
    
    # 关键：URL重写规则
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # PHP处理
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        
        # PATH_INFO支持(关键)
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }
}
```

### 2.2 常见问题与解决方案

#### 问题1: 404错误 - "404 Not Found nginx"
当访问诸如`/api`等路由时收到404错误。

**原因**：
1. Nginx配置中缺少URL重写规则
2. 未将请求正确转发到PHP处理器

**解决方案**：
确保配置中包含以下关键部分：
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

#### 问题2: PHP脚本执行，但路由无效
可以访问静态文件和直接的PHP文件，但框架路由不工作。

**原因**：
1. FastCGI配置不正确
2. PATH_INFO参数未正确传递

**解决方案**：
添加以下配置：
```nginx
location ~ [^/]\.php(/|$) {
    try_files $uri =404;
    fastcgi_split_path_info ^(.+\.php)(/.+)$;
    fastcgi_param PATH_INFO $fastcgi_path_info;
    # 其他fastcgi参数...
}
```

#### 问题3: 宝塔面板环境特殊处理
**解决方案**：
1. 找到网站的主配置文件（通常在`/www/server/panel/vhost/nginx/`）
2. 直接在server块内添加路由规则，而不是使用rewrite目录
3. 重启Nginx服务

## 3. 环境差异及调试技巧

### 3.1 环境差异对比

| 特性 | PHP内置服务器 | Nginx服务器 |
|------|--------------|------------|
| 路由处理 | 自动处理 | 需要显式配置 |
| 性能 | 低(单进程) | 高(多进程) |
| 静态文件 | 慢 | 快 |
| 配置复杂度 | 低 | 高 |
| 适用场景 | 开发测试 | 生产环境 |

### 3.2 调试技巧

1. **检查PHP-FPM/CGI状态**:
   ```bash
   # 检查进程
   ps aux | grep php
   # 或Windows:
   tasklist | findstr php
   
   # 检查监听端口
   netstat -anp | grep php-fpm
   # 或Windows:
   netstat -ano | findstr "9000"
   ```

2. **测试PHP处理**:
   创建测试文件(public/test.php):
   ```php
   <?php 
   echo "Server Info:<pre>"; 
   print_r($_SERVER);
   echo "</pre>";
   ```
   
3. **Nginx错误日志**:
   ```bash
   tail -f /var/log/nginx/error.log
   # 或Windows:
   type D:\path\to\nginx\logs\error.log
   ```

4. **框架调试模式**:
   在`config/app.php`中启用:
   ```php
   'debug' => true,
   'display_errors' => true,
   ```

## 4. 最佳实践建议

1. **开发流程**:
   - 使用PHP内置服务器进行开发和初步测试
   - 部署到Nginx前在本地Nginx环境中测试
   - 使用相同版本的PHP-FPM/CGI

2. **配置管理**:
   - 为不同环境(开发/测试/生产)创建不同的Nginx配置
   - 在版本控制中包含Nginx配置样例
   - 记录配置变更

3. **安全考虑**:
   - 生产环境禁用PHP错误显示
   - 限制对敏感目录的访问
   - 使用HTTPS

---

## 快速故障排除流程

如果遇到路由问题，按照以下步骤排查：

1. 确认PHP脚本可以执行(创建test.php并访问)
2. 检查PHP-FPM/CGI是否运行在正确端口
3. 验证Nginx配置包含正确的URL重写规则
4. 检查Nginx错误日志
5. 确保PATH_INFO参数正确传递
6. 重启Nginx和PHP-FPM/CGI服务

—— SimplePHP开发团队