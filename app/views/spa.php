<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * SPA应用模板
 */

use SimplePHP\Core\Frontend;

// 初始化前端类
$frontend = new Frontend();

// 获取CSRF令牌
$csrfToken = isset($this->csrf) && method_exists($this->csrf, 'getToken') 
    ? $this->csrf->getToken() : '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken) ?>">
    
    <title><?= $title ?? 'SimplePHP应用' ?></title>
    
    <!-- 基础样式 -->
    <link rel="stylesheet" href="/css/style.css">
    
    <!-- 传递初始状态到前端 -->
    <?= $frontend->renderInitialState($initialData ?? []) ?>
    
    <!-- 应用特定样式 -->
    <?= $frontend->css($cssBundle ?? 'app') ?>
</head>
<body>
    <!-- 应用挂载点 -->
    <div id="app">
        <!-- 初始加载指示器 -->
        <div class="loading-indicator">
            <div class="spinner"></div>
            <p>应用加载中...</p>
        </div>
    </div>
    
    <!-- 应用脚本 -->
    <?= $frontend->js($jsBundle ?? 'app') ?>
    
    <!-- 页面特定脚本 -->
    <?php if (isset($scripts)): ?>
        <?= $scripts ?>
    <?php endif; ?>
</body>
</html> 