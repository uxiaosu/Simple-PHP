<?php

namespace SimplePHP\Core;

require_once ROOT_PATH . '/config/Config.php';

Config::load();

error_reporting(E_ALL);
$displayErrors = Config::get('app.display_errors', false);
ini_set('display_errors', $displayErrors ? 1 : 0);

ini_set('log_errors', 1);
ini_set('error_log', Config::get('app.log_path', ROOT_PATH . '/storage/logs') . '/php_errors.log');

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        error_log("错误 [$errno] $errstr - $errfile:$errline");
        return false;
    }

    if ($errno == E_USER_ERROR || $errno == E_ERROR) {
        ob_clean();
        http_response_code(500);

        echo "<!DOCTYPE html>";
        echo "<html lang='zh'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<title>服务器错误</title>";
        echo "<style>";
        echo "body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; padding: 20px; text-align: center; }";
        echo ".error-container { max-width: 600px; margin: 50px auto; background: #f8f8f8; padding: 20px; border-radius: 5px; }";
        echo "h1 { color: #444; }";
        echo "</style>";
        echo "</head>";
        echo "<body>";
        echo "<div class='error-container'>";
        echo "<h1>服务器错误</h1>";
        echo "<p>很抱歉，处理您的请求时出现错误</p>";
        echo "<p>请稍后再试或联系管理员</p>";
        echo "</div>";
        echo "</body>";
        echo "</html>";
        exit(1);
    }

    return true;
});

spl_autoload_register(function ($className) {
    $className = str_replace('\\', '/', $className);
    $className = str_replace('SimplePHP/', '', $className);

    $file = ROOT_PATH . '/' . $className . '.php';

    if (file_exists($file)) {
        require_once $file;
        return true;
    }

    return false;
});

require_once ROOT_PATH . '/core/router/Router.php';
require_once ROOT_PATH . '/core/database/Database.php';
require_once ROOT_PATH . '/core/security/Security.php';
require_once ROOT_PATH . '/core/Application.php';

date_default_timezone_set(Config::get('app.timezone', 'UTC'));

ob_start();

use SimplePHP\Core\Security\Security;
use SimplePHP\Core\Database\Database;
use SimplePHP\Core\Cache;
use SimplePHP\Core\Http\Response;
use SimplePHP\Core\Router\Router;

class Bootstrap
{
    private $router;
    private $security;
    private $db;
    private $cache;
    private $response;

    public function __construct()
    {
        $this->security = new Security();
        $this->db = new Database();
        $this->router = new \SimplePHP\Core\Router\Router();
        $this->cache = new Cache();
        $this->response = new Response();
    }

    public function run()
    {
        try {
            $cacheEnabled = Config::get('cache.page_cache.enabled', false);
            $excludeRoutes = Config::get('cache.page_cache.exclude_routes', []);
            $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

            $isGetRequest = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET';
            $requestHasQueryParams = !empty($_SERVER['QUERY_STRING']);
            $shouldCheckCache = $isGetRequest && !$requestHasQueryParams;

            $cacheKey = 'page_' . $requestUri;

            if ($cacheEnabled && $shouldCheckCache && !in_array($requestUri, $excludeRoutes)) {
                if ($this->cache->hasPageCache($cacheKey)) {
                    $cachedContent = $this->cache->getPage($cacheKey);

                    $this->response->setContent($cachedContent)
                        ->addCacheControl(Config::get('cache.page_cache.ttl', 1800))
                        ->addHeader('X-Cache', 'HIT')
                        ->send();

                    return;
                }
            }

            $this->security->applySecurity();

            $content = $this->router->dispatch();

            if ($content !== null && $cacheEnabled && $shouldCheckCache && !in_array($requestUri, $excludeRoutes)) {
                $this->cache->cachePage($cacheKey, $content, Config::get('cache.page_cache.ttl', 1800));
            }

            if ($content !== null) {
                $this->response->setContent($content);

                if ($cacheEnabled && !in_array($requestUri, $excludeRoutes)) {
                    $this->response->addCacheControl(Config::get('cache.page_cache.ttl', 1800))
                        ->addHeader('X-Cache', 'MISS');
                } else {
                    $this->response->addHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
                        ->addHeader('Pragma', 'no-cache');
                }

                $this->response->send();
            } else {
                $this->response->setStatusCode(404)
                    ->setContent('404 - 页面未找到')
                    ->send();
            }
        } catch (\Throwable $e) {
            $this->handleException($e);
        }
    }
}