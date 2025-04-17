/**
 * SimplePHP 管理后台SPA应用入口文件
 */

// 导入模块
import API from './api';
import { registerComponent, renderComponent } from './components';

// 获取服务器端传递的初始数据
const initialState = window.__INITIAL_STATE__ || {};

// 全局状态
const state = {
  // 用户信息
  user: initialState.user || null,
  
  // 应用配置
  config: initialState.config || {
    apiBaseUrl: '/api',
    debug: false,
    defaultLocale: 'zh_CN',
    features: {
      darkMode: true,
      notifications: true
    }
  },
  
  // 客户端路由
  routes: initialState.routes || [],
  
  // 元数据
  meta: initialState.meta || {
    title: '管理后台',
    version: '1.0.0',
    timestamp: Date.now()
  },
  
  // 当前路由
  currentRoute: '/',
  
  // UI状态
  ui: {
    sidebarCollapsed: false,
    darkMode: false,
    loading: false
  }
};

/**
 * 路由管理器
 */
const router = {
  // 当前路由信息
  current: null,
  
  // 路由表
  routes: state.routes,
  
  /**
   * 初始化路由
   */
  init() {
    window.addEventListener('popstate', this.handlePopState.bind(this));
    
    // 为所有导航链接添加事件处理
    document.addEventListener('click', (e) => {
      // 查找最近的导航链接
      const navLink = e.target.closest('.nav-link[data-route]');
      if (navLink) {
        e.preventDefault();
        this.navigate(navLink.dataset.route);
      }
    });
    
    // 初始导航
    this.handlePopState();
  },
  
  /**
   * 处理浏览器历史变化
   */
  handlePopState() {
    const path = window.location.pathname.replace('/admin', '') || '/';
    this.loadRoute(path, false);
  },
  
  /**
   * 导航到指定路径
   * 
   * @param {string} path 路径
   */
  navigate(path) {
    this.loadRoute(path, true);
  },
  
  /**
   * 加载路由
   * 
   * @param {string} path 路径
   * @param {boolean} pushState 是否更新历史
   */
  loadRoute(path, pushState = true) {
    const route = this.findRoute(path);
    
    if (route) {
      // 更新当前路由
      this.current = route;
      state.currentRoute = path;
      
      // 更新页面标题
      document.title = `${route.title} - ${state.meta.title}`;
      
      // 更新浏览器历史
      if (pushState) {
        const url = `/admin${path === '/' ? '' : path}`;
        window.history.pushState({ path }, route.title, url);
      }
      
      // 渲染路由内容
      this.renderContent(route);
    } else {
      console.error(`路由不存在: ${path}`);
      this.navigate('/');
    }
  },
  
  /**
   * 查找路由
   * 
   * @param {string} path 路径
   * @returns {Object|null} 路由对象
   */
  findRoute(path) {
    return this.routes.find(r => r.path === path) || null;
  },
  
  /**
   * 渲染路由内容
   * 
   * @param {Object} route 路由对象
   */
  renderContent(route) {
    const contentArea = document.getElementById('content-area');
    if (!contentArea) return;
    
    // 设置加载状态
    state.ui.loading = true;
    updateUI();
    
    // 根据路由加载内容
    switch (route.name) {
      case 'dashboard':
        contentArea.innerHTML = renderDashboard();
        break;
      case 'users':
        contentArea.innerHTML = renderUsers();
        break;
      case 'settings':
        contentArea.innerHTML = renderSettings();
        break;
      case 'profile':
        contentArea.innerHTML = renderProfile();
        break;
      default:
        contentArea.innerHTML = '<div class="error-page">页面不存在</div>';
    }
    
    // 清除加载状态
    state.ui.loading = false;
    updateUI();
  }
};

/**
 * 更新UI状态
 */
function updateUI() {
  // 更新加载指示器
  const loadingIndicator = document.querySelector('.loading-indicator');
  if (loadingIndicator) {
    loadingIndicator.style.display = state.ui.loading ? 'flex' : 'none';
  }
  
  // 更新侧边栏状态
  const sidebar = document.querySelector('.sidebar');
  if (sidebar) {
    sidebar.classList.toggle('collapsed', state.ui.sidebarCollapsed);
  }
  
  // 更新深色模式
  document.body.classList.toggle('dark-mode', state.ui.darkMode);
  
  // 更新活动导航项
  document.querySelectorAll('.nav-link').forEach(link => {
    const isActive = link.dataset.route === state.currentRoute;
    link.classList.toggle('active', isActive);
  });
}

/**
 * 渲染管理后台框架
 */
function renderAdminApp() {
  const app = document.getElementById('app');
  if (!app) return;
  
  app.innerHTML = `
    <div class="admin-layout">
      <aside class="sidebar">
        <div class="sidebar-header">
          <h3>SimplePHP 管理后台</h3>
          <button id="toggle-sidebar" class="btn-icon">
            <i class="menu-icon"></i>
          </button>
        </div>
        <nav class="sidebar-nav">
          <ul class="nav-list">
            ${state.routes.map(route => `
              <li class="nav-item">
                <a href="#" class="nav-link ${route.path === state.currentRoute ? 'active' : ''}" data-route="${route.path}">
                  <i class="nav-icon icon-${route.name}"></i>
                  <span class="nav-text">${route.title}</span>
                </a>
              </li>
            `).join('')}
          </ul>
        </nav>
        <div class="sidebar-footer">
          <div class="user-info">
            <div class="user-avatar"></div>
            <div class="user-details">
              <div class="user-name">${state.user?.username || '访客'}</div>
              <div class="user-role">${state.user?.role || '未登录'}</div>
            </div>
          </div>
          <button id="toggle-theme" class="btn-icon">
            <i class="theme-icon"></i>
          </button>
        </div>
      </aside>
      <main class="content">
        <header class="content-header">
          <h2 id="page-title">加载中...</h2>
          <div class="header-actions">
            <div class="search-box">
              <input type="text" placeholder="搜索..." />
              <button class="btn-search">
                <i class="search-icon"></i>
              </button>
            </div>
            <div class="notifications">
              <button class="btn-notification">
                <i class="notification-icon"></i>
                <span class="badge">3</span>
              </button>
            </div>
          </div>
        </header>
        <div id="content-area" class="content-body">
          <div class="loading-indicator">
            <div class="spinner"></div>
            <p>内容加载中...</p>
          </div>
        </div>
      </main>
    </div>
  `;
  
  // 绑定事件
  document.getElementById('toggle-sidebar')?.addEventListener('click', () => {
    state.ui.sidebarCollapsed = !state.ui.sidebarCollapsed;
    updateUI();
  });
  
  document.getElementById('toggle-theme')?.addEventListener('click', () => {
    state.ui.darkMode = !state.ui.darkMode;
    updateUI();
  });
  
  // 初始化路由
  router.init();
}

/**
 * 渲染仪表盘内容
 */
function renderDashboard() {
  return `
    <div class="dashboard">
      <div class="dashboard-stats">
        <div class="stat-card">
          <div class="stat-value">254</div>
          <div class="stat-label">总用户数</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">18</div>
          <div class="stat-label">今日新增</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">1,235</div>
          <div class="stat-label">总访问量</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">$3,240</div>
          <div class="stat-label">总收入</div>
        </div>
      </div>
      <div class="dashboard-chart">
        <h3>访问统计</h3>
        <div class="chart-placeholder">图表加载中...</div>
      </div>
      <div class="dashboard-recent">
        <h3>最近活动</h3>
        <div class="activity-list">
          <div class="activity-item">
            <div class="activity-icon"></div>
            <div class="activity-content">
              <div class="activity-title">新用户注册</div>
              <div class="activity-time">5分钟前</div>
            </div>
          </div>
          <div class="activity-item">
            <div class="activity-icon"></div>
            <div class="activity-content">
              <div class="activity-title">系统更新完成</div>
              <div class="activity-time">1小时前</div>
            </div>
          </div>
          <div class="activity-item">
            <div class="activity-icon"></div>
            <div class="activity-content">
              <div class="activity-title">数据库备份成功</div>
              <div class="activity-time">3小时前</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;
}

/**
 * 渲染用户管理内容
 */
function renderUsers() {
  return `
    <div class="users-page">
      <div class="page-actions">
        <button class="btn btn-primary">添加用户</button>
        <button class="btn btn-outline">导出数据</button>
      </div>
      <div class="users-table">
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>用户名</th>
              <th>邮箱</th>
              <th>角色</th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>admin</td>
              <td>admin@example.com</td>
              <td>管理员</td>
              <td>活跃</td>
              <td>
                <button class="btn-icon"><i class="edit-icon"></i></button>
                <button class="btn-icon"><i class="delete-icon"></i></button>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>user1</td>
              <td>user1@example.com</td>
              <td>编辑</td>
              <td>活跃</td>
              <td>
                <button class="btn-icon"><i class="edit-icon"></i></button>
                <button class="btn-icon"><i class="delete-icon"></i></button>
              </td>
            </tr>
            <tr>
              <td>3</td>
              <td>user2</td>
              <td>user2@example.com</td>
              <td>用户</td>
              <td>禁用</td>
              <td>
                <button class="btn-icon"><i class="edit-icon"></i></button>
                <button class="btn-icon"><i class="delete-icon"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  `;
}

/**
 * 渲染设置内容
 */
function renderSettings() {
  return `
    <div class="settings-page">
      <div class="settings-tabs">
        <div class="tab active">常规设置</div>
        <div class="tab">系统设置</div>
        <div class="tab">安全设置</div>
        <div class="tab">通知设置</div>
      </div>
      <div class="settings-content">
        <div class="settings-section">
          <h3>网站设置</h3>
          <div class="form-group">
            <label>网站名称</label>
            <input type="text" value="SimplePHP管理后台" />
          </div>
          <div class="form-group">
            <label>网站描述</label>
            <textarea>现代化PHP框架管理系统</textarea>
          </div>
          <div class="form-group">
            <label>Logo上传</label>
            <div class="file-upload">
              <button class="btn">选择文件</button>
              <span>未选择文件</span>
            </div>
          </div>
        </div>
        <div class="settings-section">
          <h3>显示设置</h3>
          <div class="form-group">
            <label>主题颜色</label>
            <div class="color-picker">
              <div class="color-option active" style="background-color: #4361ee;"></div>
              <div class="color-option" style="background-color: #3a0ca3;"></div>
              <div class="color-option" style="background-color: #f72585;"></div>
              <div class="color-option" style="background-color: #4cc9f0;"></div>
            </div>
          </div>
          <div class="form-group">
            <label>每页显示记录数</label>
            <select>
              <option>10</option>
              <option selected>20</option>
              <option>50</option>
              <option>100</option>
            </select>
          </div>
        </div>
        <div class="settings-actions">
          <button class="btn btn-primary">保存设置</button>
          <button class="btn btn-outline">重置</button>
        </div>
      </div>
    </div>
  `;
}

/**
 * 渲染个人资料内容
 */
function renderProfile() {
  return `
    <div class="profile-page">
      <div class="profile-header">
        <div class="profile-avatar"></div>
        <div class="profile-info">
          <h3>${state.user?.username || '未知用户'}</h3>
          <div class="profile-meta">
            <span>${state.user?.email || ''}</span>
            <span>${state.user?.role || ''}</span>
          </div>
        </div>
      </div>
      <div class="profile-content">
        <div class="profile-section">
          <h3>个人信息</h3>
          <div class="form-group">
            <label>用户名</label>
            <input type="text" value="${state.user?.username || ''}" />
          </div>
          <div class="form-group">
            <label>电子邮箱</label>
            <input type="email" value="${state.user?.email || ''}" />
          </div>
          <div class="form-group">
            <label>真实姓名</label>
            <input type="text" value="管理员" />
          </div>
        </div>
        <div class="profile-section">
          <h3>修改密码</h3>
          <div class="form-group">
            <label>当前密码</label>
            <input type="password" />
          </div>
          <div class="form-group">
            <label>新密码</label>
            <input type="password" />
          </div>
          <div class="form-group">
            <label>确认新密码</label>
            <input type="password" />
          </div>
        </div>
        <div class="profile-actions">
          <button class="btn btn-primary">保存更改</button>
          <button class="btn btn-outline">取消</button>
        </div>
      </div>
    </div>
  `;
}

// 在DOM加载完成后渲染应用
document.addEventListener('DOMContentLoaded', renderAdminApp); 