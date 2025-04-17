<?php 
// 设置页面标题
$this->title = $title ?? '联系我们'; 
// 设置页面描述
$this->description = '通过此表单向SimplePHP团队发送消息。';

// 获取可能存在的验证错误和旧表单数据
$errors = $errors ?? [];
$formData = $formData ?? [];

/**
 * 辅助函数，用于显示字段的验证错误
 * @param string $field 字段名
 * @param array $errors 错误数组
 * @return string HTML错误信息
 */
function display_error($field, $errors) {
    if (!empty($errors[$field])) {
        return '<div class="invalid-feedback d-block">' . htmlspecialchars($errors[$field]) . '</div>';
    }
    return '';
}

/**
 * 辅助函数，用于获取旧表单数据
 * @param string $field 字段名
 * @param array $formData 旧数据数组
 * @return string 字段值
 */
function old($field, $formData) {
    return htmlspecialchars($formData[$field] ?? '');
}

?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4 p-md-5">
                <h1 class="display-5 fw-bold mb-4 text-center">
                    <i class="fas fa-envelope-open-text me-2 text-primary"></i> <?= htmlspecialchars($this->title) ?>
                </h1>
                <p class="text-center text-muted mb-5">我们很乐意收到您的来信！请填写下面的表格，我们会尽快回复您。</p>
                
                <form action="<?= url('contact') ?>" method="POST" novalidate>
                    <!-- CSRF Token -->
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($this->csrf_token ?? '') /* 从布局传递CSRF令牌 */ ?>">
                    
                    <!-- 姓名 -->
                    <div class="mb-4 form-floating">
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="form-control <?= !empty($errors['name']) ? 'is-invalid' : '' ?>" 
                               placeholder="您的姓名" 
                               value="<?= old('name', $formData) ?>" 
                               required>
                        <label for="name">您的姓名</label>
                        <?= display_error('name', $errors) ?>
                    </div>
                    
                    <!-- 邮箱 -->
                    <div class="mb-4 form-floating">
                        <input type="email" 
                               name="email" 
                               id="email" 
                               class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>" 
                               placeholder="您的邮箱地址" 
                               value="<?= old('email', $formData) ?>" 
                               required>
                        <label for="email">您的邮箱地址</label>
                        <?= display_error('email', $errors) ?>
                    </div>
                    
                    <!-- 消息 -->
                    <div class="mb-4 form-floating">
                        <textarea name="message" 
                                  id="message" 
                                  class="form-control <?= !empty($errors['message']) ? 'is-invalid' : '' ?>" 
                                  placeholder="您的留言" 
                                  style="height: 150px" 
                                  required><?= old('message', $formData) ?></textarea>
                        <label for="message">您的留言</label>
                        <?= display_error('message', $errors) ?>
                    </div>
                    
                    <!-- 提交按钮 -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane me-2"></i> 发送消息
                        </button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</div> 