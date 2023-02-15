<?php      
class Route {     
    public function __construct($parameters){
        $paramaters_explode = explode("/", trim($parameters, "/"),3);    

        $controller = isset($paramaters_explode[0]) && !empty($paramaters_explode[0]) ? $paramaters_explode[0] : 'home';
        $instance =  isset($paramaters_explode[1]) && !empty($paramaters_explode[1]) ? $paramaters_explode[1] : 'index';
        $args = isset($paramaters_explode[2]) ? explode("/", trim($paramaters_explode[2], "/")) : [];     
     
        $controller = ucwords($controller);
        require_once "controller/{$controller}.php";
        if(class_exists($controller)){
            $controller_class = new $controller();
            if(method_exists($controller_class, $instance)){
                $controller_class->$instance($args);
            }else{
                die("Undefined {$instance} Method in {$controller} Controller");
            }
        }else{
            die("Undefined {$controller} Controller");
        }
    }
}

?>
