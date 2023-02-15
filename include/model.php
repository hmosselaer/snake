<?php
class Model {
    public function load_model($model){
        $model = ucwords($model);

        if(is_file("model/{$model}.php")) {
            require_once "model/{$model}.php";

            if(class_exists($model)){
                return new $model();
            }else{
                die("Undefined {$model} Model");
            }
        }
    }
}

?>
