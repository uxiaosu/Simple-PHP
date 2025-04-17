<?php 
// 设置页面标题
$this->title = $title ?? '关于我们'; 
// 设置页面描述
$this->description = '了解更多关于SimplePHP框架的信息';
?>

<div class="container py-5">
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto text-center">
            <h1 class="display-4 fw-bold">关于 SimplePHP</h1>
            <p class="lead text-muted mb-4">一个为现代Web应用而设计的轻量级、安全、易用的PHP框架</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="https://github.com/simplephp/framework" class="btn btn-outline-primary">
                    <i class="fab fa-github me-2"></i> GitHub
                </a>
                <a href="/docs" class="btn btn-primary">
                    <i class="fas fa-book me-2"></i> 查看文档
                </a>
            </div>
        </div>
    </div>
    
    <!-- 框架介绍 -->
    <div class="row mb-5">
        <div class="col-md-6">
            <h2 class="fw-bold border-start border-4 border-primary ps-3 mb-4">我们的使命</h2>
            <p class="lead">SimplePHP 的使命是为 PHP 开发者提供一个轻量级但功能强大的框架，使 Web 应用程序开发变得简单而安全。</p>
            <p>在当今复杂的 Web 开发环境中，我们看到许多框架要么过于臃肿，要么过于简单。SimplePHP 旨在寻找完美的平衡点 - 提供足够的功能来构建现代应用程序，同时保持代码库轻量化和高效。</p>
            <p>我们专注于三个核心原则：</p>
            <ul class="list-unstyled">
                <li class="d-flex mb-3">
                    <div class="feature-icon bg-primary text-white me-3">
                        <i class="fas fa-feather"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold">轻量级</h5>
                        <p class="text-muted mb-0">优化的核心代码库，无冗余功能，确保最佳性能。</p>
                    </div>
                </li>
                <li class="d-flex mb-3">
                    <div class="feature-icon bg-primary text-white me-3">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold">安全性</h5>
                        <p class="text-muted mb-0">内置保护措施，防止常见的 Web 漏洞和攻击。</p>
                    </div>
                </li>
                <li class="d-flex">
                    <div class="feature-icon bg-primary text-white me-3">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold">易用性</h5>
                        <p class="text-muted mb-0">直观的 API 和全面的文档，缩短学习曲线。</p>
                    </div>
                </li>
            </ul>
        </div>
        <div class="col-md-6 d-flex align-items-center">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-3">
                            <path d="M7 8L3 12L7 16" stroke="#4361ee" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17 8L21 12L17 16" stroke="#4361ee" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14 4L10 20" stroke="#4361ee" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <h3 class="fw-bold">SimplePHP 框架</h3>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>当前版本</span>
                            <span class="badge bg-primary rounded-pill">v2.0.0</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>发布日期</span>
                            <span>2023年6月1日</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>PHP 要求</span>
                            <span>PHP 7.4+</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>许可证</span>
                            <span>MIT</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 团队介绍 -->
    <h2 class="fw-bold border-start border-4 border-primary ps-3 mb-4">我们的团队</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <img src="https://via.placeholder.com/150" alt="张三" class="rounded-circle mb-3" width="120" height="120">
                    <h4 class="fw-bold">张三</h4>
                    <p class="text-muted">创始人 & 首席架构师</p>
                    <p class="small">拥有10年PHP开发经验，专注于框架设计和性能优化。</p>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <a href="#" class="btn btn-sm btn-outline-primary rounded-circle">
                            <i class="fab fa-github"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-primary rounded-circle">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-primary rounded-circle">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <img src="https://via.placeholder.com/150" alt="李四" class="rounded-circle mb-3" width="120" height="120">
                    <h4 class="fw-bold">李四</h4>
                    <p class="text-muted">安全主管</p>
                    <p class="small">网络安全专家，负责框架的安全策略和功能实现。</p>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <a href="#" class="btn btn-sm btn-outline-primary rounded-circle">
                            <i class="fab fa-github"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-primary rounded-circle">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-primary rounded-circle">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <img src="https://via.placeholder.com/150" alt="王五" class="rounded-circle mb-3" width="120" height="120">
                    <h4 class="fw-bold">王五</h4>
                    <p class="text-muted">开发者体验负责人</p>
                    <p class="small">专注于提升框架的易用性和开发体验，负责文档和教程编写。</p>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <a href="#" class="btn btn-sm btn-outline-primary rounded-circle">
                            <i class="fab fa-github"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-primary rounded-circle">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-primary rounded-circle">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 联系我们 -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="fw-bold text-center mb-4">联系我们</h2>
                    <p class="text-center mb-4">有问题或建议？请随时与我们联系，我们很乐意听取您的反馈！</p>
                    <div class="row g-4">
                        <div class="col-md-4 text-center">
                            <div class="d-inline-flex mb-3 feature-icon bg-primary text-white">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h5 class="fw-bold">电子邮件</h5>
                            <p class="mb-0"><a href="mailto:info@simplephp.com" class="text-decoration-none">info@simplephp.com</a></p>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="d-inline-flex mb-3 feature-icon bg-primary text-white">
                                <i class="fas fa-comment-dots"></i>
                            </div>
                            <h5 class="fw-bold">社区</h5>
                            <p class="mb-0"><a href="#" class="text-decoration-none">加入我们的论坛</a></p>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="d-inline-flex mb-3 feature-icon bg-primary text-white">
                                <i class="fab fa-github"></i>
                            </div>
                            <h5 class="fw-bold">GitHub</h5>
                            <p class="mb-0"><a href="#" class="text-decoration-none">报告问题</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 