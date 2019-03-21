<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */
/**
 * Smarty_Internal_Configfilelexer
 *
 * This is the config file lexer.
 * It is generated from the smarty_internal_configfilelexer.plex file
 *
 * @package    Smarty
 * @subpackage Compiler
 * @author     Uwe Tews
 */
class Smarty_Internal_Configfilelexer
{
    /**
     * Source
     *
     * @var string
     */
    public $data = NULL;
    /**
     * Source length
     *
     * @var int
     */
    public $dataLength = NULL;
    /**
     * byte counter
     *
     * @var int
     */
    public $counter = NULL;
    /**
     * token number
     *
     * @var int
     */
    public $token = NULL;
    /**
     * token value
     *
     * @var string
     */
    public $value = NULL;
    /**
     * current line
     *
     * @var int
     */
    public $line = NULL;
    /**
     * state number
     *
     * @var int
     */
    public $state = 1;
    /**
     * Smarty object
     *
     * @var Smarty
     */
    public $smarty = NULL;
    /**
     * trace file
     *
     * @var resource
     */
    public $yyTraceFILE = NULL;
    /**
     * trace prompt
     *
     * @var string
     */
    public $yyTracePrompt = NULL;
    /**
     * state names
     *
     * @var array
     */
    public $state_name = array("1" => "START", "2" => "VALUE", "3" => "NAKED_STRING_VALUE", "4" => "COMMENT", "5" => "SECTION", "6" => "TRIPPLE");
    /**
     * token names
     *
     * @var array
     */
    public $smarty_token_names = array();
    /**
     * compiler object
     *
     * @var Smarty_Internal_Config_File_Compiler
     */
    private $compiler = NULL;
    /**
     * copy of config_booleanize
     *
     * @var bool
     */
    private $configBooleanize = false;
    /**
     * storage for assembled token patterns
     *
     * @var string
     */
    private $yy_global_pattern1 = NULL;
    private $yy_global_pattern2 = NULL;
    private $yy_global_pattern3 = NULL;
    private $yy_global_pattern4 = NULL;
    private $yy_global_pattern5 = NULL;
    private $yy_global_pattern6 = NULL;
    private $_yy_state = 1;
    private $_yy_stack = array();
    const START = 1;
    const VALUE = 2;
    const NAKED_STRING_VALUE = 3;
    const COMMENT = 4;
    const SECTION = 5;
    const TRIPPLE = 6;
    /**
     * constructor
     *
     * @param   string                             $data template source
     * @param Smarty_Internal_Config_File_Compiler $compiler
     */
    public function __construct($data, Smarty_Internal_Config_File_Compiler $compiler)
    {
        $this->data = $data . "\n";
        $this->dataLength = strlen($data);
        $this->counter = 0;
        if (preg_match("/^\\xEF\\xBB\\xBF/", $this->data, $match)) {
            $this->counter += strlen($match[0]);
        }
        $this->line = 1;
        $this->compiler = $compiler;
        $this->smarty = $compiler->smarty;
        $this->configBooleanize = $this->smarty->config_booleanize;
    }
    public function replace($input)
    {
        return $input;
    }
    public function PrintTrace()
    {
        $this->yyTraceFILE = fopen("php://output", "w");
        $this->yyTracePrompt = "<br>";
    }
    public function yylex()
    {
        return $this->{"yylex" . $this->_yy_state}();
    }
    public function yypushstate($state)
    {
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%sState push %s\n", $this->yyTracePrompt, isset($this->state_name[$this->_yy_state]) ? $this->state_name[$this->_yy_state] : $this->_yy_state);
        }
        array_push($this->_yy_stack, $this->_yy_state);
        $this->_yy_state = $state;
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%snew State %s\n", $this->yyTracePrompt, isset($this->state_name[$this->_yy_state]) ? $this->state_name[$this->_yy_state] : $this->_yy_state);
        }
    }
    public function yypopstate()
    {
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%sState pop %s\n", $this->yyTracePrompt, isset($this->state_name[$this->_yy_state]) ? $this->state_name[$this->_yy_state] : $this->_yy_state);
        }
        $this->_yy_state = array_pop($this->_yy_stack);
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%snew State %s\n", $this->yyTracePrompt, isset($this->state_name[$this->_yy_state]) ? $this->state_name[$this->_yy_state] : $this->_yy_state);
        }
    }
    public function yybegin($state)
    {
        $this->_yy_state = $state;
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%sState set %s\n", $this->yyTracePrompt, isset($this->state_name[$this->_yy_state]) ? $this->state_name[$this->_yy_state] : $this->_yy_state);
        }
    }
    public function yylex1()
    {
        if (!isset($this->yy_global_pattern1)) {
            $this->yy_global_pattern1 = $this->replace("/\\G(#|;)|\\G(\\[)|\\G(\\])|\\G(=)|\\G([ \t\r]+)|\\G(\n)|\\G([0-9]*[a-zA-Z_]\\w*)|\\G([\\S\\s])/isS");
        }
        if (!isset($this->dataLength)) {
            $this->dataLength = strlen($this->data);
        }
        if ($this->dataLength <= $this->counter) {
            return false;
        }
        if (preg_match($this->yy_global_pattern1, $this->data, $yymatches, 0, $this->counter)) {
            if (!isset($yymatches[0][1])) {
                $yymatches = preg_grep("/(.|\\s)+/", $yymatches);
            } else {
                $yymatches = array_filter($yymatches);
            }
            if (empty($yymatches)) {
                throw new Exception("Error: lexing failed because a rule matched" . " an empty string.  Input \"" . substr($this->data, $this->counter, 5) . "... state START");
            }
            next($yymatches);
            $this->token = key($yymatches);
            $this->value = current($yymatches);
            $r = $this->{"yy_r1_" . $this->token}();
            if ($r === NULL) {
                $this->counter += strlen($this->value);
                $this->line += substr_count($this->value, "\n");
                return true;
            }
            if ($r === true) {
                return $this->yylex();
            }
            if ($r === false) {
                $this->counter += strlen($this->value);
                $this->line += substr_count($this->value, "\n");
                if ($this->dataLength <= $this->counter) {
                    return false;
                }
                continue;
            }
            break;
        }
        throw new Exception("Unexpected input at line" . $this->line . ": " . $this->data[$this->counter]);
    }
    public function yy_r1_1()
    {
        $this->token = Smarty_Internal_Configfileparser::TPC_COMMENTSTART;
        $this->yypushstate(self::COMMENT);
    }
    public function yy_r1_2()
    {
        $this->token = Smarty_Internal_Configfileparser::TPC_OPENB;
        $this->yypushstate(self::SECTION);
    }
    public function yy_r1_3()
    {
        $this->token = Smarty_Internal_Configfileparser::TPC_CLOSEB;
    }
    public function yy_r1_4()
    {
        $this->token = Smarty_Internal_Configfileparser::TPC_EQUAL;
        $this->yypushstate(self::VALUE);
    }
    public function yy_r1_5()
    {
        return false;
    }
    public function yy_r1_6()
    {
        $this->token = Smarty_Internal_Configfileparser::TPC_NEWLINE;
    }
    public function yy_r1_7()
    {
        $this->token = Smarty_Internal_Configfileparser::TPC_ID;
    }
    public function yy_r1_8()
    {
        $this->token = Smarty_Internal_Configfileparser::TPC_OTHER;
    }
    public function yylex2()
    {
        if (!isset($this->yy_global_pattern2)) {
            $this->yy_global_pattern2 = $this->replace("/\\G([ \t\r]+)|\\G(\\d+\\.\\d+(?=[ \t\r]*[\n#;]))|\\G(\\d+(?=[ \t\r]*[\n#;]))|\\G(\"\"\")|\\G('[^'\\\\]*(?:\\\\.[^'\\\\]*)*'(?=[ \t\r]*[\n#;]))|\\G(\"[^\"\\\\]*(?:\\\\.[^\"\\\\]*)*\"(?=[ \t\r]*[\n#;]))|\\G([a-zA-Z]+(?=[ \t\r]*[\n#;]))|\\G([^\n]+?(?=[ \t\r]*\n))|\\G(\n)/isS");
        }
        if (!isset($this->dataLength)) {
            $this->dataLength = strlen($this->data);
        }
        if ($this->dataLength <= $this->counter) {
            return false;
        }
        if (preg_match($this->yy_global_pattern2, $this->data, $yymatches, 0, $this->counter)) {
            if (!isset($yymatches[0][1])) {
                $yymatches = preg_grep("/(.|\\s)+/", $yymatches);
            } else {
                $yymatches = array_filter($yymatches);
            }
            if (empty($yymatches)) {
                throw new Exception("Error: lexing failed because a rule matched" . " an empty string.  Input \"" . substr($this->data, $this->counter, 5) . "... state VALUE");
            }
            next($yymatches);
            $this->token = key($yymatches);
            $this->value = current($yymatches);
            $r = $this->{"yy_r2_" . $this->token}();
            if ($r === NULL) {
                $this->counter += strlen($this->value);
                $this->line += substr_count($this->value, "\n");
                return true;
            }
            if ($r === true) {
                return $this->yylex();
            }
            if ($r === false) {
                $this->counter += strlen($this->value);
                $this->line += substr_count($this->value, "\n");
                if ($this->dataLength <= $this->counter) {
                    return false;
                }
                continue;
            }
            break;
        }
        throw new Exception("Unexpected input at line" . $this->line . ": " . $this->data[$this->counter]);
    }
    public function yy_r2_1()
    {
        return false;
    }
    public function yy_r2_2()
    {
        $this->token = Smarty_Internal_Configfileparser::TPC_FLOAT;
        $this->yypopstate();
    }
    public function yy_r2_3()
    {
        $this->token = Smarty_Internal_Configfileparser::TPC_INT;
        $this->yypopstate();
    }
    public function yy_r2_4()
    {
        $this->token = Smarty_Internal_Configfileparser::TPC_TRIPPLE_QUOTES;
        $this->yypushstate(self::TRIPPLE);
    }
    public function yy_r2_5()
    {
        $this->token = Smarty_Internal_Configfileparser::TPC_SINGLE_QUOTED_STRING;
        $this->yypopstate();
    }
    public function yy_r2_6()
    {
        $this->token = Smarty_Internal_Configfileparser::TPC_DOUBLE_QUOTED_STRING;
        $this->yypopstate();
    }
    public function yy_r2_7()
    {
        if (!$this->configBooleanize || !in_array(strtolower($this->value), array("true", "false", "on", "off", "yes", "no"))) {
            $this->yypopstate();
            $this->yypushstate(self::NAKED_STRING_VALUE);
            return true;
        }
        $this->token = Smarty_Internal_Configfileparser::TPC_BOOL;
        $this->yypopstate();
    }
    public function yy_r2_8()
    {
        $this->token = Smarty_Internal_Configfileparser::TPC_NAKED_STRING;
        $this->yypopstate();
    }
    public function yy_r2_9()
    {
        $this->token = Smarty_Internal_Configfileparser::TPC_NAKED_STRING;
        $this->value = "";
        $this->yypopstate();
    }
    public function yylex3()
    {
        if (!isset($this->yy_global_pattern3)) {
            $this->yy_global_pattern3 = $this->replace("/\\G([^\n]+?(?=[ \t\r]*\n))/isS");
        }
        if (!isset($this->dataLength)) {
            $this->dataLength = strlen($this->data);
        }
        if ($this->dataLength <= $this->counter) {
            return false;
        }
        if (preg_match($this->yy_global_pattern3, $this->data, $yymatches, 0, $this->counter)) {
            if (!isset($yymatches[0][1])) {
                $yymatches = preg_grep("/(.|\\s)+/", $yymatches);
            } else {
                $yymatches = array_filter($yymatches);
            }
            if (empty($yymatches)) {
                throw new Exception("Error: lexing failed because a rule matched" . " an empty string.  Input \"" . substr($this->data, $this->counter, 5) . "... state NAKED_STRING_VALUE");
            }
            next($yymatches);
            $this->token = key($yymatches);
            $this->value = current($yymatches);
            $r = $this->{"yy_r3_" . $this->token}();
            if ($r === NULL) {
                $this->counter += strlen($this->value);
                $this->line += substr_count($this->value, "\n");
                return true;
            }
            if ($r === true) {
                return $this->yylex();
            }
            if ($r === false) {
                $this->counter += strlen($this->value);
                $this->line += substr_count($this->value, "\n");
                if ($this->dataLength <= $this->counter) {
                    return false;
                }
                continue;
            }
            break;
        }
        throw new Exception("Unexpected input at line" . $this->line . ": " . $this->data[$this->counter]);
    }
    public function yy_r3_1()
    {
        $this->token = Smarty_Internal_Configfileparser::TPC_NAKED_STRING;
        $this->yypopstate();
    }
    public function yylex4()
    {
        if (!isset($this->yy_global_pattern4)) {
            $this->yy_global_pattern4 = $this->replace("/\\G([ \t\r]+)|\\G([^\n]+?(?=[ \t\r]*\n))|\\G(\n)/isS");
        }
        if (!isset($this->dataLength)) {
            $this->dataLength = strlen($this->data);
        }
        if ($this->dataLength <= $this->counter) {
            return false;
        }
        if (preg_match($this->yy_global_pattern4, $this->data, $yymatches, 0, $this->counter)) {
            if (!isset($yymatches[0][1])) {
                $yymatches = preg_grep("/(.|\\s)+/", $yymatches);
            } else {
                $yymatches = array_filter($yymatches);
            }
            if (empty($yymatches)) {
                throw new Exception("Error: lexing failed because a rule matched" . " an empty string.  Input \"" . substr($this->data, $this->counter, 5) . "... state COMMENT");
            }
            next($yymatches);
            $this->token = key($yymatches);
            $this->value = current($yymatches);
            $r = $this->{"yy_r4_" . $this->token}();
            if ($r === NULL) {
                $this->counter += strlen($this->value);
                $this->line += substr_count($this->value, "\n");
                return true;
            }
            if ($r === true) {
                return $this->yylex();
            }
            if ($r === false) {
                $this->counter += strlen($this->value);
                $this->line += substr_count($this->value, "\n");
                if ($this->dataLength <= $this->counter) {
                    return false;
                }
                continue;
            }
            break;
        }
        throw new Exception("Unexpected input at line" . $this->line . ": " . $this->data[$this->counter]);
    }
    public function yy_r4_1()
    {
        return false;
    }
    public function yy_r4_2()
    {
        $this->token = Smarty_Internal_Configfileparser::TPC_NAKED_STRING;
    }
    public function yy_r4_3()
    {
        $this->token = Smarty_Internal_Configfileparser::TPC_NEWLINE;
        $this->yypopstate();
    }
    public function yylex5()
    {
        if (!isset($this->yy_global_pattern5)) {
            $this->yy_global_pattern5 = $this->replace("/\\G(\\.)|\\G(.*?(?=[\\.=[\\]\r\n]))/isS");
        }
        if (!isset($this->dataLength)) {
            $this->dataLength = strlen($this->data);
        }
        if ($this->dataLength <= $this->counter) {
            return false;
        }
        if (preg_match($this->yy_global_pattern5, $this->data, $yymatches, 0, $this->counter)) {
            if (!isset($yymatches[0][1])) {
                $yymatches = preg_grep("/(.|\\s)+/", $yymatches);
            } else {
                $yymatches = array_filter($yymatches);
            }
            if (empty($yymatches)) {
                throw new Exception("Error: lexing failed because a rule matched" . " an empty string.  Input \"" . substr($this->data, $this->counter, 5) . "... state SECTION");
            }
            next($yymatches);
            $this->token = key($yymatches);
            $this->value = current($yymatches);
            $r = $this->{"yy_r5_" . $this->token}();
            if ($r === NULL) {
                $this->counter += strlen($this->value);
                $this->line += substr_count($this->value, "\n");
                return true;
            }
            if ($r === true) {
                return $this->yylex();
            }
            if ($r === false) {
                $this->counter += strlen($this->value);
                $this->line += substr_count($this->value, "\n");
                if ($this->dataLength <= $this->counter) {
                    return false;
                }
                continue;
            }
            break;
        }
        throw new Exception("Unexpected input at line" . $this->line . ": " . $this->data[$this->counter]);
    }
    public function yy_r5_1()
    {
        $this->token = Smarty_Internal_Configfileparser::TPC_DOT;
    }
    public function yy_r5_2()
    {
        $this->token = Smarty_Internal_Configfileparser::TPC_SECTION;
        $this->yypopstate();
    }
    public function yylex6()
    {
        if (!isset($this->yy_global_pattern6)) {
            $this->yy_global_pattern6 = $this->replace("/\\G(\"\"\"(?=[ \t\r]*[\n#;]))|\\G([\\S\\s])/isS");
        }
        if (!isset($this->dataLength)) {
            $this->dataLength = strlen($this->data);
        }
        if ($this->dataLength <= $this->counter) {
            return false;
        }
        if (preg_match($this->yy_global_pattern6, $this->data, $yymatches, 0, $this->counter)) {
            if (!isset($yymatches[0][1])) {
                $yymatches = preg_grep("/(.|\\s)+/", $yymatches);
            } else {
                $yymatches = array_filter($yymatches);
            }
            if (empty($yymatches)) {
                throw new Exception("Error: lexing failed because a rule matched" . " an empty string.  Input \"" . substr($this->data, $this->counter, 5) . "... state TRIPPLE");
            }
            next($yymatches);
            $this->token = key($yymatches);
            $this->value = current($yymatches);
            $r = $this->{"yy_r6_" . $this->token}();
            if ($r === NULL) {
                $this->counter += strlen($this->value);
                $this->line += substr_count($this->value, "\n");
                return true;
            }
            if ($r === true) {
                return $this->yylex();
            }
            if ($r === false) {
                $this->counter += strlen($this->value);
                $this->line += substr_count($this->value, "\n");
                if ($this->dataLength <= $this->counter) {
                    return false;
                }
                continue;
            }
            break;
        }
        throw new Exception("Unexpected input at line" . $this->line . ": " . $this->data[$this->counter]);
    }
    public function yy_r6_1()
    {
        $this->token = Smarty_Internal_Configfileparser::TPC_TRIPPLE_QUOTES_END;
        $this->yypopstate();
        $this->yypushstate(self::START);
    }
    public function yy_r6_2()
    {
        $to = strlen($this->data);
        preg_match("/\"\"\"[ \t\r]*[\n#;]/", $this->data, $match, PREG_OFFSET_CAPTURE, $this->counter);
        if (isset($match[0][1])) {
            $to = $match[0][1];
        } else {
            $this->compiler->trigger_template_error("missing or misspelled literal closing tag");
        }
        $this->value = substr($this->data, $this->counter, $to - $this->counter);
        $this->token = Smarty_Internal_Configfileparser::TPC_TRIPPLE_TEXT;
    }
}

?>