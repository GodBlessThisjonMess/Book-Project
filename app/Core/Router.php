<?php

namespace App\Core;

class Router {
    private $routes = [];

    /**
     * Mendaftarkan rute baru
     *
     * @param string $method GET|POST
     * @param string $route Jalur URL, contoh: '/books/{id}'
     * @param string $action Nama controller & method, contoh: 'BookController@show'
     */
    public function add($method, $route, $action) {
        // Konversi ekspresi placeholder rute '/books/{id}' menjadi regex pencocokan
        $routeRegex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_-]+)', $route);
        $routeRegex = '#^' . $routeRegex . '$#';
        
        $this->routes[] = [
            'method' => strtoupper($method),
            'route' => $routeRegex,
            'action' => $action
        ];
    }

    /**
     * Menjalankan routing pencarian rute yang cocok berdasarkan URL
     *
     * @param string $url URL saat ini
     * @param string $requestMethod GET|POST
     */
    public function dispatch($url, $requestMethod) {
        // Normalisasi URL (hilangkan query string dan slash akhir)
        $url = parse_url($url, PHP_URL_PATH);
        $url = rtrim($url, '/');
        if (empty($url)) {
            $url = '/';
        }

        $requestMethod = strtoupper($requestMethod);

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && preg_match($route['route'], $url, $matches)) {
                // Ambil parameter URL yang diberi nama (misal: 'id')
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }

                // Pecah nama Controller dan Method
                list($controllerClass, $method) = explode('@', $route['action']);
                $fullControllerClass = "App\\Controllers\\" . $controllerClass;
                
                if (class_exists($fullControllerClass)) {
                    // Instansiasi controller menggunakan penyelesai Dependency Injection (DI) otomatis
                    $controller = $this->instantiateController($fullControllerClass);
                    
                    if (method_exists($controller, $method)) {
                        call_user_func_array([$controller, $method], $params);
                        return;
                    } else {
                        throw new \Exception("Metode $method tidak ditemukan di dalam class $controllerClass.");
                    }
                } else {
                    throw new \Exception("Class Controller $controllerClass tidak ditemukan.");
                }
            }
        }

        // Jika rute tidak ditemukan
        http_response_code(404);
        echo "<div style='text-align:center; padding: 50px; font-family: sans-serif; background: #121214; color: #e1e1e6; height:100vh;'>";
        echo "<h1 style='font-size: 80px; margin: 0; color: #e54d2e;'>404</h1>";
        echo "<h2>Halaman Tidak Ditemukan</h2>";
        echo "<p style='color: #8d8d99;'>Maaf, jalur URL '" . htmlspecialchars($url) . "' tidak terdaftar dalam sistem kami.</p>";
        echo "<a href='/' style='display:inline-block; margin-top:20px; padding:10px 20px; background:#3b82f6; color:white; text-decoration:none; border-radius:5px;'>Kembali ke Dashboard</a>";
        echo "</div>";
    }

    /**
     * Menyelesaikan Dependensi Controller secara otomatis melalui Reflection API (Mini DI Container)
     */
    private function instantiateController(string $controllerClass) {
        $reflector = new \ReflectionClass($controllerClass);
        $constructor = $reflector->getConstructor();
        
        if ($constructor === null) {
            return new $controllerClass();
        }
        
        $parameters = $constructor->getParameters();
        $dependencies = [];
        
        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            if ($type && !$type->isBuiltin()) {
                $dependencyClassName = $type->getName();
                $concreteClassName = $this->resolveRepository($dependencyClassName);
                
                if (class_exists($concreteClassName)) {
                    $dependencies[] = new $concreteClassName();
                } else {
                    throw new \Exception("Gagal menyelesaikan ketergantungan (dependency) untuk $dependencyClassName.");
                }
            }
        }
        
        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * Memetakan Interface ke Kelas Repositori Konkret
     */
    private function resolveRepository(string $interfaceName): string {
        $bindings = [
            'App\\Repositories\\BookRepositoryInterface'     => 'App\\Repositories\\BookRepository',
            'App\\Repositories\\CalendarRepositoryInterface' => 'App\\Repositories\\CalendarRepository',
            'App\\Repositories\\ReviewRepositoryInterface'   => 'App\\Repositories\\ReviewRepository',
            'App\\Repositories\\JournalRepositoryInterface'  => 'App\\Repositories\\JournalRepository',
            'App\\Repositories\\UserRepositoryInterface'     => 'App\\Repositories\\UserRepository'
        ];
        
        return $bindings[$interfaceName] ?? $interfaceName;
    }
}
