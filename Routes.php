<?php
use Fennec\Library\Router;

$routes = array(
    array(
        'name' => 'slider',
        'route' => '/slider/',
        'module' => 'Slider',
        'controller' => 'Index',
        'action' => 'slider',
        'layout' => null
    ),
    array(
        'name' => 'admin-slider',
        'route' => '/admin/slider/',
        'module' => 'Slider',
        'controller' => 'Admin\\Index',
        'action' => 'index',
        'layout' => 'Admin/Default'
    ),
    array(
        'name' => 'admin-slider-newbanner',
        'route' => '/admin/slider/newbanner/',
        'module' => 'Slider',
        'controller' => 'Admin\\Index',
        'action' => 'form',
        'layout' => 'Admin/Default'
    ),
    array(
        'name' => 'admin-slider-editbanner',
        'route' => '/admin/slider/editbanner/([0-9]+)/',
        'params' => array(
            'id'
        ),
        'module' => 'Slider',
        'controller' => 'Admin\\Index',
        'action' => 'form',
        'layout' => 'Admin/Default'
    ),
    array(
        'name' => 'admin-slider-deletebanner',
        'route' => '/admin/slider/delete/([0-9]+)/',
        'params' => array(
            'id'
        ),
        'module' => 'Slider',
        'controller' => 'Admin\\Index',
        'action' => 'delete',
        'layout' => null
    ),
    array(
        'name' => 'admin-slider-reorderbanenrs',
        'route' => '/admin/slider/reorder/',
        'module' => 'Slider',
        'controller' => 'Admin\\Index',
        'action' => 'reorder',
        'layout' => null
    )
);

foreach ($routes as $route) {
    Router::addRoute($route);
}
