<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 文档控制器
 */

namespace SimplePHP\App\Controllers;

use SimplePHP\Core\Controller;

class DocsController extends Controller
{
    /**
     * 获取安全的视图路径
     * @param string $view 视图名称
     * @param array $data 视图数据
     * @return mixed 视图渲染结果
     */
    private function secureView($view, $data)
    {
        // 实际视图路径映射
        $viewMap = [
            'guide/index' => 'docs/index',
            'guide/basics' => 'docs/getting-started',
            'guide/controllers' => 'docs/controllers',
            'guide/models' => 'docs/models',
            'guide/views' => 'docs/views',
            'guide/advanced' => 'docs/advanced',
            'guide/coming-soon' => 'docs/coming-soon',
            'guide/api' => 'api/index'
        ];
        
        // 如果有映射关系，使用映射后的视图路径
        if (isset($viewMap[$view])) {
            $actualView = $viewMap[$view];
        } else {
            $actualView = $view;
        }
        
        return $this->view($actualView, $data);
    }
    
    /**
     * 检查视图文件是否存在
     * @param string $view 抽象视图名称
     * @return bool 文件是否存在
     */
    private function viewExists($view)
    {
        $viewMap = [
            'guide/index' => 'docs/index',
            'guide/basics' => 'docs/getting-started',
            'guide/controllers' => 'docs/controllers',
            'guide/models' => 'docs/models',
            'guide/views' => 'docs/views',
            'guide/advanced' => 'docs/advanced',
            'guide/coming-soon' => 'docs/coming-soon',
            'guide/api' => 'api/index'
        ];
        
        if (isset($viewMap[$view])) {
            $actualView = $viewMap[$view];
        } else {
            $actualView = $view;
        }
        
        return file_exists(ROOT_PATH . '/app/views/' . $actualView . '.php');
    }
    
    /**
     * 文档首页
     */
    public function index()
    {
        $data = [
            'title' => 'SimplePHP框架文档',
            'description' => '轻量级、安全、易用的PHP框架的完整文档',
            'pageTitle' => 'SimplePHP框架文档',
            'pageDescription' => '欢迎使用SimplePHP框架文档。本文档将帮助您快速上手并充分利用框架的所有功能。'
        ];
        
        return $this->secureView('guide/index', $data);
    }
    
    /**
     * 入门指南
     */
    public function gettingStarted()
    {
        $data = [
            'title' => '入门指南 - SimplePHP框架文档',
            'description' => '快速开始使用SimplePHP框架的指南',
            'pageTitle' => '入门指南',
            'pageDescription' => '本指南将帮助您快速了解SimplePHP框架，并指导您完成安装、配置和创建第一个应用的过程。'
        ];
        
        return $this->secureView('guide/basics', $data);
    }
    
    /**
     * 控制器文档
     */
    public function controllers()
    {
        $data = [
            'title' => '控制器 - SimplePHP框架文档',
            'description' => 'SimplePHP框架控制器使用指南',
            'pageTitle' => '控制器',
            'pageDescription' => '控制器是应用程序的核心组件，负责处理请求并生成响应。'
        ];
        
        // 如果控制器页面不存在，可以显示一个占位页面
        if (!$this->viewExists('guide/controllers')) {
            return $this->secureView('guide/coming-soon', $data);
        }
        
        return $this->secureView('guide/controllers', $data);
    }
    
    /**
     * 模型文档
     */
    public function models()
    {
        $data = [
            'title' => '模型 - SimplePHP框架文档',
            'description' => 'SimplePHP框架模型使用指南',
            'pageTitle' => '模型',
            'pageDescription' => '模型代表应用程序的数据结构，负责管理和处理数据。'
        ];
        
        // 如果模型页面不存在，可以显示一个占位页面
        if (!$this->viewExists('guide/models')) {
            return $this->secureView('guide/coming-soon', $data);
        }
        
        return $this->secureView('guide/models', $data);
    }
    
    /**
     * 视图文档
     */
    public function views()
    {
        $data = [
            'title' => '视图 - SimplePHP框架文档',
            'description' => 'SimplePHP框架视图使用指南',
            'pageTitle' => '视图',
            'pageDescription' => '视图负责生成应用程序的用户界面，用于向用户呈现数据。'
        ];
        
        // 如果视图页面不存在，可以显示一个占位页面
        if (!$this->viewExists('guide/views')) {
            return $this->secureView('guide/coming-soon', $data);
        }
        
        return $this->secureView('guide/views', $data);
    }
    
    /**
     * 高级主题文档
     */
    public function advanced()
    {
        $data = [
            'title' => '高级主题 - SimplePHP框架文档',
            'description' => 'SimplePHP框架高级用法和特性',
            'pageTitle' => '高级主题',
            'pageDescription' => '探索SimplePHP框架的高级功能和特性，包括中间件、事件、缓存等。'
        ];
        
        // 如果高级主题页面不存在，可以显示一个占位页面
        if (!$this->viewExists('guide/advanced')) {
            return $this->secureView('guide/coming-soon', $data);
        }
        
        return $this->secureView('guide/advanced', $data);
    }
    
    /**
     * API文档
     */
    public function api()
    {
        $data = [
            'title' => 'API文档 - SimplePHP框架',
            'description' => 'SimplePHP框架API接口完整文档',
            'pageTitle' => 'API文档',
            'pageDescription' => 'SimplePHP提供强大的RESTful API，使您能够以编程方式与应用程序的数据和功能进行交互。'
        ];
        
        return $this->secureView('guide/api', $data);
    }
    
    /* 安全路由别名 - 使用更抽象的URL */
    
    /**
     * 入门指南 (安全URL别名)
     */
    public function basics()
    {
        // 重用已有方法
        return $this->gettingStarted();
    }
    
    /**
     * 控制器文档 (安全URL别名)
     */
    public function c()
    {
        // 重用已有方法
        return $this->controllers();
    }
    
    /**
     * 模型文档 (安全URL别名)
     */
    public function m()
    {
        // 重用已有方法
        return $this->models();
    }
    
    /**
     * 视图文档 (安全URL别名)
     */
    public function v()
    {
        // 重用已有方法
        return $this->views();
    }
    
    /**
     * 高级主题文档 (安全URL别名)
     */
    public function adv()
    {
        // 重用已有方法
        return $this->advanced();
    }
} 