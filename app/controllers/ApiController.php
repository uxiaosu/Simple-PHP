<?php
namespace SimplePHP\App\Controllers;

use SimplePHP\Core\Base\Controller;
use SimplePHP\Core\Http\Request;
use SimplePHP\Core\Http\Response;

/**
 * API控制器
 * 用于处理AJAX请求和提供API接口
 */
class ApiController extends Controller
{
    /**
     * 初始化方法
     */
    public function __construct()
    {
        parent::__construct();
        
        // 设置响应内容类型为JSON
        $this->response->setContentType('application/json');
    }
    
    /**
     * 返回成功响应
     * 
     * @param mixed $data 响应数据
     * @param string $message 响应消息
     * @param int $code HTTP状态码
     * @return Response
     */
    protected function success($data = null, $message = '操作成功', $code = 200)
    {
        return $this->response->withJson([
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }
    
    /**
     * 返回失败响应
     * 
     * @param string $message 错误消息
     * @param int $code HTTP状态码
     * @param mixed $data 额外数据
     * @return Response
     */
    protected function error($message = '操作失败', $code = 400, $data = null)
    {
        return $this->response->withJson([
            'success' => false,
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }
    
    /**
     * 检查请求是否为AJAX请求
     * 
     * @return bool
     */
    protected function isAjax()
    {
        return $this->request->isAjax();
    }
    
    /**
     * 获取JSON请求体数据
     * 
     * @return array
     */
    protected function getJsonInput()
    {
        $content = $this->request->getBody();
        if (empty($content)) {
            return [];
        }
        
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }
        
        return $data;
    }
    
    /**
     * 默认首页方法 - 测试API可用性
     * 
     * @return Response
     */
    public function indexAction()
    {
        $apiInfo = [
            'name' => 'SimplePHP API',
            'version' => '1.0.0',
            'status' => 'running',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        return $this->success($apiInfo, 'API服务正常运行');
    }
    
    /**
     * 示例API - 获取服务器信息
     * 
     * @return Response
     */
    public function serverInfoAction()
    {
        // 收集服务器信息
        $serverInfo = [
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? '未知',
            'server_protocol' => $_SERVER['SERVER_PROTOCOL'] ?? '未知',
            'request_time' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] ?? time()),
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? '未知',
            'server_addr' => $_SERVER['SERVER_ADDR'] ?? '未知',
            'server_name' => $_SERVER['SERVER_NAME'] ?? '未知',
            'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? '未知',
            'http_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '未知'
        ];
        
        return $this->success($serverInfo, '服务器信息');
    }
    
    /**
     * 示例API - 表单处理
     * 
     * @return Response
     */
    public function formHandlerAction()
    {
        // 检查请求方法
        if (!$this->request->isPost()) {
            return $this->error('仅支持POST请求', 405);
        }
        
        // 获取表单数据
        $formData = $this->getJsonInput();
        if (empty($formData)) {
            $formData = $this->request->getPost();
        }
        
        // 简单验证
        if (empty($formData)) {
            return $this->error('未提供表单数据', 400);
        }
        
        // 在这里可以添加更复杂的表单验证和处理逻辑
        
        // 返回处理结果
        $result = [
            'received' => $formData,
            'processed_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->success($result, '表单数据处理成功');
    }
    
    /**
     * 示例API - 文件上传处理
     * 
     * @return Response
     */
    public function uploadAction()
    {
        // 检查请求方法
        if (!$this->request->isPost()) {
            return $this->error('仅支持POST请求', 405);
        }
        
        // 获取上传文件
        $files = $this->request->getFiles();
        if (empty($files)) {
            return $this->error('未上传任何文件', 400);
        }
        
        $results = [];
        $uploadsDir = ROOT_PATH . '/public/uploads/';
        
        // 确保上传目录存在
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }
        
        // 处理每个上传文件
        foreach ($files as $key => $file) {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $results[$key] = [
                    'success' => false,
                    'message' => '文件上传失败: ' . $this->getUploadErrorMessage($file['error'])
                ];
                continue;
            }
            
            // 为避免文件名冲突，生成唯一文件名
            $filename = uniqid() . '_' . $file['name'];
            $destination = $uploadsDir . $filename;
            
            // 移动上传文件
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $results[$key] = [
                    'success' => true,
                    'message' => '文件上传成功',
                    'filename' => $filename,
                    'path' => '/uploads/' . $filename,
                    'size' => $file['size'],
                    'type' => $file['type']
                ];
            } else {
                $results[$key] = [
                    'success' => false,
                    'message' => '文件移动失败'
                ];
            }
        }
        
        return $this->success($results, '文件上传处理完成');
    }
    
    /**
     * 获取上传错误信息
     * 
     * @param int $errorCode 错误代码
     * @return string 错误信息
     */
    private function getUploadErrorMessage($errorCode)
    {
        $errors = [
            UPLOAD_ERR_INI_SIZE => '上传文件超过了php.ini中upload_max_filesize指令的限制',
            UPLOAD_ERR_FORM_SIZE => '上传文件超过了表单中MAX_FILE_SIZE指令的限制',
            UPLOAD_ERR_PARTIAL => '文件只有部分被上传',
            UPLOAD_ERR_NO_FILE => '没有文件被上传',
            UPLOAD_ERR_NO_TMP_DIR => '找不到临时文件夹',
            UPLOAD_ERR_CANT_WRITE => '文件写入失败',
            UPLOAD_ERR_EXTENSION => '文件上传因PHP扩展被停止'
        ];
        
        return isset($errors[$errorCode]) ? $errors[$errorCode] : '未知错误';
    }
} 