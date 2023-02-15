<?php
class Render {
    public function view($view_filename, $data = [], $result = []){
        $data = (object)$data;
        $result = (object)$result;

        include_once("view/{$view_filename}.php");
    }
}

?>
