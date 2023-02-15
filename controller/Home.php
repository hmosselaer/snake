<?php
class Home extends Controller {  
    public function index($data=[]) {
        if(isset($_GET['user'])) $_SESSION['user'] = $_GET['user'];

        echo $this->render->view('home',$data);
    }
}

?>
