<?php
class Score extends Controller {
    public function index($data=[]) {
        foreach(file(__DIR__."/../data/score") as $file_value) if(trim($file_value)) {
            $file_value = trim($file_value);
            $file_value = explode("|||",$file_value);

            $score[user] = $file_value[1];
            $score[point] = $file_value[2];

            $scores[] = $score;
        }

        usort($scores, function($a, $b) {
           return $b['point'] <=> $a['point'];
        });

       for($a=1;$a<=10;$a++) if($scores[$a-1]) $result->score[$a] = '#'.$a.': '.$scores[$a-1][user].' ('.$scores[$a-1][point].')';

        echo $this->render->view('score',$data,$result);
    }

    public function post($data=[]) {
        if((int)$_GET['score'] > 0) file_put_contents(__DIR__."/../data/score",date("Y-m-d H:i:s")."|||{$_GET['user']}|||{$_GET['score']}\n",FILE_APPEND);
    }
}

?>
