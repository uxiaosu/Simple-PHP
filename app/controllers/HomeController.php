<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 首页控制器
 */

namespace SimplePHP\App\Controllers;

use SimplePHP\Core\Controller;

class HomeController extends Controller
{
    /**
     * 首页
     */
    public function index()
    {
        $data = [
            'title' => 'SimplePHP框架',
            'description' => '轻量级、安全、易用的PHP框架',
            'features' => [
                '轻量级' => '框架核心代码精简，仅包含必要功能',
                '安全性' => '内置多种安全措施，防止常见Web攻击',
                '易用性' => '简单直观的API，快速上手，易于使用',
                'MVC架构' => '清晰的模型-视图-控制器分离',
                '数据库抽象' => '强大的查询构建器和ORM功能',
                '安全路由' => '灵活且安全的路由系统'
            ]
        ];
        
        return $this->view('home/index', $data);
    }
    
    /**
     * 关于页面
     */
    public function about()
    {
        $data = [
            'title' => '关于SimplePHP',
            'content' => '这是一个轻量级、安全、易用的PHP框架，专为快速开发现代Web应用而设计。'
        ];
        
        return $this->view('home/about', $data);
    }
    
    /**
     * 联系页面
     */
    public function contact()
    {
        // 处理表单提交
        if ($this->isMethod('POST')) {
            $data = [
                'name' => $this->post('name'),
                'email' => $this->post('email'),
                'message' => $this->post('message')
            ];
            
            // 验证数据
            $rules = [
                'name' => 'required|min:2|max:50',
                'email' => 'required|email',
                'message' => 'required|min:10'
            ];
            
            $errors = $this->validate($data, $rules);
            
            if (empty($errors)) {
                // 数据验证通过，可以处理表单数据
                // 例如，发送电子邮件或保存到数据库
                
                // 重定向到感谢页面
                return $this->redirect('/home/thank');
            }
            
            // 如果有错误，带着错误信息和表单数据重新渲染表单
            return $this->view('home/contact', [
                'title' => '联系我们',
                'errors' => $errors,
                'formData' => $data
            ]);
        }
        
        // 显示联系表单
        return $this->view('home/contact', [
            'title' => '联系我们'
        ]);
    }
    
    /**
     * 感谢页面
     */
    public function thank()
    {
        return $this->view('home/thank', [
            'title' => '感谢您的留言'
        ]);
    }
    
    /**
     * 演示API
     */
    public function api()
    {
        $data = [
            'status' => 'success',
            'message' => 'API测试成功',
            'data' => [
                'framework' => 'SimplePHP',
                'version' => '1.0.0',
                'timestamp' => time()
            ]
        ];
        
        return $this->json($data);
    }

    /**
     * 博客页面
     */
    public function blog()
    {
        $data = [
            'title' => 'SimplePHP博客',
            'description' => '框架相关文章和最新动态',
            'pageTitle' => '博客文章',
            'pageDescription' => '探索SimplePHP框架的最新动态、技术文章和使用教程。'
        ];
        
        // 如果真实视图不存在，使用通用的即将上线页面
        if (!file_exists(ROOT_PATH . '/app/views/home/blog.php')) {
            return $this->view('home/coming-soon', $data);
        }
        
        return $this->view('home/blog', $data);
    }

    /**
     * FAQ页面
     */
    public function faq()
    {
        $data = [
            'title' => 'SimplePHP常见问题',
            'description' => '关于SimplePHP框架的常见问题解答',
            'pageTitle' => '常见问题',
            'pageDescription' => '查看关于SimplePHP框架的常见问题解答，帮助您更好地使用本框架。'
        ];
        
        // 如果真实视图不存在，使用通用的即将上线页面
        if (!file_exists(ROOT_PATH . '/app/views/home/faq.php')) {
            return $this->view('home/coming-soon', $data);
        }
        
        return $this->view('home/faq', $data);
    }

    /**
     * 更新日志页面
     */
    public function changelog()
    {
        $data = [
            'title' => 'SimplePHP更新日志',
            'description' => '框架版本历史和更新记录',
            'pageTitle' => '更新日志',
            'pageDescription' => '了解SimplePHP框架的版本历史和各版本的更新内容。'
        ];
        
        // 如果真实视图不存在，使用通用的即将上线页面
        if (!file_exists(ROOT_PATH . '/app/views/home/changelog.php')) {
            return $this->view('home/coming-soon', $data);
        }
        
        return $this->view('home/changelog', $data);
    }

    /**
     * 隐私政策页面
     */
    public function privacy()
    {
        $data = [
            'title' => 'SimplePHP隐私政策',
            'description' => '关于用户数据收集和使用的详细说明',
            'pageTitle' => '隐私政策',
            'pageDescription' => '本页面详细说明了我们如何收集、使用和保护您的个人信息。'
        ];
        
        // 如果真实视图不存在，使用通用的即将上线页面
        if (!file_exists(ROOT_PATH . '/app/views/home/privacy.php')) {
            return $this->view('home/coming-soon', $data);
        }
        
        return $this->view('home/privacy', $data);
    }

    /**
     * 服务条款页面
     */
    public function terms()
    {
        $data = [
            'title' => 'SimplePHP服务条款',
            'description' => '使用本框架和相关服务的条款和条件',
            'pageTitle' => '服务条款',
            'pageDescription' => '请仔细阅读使用SimplePHP框架的条款和条件。'
        ];
        
        // 如果真实视图不存在，使用通用的即将上线页面
        if (!file_exists(ROOT_PATH . '/app/views/home/terms.php')) {
            return $this->view('home/coming-soon', $data);
        }
        
        return $this->view('home/terms', $data);
    }
} 