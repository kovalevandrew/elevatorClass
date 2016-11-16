<?php

// Elevator class - elevator logic
class ElevatorClass
{
    private $isMoving = false;
    private $dir;
    private $Reqfls = [];
    private $currfl;
    private $totalfls;

    public function __construct(){
        global $config;
        $this->totalfls = $config['total_fls'];
    }

    /*
     * Set moving
     * Input  $moving
     * Output void
     */

    public function setIsMov($moving){
        $this->isMoving = $moving;
    }

    /*
     * Get moving
     * Output $moving
     */

    public function getIsMov(){
        return $this->isMoving;
    }

    /*
     * Set dir
     * Input  $dir
     * Output void
     */

    public function setDir($d){
        $this->dir = $d;
    }

    /*
     * Get dir
     * Output $dir
     */

    public function getDir(){
        return $this->dir;
    }

    /*
     * Set current fl
     * Input  $fl
     * Output void
     */

    public function setcurrfl($fl){
        $this->currfl = $fl;
    }

    /*
     * Add fl you want to move to requested fls
     * Input  $level fl level you want to move to
     *        $dir up or down
     * Output void
     */

    public function requestfl($level, $dir){
        $fl = new Reqfl();
        $fl->setLevel($level);
        $fl->setDir($dir);
        $this->addReqfls($fl);
    }

    /*
     * Add fl you want to move to requested fls
     * Input  $level fl level you want to move to
     *        $dir up or down
     * Output void
     */

    public function moveTofl($level, $dir){
        $this->requestfl($level, $dir);
    }

    /*
     * Transport from this fl to another fl
     * Input  $fromLevel
     *        $toLevel
     * Output void
     */

    public function transport($fromLevel, $toLevel){

        if ($fromLevel < $toLevel) {
            $d = "up";
        } else {
            $d = "down";
        }

        $this->requestfl($fromLevel, $d);
        $this->moveTofl($toLevel, $d);
    }

    /*
     * Get current fl
     * Output $currfl
     */

    public function getcurrfl(){
        return $this->currfl;
    }

    /*
     * Get total fls in elevator
     * Output $totalfls
     */

    public function getTotalfls(){
        return $this->totalfls;
    }

    /*
     * Check requested fl existed or not
     * Input $Reqfl
     * Output true/false
     */

    public function existedReqfl($Reqfl){
        foreach ($this->Reqfls as $fl) {
            if ($fl->dir == $Reqfl->dir && $fl->level == $Reqfl->level) {
                return true;
            }
        }

        return false;
    }

    /*
     * Get requested fls
     * Output $Reqfls
     */

    public function getReqfls(){
        return $this->Reqfls;
    }

    /*
     * Add requested fl
     * Input $Reqfl
     * Output void
     */

    public function addReqfls($Reqfl){
        if (!$this->existedReqfl($Reqfl)) {
            $this->Reqfls[] = $Reqfl;
            $this->sortReqfls();
            $this->buildCost();
        }
    }

    /*
     * Remove requested fl
     * Input $Reqfl
     * Output void
     */

    public function removeReqfls($Reqfl){
        $fls = [];
        $total = $this->totalReqfls();
        for ($i = 0; $i < $total; $i++) {
            $fl = $this->Reqfls[$i];
            if ($fl->dir == $Reqfl->dir && $fl->level == $Reqfl->level) {
                unset($this->Reqfls[$i]);
            }
        }
    }

    /*
     * Get total requested fls
     * Output totalReqfls
     */

    public function totalReqfls(){
        return count($this->Reqfls);
    }

    /*
     * Check Elevator has request fls or not
     * Output true/false
     */

    public function hasReqfls(){
        return $this->totalReqfls() > 0;
    }

    /*
     * Elevator is change dir from up to down or down to up
     */

    public function switchdir(){
        if ($this->totalReqfls() > 0) {
            $this->setDir($this->getDir() == "up" ? "down" : "up");
        } else {
            $this->isMoving = false;
            $this->dir = "stand";
        }
    }

    /*
     * Auto swicth dir if elevator is at first fl or last fl
     */

    public function detectSwitchDir(){
        if ($this->currfl == 1) {
            $this->dir = "up";
        } else if ($this->currfl == $this->totalfls) {
            $this->dir = "down";
        }
    }

    /*
     * Check Elevator is moving up or not
     */

    public function isUp(){
        return $this->getDir() == "up";
    }

    /*
     * Check Elevator is moving down or not
     */

    public function isDown(){
        return $this->getDir() == "down";
    }

    /*
     * Check Elevator is stand or moving
     */

    public function isStand(){
        if ($this->dir == "stand" && $this->totalReqfls() == 0) {
            return true;
        }

        return false;
    }

    /*
     * Open door
     */

    public function openDoor(){
        if ($this->currfl)){
            return true;
        }

        return false;
    }

    /*
     * Close door
     */

    public function closeDoor(){
        if ($this->currfl)){
            return true;
        }

        return false;
    }

    /*
     * Process with press alart button
     */

    public function alarm(){
        return true
    }

    /*
     * E elevator will process when current fl is at requested fl
     * Input  $fl is Reqfl
     * Output void
     */

    public function processAtReqfl($fl){
        if ($this->currfl == $fl->level) {
            $this->openDoor();
            $this->closeDoor();
            $this->removeReqfls($fl);
            $this->buildCost();
        }

        $this->isMoving = true;
        $this->dir = $fl->dir;
        $maxfl = $this->getMaxRequestflLevelBydir($this->dir);
        if ($maxfl == null) {
            $this->switchdir();
        } else {
            $this->detectSwitchDir();
        }
        if ($this->isUp()) {
            $this->currfl += 1;
        } else if ($this->isDown()) {
            $this->currfl -= 1;
        }
    }

    /*
     * Run elevator
     */

    public function run(){
        if ($this->isStand()) {
            $this->isMoving = false;
            $this->dir = "stand";
            return;
        }
        $fl = $this->getMinCost();
        if ($fl == null) {
            return;
        }

        $this->processAtReqfl($fl);

        $this->run();
    }

    /*
     * Get requested min cost of level to move elevator to this fl
     */

    public function getMinCost(){
        if ($this->currfl == 1 && $this->totalReqfls() == 0) {
            return null;
        }
        $this->buildCost();
        $min = $this->totalfls;
        $minfl = null;
        foreach ($this->Reqfls as $fl) {
            if ($fl->cost <= $min) {
                $min = $fl->cost;
                $minfl = $fl;
            }
        }
        return $minfl;
    }

    /*
     * Get requested last level to switch dir;
     * Input  $d is dir
     * Output object last fl to switch dir
     */

    public function getMaxRequestflLevelBydir($dir){
        if ($dir == "up") {
            return $this->getRequestedMaxLevel($dir);
        }

        if ($d == "down") {
            return $this->getRequestedMinLevel($dir);
        }
    }

    /*
     * Get requested farthest level to move elevator to this fl;
     * Input  $d is dir
     * Output object maxfl
     */

    public function getRequestedMaxLevel($dir){
        $max = 1;
        $maxfl = null;
        foreach ($this->Reqfls as $fl) {
            if ($fl->dir == $dir && $fl->level > $max) {
                $max = $fl->level;
                $maxfl = $fl;
            }
        }

        return $maxfl;
    }

    /*
     * Get requested nearest level to move elevator to this fl;
     * Input  $d is dir
     * Output object minfl
     */

    public function getRequestedMinLevel($dir){
        $min = $this->totalfls;
        $minfl = null;
        foreach ($this->Reqfls as $fl) {
            if ($fl->dir == $dir && $fl->level <= $min) {
                $min = $fl->level;
                $minfl = $fl;
            }
        }

        return $minfl;
    }

    /*
     * Build cost in requested fls
     *
     */

    public function buildCost(){
        $total = $this->totalfls;
        $fls = [];
        foreach ($this->Reqfls as $fl) {
            $fl->cost = $fl->level - $this->currfl;
            $fl->cost = $fl->cost < 0 ? -$fl->cost : $fl->cost;
            if ($this->isMoving && $this->dir != $fl->dir) {
                $fl->cost += $total;
            }

            $fls[] = $fl;
        }
        $this->Reqfls = $fls;
    }

    /*
     * Sort request fls by dir
     * returns sorted array
     */

    public function sortReqfls(){
        $args = ["dir", "level"];
        usort($this->Reqfls, function ($a, $b) use ($args) {
            $i = 0;
            $c = count($args);
            $cmp = 0;
            while ($cmp == 0 && $i < $c) {
                $cmp = strcmp($a->$args[$i], $b->$args[$i]);
                $i++;
            }
            return $cmp;
        });
    }

}
