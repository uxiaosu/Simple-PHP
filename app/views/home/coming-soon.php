<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 即将上线页面 - 用于尚未实现的功能
 */
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card shadow-sm border-0 rounded-lg overflow-hidden">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="fas fa-clock text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h1 class="display-5 fw-bold mb-3"><?= $pageTitle ?? '即将上线' ?></h1>
                    <p class="lead mb-4"><?= $pageDescription ?? '本功能正在开发中，即将推出，请耐心等待。' ?></p>
                    <div class="progress mb-4" style="height: 8px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 75%"></div>
                    </div>
                    <p class="text-muted mb-4">我们正在努力完善此功能，为您提供更好的用户体验。</p>
                    <a href="<?= url('') ?>" class="btn btn-primary px-4 py-2">
                        <i class="fas fa-home me-2"></i> 返回首页
                    </a>
                </div>
            </div>
        </div>
    </div>
</div> 