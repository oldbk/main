<?php

foreach (\components\Helper\RoutesHelper::getRoutes() as $route) {
    include (string)$route;
}