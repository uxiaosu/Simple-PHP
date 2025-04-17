<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 主布局文件 (示例)
 */

use SimplePHP\Core\Config;

// --- 辅助函数 ---

/**
 * 生成相对于应用根目录的URL
 * @param string $path 路径
 * @return string 完整的URL
 */
function url($path = '') {
    $basePath = Config::get('app.base_path', '');
    $path = trim($path, '/');
    return rtrim($basePath ?: '/', '/') . '/' . $path;
}

/**
 * 生成带版本号的资源URL，用于缓存控制
 * @param string $path 资源路径 (相对于public目录)
 * @return string 带版本号的URL
 */
function asset($path) {
    $publicPath = ROOT_PATH . '/public';
    $filePath = $publicPath . '/' . ltrim($path, '/');
    $version = '';
    if (file_exists($filePath)) {
        $version = '?v=' . substr(md5_file($filePath), 0, 8);
    }
    return url($path) . $version;
}

// --- 安全与配置 ---

// CSRF令牌
$csrfToken = '';
if (class_exists('\SimplePHP\Core\Security\Csrf') && method_exists(
 SimplePHP\Core\Security\Csrf::class, 'getToken')) {
    try {
        // 假设CSRF实例可以通过某种方式获取，或者直接实例化
        // $csrf = new \SimplePHP\Core\Security\Csrf();
        // $csrfToken = $csrf->getToken(); 
        // 暂时使用Session作为示例
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $csrfToken = $_SESSION['csrf_token'];
    } catch (\Throwable $e) {
        error_log("CSRF Token Error: " . $e->getMessage());
    }
}

// 设置安全HTTP头
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
}

// 内容安全策略 (CSP)
// 注意：这是一个比较严格的示例，您可能需要根据实际使用的资源调整
$csp = [
    "default-src" => "'self'",
    "script-src"  => "'self' https://cdn.jsdelivr.net 'unsafe-inline'", // 允许内联脚本和CDN JS
    "style-src"   => "'self' https://cdn.jsdelivr.net 'unsafe-inline'", // 允许内联样式和CDN CSS
    "img-src"     => "'self' data: https://images.unsplash.com https:", // 允许本站、data URI、Unsplash图片
    "font-src"    => "'self' https://cdn.jsdelivr.net data:", // 允许本站、CDN字体和data URI
    "connect-src" => "'self'", // 允许Ajax连接到本站
    "frame-ancestors" => "'self'", // 只允许本站嵌入
    "form-action" => "'self'", // 表单提交到本站
];
$cspHeader = "Content-Security-Policy: " . implode('; ', array_map(function($k, $v) {
    return $k . ' ' . $v;
}, array_keys($csp), $csp));
header($cspHeader);

?>
<!DOCTYPE html>
<html lang="<?= Config::get('app.locale', 'zh-CN') ?>" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO 和 网站信息 -->
    <title><?= htmlspecialchars($this->title ?? Config::get('app.name', 'SimplePHP')) ?></title>
    <meta name="description" content="<?= htmlspecialchars($this->description ?? Config::get('app.description', 'A SimplePHP website')) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($this->keywords ?? 'SimplePHP, PHP, MVC') ?>">
    <meta name="theme-color" content="#4361ee"> <!-- 主题色 -->

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken) ?>">

    <!-- 网站图标 -->
    <link rel="icon" href="<?= url('/favicon.ico') ?>" type="image/x-icon">
    <link rel="shortcut icon" href="<?= url('/favicon.ico') ?>" type="image/x-icon">

    <!-- 预加载关键CSS/JS (可选，提升性能) -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" as="style">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js" as="script">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" as="script">
    
    <!-- 核心CSS -->
    <!-- 建议：如果CDN不稳定，下载到本地并使用 asset() 函数加载 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('/css/style.css') ?>">

    <!-- 页面特定头部内容 -->
    <?php if (isset($this->head)) echo $this->head; /* 用于视图注入额外<head>内容 */ ?>

</head>
<body class="d-flex flex-column min-vh-100">
    <!-- 页面加载进度条 -->
    <div class="progress fixed-top" style="height: 3px; z-index: 1100; display: none;" id="page-loading-container">
        <div class="progress-bar progress-bar-striped progress-bar-animated" id="page-loading-bar" role="progressbar" style="width: 0%; background: linear-gradient(90deg, #4361ee, #7209b7);"></div>
    </div>

    <!-- 导航栏 -->
    <nav class="site-navbar py-2 navbar-light" id="mainNav">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <!-- 品牌Logo -->
                <a class="navbar-brand d-flex align-items-center" href="<?= url('/') ?>">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-2">
                        <path d="M7 8L3 12L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17 8L21 12L17 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 4L10 20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span><?= Config::get('app.name', 'SimplePHP') ?></span>
                </a>
                
                <!-- 移动菜单按钮 -->
                <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavContent" aria-controls="mainNavContent" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- 导航内容 -->
                <div class="collapse navbar-collapse" id="mainNavContent">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link <?= ($this->currentRoute ?? '') === '' ? 'active' : '' ?>" href="<?= url('/') ?>"><i class="fas fa-home me-1"></i> 首页</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($this->currentRoute ?? '') === 'about' ? 'active' : '' ?>" href="<?= url('about') ?>"><i class="fas fa-info-circle me-1"></i> 关于</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= strpos(($this->currentRoute ?? ''), 'guide') === 0 ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-book me-1"></i> 文档
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= url('guide/basics') ?>"><i class="fas fa-rocket me-2 text-primary"></i>入门指南</a></li>
                                <li><a class="dropdown-item" href="<?= url('guide/c') ?>"><i class="fas fa-gamepad me-2 text-primary"></i>控制器</a></li>
                                <li><a class="dropdown-item" href="<?= url('guide/m') ?>"><i class="fas fa-database me-2 text-primary"></i>模型</a></li>
                                <li><a class="dropdown-item" href="<?= url('guide/v') ?>"><i class="fas fa-paint-brush me-2 text-primary"></i>视图</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= url('guide/adv') ?>"><i class="fas fa-graduation-cap me-2 text-primary"></i>高级教程</a></li>
                            </ul>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link <?= ($this->currentRoute ?? '') === 'api' ? 'active' : '' ?>" href="<?= url('api') ?>"><i class="fas fa-code me-1"></i> API</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($this->currentRoute ?? '') === 'blog' ? 'active' : '' ?>" href="<?= url('blog') ?>"><i class="fas fa-rss me-1"></i> 博客</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($this->currentRoute ?? '') === 'contact' ? 'active' : '' ?>" href="<?= url('contact') ?>"><i class="fas fa-envelope me-1"></i> 联系</a>
                        </li>
                    </ul>
                    
                    <!-- 右侧操作 -->
                    <div class="navbar-actions d-flex align-items-center">
                        <!-- 示例：登录/注册链接 -->
                        <!-- 
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="<?= url('dashboard') ?>" class="btn btn-sm btn-outline-secondary me-2">控制台</a>
                            <a href="<?= url('logout') ?>" class="btn btn-sm btn-danger">登出</a>
                        <?php else: ?>
                            <a href="<?= url('login') ?>" class="btn btn-sm btn-outline-primary me-2">登录</a>
                            <a href="<?= url('register') ?>" class="btn btn-sm btn-primary">注册</a>
                        <?php endif; ?>
                         -->

                        <!-- 深色模式切换 -->
                        <button id="darkModeToggle" class="theme-toggle ms-2" title="切换深色/浅色模式">
                            <i class="fas fa-moon" id="darkModeIcon"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- 主内容区 -->
    <main class="flex-shrink-0 py-4">
        <div class="container">
            <!-- 显示Flash消息 (如果框架支持) -->
            <?php /*
            if (function_exists('flash') && $message = flash()->get()) {
                echo '<div class="alert alert-' . ($message['type'] ?? 'info') . ' alert-dismissible fade show" role="alert">';
                echo $message['body'];
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                echo '</div>';
            }
            */ ?>

            <!-- 显示来自控制器设置的错误/成功消息 -->
            <?php if (isset($this->error)): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-times-circle me-2"></i> <?= htmlspecialchars($this->error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($this->success)): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($this->success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <!-- 页面内容注入点 -->
            <?= $content /* 由控制器 $this->view() 方法渲染的内容 */ ?>
        </div>
    </main>

    <!-- 页脚 -->
    <footer class="footer mt-auto py-4 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                    <small class="text-muted">&copy; <?= date('Y') ?> <?= Config::get('app.name', 'SimplePHP') ?>. All Rights Reserved.</small>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <ul class="list-inline mb-0 small">
                        <li class="list-inline-item"><a href="<?= url('privacy') ?>">隐私政策</a></li>
                        <li class="list-inline-item">|</li>
                        <li class="list-inline-item"><a href="<?= url('terms') ?>">服务条款</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <!-- 回到顶部按钮 -->
    <button id="back-to-top" class="btn btn-primary rounded-circle position-fixed shadow-lg" style="bottom: 20px; right: 20px; width: 45px; height: 45px; z-index: 1000;">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- 核心JS -->
    <!-- 建议：如果CDN不稳定，下载到本地并使用 asset() 函数加载 -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- 图片优化脚本 (确保已创建 public/js/image-lazy-load.js) -->
    <script src="<?= asset('/js/image-lazy-load.js') ?>" defer></script>
    
    <!-- 页面特定脚本 -->
    <?php if (isset($this->scripts)) echo $this->scripts; /* 用于视图注入额外脚本 */ ?>
    
    <!-- 通用内联脚本 -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 简单的页面加载指示器
        const loadingContainer = document.getElementById('page-loading-container');
        const loadingBar = document.getElementById('page-loading-bar');
        if (loadingContainer && loadingBar) {
            loadingContainer.style.display = 'block';
            let width = 10;
            loadingBar.style.width = width + '%';
            const interval = setInterval(function() {
                width += Math.random() * 10;
                if (width >= 95) {
                    width = 95; // 防止过早到达100%
                    clearInterval(interval);
                }
                loadingBar.style.width = width + '%';
            }, 200);
            
            window.addEventListener('load', function() {
                clearInterval(interval);
                loadingBar.style.width = '100%';
                setTimeout(function() {
                    loadingContainer.style.opacity = '0';
                    setTimeout(function() { loadingContainer.style.display = 'none'; }, 300);
                }, 300);
            });
        }

        // 深色模式切换逻辑
        const darkModeToggle = document.getElementById('darkModeToggle');
        const darkModeIcon = document.getElementById('darkModeIcon');
        const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        let isDarkMode = localStorage.getItem('darkMode') === 'true' || (localStorage.getItem('darkMode') === null && prefersDark);

        function applyDarkMode(state) {
            document.body.classList.toggle('dark-mode', state);
            if (darkModeIcon) {
                darkModeIcon.classList.toggle('fa-moon', !state);
                darkModeIcon.classList.toggle('fa-sun', state);
            }
        }

        applyDarkMode(isDarkMode);

        if (darkModeToggle) {
            darkModeToggle.addEventListener('click', function() {
                isDarkMode = !isDarkMode;
                localStorage.setItem('darkMode', isDarkMode);
                applyDarkMode(isDarkMode);
            });
        }
        
        // 回到顶部按钮逻辑
        const backToTop = document.getElementById('back-to-top');
        if (backToTop) {
            window.addEventListener('scroll', function() {
                if (window.scrollY > 200) {
                    backToTop.style.display = 'flex';
                } else {
                    backToTop.style.display = 'none';
                }
            });
            backToTop.addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
        
        // CSRF Token for AJAX (jQuery example)
        if (window.jQuery) {
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfTokenMeta) {
                $.ajaxSetup({
                    headers: { 'X-CSRF-TOKEN': csrfTokenMeta.getAttribute('content') }
                });
            }
        }
        
        // Bootstrap Tooltip初始化 (如果需要)
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Bootstrap Dropdown初始化 (确保下拉菜单工作)
        const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
        dropdownElementList.map(function (dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl);
        });

        console.log('SimplePHP Layout Initialized.');
    });
    </script>
</body>
</html> 