<?php
class Router {
    private $routes = [];

    public function add($url, $controller, $method, $requestMethod = 'GET') {
        $this->routes[] = [
            'url' => $url,
            'controller' => $controller,
            'method' => $method,
            'requestMethod' => $requestMethod
        ];
    }

    public function dispatch($url, $requestMethod) {
        foreach ($this->routes as $route) {
            if ($route['requestMethod'] != $requestMethod) {
                continue;
            }

            $pattern = $this->convertToRegex($route['url']);
            
            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches); // Remove full match
                
                $controllerFile = "../App/Controllers/" . $route['controller'] . ".php";
                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    $controller = new $route['controller']();
                    
                    // Pass matches as arguments to the method
                    call_user_func_array([$controller, $route['method']], $matches);
                    return;
                }
            }
        }
        
        // 404 handling
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>The page you're looking for doesn't exist.</p>";
    }

    private function convertToRegex($url) {
        $url = preg_replace('/\//', '\\/', $url);
        $url = preg_replace('/{([a-z]+)}/', '([a-zA-Z0-9-]+)', $url);
        return '/^' . $url . '$/';
    }
}
?>