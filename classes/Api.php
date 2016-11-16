<?php
// Api class that allow us to use eleveator class through browser get request
class Api
{

    private $elevator;

    public function __construct(){
        $this->elevator = new ElevatorClass();
    }

    public function request(){
        $level = $_GET['level'];
        $dir = $_GET['dir'];
        $this->elevator->requestfl($level, $dir);
        echo json_encode(['status' => "Ok", "message" => "Request for floor added."]);
    }

    public function send(){
        $level = $_GET['level'];
        $dir = $_GET['dir'];
        $this->elevator->moveTofl($level, $dir);
        echo json_encode(['status' => "Ok", "message" => "Move floor added."]);
    }

    public function openDoor(){
        $this->elevator->openDoor();
        echo json_encode(['status' => "Ok", "message" => "Door opened."]);
    }

    public function closeDoor(){
        $this->elevator->closeDoor();
        echo json_encode(['status' => "Ok", "message" => "Door closed."]);
    }

    public function alarm(){
        $this->elevator->alarm();
        echo json_encode(['status' => "Ok", "message" => "Alarm."]);
    }

}
