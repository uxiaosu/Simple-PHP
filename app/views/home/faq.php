<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 常见问题页面
 */
?>
<div class="container py-5">
    <!-- 页面标题部分 -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto text-center">
            <h1 class="display-4 fw-bold mb-3">常见问题</h1>
            <p class="lead text-muted">查看关于SimplePHP框架的常见问题解答，帮助您更好地使用本框架。</p>
        </div>
    </div>
    
    <!-- FAQ内容 -->
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="accordion shadow-sm" id="faqAccordion">
                <!-- 问题1 -->
                <div class="accordion-item border-0 mb-3">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            SimplePHP框架是什么？
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>SimplePHP是一个轻量级、安全、易用的PHP MVC框架，专为现代Web应用程序开发而设计。它提供了清晰的架构和必要的工具，帮助开发者构建高效、安全的Web应用，同时保持代码的简洁和可维护性。</p>
                            <p>框架的主要特点包括：</p>
                            <ul>
                                <li>轻量级核心，仅包含必要功能</li>
                                <li>内置多种安全措施，防止常见Web攻击</li>
                                <li>简单直观的API，易于学习和使用</li>
                                <li>清晰的MVC架构，便于代码组织</li>
                                <li>强大的查询构建器和ORM功能</li>
                                <li>灵活且安全的路由系统</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- 问题2 -->
                <div class="accordion-item border-0 mb-3">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            如何安装SimplePHP框架？
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>安装SimplePHP框架非常简单，您可以通过以下方式之一进行安装：</p>
                            <h6 class="fw-bold mt-3">1. 使用Composer（推荐）</h6>
                            <pre><code>composer create-project simplephp/framework my-project</code></pre>
                            
                            <h6 class="fw-bold mt-3">2. 从GitHub克隆</h6>
                            <pre><code>git clone https://github.com/simplephp/framework.git my-project
cd my-project
composer install</code></pre>
                            
                            <h6 class="fw-bold mt-3">3. 直接下载</h6>
                            <p>从官方网站下载最新版本的SimplePHP，解压到您的Web服务器目录。</p>
                            
                            <p class="mt-3">安装完成后，请确保Web服务器的文档根目录指向<code>public</code>文件夹，并确保服务器有足够的权限读取和写入应用程序目录。</p>
                        </div>
                    </div>
                </div>
                
                <!-- 问题3 -->
                <div class="accordion-item border-0 mb-3">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            SimplePHP框架的系统要求是什么？
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>SimplePHP框架的系统要求相对较低，但为了获得最佳性能和安全性，我们建议：</p>
                            <ul>
                                <li>PHP 7.4或更高版本</li>
                                <li>MySQL 5.7+、MariaDB 10.3+或其他PDO支持的数据库</li>
                                <li>启用以下PHP扩展：
                                    <ul>
                                        <li>PDO PHP扩展</li>
                                        <li>JSON PHP扩展</li>
                                        <li>Mbstring PHP扩展</li>
                                        <li>Fileinfo PHP扩展</li>
                                    </ul>
                                </li>
                                <li>可选但推荐：
                                    <ul>
                                        <li>Opcache PHP扩展（提高性能）</li>
                                        <li>APCu PHP扩展（用于缓存）</li>
                                    </ul>
                                </li>
                            </ul>
                            <p>框架可以在Apache、Nginx或其他支持PHP的Web服务器上运行。</p>
                        </div>
                    </div>
                </div>
                
                <!-- 问题4 -->
                <div class="accordion-item border-0 mb-3">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            如何创建和定义路由？
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>在SimplePHP框架中，路由定义在<code>config/routes.php</code>文件中。每个路由都定义为一个包含HTTP方法、URI模式和处理器的数组。</p>
                            <p>基本路由定义示例：</p>
                            <pre><code>// 基础路由
return [
    ['GET', '/', 'HomeController@index'],
    ['GET', '/about', 'HomeController@about'],
    ['POST', '/contact', 'HomeController@contact'],
];</code></pre>
                            <p>您也可以定义带参数的路由：</p>
                            <pre><code>['GET', '/user/{id}', 'UserController@show'],
['GET', '/post/{slug}', 'PostController@show'],</code></pre>
                            <p>然后在控制器中，您可以接收这些参数：</p>
                            <pre><code>public function show($id)
{
    // 使用 $id 参数
}</code></pre>
                        </div>
                    </div>
                </div>
                
                <!-- 问题5 -->
                <div class="accordion-item border-0 mb-3">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                            SimplePHP框架如何处理数据库操作？
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>SimplePHP框架提供了直观的数据库抽象层，包括查询构建器和简单的ORM功能。数据库配置在<code>config/database.php</code>文件中定义。</p>
                            <p>基本查询示例：</p>
                            <pre><code>// 使用查询构建器
$users = $this->db->table('users')
    ->select('id', 'name', 'email')
    ->where('active', 1)
    ->orderBy('name', 'asc')
    ->get();

// 创建新记录
$userId = $this->db->table('users')->insert([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'created_at' => date('Y-m-d H:i:s')
]);</code></pre>
                            <p>使用模型：</p>
                            <pre><code>// 使用User模型
$user = new User();
$allUsers = $user->all();

// 查找特定用户
$user = $user->find(1);

// 更新用户
$user->name = 'Jane Doe';
$user->save();</code></pre>
                        </div>
                    </div>
                </div>
                
                <!-- 问题6 -->
                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingSix">
                        <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                            SimplePHP框架是否支持中间件？
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>是的，SimplePHP框架支持中间件模式，允许您在请求处理过程中插入自定义逻辑。中间件可以用于：</p>
                            <ul>
                                <li>身份验证和授权</li>
                                <li>CSRF保护</li>
                                <li>日志记录</li>
                                <li>请求限流</li>
                                <li>响应修改</li>
                            </ul>
                            <p>中间件定义示例：</p>
                            <pre><code>// 创建中间件类
class AuthMiddleware
{
    public function handle($request, $next)
    {
        if (!isset($_SESSION['user_id'])) {
            // 未登录，重定向到登录页面
            return redirect('/login');
        }
        
        // 继续处理请求
        return $next($request);
    }
}</code></pre>
                            <p>然后，您可以在路由配置中应用中间件：</p>
                            <pre><code>// 应用中间件到路由
['GET', '/dashboard', 'DashboardController@index', ['middleware' => 'auth']],</code></pre>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 联系支持 -->
            <div class="mt-5 p-4 bg-light rounded-3 text-center">
                <h3 class="fw-bold mb-3">没有找到您的问题？</h3>
                <p class="mb-4">如果您有其他问题，请随时联系我们的支持团队。</p>
                <a href="<?= url('contact') ?>" class="btn btn-primary px-4 py-2">
                    <i class="fas fa-envelope me-2"></i> 联系我们
                </a>
            </div>
        </div>
    </div>
</div>