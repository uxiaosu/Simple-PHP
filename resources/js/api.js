/**
 * SimplePHP 前端API通信模块
 * 基于fetch API的轻量级请求封装
 */

const API = {
  /**
   * 基础URL
   */
  baseUrl: '/api',
  
  /**
   * 默认请求头
   */
  defaultHeaders: {
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  },
  
  /**
   * 获取CSRF令牌
   * @returns {string} CSRF令牌
   */
  getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  },
  
  /**
   * 生成完整URL
   * @param {string} endpoint API端点
   * @returns {string} 完整URL
   */
  buildUrl(endpoint) {
    // 移除前导斜杠
    if (endpoint.startsWith('/')) {
      endpoint = endpoint.substring(1);
    }
    return `${this.baseUrl}/${endpoint}`;
  },
  
  /**
   * 处理响应
   * @param {Response} response Fetch API 响应对象
   * @returns {Promise} 处理结果Promise
   */
  async handleResponse(response) {
    const contentType = response.headers.get('content-type');
    
    // 处理JSON响应
    if (contentType && contentType.includes('application/json')) {
      const data = await response.json();
      
      if (!response.ok) {
        const error = new Error(data.message || response.statusText);
        error.response = response;
        error.data = data;
        throw error;
      }
      
      return data;
    }
    
    // 处理非JSON响应
    if (!response.ok) {
      const error = new Error(response.statusText);
      error.response = response;
      throw error;
    }
    
    return await response.text();
  },
  
  /**
   * 发送请求
   * @param {string} method 请求方法
   * @param {string} endpoint 请求端点
   * @param {Object} data 请求数据
   * @param {Object} options 请求选项
   * @returns {Promise} 请求结果Promise
   */
  async request(method, endpoint, data = null, options = {}) {
    const url = this.buildUrl(endpoint);
    const headers = { ...this.defaultHeaders, ...options.headers };
    
    // 添加CSRF令牌
    const csrfToken = this.getCsrfToken();
    if (csrfToken && ['POST', 'PUT', 'DELETE'].includes(method.toUpperCase())) {
      headers['X-CSRF-TOKEN'] = csrfToken;
    }
    
    const fetchOptions = {
      method: method.toUpperCase(),
      headers,
      ...options
    };
    
    // 添加请求体
    if (data) {
      if (headers['Content-Type'] === 'application/json') {
        fetchOptions.body = JSON.stringify(data);
      } else if (data instanceof FormData) {
        fetchOptions.body = data;
        // 当使用FormData时，让浏览器自动设置Content-Type
        delete headers['Content-Type'];
      }
    }
    
    try {
      const response = await fetch(url, fetchOptions);
      return await this.handleResponse(response);
    } catch (error) {
      if (!error.response) {
        // 网络错误
        error.message = '网络请求失败，请检查您的网络连接';
      }
      throw error;
    }
  },
  
  /**
   * GET请求
   * @param {string} endpoint 请求端点
   * @param {Object} options 请求选项
   * @returns {Promise} 请求结果Promise
   */
  get(endpoint, options = {}) {
    return this.request('GET', endpoint, null, options);
  },
  
  /**
   * POST请求
   * @param {string} endpoint 请求端点
   * @param {Object} data 请求数据
   * @param {Object} options 请求选项
   * @returns {Promise} 请求结果Promise
   */
  post(endpoint, data, options = {}) {
    return this.request('POST', endpoint, data, options);
  },
  
  /**
   * PUT请求
   * @param {string} endpoint 请求端点
   * @param {Object} data 请求数据
   * @param {Object} options 请求选项
   * @returns {Promise} 请求结果Promise
   */
  put(endpoint, data, options = {}) {
    return this.request('PUT', endpoint, data, options);
  },
  
  /**
   * DELETE请求
   * @param {string} endpoint 请求端点
   * @param {Object} options 请求选项
   * @returns {Promise} 请求结果Promise
   */
  delete(endpoint, options = {}) {
    return this.request('DELETE', endpoint, null, options);
  }
};

// 导出API对象
window.SimplePHP = window.SimplePHP || {};
window.SimplePHP.API = API;

export default API; 