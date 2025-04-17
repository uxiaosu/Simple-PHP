<?php
/**
 * 路由配置文件
 * 定义所有应用程序的路由
 */

// 返回路由配置数组
return [
    // 基础路由
    ['GET', '/', 'HomeController@index'],
    ['GET', '/about', 'HomeController@about'],
    ['GET', '/contact', 'HomeController@contact'],
    ['POST', '/contact', 'HomeController@contact'],

    // API路由
    ['GET', '/api', 'DocsController@api'],
    
    // API v1 路由组
    ['GET', '/api/v1', 'ApiController@indexAction'],
    ['GET', '/api/v1/server-info', 'ApiController@serverInfoAction'],
    ['POST', '/api/v1/form', 'ApiController@formHandlerAction'],
    ['POST', '/api/v1/upload', 'ApiController@uploadAction'],
    
    // 新增的API路由
    ['GET', '/api/users', 'Api\UserController@index'],
    ['GET', '/api/users/{id}', 'Api\UserController@show'],
    ['POST', '/api/users', 'Api\UserController@store'],
    ['PUT', '/api/users/{id}', 'Api\UserController@update'],
    ['DELETE', '/api/users/{id}', 'Api\UserController@destroy'],
    
    // 认证API
    ['POST', '/api/auth/login', 'Api\AuthController@login'],
    ['POST', '/api/auth/logout', 'Api\AuthController@logout'],
    ['GET', '/api/auth/user', 'Api\AuthController@user'],
    
    // 仪表盘API
    ['GET', '/api/dashboard/stats', 'Api\DashboardController@stats'],
    ['GET', '/api/dashboard/activity', 'Api\DashboardController@activity'],

    // 文档路由
    ['GET', '/docs', 'DocsController@index'],
    ['GET', '/docs/getting-started', 'DocsController@gettingStarted'],
    ['GET', '/docs/controllers', 'DocsController@controllers'],
    ['GET', '/docs/models', 'DocsController@models'],
    ['GET', '/docs/views', 'DocsController@views'],
    ['GET', '/docs/advanced', 'DocsController@advanced'],

    // 安全的文档路由别名 - 使用更抽象的标识符
    ['GET', '/guide', 'DocsController@index'],
    ['GET', '/guide/basics', 'DocsController@gettingStarted'],
    ['GET', '/guide/c', 'DocsController@controllers'],
    ['GET', '/guide/m', 'DocsController@models'],
    ['GET', '/guide/v', 'DocsController@views'],
    ['GET', '/guide/adv', 'DocsController@advanced'],

    // 博客与其他页面路由
    ['GET', '/blog', 'HomeController@blog'],
    ['GET', '/faq', 'HomeController@faq'],
    ['GET', '/changelog', 'HomeController@changelog'],
    ['GET', '/privacy', 'HomeController@privacy'],
    ['GET', '/terms', 'HomeController@terms'],
    
    // SPA管理后台路由 - 捕获所有子路径
    ['GET', '/admin', 'AdminController@index'],
    ['GET', '/admin/{path}', 'AdminController@index'],
]; 