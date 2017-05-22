<?php

namespace Hoppermagic\Kobalt\Helpers;


class RouteHelper
{
    /**
     * Generates a route for an optional resource
     * eg ["project" => "1"]
     *
     * @param $action
     * @param $route_params
     * @return Route
     */
    public function getNamedRoute($action, $route_params = [])
    {
        $segments = explode('.', app('router')->currentRouteName());
        array_pop($segments);

//        dd($route_params);

        $route_name = implode('.', $segments) . '.' .$action;
        //dd($route_name);

        if (!app('router')->has($route_name)){
            dd('>> Cant generate a route for: ' .$route_name);
        }

        return route($route_name, $route_params);
    }



    /**
     * Generates the path for the edit button on the image thumbnails
     *
     * @param $nested_wildcard
     * @return string
     */
    public function getNestedEditPath($nested_wildcard)
    {
        $segments = explode('/', app('request')->path());
        array_pop($segments);

        $nested_edit_path = '/'. implode('/', $segments) . '/' .$nested_wildcard . '/';

        return $nested_edit_path;
    }
}
