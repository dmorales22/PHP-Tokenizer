<?php 
    class EvalSectionException extends Exception {
        function EvalSectionException($m) {
            print(Fall21PHPProg::EOL . "Parsing or execution Exception: " . $m . Fall21PHPProg::EOL);
        }
    }
?>