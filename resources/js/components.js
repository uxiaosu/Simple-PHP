/**
 * SimplePHP 组件渲染系统
 * 简化组件式开发，支持不同前端框架的集成
 */

// 组件注册表
const components = {};

// 组件渲染器
const renderers = {
  // 默认渲染器
  default: function(component, props, targetId) {
    const target = document.getElementById(targetId);
    if (!target) return false;
    target.innerHTML = `<div class="simplephp-component">组件 ${component} 已加载</div>`;
    return true;
  },
  
  // React渲染器
  react: function(component, props, targetId) {
    if (!window.React || !window.ReactDOM) return false;
    
    const Component = components[component];
    if (!Component) return false;
    
    const target = document.getElementById(targetId);
    if (!target) return false;
    
    ReactDOM.render(
      React.createElement(Component, props),
      target
    );
    return true;
  },
  
  // Vue渲染器
  vue: function(component, props, targetId) {
    if (!window.Vue) return false;
    
    const Component = components[component];
    if (!Component) return false;
    
    const target = document.getElementById(targetId);
    if (!target) return false;
    
    new Vue({
      render: h => h(Component, { props }),
    }).$mount(target);
    return true;
  }
};

/**
 * 注册组件
 * @param {string} name 组件名称
 * @param {Object} implementation 组件实现
 * @param {string} framework 框架类型 (react, vue, default)
 */
function registerComponent(name, implementation, framework = 'default') {
  components[name] = {
    implementation,
    framework
  };
}

/**
 * 渲染组件
 * @param {string} name 组件名称
 * @param {Object} props 组件属性
 * @param {string} targetId 目标元素ID
 * @returns {boolean} 渲染是否成功
 */
function renderComponent(name, props = {}, targetId) {
  const component = components[name];
  if (!component) {
    console.error(`组件 "${name}" 未注册`);
    return false;
  }
  
  const renderer = renderers[component.framework] || renderers.default;
  return renderer(component.implementation, props, targetId);
}

/**
 * 自动扫描页面上的组件并渲染
 */
function autoRenderComponents() {
  document.querySelectorAll('[data-component]').forEach(el => {
    const componentName = el.dataset.component;
    
    // 获取组件属性
    let props = {};
    if (el.dataset.props) {
      try {
        props = JSON.parse(el.dataset.props);
      } catch (e) {
        console.error(`无法解析组件 "${componentName}" 的属性:`, e);
      }
    }
    
    renderComponent(componentName, props, el.id);
  });
}

// 导出组件系统
window.SimplePHP = window.SimplePHP || {};
window.SimplePHP.registerComponent = registerComponent;
window.SimplePHP.renderComponent = renderComponent;

// 在DOM加载完成后自动渲染组件
document.addEventListener('DOMContentLoaded', autoRenderComponents);

export { registerComponent, renderComponent, autoRenderComponents }; 