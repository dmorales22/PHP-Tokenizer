<?php 
include 'Token.php';

class Tokenizer {
    private array $e;
    private int $i;
    public string $currentChar = ""; 
    
    function __construct(string $s) {
        $this->e = str_split($s);
        $this->i = 0; 
    }
    
    function nextToken() {
        $tokenType = new TokenType;
        while ($this->i < count($this->e) && (strrpos($this->e[$this->i], " ") > -1
                or strrpos($this->e[$this->i], "\n") > -1 
                or strrpos($this->e[$this->i], "\t") > -1
                or strrpos($this->e[$this->i], "\r") > -1)) {
            $this->i = $this->i + 1;
        }
        if ($this->i >= count($this->e)) {
            $token = new Token($tokenType::EOF, "");
            return $token;
        }
        
        $inputString = "";
        while ($this->i < count($this->e) 
                && (strrpos($this->e[$this->i], "0") > -1
                or strrpos($this->e[$this->i], "1") > -1
                or strrpos($this->e[$this->i], "2") > -1
                or strrpos($this->e[$this->i], "3") > -1
                or strrpos($this->e[$this->i], "4") > -1
                or strrpos($this->e[$this->i], "5") > -1
                or strrpos($this->e[$this->i], "6") > -1
                or strrpos($this->e[$this->i], "7") > -1
                or strrpos($this->e[$this->i], "8") > -1
                or strrpos($this->e[$this->i], "9") > -1
                )) {
            $inputString .= $this->e[$this->i];
            $this->i = $this->i + 1; 
        }
        
        if ("" != $inputString) {
            $token = new Token($tokenType::INT, $inputString);
            return $token;
        }
        
        // check for ID or reserved word        
        while ($this->i < count($this->e) 
                && (strrpos($this->e[$this->i], "a") > -1
                or strrpos($this->e[$this->i], "b") > -1
                or strrpos($this->e[$this->i], "c") > -1 
                or strrpos($this->e[$this->i], "d") > -1
                or strrpos($this->e[$this->i], "e") > -1
                or strrpos($this->e[$this->i], "f") > -1
                or strrpos($this->e[$this->i], "g") > -1
                or strrpos($this->e[$this->i], "h") > -1
                or strrpos($this->e[$this->i], "i") > -1
                or strrpos($this->e[$this->i], "j") > -1
                or strrpos($this->e[$this->i], "k") > -1
                or strrpos($this->e[$this->i], "l") > -1
                or strrpos($this->e[$this->i], "m") > -1
                or strrpos($this->e[$this->i], "n") > -1
                or strrpos($this->e[$this->i], "o") > -1
                or strrpos($this->e[$this->i], "p") > -1
                or strrpos($this->e[$this->i], "q") > -1
                or strrpos($this->e[$this->i], "r") > -1
                or strrpos($this->e[$this->i], "s") > -1
                or strrpos($this->e[$this->i], "t") > -1
                or strrpos($this->e[$this->i], "u") > -1
                or strrpos($this->e[$this->i], "v") > -1
                or strrpos($this->e[$this->i], "w") > -1
                or strrpos($this->e[$this->i], "x") > -1
                or strrpos($this->e[$this->i], "y") > -1
                or strrpos($this->e[$this->i], "z") > -1
                or strrpos($this->e[$this->i], "A") > -1
                or strrpos($this->e[$this->i], "B") > -1 
                or strrpos($this->e[$this->i], "C") > -1
                or strrpos($this->e[$this->i], "D") > -1
                or strrpos($this->e[$this->i], "E") > -1
                or strrpos($this->e[$this->i], "F") > -1
                or strrpos($this->e[$this->i], "G") > -1
                or strrpos($this->e[$this->i], "H") > -1
                or strrpos($this->e[$this->i], "I") > -1
                or strrpos($this->e[$this->i], "J") > -1
                or strrpos($this->e[$this->i], "K") > -1
                or strrpos($this->e[$this->i], "L") > -1 
                or strrpos($this->e[$this->i], "M") > -1
                or strrpos($this->e[$this->i], "N") > -1
                or strrpos($this->e[$this->i], "O") > -1
                or strrpos($this->e[$this->i], "P") > -1
                or strrpos($this->e[$this->i], "Q") > -1
                or strrpos($this->e[$this->i], "R") > -1
                or strrpos($this->e[$this->i], "S") > -1
                or strrpos($this->e[$this->i], "T") > -1
                or strrpos($this->e[$this->i], "U") > -1
                or strrpos($this->e[$this->i], "V") > -1
                or strrpos($this->e[$this->i], "W") > -1
                or strrpos($this->e[$this->i], "X") > -1
                or strrpos($this->e[$this->i], "Y") > -1
                or strrpos($this->e[$this->i], "Z") > -1
                or strrpos($this->e[$this->i], "_") > -1
                )) {
            $inputString .= $this->e[$this->i++];
        }

        if ("" != $inputString) {
            if ("if" == $inputString) {
                $token = new Token($tokenType::IF, "");
                return $token; 
            }

            if ("else" == $inputString) {
                $token = new Token($tokenType::ELSE, "");
                return $token;
            }
            $token = new Token($tokenType::ID, $inputString);
            return $token;
        }
        
        // We're left with strings or one character tokens
        switch ($this->e[$this->i++]) {
            case '{':
                $token = new Token($tokenType::LBRACKET, "{");
                return $token;              
            case '}':
                $token = new Token($tokenType::RBRACKET,"}");
                return $token;
            case '[':
                $token = new Token($tokenType::LSQUAREBRACKET, "[");
                return $token;
            case ']':
                $token = new Token($tokenType::RSQUAREBRACKET,"]");
                return $token;
            case '<':
                $token = new Token($tokenType::LESS,"<");
                return $token;
            case '>':
                $token = new Token($tokenType::GREATER, ">");
                return $token;
            case '=':
                $token = new Token($tokenType::EQUAL,"=");
                return $token;
            case '"':
                $value = "";

                while ($this->i < count($this->e) && $this->e[$this->i] != '"') {
                    $c = $this->e[$this->i++];
                    if ($this->i >= count($this->e)) {
                        $token = new Token($tokenType::OTHER, "");
                        return $token;
                    }
                    // check for escaped double quote
                    if ($c == '\\' && $this->e[$this->i] == '"'){
                        $c = '"';
                        $this->i = $this->i + 1;
                    }
                    $value .= $c;
                } 
                $this->i++;
                $token = new Token($tokenType::STRING, $value); //STRING
                return $token;
            default:
                // OTHER should result in exception
                $token = new Token($tokenType::OTHER, ""); //OTHER
                return $token;
        }
    }
}
?>
