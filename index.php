<?php
    require_once './classes/Request.php';
    require_once './classes/Router.php';

    $request = new Request();
    $router = new Router();

    $request->testRequest();
    
    $router->getResponse($request->getRoute());
?>