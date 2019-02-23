<?php

$router = new \Bramus\Router\Router();

$router->before('GET|POST', '/(.*)', function($uri) {

});
 
$router->set404(function () {
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    echo '404, route not found!';
});

if(empty(Session::get('user_role')) && !empty(Session::get('auth_roles'))) {
    Session::set('user_role', \Delight\Auth\Role::getMap()[$_SESSION['auth_roles']]);
}

$router->match('GET|POST', '/', function() { _view('default', true, false); } );
$router->match('GET|POST', '/login', function() { _view('login', true); } );
$router->match('GET|POST', '/logout', function() { _view('logout', false); } );
$router->match('GET|POST', '/signup', function() { _view('signup', true); } );
$router->match('GET|POST', '/captcha', function () { _view('captcha', false, false); });

$router->run();

function _view($page, $tpl = true, $require_auth = false, $data = []){
    
    $suffix = '';
    
    $db     = DatabaseFactory::getFactory()->getConnection();
    $auth = new \Delight\Auth\Auth($db);
    
    if ($auth->isLoggedIn()) {
        $email = $auth->getEmail();
        $suffix = '_auth';
    } elseif ($require_auth == true) {
        Redirect::to('');
    }
    
    $actual_uri = substr(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), 3);
    
    if($tpl) header('Content-Type: text/html; charset=utf-8');
    
    $app = Config::get('APP_ID');
    
    require_once('../../app/' . $page . '.php');
    
    if($tpl){
        require_once('../../view/'.$app.'/_header' . $suffix . '.php');
        require_once('../../view/'.$app.'/' . $page . '.php');
        require_once('../../view/'.$app.'/_footer' . $suffix . '.php');
    }
    
}
