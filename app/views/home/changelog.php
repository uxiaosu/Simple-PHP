<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 更新日志页面
 */
?>
<div class="container py-5">
    <!-- 页面标题部分 -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto text-center">
            <h1 class="display-4 fw-bold mb-3">更新日志</h1>
            <p class="lead text-muted">了解SimplePHP框架的版本历史和各版本的更新内容。</p>
        </div>
    </div>
    
    <!-- 更新日志内容 -->
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- 最新版本 -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0 fw-bold">v1.2.0</h3>
                        <span class="badge bg-light text-primary">最新版本</span>
                    </div>
                    <small>发布日期：2025年3月15日</small>
                </div>
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">主要更新</h5>
                    <ul class="mb-4">
                        <li>新增高级缓存系统，提供文件、Redis和Memcached驱动</li>
                        <li>改进路由系统，支持路由组和中间件组</li>
                        <li>新增命令行工具，用于生成控制器、模型和迁移文件</li>
                        <li>改进ORM系统，支持更复杂的关系和查询</li>
                    </ul>
                    
                    <h5 class="fw-bold mb-3">次要更新</h5>
                    <ul class="mb-4">
                        <li>改进错误处理和日志系统</li>
                        <li>优化查询构建器性能</li>
                        <li>更新依赖包到最新版本</li>
                        <li>改进文档和示例代码</li>
                    </ul>
                    
                    <h5 class="fw-bold mb-3">Bug修复</h5>
                    <ul>
                        <li>修复了CSRF令牌验证的一个潜在安全问题</li>
                        <li>修复了在某些PHP环境中session处理的问题</li>
                        <li>修复了查询构建器中的JOIN子句问题</li>
                        <li>修复了文件上传处理中的一个错误</li>
                    </ul>
                </div>
                <div class="card-footer bg-light p-3">
                    <a href="#" class="btn btn-sm btn-outline-primary">下载此版本 <i class="fas fa-download ms-1"></i></a>
                    <a href="#" class="btn btn-sm btn-link">查看完整变更 <i class="fas fa-external-link-alt ms-1"></i></a>
                </div>
            </div>
            
            <!-- 版本1.1.5 -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-secondary text-white py-3">
                    <h3 class="mb-0 fw-bold">v1.1.5</h3>
                    <small>发布日期：2025年2月20日</small>
                </div>
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">主要更新</h5>
                    <ul class="mb-4">
                        <li>新增XSS过滤中间件，自动清理用户输入</li>
                        <li>改进表单验证系统，支持更多验证规则</li>
                    </ul>
                    
                    <h5 class="fw-bold mb-3">次要更新</h5>
                    <ul class="mb-4">
                        <li>优化核心组件性能</li>
                        <li>改进数据库连接池管理</li>
                        <li>更新前端依赖包</li>
                    </ul>
                    
                    <h5 class="fw-bold mb-3">Bug修复</h5>
                    <ul>
                        <li>修复了路由参数处理中的一个问题</li>
                        <li>修复了视图渲染中的一个内存泄漏问题</li>
                        <li>修复了多个小错误和代码优化</li>
                    </ul>
                </div>
                <div class="card-footer bg-light p-3">
                    <a href="#" class="btn btn-sm btn-outline-secondary">下载此版本 <i class="fas fa-download ms-1"></i></a>
                    <a href="#" class="btn btn-sm btn-link">查看完整变更 <i class="fas fa-external-link-alt ms-1"></i></a>
                </div>
            </div>
            
            <!-- 版本1.1.0 -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-secondary text-white py-3">
                    <h3 class="mb-0 fw-bold">v1.1.0</h3>
                    <small>发布日期：2025年1月15日</small>
                </div>
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">主要更新</h5>
                    <ul class="mb-4">
                        <li>新增事件系统，支持自定义事件和监听器</li>
                        <li>新增API资源类，简化API开发</li>
                        <li>新增文件存储抽象层，支持本地和云存储</li>
                    </ul>
                    
                    <h5 class="fw-bold mb-3">次要更新</h5>
                    <ul class="mb-4">
                        <li>改进路由系统，支持资源路由</li>
                        <li>改进验证系统，支持自定义验证规则</li>
                        <li>优化框架核心性能</li>
                    </ul>
                    
                    <h5 class="fw-bold mb-3">Bug修复</h5>
                    <ul>
                        <li>修复了数据库连接的一个问题</li>
                        <li>修复了模板引擎中的一个bug</li>
                        <li>修复了中间件执行顺序的问题</li>
                    </ul>
                </div>
                <div class="card-footer bg-light p-3">
                    <a href="#" class="btn btn-sm btn-outline-secondary">下载此版本 <i class="fas fa-download ms-1"></i></a>
                    <a href="#" class="btn btn-sm btn-link">查看完整变更 <i class="fas fa-external-link-alt ms-1"></i></a>
                </div>
            </div>
            
            <!-- 首个稳定版本 -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white py-3">
                    <h3 class="mb-0 fw-bold">v1.0.0</h3>
                    <small>发布日期：2024年12月1日</small>
                </div>
                <div class="card-body p-4">
                    <p class="lead mb-4">SimplePHP框架的首个稳定版本，提供了完整的MVC架构和核心功能。</p>
                    
                    <h5 class="fw-bold mb-3">核心特性</h5>
                    <ul class="mb-4">
                        <li>简洁高效的MVC架构</li>
                        <li>强大的路由系统</li>
                        <li>灵活的数据库抽象层和查询构建器</li>
                        <li>直观的模板系统</li>
                        <li>全面的安全特性，包括CSRF保护、XSS过滤等</li>
                        <li>简单易用的表单验证</li>
                        <li>中间件支持</li>
                        <li>完善的错误处理和日志系统</li>
                    </ul>
                </div>
                <div class="card-footer bg-light p-3">
                    <a href="#" class="btn btn-sm btn-outline-dark">下载此版本 <i class="fas fa-download ms-1"></i></a>
                    <a href="#" class="btn btn-sm btn-link">查看完整变更 <i class="fas fa-external-link-alt ms-1"></i></a>
                </div>
            </div>
            
            <!-- 早期版本链接 -->
            <div class="mt-5 text-center">
                <h4 class="fw-bold mb-3">查看早期版本</h4>
                <p class="text-muted mb-4">您可以在我们的GitHub仓库中查看所有历史版本。</p>
                <a href="#" class="btn btn-outline-primary px-4">
                    <i class="fab fa-github me-2"></i> 访问GitHub仓库
                </a>
            </div>
        </div>
    </div>
</div> 