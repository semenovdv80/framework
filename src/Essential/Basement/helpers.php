<?php

/**
 * Show view template
 *
 * @param $view_path
 * @param null $data
 */
function view($view_path, $data = null)
{
    if (is_array($data)) {
        extract($data, EXTR_OVERWRITE); //make variables from array keys
    }

    $view_path = str_replace(".", "/", $view_path);//path to view template, change dotes to slash
    $view_path = $_SERVER['DOCUMENT_ROOT'] . '/resources/views/' . $view_path . '.php';

    if (file_exists($view_path)) {
        ob_start();
        include $view_path;
        $view = ob_get_contents();
        ob_clean();

        preg_match_all("/@include.*\)/", $view, $includ_match);//поиск строчек @include в коде вида
        if (!empty($includ_match[0])) {
            foreach ($includ_match[0] as $includ) {
                preg_match("/(\'|\").*(\'|\")/", $includ, $includ_patch);//поиск пути подключения
                if (!empty($includ_patch[0])) {
                    $patch = str_replace(".", "/", trim($includ_patch[0], "\'\""));//если путь к шаблону прописан через точку заменяем на слеш
                    include $_SERVER['DOCUMENT_ROOT'] . '/resources/views/' . $patch . '.php';
                    $includ_view = ob_get_clean();
                    $view = preg_replace("/@include\($includ_patch[0]\).*/", $includ_view, $view);
                }
            }
        }
    } else {
        echo "Представление/View не найдено";
        die();
    }

    preg_match("/@extends.*\)/", $view, $layo_match);//поиск строки подключения layout шаблона в коде вида
    preg_match_all("/@section.*\)/", $view, $sect_match);//поиск строчек секций в коде вида

    if (!empty($layo_match[0]) && !empty($sect_match[0])) {
        preg_match("/(\'|\").*(\'|\")/", $layo_match[0], $layo_path);//поиск пути к  layout шаблону
        if (!empty($layo_path[0])) {
            $layout_path = str_replace(".", "/", trim($layo_path[0], "\'\""));//если путь к шаблону прописан через точку заменяем на слеш
            ob_start();
            include_once('resources/views/' . $layout_path . '.php');//начинаем строить вывод с layout шаблона
            $output = ob_get_contents();
            ob_clean();
        };

        foreach ($sect_match[0] as $section) {
            preg_match("/(\'|\").*(\'|\")/", $section, $sect_name);//поиск имени секции
            if (!empty($sect_name[0])) {

                preg_match("/@section\($sect_name[0]\)(.*?)@endsection/si", $view, $out);//ищем содержимое каждой секции между @section и @endsection
                if (!empty($out[0])) {
                    $out = str_replace([$section, '@endsection'], '', $out[0]); //удаляем строчки подключения @section.. и @endsection из секции вида
                    $out = trim($out); //удаляем лишние пробелы
                    $output = preg_replace("/@yield\($sect_name[0]\).*/", $out, $output);//заменяем строчку подключения @yield на содержимое секции из вида $out
                }
            }
        }
        $output = preg_replace("/@yield(.*)\)/si", '', $output);//удаляем все строчки @yield в шаблоне (если что-то не было заменено контентом)
        session()->unflash();
        echo($output);
    } else {
        session()->unflash();
        echo $view;
    }
}

/**
 * Get object of current request
 *
 * @return \Essential\Http\Request
 */
function request()
{
    return \Essential\Http\Request::getCurrent();
}

/**
 * Get object of current session
 *
 * @return \Essential\Http\Session
 */
function session()
{
    return \Essential\Http\Session::getCurrent();
}

/**
 * Get object of redirect
 *
 * @return \Essential\Http\Redirect
 */
function redirect()
{
    return new \Essential\Http\Redirect();
}

function callback($buffer)
{

}
