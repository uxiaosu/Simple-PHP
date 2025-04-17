/**
 * SimplePHP 前端应用入口文件
 */

// 导入模块
import API from './api';
import { registerComponent, renderComponent } from './components';

// 创建全局命名空间
window.SimplePHP = window.SimplePHP || {};
window.SimplePHP.API = API;
window.SimplePHP.registerComponent = registerComponent;
window.SimplePHP.renderComponent = renderComponent;

// 应用配置
const appConfig = {
  debug: true,
  apiBaseUrl: '/api',
  version: '1.0.0'
};

/**
 * 应用初始化函数
 */
function initApp() {
  console.log(`SimplePHP 前端框架 v${appConfig.version} 已初始化`);
  
  // 设置API基础URL
  API.baseUrl = appConfig.apiBaseUrl;
  
  // 注册内置组件
  registerDefaultComponents();
  
  // 全局事件处理
  setupEventHandlers();
  
  // 执行其他初始化操作
  if (typeof window.__INITIAL_STATE__ !== 'undefined') {
    // 使用服务器传来的初始状态
    console.log('检测到服务器端状态数据');
  }
}

/**
 * 注册默认组件
 */
function registerDefaultComponents() {
  // 注册示例组件
  registerComponent('hello-world', {
    render: (props) => {
      const name = props.name || 'World';
      return `<div class="hello-component">
        <h3>Hello, ${name}!</h3>
        <p>这是一个简单的组件示例</p>
      </div>`;
    }
  });
}

/**
 * 设置全局事件处理
 */
function setupEventHandlers() {
  // 处理AJAX表单提交
  document.addEventListener('submit', function(event) {
    const form = event.target;
    
    // 检查是否为AJAX表单
    if (form.dataset.ajax !== 'true') return;
    
    event.preventDefault();
    
    const url = form.action;
    const method = form.method.toUpperCase();
    const formData = new FormData(form);
    
    // 提取表单数据
    const data = {};
    formData.forEach((value, key) => {
      data[key] = value;
    });
    
    // 发送AJAX请求
    API.request(method, url, data)
      .then(response => {
        // 触发自定义事件
        const successEvent = new CustomEvent('ajax:success', {
          detail: { response, form }
        });
        form.dispatchEvent(successEvent);
      })
      .catch(error => {
        // 触发自定义事件
        const errorEvent = new CustomEvent('ajax:error', {
          detail: { error, form }
        });
        form.dispatchEvent(errorEvent);
      });
  });
  
  // 其他全局事件处理...
}

// 在DOM加载完成后初始化应用
document.addEventListener('DOMContentLoaded', initApp);

// 导出公共API
export default {
  API,
  registerComponent,
  renderComponent,
  init: initApp
}; 