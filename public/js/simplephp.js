/**
 * SimplePHP 前端交互库
 * 提供SimplePHP框架的前端交互功能
 */

// 立即执行函数，防止全局变量污染
(function(window, document, $) {
    'use strict';
    
    // SimplePHP命名空间
    window.SimplePHP = window.SimplePHP || {};
    
    // 工具函数
    SimplePHP.utils = {
        /**
         * 防抖函数
         * @param {Function} func 要执行的函数
         * @param {number} wait 等待时间(毫秒)
         * @return {Function} 防抖处理后的函数
         */
        debounce: function(func, wait) {
            let timeout;
            return function() {
                const context = this, args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    func.apply(context, args);
                }, wait);
            };
        },
        
        /**
         * 节流函数
         * @param {Function} func 要执行的函数
         * @param {number} limit 限制时间(毫秒)
         * @return {Function} 节流处理后的函数
         */
        throttle: function(func, limit) {
            let lastCall = 0;
            return function() {
                const now = Date.now();
                if (now - lastCall >= limit) {
                    lastCall = now;
                    func.apply(this, arguments);
                }
            };
        },
        
        /**
         * 格式化日期
         * @param {Date|string} date 日期对象或日期字符串
         * @param {string} format 格式化模式 (如: 'YYYY-MM-DD HH:mm:ss')
         * @return {string} 格式化后的日期字符串
         */
        formatDate: function(date, format) {
            if (!date) return '';
            if (typeof date === 'string') {
                date = new Date(date);
            }
            
            const map = {
                'YYYY': date.getFullYear(),
                'MM': String(date.getMonth() + 1).padStart(2, '0'),
                'DD': String(date.getDate()).padStart(2, '0'),
                'HH': String(date.getHours()).padStart(2, '0'),
                'mm': String(date.getMinutes()).padStart(2, '0'),
                'ss': String(date.getSeconds()).padStart(2, '0')
            };
            
            return format.replace(/YYYY|MM|DD|HH|mm|ss/g, matched => map[matched]);
        },
        
        /**
         * 获取URL参数
         * @param {string} name 参数名
         * @return {string|null} 参数值或null
         */
        getUrlParam: function(name) {
            const url = window.location.href;
            name = name.replace(/[\[\]]/g, '\\$&');
            const regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
            const results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, ' '));
        },
        
        /**
         * 复制文本到剪贴板
         * @param {string} text 要复制的文本
         * @return {boolean} 是否复制成功
         */
        copyToClipboard: function(text) {
            try {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                return true;
            } catch (e) {
                console.error('复制到剪贴板失败:', e);
                return false;
            }
        }
    };
    
    // 表单处理
    SimplePHP.form = {
        /**
         * 表单序列化为JSON对象
         * @param {HTMLFormElement|jQuery} form 表单元素
         * @return {Object} 表单数据对象
         */
        serializeToJson: function(form) {
            if (form instanceof jQuery) {
                form = form[0];
            }
            
            const formData = new FormData(form);
            const obj = {};
            
            for (const [key, value] of formData.entries()) {
                if (obj[key] !== undefined) {
                    if (!Array.isArray(obj[key])) {
                        obj[key] = [obj[key]];
                    }
                    obj[key].push(value);
                } else {
                    obj[key] = value;
                }
            }
            
            return obj;
        },
        
        /**
         * 表单验证
         * @param {HTMLFormElement|jQuery} form 表单元素
         * @param {Object} rules 验证规则
         * @return {boolean} 是否通过验证
         */
        validate: function(form, rules) {
            if (form instanceof jQuery) {
                form = form[0];
            }
            
            const data = this.serializeToJson(form);
            let isValid = true;
            const errors = {};
            
            // 清除之前的错误信息
            form.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            form.querySelectorAll('.invalid-feedback').forEach(el => {
                el.remove();
            });
            
            // 验证规则
            for (const field in rules) {
                if (!rules.hasOwnProperty(field)) continue;
                
                const fieldRules = rules[field];
                const fieldValue = data[field];
                const fieldElement = form.querySelector(`[name="${field}"]`);
                
                if (!fieldElement) continue;
                
                // 检查每个规则
                for (const rule in fieldRules) {
                    if (!fieldRules.hasOwnProperty(rule)) continue;
                    
                    const ruleValue = fieldRules[rule].value;
                    const message = fieldRules[rule].message || '输入无效';
                    
                    let fieldError = false;
                    
                    switch (rule) {
                        case 'required':
                            if (ruleValue && !fieldValue) {
                                fieldError = true;
                            }
                            break;
                        case 'minLength':
                            if (fieldValue && fieldValue.length < ruleValue) {
                                fieldError = true;
                            }
                            break;
                        case 'maxLength':
                            if (fieldValue && fieldValue.length > ruleValue) {
                                fieldError = true;
                            }
                            break;
                        case 'pattern':
                            if (fieldValue && !new RegExp(ruleValue).test(fieldValue)) {
                                fieldError = true;
                            }
                            break;
                        case 'email':
                            if (ruleValue && fieldValue && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(fieldValue)) {
                                fieldError = true;
                            }
                            break;
                        case 'match':
                            const matchField = form.querySelector(`[name="${ruleValue}"]`);
                            if (matchField && fieldValue !== matchField.value) {
                                fieldError = true;
                            }
                            break;
                    }
                    
                    if (fieldError) {
                        isValid = false;
                        errors[field] = message;
                        
                        // 显示错误信息
                        fieldElement.classList.add('is-invalid');
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.innerText = message;
                        fieldElement.parentNode.appendChild(feedback);
                        
                        break;
                    }
                }
            }
            
            return { isValid, errors };
        }
    };
    
    // AJAX请求
    SimplePHP.ajax = {
        /**
         * 发起AJAX请求
         * @param {Object} options 请求选项
         * @return {Promise} Promise对象
         */
        request: function(options) {
            const defaults = {
                url: '',
                method: 'GET',
                data: null,
                dataType: 'json',
                contentType: 'application/json',
                headers: {},
                beforeSend: null,
                complete: null,
                showLoading: true
            };
            
            options = Object.assign({}, defaults, options);
            
            // 显示加载提示
            if (options.showLoading) {
                SimplePHP.ui.showLoading();
            }
            
            // 设置CSRF令牌
            if (!options.headers['X-CSRF-TOKEN'] && $('meta[name="csrf-token"]').length) {
                options.headers['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');
            }
            
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: options.url,
                    type: options.method,
                    data: options.method.toUpperCase() === 'GET' ? options.data : 
                          (options.contentType === 'application/json' ? JSON.stringify(options.data) : options.data),
                    dataType: options.dataType,
                    contentType: options.contentType,
                    headers: options.headers,
                    beforeSend: function(xhr) {
                        if (typeof options.beforeSend === 'function') {
                            options.beforeSend(xhr);
                        }
                    },
                    success: function(response) {
                        resolve(response);
                    },
                    error: function(xhr, status, error) {
                        const response = {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText
                        };
                        
                        try {
                            response.data = JSON.parse(xhr.responseText);
                        } catch (e) {
                            response.data = null;
                        }
                        
                        reject(response);
                    },
                    complete: function(xhr, status) {
                        if (options.showLoading) {
                            SimplePHP.ui.hideLoading();
                        }
                        
                        if (typeof options.complete === 'function') {
                            options.complete(xhr, status);
                        }
                    }
                });
            });
        },
        
        /**
         * GET请求
         * @param {string} url 请求地址
         * @param {Object} data 请求参数
         * @param {Object} options 其他选项
         * @return {Promise} Promise对象
         */
        get: function(url, data = {}, options = {}) {
            return this.request(Object.assign({}, { url, method: 'GET', data }, options));
        },
        
        /**
         * POST请求
         * @param {string} url 请求地址
         * @param {Object} data 请求数据
         * @param {Object} options 其他选项
         * @return {Promise} Promise对象
         */
        post: function(url, data = {}, options = {}) {
            return this.request(Object.assign({}, { url, method: 'POST', data }, options));
        }
    };
    
    // UI交互
    SimplePHP.ui = {
        loadingCount: 0,
        
        /**
         * 显示加载提示
         * @param {string} message 提示消息
         */
        showLoading: function(message = '加载中...') {
            this.loadingCount++;
            
            if (this.loadingCount === 1) {
                if (!document.getElementById('simplephp-loading')) {
                    const loading = document.createElement('div');
                    loading.id = 'simplephp-loading';
                    loading.className = 'position-fixed w-100 h-100 d-flex align-items-center justify-content-center';
                    loading.style.cssText = 'top: 0; left: 0; background-color: rgba(0,0,0,0.2); z-index: 9999;';
                    
                    const spinner = document.createElement('div');
                    spinner.className = 'bg-white p-4 rounded shadow d-flex flex-column align-items-center';
                    
                    const spinnerEl = document.createElement('div');
                    spinnerEl.className = 'spinner-border text-primary mb-2';
                    spinnerEl.setAttribute('role', 'status');
                    
                    const spinnerText = document.createElement('span');
                    spinnerText.className = 'spinner-text';
                    spinnerText.innerText = message;
                    
                    spinner.appendChild(spinnerEl);
                    spinner.appendChild(spinnerText);
                    loading.appendChild(spinner);
                    document.body.appendChild(loading);
                } else {
                    document.querySelector('#simplephp-loading .spinner-text').innerText = message;
                    document.getElementById('simplephp-loading').style.display = 'flex';
                }
            }
        },
        
        /**
         * 隐藏加载提示
         */
        hideLoading: function() {
            this.loadingCount = Math.max(0, this.loadingCount - 1);
            
            if (this.loadingCount === 0) {
                const loading = document.getElementById('simplephp-loading');
                if (loading) {
                    loading.style.display = 'none';
                }
            }
        },
        
        /**
         * 显示提示消息
         * @param {string} message 消息内容
         * @param {string} type 消息类型 (success|info|warning|danger)
         * @param {number} duration 显示时长(毫秒)
         */
        toast: function(message, type = 'info', duration = 3000) {
            const id = 'toast-' + new Date().getTime();
            const toast = document.createElement('div');
            toast.id = id;
            toast.className = `toast position-fixed bottom-0 end-0 m-3 bg-${type} text-white`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            toast.style.zIndex = 1080;
            
            toast.innerHTML = `
                <div class="toast-header bg-${type} text-white">
                    <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">${message}</div>
            `;
            
            document.body.appendChild(toast);
            
            const toastInstance = new bootstrap.Toast(toast, { animation: true, autohide: true, delay: duration });
            toastInstance.show();
            
            toast.addEventListener('hidden.bs.toast', function() {
                toast.remove();
            });
        },
        
        /**
         * 显示确认对话框
         * @param {string} message 确认消息
         * @param {Function} callback 回调函数
         * @param {Object} options 其他选项
         */
        confirm: function(message, callback, options = {}) {
            const defaults = {
                title: '确认',
                confirmText: '确定',
                cancelText: '取消',
                confirmClass: 'btn-primary',
                cancelClass: 'btn-secondary'
            };
            
            options = Object.assign({}, defaults, options);
            
            const id = 'confirm-modal-' + new Date().getTime();
            const modal = document.createElement('div');
            modal.id = id;
            modal.className = 'modal fade';
            modal.setAttribute('tabindex', '-1');
            modal.setAttribute('aria-hidden', 'true');
            
            modal.innerHTML = `
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${options.title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>${message}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn ${options.cancelClass}" data-bs-dismiss="modal">${options.cancelText}</button>
                            <button type="button" class="btn ${options.confirmClass} confirm-btn">${options.confirmText}</button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
            
            const confirmBtn = modal.querySelector('.confirm-btn');
            confirmBtn.addEventListener('click', function() {
                modalInstance.hide();
                if (typeof callback === 'function') {
                    callback(true);
                }
            });
            
            modal.addEventListener('hidden.bs.modal', function() {
                modal.remove();
            });
        }
    };
    
    // 动画效果
    SimplePHP.animation = {
        /**
         * 应用动画效果
         * @param {HTMLElement|jQuery} element 目标元素
         * @param {string} animationName 动画名称
         * @param {Function} callback 回调函数
         */
        animate: function(element, animationName, callback) {
            if (element instanceof jQuery) {
                element = element[0];
            }
            
            element.classList.add('animate__animated', `animate__${animationName}`);
            
            function handleAnimationEnd(event) {
                event.stopPropagation();
                element.classList.remove('animate__animated', `animate__${animationName}`);
                element.removeEventListener('animationend', handleAnimationEnd);
                
                if (typeof callback === 'function') {
                    callback();
                }
            }
            
            element.addEventListener('animationend', handleAnimationEnd);
        }
    };
    
    // 数据绑定和模板
    SimplePHP.template = {
        /**
         * 渲染模板
         * @param {string} template 模板字符串
         * @param {Object} data 数据对象
         * @return {string} 渲染后的HTML
         */
        render: function(template, data) {
            return template.replace(/\{\{([^}]+)\}\}/g, function(match, key) {
                key = key.trim();
                return data[key] !== undefined ? data[key] : '';
            });
        },
        
        /**
         * 绑定数据到元素
         * @param {HTMLElement|jQuery} container 容器元素
         * @param {Object} data 数据对象
         */
        bind: function(container, data) {
            if (container instanceof jQuery) {
                container = container[0];
            }
            
            // 查找所有绑定点
            const bindElements = container.querySelectorAll('[data-bind]');
            
            bindElements.forEach(element => {
                const bindKey = element.getAttribute('data-bind');
                if (data[bindKey] !== undefined) {
                    if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA' || element.tagName === 'SELECT') {
                        element.value = data[bindKey];
                    } else {
                        element.textContent = data[bindKey];
                    }
                }
            });
        }
    };
    
    // 初始化
    document.addEventListener('DOMContentLoaded', function() {
        // 初始化提示工具
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // 初始化弹出框
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    });
    
})(window, document, jQuery); 