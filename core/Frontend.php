<?php
/**
 * SimplePHP - 轻量级安全PHP框架
 * 前端集成支持模块
 */

namespace SimplePHP\Core;

use SimplePHP\Core\Config;

class Frontend 
{
    /**
     * 资源清单数据
     * @var array
     */
    private $manifest = [];
    
    /**
     * 资源基础路径
     * @var string
     */
    private $assetPath;
    
    /**
     * 构造函数
     */
    public function __construct() 
    {
        $this->assetPath = Config::get('frontend.assets_path', '/assets');
        $this->loadManifest();
    }
    
    /**
     * 加载资源清单文件
     */
    private function loadManifest() 
    {
        $manifestFile = ROOT_PATH . '/public/assets/manifest.json';
        if (file_exists($manifestFile)) {
            $this->manifest = json_decode(file_get_contents($manifestFile), true) ?? [];
        }
    }
    
    /**
     * 获取资源路径
     * 
     * @param string $name 资源名称
     * @return string 完整资源路径
     */
    public function asset($name) 
    {
        return $this->assetPath . '/' . ($this->manifest[$name] ?? $name);
    }
    
    /**
     * 生成初始状态脚本标签
     * 
     * @param mixed $data 要传递给前端的数据
     * @param string $varName 全局变量名称
     * @return string 生成的HTML
     */
    public function renderInitialState($data, $varName = '__INITIAL_STATE__') 
    {
        return '<script>window.' . $varName . ' = ' . 
               json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . 
               ';</script>';
    }
    
    /**
     * 生成React/Vue组件容器
     * 
     * @param string $name 组件名称
     * @param array $props 组件属性
     * @param string $containerId 容器ID (可选)
     * @return string 组件容器HTML
     */
    public function renderComponent($name, $props = [], $containerId = null) 
    {
        if ($containerId === null) {
            $containerId = 'component-' . md5($name . json_encode($props));
        }
        
        $html = '<div id="' . htmlspecialchars($containerId) . '"></div>';
        $html .= '<script>';
        $html .= 'document.addEventListener("DOMContentLoaded", function() {';
        $html .= '  if (window.SimplePHP && window.SimplePHP.renderComponent) {';
        $html .= '    window.SimplePHP.renderComponent("' . $name . '", ' 
               . json_encode($props) . ', "' . $containerId . '");';
        $html .= '  }';
        $html .= '});</script>';
        
        return $html;
    }
    
    /**
     * 生成CSS链接标签
     * 
     * @param string $name CSS文件名
     * @param array $attributes 额外属性
     * @return string 生成的HTML
     */
    public function css($name, $attributes = []) 
    {
        $path = $this->asset($name . '.css');
        
        // 添加版本号以支持缓存刷新
        $version = $this->getFileVersion($name . '.css');
        if ($version) {
            $path .= '?v=' . $version;
        }
        
        // 添加资源缓存属性
        $attributes['rel'] = 'stylesheet';
        if (!isset($attributes['media'])) {
            $attributes['media'] = 'all';
        }
        
        $attr = '';
        foreach ($attributes as $key => $value) {
            $attr .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
        
        return '<link href="' . $path . '"' . $attr . '>';
    }
    
    /**
     * 生成JavaScript脚本标签
     * 
     * @param string $name JS文件名
     * @param array $attributes 额外属性
     * @return string 生成的HTML
     */
    public function js($name, $attributes = []) 
    {
        $path = $this->asset($name . '.js');
        
        // 添加版本号以支持缓存刷新
        $version = $this->getFileVersion($name . '.js');
        if ($version) {
            $path .= '?v=' . $version;
        }
        
        // 如果不是关键脚本，添加defer属性以延迟加载
        if (!isset($attributes['defer']) && !isset($attributes['async']) && 
            !in_array($name, Config::get('frontend.critical_scripts', []))) {
            $attributes['defer'] = 'defer';
        }
        
        $attr = '';
        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $attr .= ' ' . $key;
            } else {
                $attr .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
            }
        }
        
        return '<script src="' . $path . '"' . $attr . '></script>';
    }
    
    /**
     * 获取文件版本号（基于修改时间）
     * 
     * @param string $filename 文件名
     * @return string|null 版本号或null
     */
    private function getFileVersion($filename) 
    {
        $filePath = ROOT_PATH . '/public' . $this->assetPath . '/' . ($this->manifest[$filename] ?? $filename);
        
        if (file_exists($filePath)) {
            return filemtime($filePath);
        }
        
        return null;
    }
    
    /**
     * 生成内联关键CSS
     * 
     * @param string $name CSS文件名
     * @return string 生成的HTML
     */
    public function inlineCriticalCss($name) 
    {
        $path = ROOT_PATH . '/public' . $this->assetPath . '/critical/' . $name . '.css';
        
        if (file_exists($path)) {
            $css = file_get_contents($path);
            return '<style>' . $css . '</style>';
        }
        
        return '';
    }
    
    /**
     * 懒加载CSS (使用preload替代直接加载)
     * 
     * @param string $name CSS文件名
     * @return string 生成的HTML
     */
    public function lazyLoadCss($name) 
    {
        $path = $this->asset($name . '.css');
        
        // 添加版本号以支持缓存刷新
        $version = $this->getFileVersion($name . '.css');
        if ($version) {
            $path .= '?v=' . $version;
        }
        
        $html = '<link rel="preload" href="' . $path . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
        $html .= '<noscript><link rel="stylesheet" href="' . $path . '"></noscript>';
        return $html;
    }
    
    /**
     * 生成资源预加载标签
     * 
     * @param string $path 资源路径
     * @param string $as 资源类型 (style, script, image, font等)
     * @param array $attributes 额外属性
     * @return string 生成的HTML
     */
    public function preload($path, $as, $attributes = []) 
    {
        $fullPath = $this->asset($path);
        
        $attributes['rel'] = 'preload';
        $attributes['href'] = $fullPath;
        $attributes['as'] = $as;
        
        // 添加crossorigin属性（用于字体等资源）
        if ($as === 'font' && !isset($attributes['crossorigin'])) {
            $attributes['crossorigin'] = 'anonymous';
        }
        
        $attr = '';
        foreach ($attributes as $key => $value) {
            $attr .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
        }
        
        return '<link' . $attr . '>';
    }
} 