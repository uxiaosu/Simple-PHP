<?php 
// 设置页面标题
$this->title = $title ?? '感谢您的留言'; 
// 设置页面描述
$this->description = '您的消息已成功发送。';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-3 text-center">
            <div class="card-body p-4 p-md-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle fa-4x text-success"></i>
                </div>
                <h1 class="display-5 fw-bold mb-3">
                    <?= htmlspecialchars($this->title) ?>
                </h1>
                <p class="lead text-muted mb-4">
                    我们已经收到了您的消息，并将尽快处理。
                </p>
                <a href="<?= url('/') ?>" class="btn btn-primary px-4">
                    <i class="fas fa-home me-2"></i> 返回首页
                </a>
            </div>
        </div>
    </div>
</div> 