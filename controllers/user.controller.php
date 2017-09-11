<?php  


class userController extends Controller{

    public function login(){
        //echo json_encode($_POST);
        $this->render($this->model->login($_POST));
    }
}