<!-- 文档页面 -->
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
                        <a href="/docs" class="list-group-item list-group-item-action active">文档首页</a>
                        <a href="/docs/getting-started" class="list-group-item list-group-item-action">入门指南</a>
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
            
            <!-- 文档版本选择 -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">文档版本</h6>
                </div>
                <div class="card-body">
                    <select class="form-select">
                        <option selected>v2.0.0 (当前版本)</option>
                        <option>v1.5.0</option>
                        <option>v1.0.0</option>
                    </select>
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
                            <a href="/api" class="d-flex align-items-center text-decoration-none text-body">
                                <i class="fas fa-file-code text-primary me-2"></i>
                                API 文档
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="https://github.com/simplephp/framework" class="d-flex align-items-center text-decoration-none text-body">
                                <i class="fab fa-github text-primary me-2"></i>
                                GitHub 仓库
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="https://github.com/simplephp/framework/issues" class="d-flex align-items-center text-decoration-none text-body">
                                <i class="fas fa-bug text-primary me-2"></i>
                                报告问题
                            </a>
                        </li>
                        <li>
                            <a href="https://github.com/simplephp/framework/releases" class="d-flex align-items-center text-decoration-none text-body">
                                <i class="fas fa-tags text-primary me-2"></i>
                                版本发布
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <!-- 文档标题 -->
            <h1 class="display-5 fw-bold mb-4">SimplePHP 文档</h1>
            <p class="lead mb-5">欢迎使用 SimplePHP 框架文档。本文档将帮助您快速上手并充分利用框架的所有功能。</p>
            
            <!-- 快速开始卡片 -->
            <div class="card border-0 bg-primary text-white shadow mb-5">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h2 class="fw-bold mb-3">快速开始</h2>
                            <p class="mb-4">新手？从这里开始了解如何安装和配置 SimplePHP，以及如何创建您的第一个应用程序。</p>
                            <a href="/docs/getting-started" class="btn btn-light">
                                <i class="fas fa-rocket me-2"></i> 开始使用
                            </a>
                        </div>
                        <div class="col-lg-4 d-none d-lg-block text-center">
                            <i class="fas fa-graduation-cap fa-6x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 文档分类 -->
            <h3 class="fw-bold border-bottom pb-2 mb-4">核心概念</h3>
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="feature-icon d-inline-flex align-items-center justify-content-center bg-primary bg-gradient text-white fs-4 rounded-3 me-3">
                                    <i class="fas fa-route"></i>
                                </div>
                                <h5 class="fw-bold mb-0">路由系统</h5>
                            </div>
                            <p class="text-muted mb-3">了解如何定义应用程序的路由，包括基本路由、参数路由、路由分组和中间件。</p>
                            <a href="/docs/routing" class="text-decoration-none">查看文档 <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="feature-icon d-inline-flex align-items-center justify-content-center bg-primary bg-gradient text-white fs-4 rounded-3 me-3">
                                    <i class="fas fa-gamepad"></i>
                                </div>
                                <h5 class="fw-bold mb-0">控制器</h5>
                            </div>
                            <p class="text-muted mb-3">控制器负责处理请求并生成响应。了解如何创建和使用控制器来组织应用程序逻辑。</p>
                            <a href="/docs/controllers" class="text-decoration-none">查看文档 <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="feature-icon d-inline-flex align-items-center justify-content-center bg-primary bg-gradient text-white fs-4 rounded-3 me-3">
                                    <i class="fas fa-database"></i>
                                </div>
                                <h5 class="fw-bold mb-0">模型</h5>
                            </div>
                            <p class="text-muted mb-3">模型代表应用程序中的数据结构。了解如何使用模型与数据库交互并实现业务逻辑。</p>
                            <a href="/docs/models" class="text-decoration-none">查看文档 <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="feature-icon d-inline-flex align-items-center justify-content-center bg-primary bg-gradient text-white fs-4 rounded-3 me-3">
                                    <i class="fas fa-paint-brush"></i>
                                </div>
                                <h5 class="fw-bold mb-0">视图</h5>
                            </div>
                            <p class="text-muted mb-3">视图负责生成应用程序的用户界面。了解如何创建和渲染视图模板。</p>
                            <a href="/docs/views" class="text-decoration-none">查看文档 <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <h3 class="fw-bold border-bottom pb-2 mb-4">数据库和存储</h3>
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="feature-icon d-inline-flex align-items-center justify-content-center bg-primary bg-gradient text-white fs-4 rounded-3 me-3">
                                    <i class="fas fa-database"></i>
                                </div>
                                <h5 class="fw-bold mb-0">数据库配置</h5>
                            </div>
                            <p class="text-muted mb-3">学习如何配置和管理数据库连接，包括支持MySQL、PostgreSQL和SQLite。</p>
                            <a href="/docs/database" class="text-decoration-none">查看文档 <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="feature-icon d-inline-flex align-items-center justify-content-center bg-primary bg-gradient text-white fs-4 rounded-3 me-3">
                                    <i class="fas fa-table"></i>
                                </div>
                                <h5 class="fw-bold mb-0">查询构建器</h5>
                            </div>
                            <p class="text-muted mb-3">使用流畅的查询构建器API，以简单直观的方式构建SQL查询。</p>
                            <a href="/docs/database/query-builder" class="text-decoration-none">查看文档 <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <h3 class="fw-bold border-bottom pb-2 mb-4">高级功能</h3>
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="feature-icon d-inline-flex align-items-center justify-content-center bg-primary bg-gradient text-white fs-4 rounded-3 me-3">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <h5 class="fw-bold mb-0">安全</h5>
                            </div>
                            <p class="text-muted mb-3">了解SimplePHP内置的安全功能，包括CSRF保护、XSS防御和SQL注入防护。</p>
                            <a href="/docs/security" class="text-decoration-none">查看文档 <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="feature-icon d-inline-flex align-items-center justify-content-center bg-primary bg-gradient text-white fs-4 rounded-3 me-3">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <h5 class="fw-bold mb-0">验证</h5>
                            </div>
                            <p class="text-muted mb-3">使用内置的验证系统确保用户输入符合应用程序要求。</p>
                            <a href="/docs/validation" class="text-decoration-none">查看文档 <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 示例和教程 -->
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-3">示例和教程</h3>
                    <p class="text-muted mb-4">通过实际案例学习如何使用SimplePHP构建各种应用程序。</p>
                    
                    <div class="list-group mb-3">
                        <a href="/docs/tutorials/blog" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">构建博客应用</h6>
                                <p class="mb-0 small text-muted">学习如何创建一个完整的博客系统，包含文章、评论和用户认证。</p>
                            </div>
                            <span class="badge bg-primary rounded-pill">初级</span>
                        </a>
                        <a href="/docs/tutorials/rest-api" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">构建RESTful API</h6>
                                <p class="mb-0 small text-muted">学习如何使用SimplePHP创建安全、高效的RESTful API。</p>
                            </div>
                            <span class="badge bg-primary rounded-pill">中级</span>
                        </a>
                        <a href="/docs/tutorials/ecommerce" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">电商网站开发</h6>
                                <p class="mb-0 small text-muted">从零开始构建一个电子商务网站，包含产品目录、购物车和支付集成。</p>
                            </div>
                            <span class="badge bg-primary rounded-pill">高级</span>
                        </a>
                    </div>
                    
                    <a href="/docs/tutorials" class="btn btn-outline-primary">
                        <i class="fas fa-book me-2"></i> 查看所有教程
                    </a>
                </div>
            </div>
            
            <!-- 贡献指南 -->
            <div class="card border-0 bg-light rounded-3 shadow-sm">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-3">参与贡献</h3>
                    <p class="mb-4">SimplePHP 是一个开源项目，我们欢迎社区贡献。无论是修复错误、改进文档还是添加新功能，您的参与都将帮助框架变得更好。</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="/docs/contributing" class="btn btn-primary">
                            <i class="fas fa-code-branch me-2"></i> 贡献指南
                        </a>
                        <a href="https://github.com/simplephp/framework" class="btn btn-outline-primary">
                            <i class="fab fa-github me-2"></i> GitHub 仓库
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 