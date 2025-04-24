<?php
namespace Lymos\Lwc\View;

class Setting{

    public $obj = null;

    public function __construct($obj = null)
    {
        $this->obj = $obj;
    }

    public function show(){
        include_once LWC_DIR . '/src/Template/setting.php';
    }
}