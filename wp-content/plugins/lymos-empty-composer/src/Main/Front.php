<?php
namespace Lymos\Lwc\Main;

class Front{

    public $obj = null;

    public function __construct($obj)
    {
        $this->obj = $obj;
        $this->_init();
    }

    public function _init(){
        $this->_addHooks();
    }

    public function _addHooks(){

    }
   
}