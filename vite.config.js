import { defineConfig } from 'vite';
import { resolve } from 'path';

// 是否启用React
const enableReact = false;

// 配置对象
const config = {
  // 基础配置
  root: 'resources',
  base: '/assets/',
  
  // 构建配置
  build: {
    outDir: '../public/assets',
    manifest: true,
    rollupOptions: {
      input: {
        app: resolve(__dirname, 'resources/js/app.js'),
        admin: resolve(__dirname, 'resources/js/admin.js'),
        api: resolve(__dirname, 'resources/js/api.js'),
        components: resolve(__dirname, 'resources/js/components.js')
      },
      output: {
        entryFileNames: '[name].js',
        chunkFileNames: '[name].js',
        assetFileNames: '[name].[ext]'
      }
    }
  },
  
  // 解析配置
  resolve: {
    alias: {
      '@': resolve(__dirname, 'resources'),
      '@js': resolve(__dirname, 'resources/js'),
      '@css': resolve(__dirname, 'resources/css'),
      '@components': resolve(__dirname, 'resources/js/components')
    }
  },
  
  // 开发服务器配置
  server: {
    host: 'localhost',
    port: 3000,
    https: false,
    open: false,
    cors: true,
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true
      }
    }
  },
  
  // CSS配置
  css: {
    devSourcemap: true
  },
  
  // 插件配置
  plugins: []
};

// 根据启用的框架添加插件
if (enableReact) {
  const react = require('@vitejs/plugin-react');
  config.plugins.push(react());
}

export default defineConfig(config); 