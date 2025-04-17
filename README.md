# SimplePHP框架

一个轻量级、高安全性、高性能的PHP框架。

## 📋 目录

- [框架简介](#框架简介)
- [技术特点](#技术特点)
- [安装方法](#安装方法)
- [基本使用](#基本使用)
  - [路由配置](#路由配置)
  - [控制器](#控制器)
  - [模型](#模型)
  - [视图](#视图)
- [高级功能](#高级功能)
  - [缓存系统](#缓存系统)
  - [安全特性](#安全特性)
  - [API开发](#api开发)
  - [SPA应用支持](#spa应用支持)
- [性能优化](#性能优化)
- [贡献指南](#贡献指南)
- [许可证](#许可证)

## 框架简介

SimplePHP是一个专注于安全性和性能的轻量级PHP框架。它提供了简洁而强大的MVC架构，同时引入了现代化的特性如缓存系统、前端资源优化和安全措施，使开发人员能够快速构建高质量的Web应用程序。

## 技术特点

- **轻量级架构**：核心文件小，加载速度快
- **MVC设计模式**：清晰的代码组织结构
- **安全机制**：
  - CSRF保护
  - XSS防御
  - SQL注入防护
  - CSP策略
  - 安全的视图路径处理
- **高性能**：
  - 页面缓存系统
  - 数据缓存
  - 前端资源优化
  - HTTP缓存控制
- **现代化功能**：
  - RESTful API支持
  - SPA应用路由
  - 资源路由
  - 前端资源懒加载
- **开发者友好**：
  - 简洁的语法
  - 详细的文档
  - 友好的错误提示

## 安装方法

### 系统要求

- PHP 7.4或更高版本
- MySQL 5.7或更高版本
- 启用的PHP扩展：PDO, JSON, mbstring, fileinfo

### 安装步骤

1. 通过Composer安装（推荐）

```bash
composer create-project simplephp/framework my-project
cd my-project
```

2. 配置环境

复制`.env.example`文件到`.env`并根据你的环境进行配置：

```bash
cp .env.example .env
```

编辑`.env`文件设置数据库连接等信息。

3. 设置权限

```bash
chmod -R 755 public/
chmod -R 755 storage/
```

4. 启动开发服务器

```bash
php -S localhost:8000 -t public/
```

现在可以在浏览器中访问`http://localhost:8000`来查看你的应用。

## 基本使用

### 路由配置

路由配置位于`config/routes.php`文件中，使用简洁的语法定义：

```php
// 基本路由
$router->get('/', 'HomeController@index');
$router->post('/contact', 'ContactController@submit');

// 参数路由
$router->get('/user/{id}', 'UserController@show');

// 路由组
$router->group('/admin', function($router) {
    $router->get('/dashboard', 'AdminController@dashboard');
    $router->get('/users', 'AdminController@users');
});

// API路由
$router->api('/api/v1', function($router) {
    $router->resource('users', 'Api\UserController');
    $router->get('stats', 'Api\StatsController@index');
});

// SPA应用路由
$router->spa('/admin/{path?}', 'AdminController@index');
```

### 控制器

控制器位于`app/controllers`目录，示例：

```php
<?php
namespace App\Controllers;

use SimplePHP\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $data = [
            'title' => '首页',
            'message' => '欢迎使用SimplePHP'
        ];
        
        return $this->view('home/index', $data);
    }
}
```

### 模型

模型位于`app/models`目录，提供了简洁的ORM功能：

```php
<?php
namespace App\Models;

use SimplePHP\Core\Model;

class User extends Model
{
    // 表名，默认为类名的小写复数形式
    protected $table = 'users';
    
    // 主键
    protected $primaryKey = 'id';
    
    // 可批量赋值的字段
    protected $fillable = ['name', 'email', 'password'];
    
    // 自定义方法
    public function posts()
    {
        return $this->hasMany('App\Models\Post');
    }
    
    // 获取活跃用户
    public static function getActive()
    {
        return self::where('active', 1)->get();
    }
}
```

### 视图

视图文件位于`app/views`目录，使用纯PHP语法：

```php
<!-- app/views/home/index.php -->
<div class="jumbotron">
    <h1><?= htmlspecialchars($title) ?></h1>
    <p class="lead"><?= htmlspecialchars($message) ?></p>
    <hr class="my-4">
    <p>使用SimplePHP框架开始构建你的Web应用</p>
    <a class="btn btn-primary btn-lg" href="<?= url('guide') ?>" role="button">查看文档</a>
</div>
```

布局文件使用方式：

```php
// 在控制器中
public function about()
{
    $data = ['title' => '关于我们'];
    return $this->view('about', $data, 'layouts/main');
}
```

## 高级功能

### 缓存系统

SimplePHP提供强大的缓存机制，支持文件、Redis和Memcached驱动：

```php
// 配置在 config/cache.php
// 使用示例：
use SimplePHP\Core\Cache;

// 存储数据
Cache::set('key', 'value', 3600); // 缓存1小时

// 获取数据
$value = Cache::get('key');

// 检查是否存在
if (Cache::has('key')) {
    // 处理逻辑
}

// 删除缓存
Cache::delete('key');

// 清空所有缓存
Cache::flush();
```

### 安全特性

框架内置多种安全机制：

```php
// CSRF保护
// 在表单中添加CSRF令牌：
<form method="POST" action="<?= url('contact') ?>">
    <input type="hidden" name="_csrf" value="<?= csrf_token() ?>">
    <!-- 表单内容 -->
</form>

// XSS防护
// 安全输出用户数据
<?= htmlspecialchars($userInput) ?>

// SQL注入防护
// 使用参数化查询（Model中自动处理）
$users = User::where('status', $status)->get();
```

### API开发

SimplePHP专为API开发提供了便捷功能：

```php
// 在控制器中
public function index()
{
    $users = User::all();
    return $this->json($users);
}

// 自定义状态码和头部信息
public function store(Request $request)
{
    $user = User::create($request->all());
    return $this->json($user, 201, [
        'X-Created-At' => time()
    ]);
}
```

API资源路由：

```php
// 在routes.php中
$router->resource('users', 'Api\UserController');

// 生成以下路由：
// GET /users
// GET /users/{id}
// POST /users
// PUT /users/{id}
// DELETE /users/{id}
```

### SPA应用支持

SimplePHP支持单页应用开发：

```php
// 在routes.php中
$router->spa('/admin/{path?}', 'AdminController@index');

// 在控制器中
public function index()
{
    return $this->view('admin/app');
}
```

## 性能优化

框架提供了多种性能优化机制：

1. **HTTP缓存控制**

```php
// 在控制器中
public function show($id)
{
    $article = Article::find($id);
    return $this->view('articles/show', [
        'article' => $article
    ])->withCache(3600); // 缓存1小时
}
```

2. **前端资源优化**

```php
// 在视图中
<link rel="stylesheet" href="<?= asset('css/app.css') ?>">
<script src="<?= asset('js/app.js') ?>" defer></script>
```

3. **关键CSS内联**

```php
// 在布局中
<style><?= include_once(ROOT_PATH . '/public/css/critical.css') ?></style>
```

4. **图片懒加载**

```html
<img data-src="/images/example.jpg" class="lazy-load" alt="Example">
```

## 贡献指南

我们欢迎任何形式的贡献，包括问题报告、功能请求和代码提交：

1. Fork项目
2. 创建特性分支 (`git checkout -b feature/amazing-feature`)
3. 提交更改 (`git commit -m '添加了一些很棒的功能'`)
4. 推送到分支 (`git push origin feature/amazing-feature`)
5. 创建Pull Request

## 许可证

该项目采用MIT许可证 - 详情请查看 LICENSE 文件。 