<?php

namespace Essential\Routing;

use Dotenv\Dotenv;

class Router
{
    private static $routes = [];

    #МАРШРУТИЗАЦИЯ
    public static function start()
    {
        session_start();

        $dotenv = new Dotenv($_SERVER['DOCUMENT_ROOT']);
        $dotenv->load();

        self::$routes = Route::getRoutes();

        //путь
        $path = strtok($_SERVER["REQUEST_URI"],'?'); //part of path without get params
        //метод запроса
        $method = $_SERVER['REQUEST_METHOD'];
        //получаем парметры маршрута
        $route = self::params($path, $method);

        if ($route == false)//если точное соответствие пути не найдено
        {
            $path_parts = explode('/', $_SERVER['REQUEST_URI']);

            for($n = count($path_parts)-1; $n > 0; $n--)
            {
                $path = dirname($path);

                foreach (self::$routes as $key => $values)
                {
                    if (strpos($key, $path) !== false && self::$routes[$key]['method'] == strtolower($method))//если найдено вхождение части пути в маршрут и соответствует метод запроса
                    {
                        $key_parts = explode('/', $key);
                        if(count($key_parts) == count($path_parts))//проверяем соответствие кол-ва частей пути и маршрута
                        {
                            $route = self::$routes[$key];
                            $may_vars = array_diff($key_parts, $path_parts);//возвращает массив частей маршрута $key, которые отличаются от пути (по идее это переменные)
                            foreach ($may_vars as $variable) {
                                preg_match("/{.*}/", $variable, $out_matches);//проверяем части найденного маршрута на переменные
                                if (empty($out_matches)){ $route = false; break;} //если в части маршрута с переменной не найдены фигурные скобки (это не тот  путь)
                            }
                            if ($route !=false)
                                $arr_vars = array_diff($path_parts, $key_parts);//возвращает массив частей пути, которые являются переменными
                            break;
                        }
                    }
                }
                if (!empty($route)) break;
            }
        }

        if ($route == false) {echo 'Маршрут не найден'; self::ErrorPage404();}//если маршрут так и не удалось найти

        $controller_name = $route['controller'];//имя контроллера
        $action = $route['action']; //имя функции в контроллере

        /*
        //Автозагрузка классов - подключение файлов (вызывается автоматически при обращении к классу! (смотрит в прописанный namespace)
        spl_autoload_register(function($class) {
            include str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'].'/'.$class) . '.php';
        });
        */

        // создаем контроллер
        $controller_name = '\App\controllers\\'.$controller_name;
        $controller = new $controller_name;

        if(method_exists($controller, $action))
        {
            // вызываем действие контроллера
            try {

                //количество переданных параметров
                $paramsCount = !empty($arr_vars) ? count($arr_vars) : 0;

                //создаем reflection-отражение текущего метода
                $reflectionMethod = new \ReflectionMethod($controller, $action);
                //получаем массив параметров переданных методу
                $parameters = $reflectionMethod->getParameters();
                //если переданы классы, инстпнцируем их
                foreach ($parameters as $parameter) {
                    $class = $parameter->getClass();
                    if (! is_null($class))
                        $instance = new $class->name;
                    else
                        $instance = $parameter;
                    //добавляем в массив параметров
                    $arr_vars[$paramsCount] = $instance;
                    $paramsCount++;
                }

                call_user_func_array(array($controller, $action), isset($arr_vars) ? $arr_vars : []);
            }
            catch (\Exception $e)
            {
                var_dump($e->getMessage());
            }
        }
        else
        {
            echo "Метод не найден";
            die();
        }
    }

    function ErrorPage404()
    {
        $host = 'http://'.$_SERVER['HTTP_HOST'].'/';
        header('HTTP/1.0 404 Not Found');
        header('HTTP/1.1 404 Not Found');
        header("Status: 404 Not Found");
        exit();
    }
    ####################################################################################################################

    #ПАРМЕТРЫ МАРШРУТА
    public static function params($path, $method)
    {
        $key = $path.'@'.strtolower($method);
        #возвращаем маршрут если в массиве маршрутов присутствует маршрут с таким ключом пути ($route) и его свойство method соответствует $_SERVER['REQUEST_METHOD'];
        return isset(self::$routes[$key])  ? self::$routes[$key] : false;
    }
    ####################################################################################################################

}
