<?php

namespace Essential\Routing;

class Route
{
    private static $routes = [];

    #РЕГИСТРАЦИЯ МАРШРУТОВ
    public static function get($route, $contract)
    {
        $contract = explode('@', $contract);
        if (!empty($contract[0]) && !empty($contract[1])) {
            $controller = $contract[0];
            $action = $contract[1];
            self::$routes[$route.'@get'] = ['method' => 'get', 'controller' => $controller, 'action' => $action];
        }
    }

    public static function post($route, $contract)
    {
        $contract = explode('@', $contract);
        if (!empty($contract[0]) && !empty($contract[1])) {
            $controller = $contract[0];
            $action = $contract[1];
            self::$routes[$route.'@post'] = ['method' => 'post', 'controller' => $controller, 'action' => $action];
        }
    }

    public static function getRoutes()
    {
        require_once $_SERVER['DOCUMENT_ROOT'].'/app/routes.php';
        return self::$routes;
    }
}
?>