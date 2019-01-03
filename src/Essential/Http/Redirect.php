<?php

namespace Essential\Http;

class Redirect
{
    /**
     * Redirect to route
     *
     * @param $path
     */
    public function route($path)
    {
        header("Location: $path");
        exit;
    }
}