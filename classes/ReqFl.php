<?php

class ReqFl
{

    public $level;
    public $dir;
    public $cost = -1;

    public function setLevel($f){
        $this->level = $f;
    }

    public function getLevel(){
        return $this->level;
    }

    public function setDirection($dir){
        $this->dir = $dir;
    }

    public function getDir(){
        return $this->dir;
    }

}
