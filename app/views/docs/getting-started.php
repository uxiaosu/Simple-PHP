<!-- 入门指南页面 -->
<div class="container py-5">
    <div class="row">
        <div class="col-lg-3">
            <!-- 文档导航 -->
            <div class="card border-0 shadow-sm sticky-lg-top mb-4" style="top: 2rem; z-index: 100;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 fw-bold">文档目录</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="/docs" class="list-group-item list-group-item-action">文档首页</a>
                        <a href="/docs/getting-started" class="list-group-item list-group-item-action active">入门指南</a>
                        <a href="/docs/installation" class="list-group-item list-group-item-action">安装配置</a>
                        <a href="/docs/routing" class="list-group-item list-group-item-action">路由系统</a>
                        <a href="/docs/controllers" class="list-group-item list-group-item-action">控制器</a>
                        <a href="/docs/models" class="list-group-item list-group-item-action">模型</a>
                        <a href="/docs/views" class="list-group-item list-group-item-action">视图</a>
                        <a href="/docs/database" class="list-group-item list-group-item-action">数据库</a>
                        <a href="/docs/validation" class="list-group-item list-group-item-action">验证</a>
                        <a href="/docs/security" class="list-group-item list-group-item-action">安全</a>
                        <a href="/docs/advanced" class="list-group-item list-group-item-action">高级主题</a>
                        <a href="/docs/deployment" class="list-group-item list-group-item-action">部署</a>
                        <a href="/docs/faq" class="list-group-item list-group-item-action">常见问题</a>
                    </div>
                </div>
            </div>
            
            <!-- 本页导航 -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">本页目录</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="#introduction" class="list-group-item list-group-item-action">介绍</a>
                        <a href="#requirements" class="list-group-item list-group-item-action">系统要求</a>
                        <a href="#installation" class="list-group-item list-group-item-action">安装</a>
                        <a href="#directory-structure" class="list-group-item list-group-item-action">目录结构</a>
                        <a href="#configuration" class="list-group-item list-group-item-action">配置</a>
                        <a href="#first-app" class="list-group-item list-group-item-action">创建第一个应用</a>
                    </div>
                </div>
            </div>
            
            <!-- 相关资源 -->
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">相关资源</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <a href="/docs/installation" class="d-flex align-items-center text-decoration-none text-body">
                                <i class="fas fa-download text-primary me-2"></i>
                                详细安装指南
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="/docs/configuration" class="d-flex align-items-center text-decoration-none text-body">
                                <i class="fas fa-cog text-primary me-2"></i>
                                配置指南
                            </a>
                        </li>
                        <li>
                            <a href="/docs/tutorials/blog" class="d-flex align-items-center text-decoration-none text-body">
                                <i class="fas fa-graduation-cap text-primary me-2"></i>
                                博客教程
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <!-- 文档内容 -->
            <div class="content">
                <h1 id="introduction" class="display-5 fw-bold mb-4">入门指南</h1>
                <p class="lead mb-5">本指南将帮助您快速了解 SimplePHP 框架，并指导您完成安装、配置和创建第一个应用的过程。</p>
                
                <!-- 系统要求 -->
                <section id="requirements" class="mb-5">
                    <h2 class="fw-bold border-bottom pb-2 mb-4">系统要求</h2>
                    <p>SimplePHP 框架有以下系统要求：</p>
                    
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <ul class="mb-0">
                                <li class="mb-2">PHP 7.4 或更高版本</li>
                                <li class="mb-2">OpenSSL PHP 扩展</li>
                                <li class="mb-2">PDO PHP 扩展</li>
                                <li class="mb-2">Mbstring PHP 扩展</li>
                                <li class="mb-2">JSON PHP 扩展</li>
                                <li>Composer 依赖管理器</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold">提示</h5>
                                <p class="mb-0">建议使用 PHP 8.0 或更高版本以获得最佳性能和功能支持。</p>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- 安装 -->
                <section id="installation" class="mb-5">
                    <h2 class="fw-bold border-bottom pb-2 mb-4">安装</h2>
                    <p>有两种方式安装 SimplePHP 框架：使用 Composer 或者下载 ZIP 包。</p>
                    
                    <h4 class="fw-bold mt-4 mb-3">方式一：使用 Composer 安装</h4>
                    <p>推荐使用 Composer 来安装 SimplePHP。在命令行中运行以下命令：</p>
                    
                    <div class="bg-dark text-white p-3 rounded mb-3">
                        <code>composer create-project simplephp/simplephp myapp</code>
                    </div>
                    
                    <p>这将在 <code>myapp</code> 目录中安装最新版本的 SimplePHP 框架。</p>
                    
                    <h4 class="fw-bold mt-4 mb-3">方式二：下载 ZIP 包</h4>
                    <p>您也可以直接从 GitHub 下载最新版本的 ZIP 包：</p>
                    
                    <div class="mb-3">
                        <a href="https://github.com/simplephp/framework/releases/latest" class="btn btn-primary">
                            <i class="fas fa-download me-2"></i> 下载最新版本
                        </a>
                    </div>
                    
                    <p>下载后，解压文件并配置您的 Web 服务器，将根目录指向 <code>public</code> 文件夹。</p>
                </section>
                
                <!-- 目录结构 -->
                <section id="directory-structure" class="mb-5">
                    <h2 class="fw-bold border-bottom pb-2 mb-4">目录结构</h2>
                    <p>安装完成后，您将看到以下目录结构：</p>
                    
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
<pre><code>myapp/
├── app/                  # 应用代码目录
│   ├── controllers/      # 控制器
│   ├── models/           # 模型
│   ├── views/            # 视图模板
│   └── middlewares/      # 中间件
├── config/               # 配置文件
├── core/                 # 框架核心代码
├── public/               # 公共访问目录
│   ├── css/              # CSS 文件
│   ├── js/               # JavaScript 文件
│   ├── img/              # 图像文件
│   └── index.php         # 入口文件
├── routes/               # 路由配置
├── storage/              # 存储目录
│   ├── logs/             # 日志文件
│   ├── cache/            # 缓存文件
│   └── uploads/          # 上传文件
├── vendor/               # Composer 依赖
├── .env                  # 环境配置
└── composer.json         # Composer 配置</code></pre>
                        </div>
                    </div>
                    
                    <p>每个目录都有特定的用途，这种清晰的组织结构可帮助您快速找到和管理应用程序的不同部分。</p>
                </section>
                
                <!-- 配置 -->
                <section id="configuration" class="mb-5">
                    <h2 class="fw-bold border-bottom pb-2 mb-4">配置</h2>
                    <p>SimplePHP 使用 <code>.env</code> 文件存储环境特定的配置，如数据库连接信息。安装后，您需要：</p>
                    
                    <ol class="mb-4">
                        <li class="mb-2">复制 <code>.env.example</code> 文件并重命名为 <code>.env</code></li>
                        <li class="mb-2">根据您的环境设置适当的值</li>
                    </ol>
                    
                    <p>典型的 <code>.env</code> 文件内容：</p>
                    
                    <div class="bg-light p-3 rounded mb-3">
<pre><code>APP_NAME=SimplePHP
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simplephp
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
SESSION_DRIVER=file
STORAGE_PATH=storage</code></pre>
                    </div>
                    
                    <div class="alert alert-warning">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold">重要提示</h5>
                                <p class="mb-0">不要将 <code>.env</code> 文件添加到版本控制系统中，因为它可能包含敏感信息。</p>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- 创建第一个应用 -->
                <section id="first-app" class="mb-5">
                    <h2 class="fw-bold border-bottom pb-2 mb-4">创建第一个应用</h2>
                    <p>让我们创建一个简单的 Hello World 应用程序，以了解 SimplePHP 的基本工作流程。</p>
                    
                    <h4 class="fw-bold mt-4 mb-3">第1步：创建路由</h4>
                    <p>打开 <code>routes/web.php</code> 文件并添加以下路由：</p>
                    
                    <div class="bg-light p-3 rounded mb-3">
<pre><code>$router->get('/hello', 'HelloController@index');</code></pre>
                    </div>
                    
                    <h4 class="fw-bold mt-4 mb-3">第2步：创建控制器</h4>
                    <p>在 <code>app/controllers</code> 目录中创建 <code>HelloController.php</code> 文件：</p>
                    
                    <div class="bg-light p-3 rounded mb-3">
<pre><code>&lt;?php

namespace App\Controllers;

use Core\Controller;

class HelloController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Hello World',
            'message' => 'Welcome to SimplePHP!'
        ];
        
        return $this->view('hello/index', $data);
    }
}</code></pre>
                    </div>
                    
                    <h4 class="fw-bold mt-4 mb-3">第3步：创建视图</h4>
                    <p>在 <code>app/views</code> 目录中创建 <code>hello/index.php</code> 文件：</p>
                    
                    <div class="bg-light p-3 rounded mb-3">
<pre><code>&lt;div class="container py-5">
    &lt;div class="row justify-content-center">
        &lt;div class="col-md-8">
            &lt;div class="card shadow">
                &lt;div class="card-body text-center">
                    &lt;h1 class="display-4 fw-bold">&lt;?= $message ?>&lt;/h1>
                    &lt;p class="lead">Congratulations! You've created your first SimplePHP application.&lt;/p>
                &lt;/div>
            &lt;/div>
        &lt;/div>
    &lt;/div>
&lt;/div></code></pre>
                    </div>
                    
                    <h4 class="fw-bold mt-4 mb-3">第4步：启动应用</h4>
                    <p>在命令行中，导航到项目根目录并运行内置的 PHP 开发服务器：</p>
                    
                    <div class="bg-dark text-white p-3 rounded mb-3">
                        <code>php -S localhost:8000 -t public</code>
                    </div>
                    
                    <p>现在，在浏览器中访问 <code>http://localhost:8000/hello</code>，您应该能看到您的第一个 SimplePHP 应用程序！</p>
                    
                    <div class="text-center mt-4">
                        <img src="https://via.placeholder.com/800x400?text=Hello+World+Screenshot" alt="Hello World 示例" class="img-fluid rounded shadow">
                    </div>
                </section>
                
                <!-- 下一步 -->
                <div class="card border-0 bg-light shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h3 class="fw-bold mb-3">下一步</h3>
                        <p>恭喜！您已经成功创建了第一个 SimplePHP 应用程序。接下来，您可以：</p>
                        <ul class="mb-4">
                            <li>了解更多关于 <a href="/docs/routing">路由系统</a> 的知识</li>
                            <li>深入研究 <a href="/docs/controllers">控制器</a> 的更多功能</li>
                            <li>学习如何使用 <a href="/docs/models">模型</a> 处理数据</li>
                            <li>探索 <a href="/docs/views">视图</a> 的高级功能</li>
                        </ul>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="/docs/routing" class="btn btn-primary">
                                <i class="fas fa-route me-2"></i> 路由系统
                            </a>
                            <a href="/docs/tutorials/blog" class="btn btn-outline-primary">
                                <i class="fas fa-graduation-cap me-2"></i> 博客教程
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 