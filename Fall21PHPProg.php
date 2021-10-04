<?php // 
include 'Tokenizer.php';
include 'EvalSectionException.php';

class Fall21PHPProg {
    public Token $currentToken;
    public Tokenizer $t;
    public array $map;
    public string $oneIndent = "   ";
    public string $result; // string containing the result of execution
    public string $EOL = PHP_EOL;
    
    function main(){
        $inputSource = "fall21Testing.txt";
        $in = file_get_contents($inputSource, 'r') or die("Unable to open file!");
        $header = "<html>".$this->EOL
                ."  <head>".$this->EOL
                ."    <title>CS 4339/5339 PHP assignment</title>".$this->EOL
                ."  </head>".$this->EOL
                ."  <body>".$this->EOL
                ."    <pre>";
        $footer = "    </pre>".$this->EOL
                ."  </body>".$this->EOL
                ."</html>";
        $this->t = new Tokenizer($in);
        print($header.$this->EOL);
        $this->currentToken = $this->t->nextToken();
        $section = 0;
        $tokenType = new TokenType;
        while ($this->currentToken->type != $tokenType::EOF) {
            print("section ". ++$section.$this->EOL);
            try {
                $this->evalSection();
                print("Section result:".$this->EOL);
                print($this->result.$this->EOL);
            } catch (EvalSectionException $ex) {
                print($this->EOL."Parsing or execution Exception: ".$ex->getMessage().$this->EOL.$this->EOL);
                // skip to the end of section
                $rsquare = $tokenType::RSQUAREBRACKET;
                $eofs = $tokenType::EOF;

                while ($this->currentToken->type != $rsquare && $this->currentToken->type != $eofs) {
                    $this->currentToken = $this->t->nextToken();
                }
                $this->currentToken = $this->t->nextToken();
            }
        }
        print($footer.$this->EOL);
    }

    function evalSection() {
        // <section> ::= [ <statement>* ]
        $tokenType = new TokenType;
        //$this->map;  
        $this->result = "";
        if ($this->currentToken->type != $tokenType::LSQUAREBRACKET) {
            throw new EvalSectionException("A section must start with \"[\"");
        }
        print("[".$this->EOL);
        $this->currentToken = $this->t->nextToken();
        //print($this->currentToken->type);
        while ($this->currentToken->type != $tokenType::RSQUAREBRACKET
                && $this->currentToken->type != $tokenType::EOF) {
            $this->evalStatement($this->oneIndent, true);
        }
        print("]".$this->EOL);
        $this->currentToken = $this->t->nextToken();
    }

    function evalStatement($indent, $exec) {
        // exec it true if we are executing the statements in addition to parsing
        // <statement> ::= STRING | <assignment> | <conditional>
        switch ($this->currentToken->type) {
            case "ID":
                $this->evalAssignment($indent, $exec);
                break;
            case "IF":
                $this->evalConditional($indent, $exec);
                break;
            case "STRING":
                if ($exec) {
                    $this->result .= $this->currentToken->value . $this->EOL;
                }
                print($indent."\"".$this->currentToken->value."\"".$this->EOL);
                $this->currentToken = $this->t->nextToken();
                break;
            default:
                throw new EvalSectionException("invalid statement");
        }
    }

    function evalAssignment($indent, $exec) {
        // <assignment> ::= ID '=' INT
        // we know currentToken is ID
        $tokenType = new TokenType;
        $key = $this->currentToken->value;
        print($indent . $key);
        $this->currentToken = $this->t->nextToken();
        if ($this->currentToken->type != $tokenType::EQUAL) {
            throw new EvalSectionException("equal sign expected");
        }
        print("=");
        $this->currentToken = $this->t->nextToken();
        if ($this->currentToken->type != $tokenType::INT) {
            throw new EvalSectionException("integer expected");
        }
        $value = intval($this->currentToken->value);
        print($value.$this->EOL);
        $this->currentToken = $this->t->nextToken();
        if ($exec) {
            $this->map[$key] = $value;
        }
    }

    function evalConditional($indent, $exec) {
        // <conditional> ::= 'if' <condition> '{' <statement>* '}' [ 'else' '{'
        // We know currentToken is "if"
        $tokenType = new TokenType;
        print($indent . "if ");
        $this->currentToken = $this->t->nextToken();
        $trueCondition = $this->evalCondition($exec);

        if ($this->currentToken->type != $tokenType::LBRACKET) {
            throw new EvalSectionException("left bracket extected");
        }
        print(" {".$this->EOL);
        $this->currentToken = $this->t->nextToken();
        while ($this->currentToken->type != $tokenType::RBRACKET
                && $this->currentToken->type != $tokenType::EOF) {
            if ($trueCondition) {
                $this->evalStatement($indent . $this->oneIndent, $exec);
            } else {
                $this->evalStatement($indent . $this->oneIndent, false);
            }
        }
        if ($this->currentToken->type == $tokenType::RBRACKET) {
            print($indent . "}".$this->EOL);
            $this->currentToken = $this->t->nextToken();
        } else {
            throw new EvalSectionException("right bracket expected");
        }
        if ($this->currentToken->type == $tokenType::ELSE) {
            print($indent . "else");
            $this->currentToken = $this->t->nextToken();
            if ($this->currentToken->type != $tokenType::LBRACKET) {
                throw new EvalSectionException("left bracket expected");
            }
            print(" {".$this->EOL);
            $this->currentToken = $this->t->nextToken();
            while ($this->currentToken->type != $tokenType::RBRACKET
                    && $this->currentToken->type != $tokenType::EOF) {
                if ($trueCondition) {
                    $this->evalStatement($indent . $this->oneIndent, false);
                } else {
                    $this->evalStatement($indent . $this->oneIndent, $exec);
                }
            }
            if ($this->currentToken->type == $tokenType::RBRACKET) {
                print($indent . "}".$this->EOL);
                $this->currentToken = $this->t->nextToken();
            } else {
                throw new EvalSectionException("right bracket expected");
            }
        }
    }
    
    function evalCondition($exec) { 
        // <condition> ::= ID ('<' | '>' | '=') INT
        $tokenType = new TokenType();
        $v1 = NULL;
        if ($this->currentToken->type != $tokenType::ID) {
            throw new EvalSectionException("identifier expected");
        }
        $key = $this->currentToken->value;
        print($key);
        if ($exec) {
            if (array_key_exists($key, $this->map)) {
                $v1 = $this->map[$key];
            }
            else {
                throw new EvalSectionException("undefined variable");
            }
        } 
        $this->currentToken = $this->t->nextToken();
        $operator = $this->currentToken->type;
        if ($this->currentToken->type != $tokenType::EQUAL and $this->currentToken->type != $tokenType::LESS and $this->currentToken->type != $tokenType::GREATER) {
            throw new EvalSectionException("comparison operator expected");
        }
     
        print($this->currentToken->value);
        $this->currentToken = $this->t->nextToken();
        if ($this->currentToken->type != $tokenType::INT) {
            throw new EvalSectionException("integer expected");
        }
        $value = intval($this->currentToken->value);
        print($value . " ");
        $this->currentToken = $this->t->nextToken();        
        // compute return value
        if (!$exec) {
            return false;
        }
        $trueResult = false;
        switch ($operator) {
            case "LESS":
                $trueResult = $v1 < $value;
                break;
            case "GREATER":
                $trueResult = $v1 > $value;
                break;
            case "EQUAL":
                $trueResult = $v1 == $value;
        }
        return $trueResult;   
    }
}

$test = new Fall21PHPProg;
$test->main();
?>