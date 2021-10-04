<?php 
include 'TokenType.php';

class Token { 
    public string $type;
    public string $value;
    
    function __construct(string $theType, string $theValue){
        $this->type = $theType;
        $this->value = $theValue;
    } 
}
?>