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
 * Main config file compiler class
 *
 * @package    Smarty
 * @subpackage Config
 */
class Smarty_Internal_Config_File_Compiler
{
    /**
     * Lexer class name
     *
     * @var string
     */
    public $lexer_class = NULL;
    /**
     * Parser class name
     *
     * @var string
     */
    public $parser_class = NULL;
    /**
     * Lexer object
     *
     * @var object
     */
    public $lex = NULL;
    /**
     * Parser object
     *
     * @var object
     */
    public $parser = NULL;
    /**
     * Smarty object
     *
     * @var Smarty object
     */
    public $smarty = NULL;
    /**
     * Smarty object
     *
     * @var Smarty_Internal_Template object
     */
    public $template = NULL;
    /**
     * Compiled config data sections and variables
     *
     * @var array
     */
    public $config_data = array();
    /**
     * compiled config data must always be written
     *
     * @var bool
     */
    public $write_compiled_code = true;
    /**
     * Initialize compiler
     *
     * @param string $lexer_class  class name
     * @param string $parser_class class name
     * @param Smarty $smarty       global instance
     */
    public function __construct($lexer_class, $parser_class, Smarty $smarty)
    {
        $this->smarty = $smarty;
        $this->lexer_class = $lexer_class;
        $this->parser_class = $parser_class;
        $this->smarty = $smarty;
        $this->config_data["sections"] = array();
        $this->config_data["vars"] = array();
    }
    /**
     * Method to compile Smarty config source.
     *
     * @param Smarty_Internal_Template $template
     *
     * @return bool true if compiling succeeded, false if it failed
     * @throws \SmartyException
     */
    public function compileTemplate(Smarty_Internal_Template $template)
    {
        $this->template = $template;
        $this->template->compiled->file_dependency[$this->template->source->uid] = array($this->template->source->filepath, $this->template->source->getTimeStamp(), $this->template->source->type);
        if ($this->smarty->debugging) {
            if (!isset($this->smarty->_debug)) {
                $this->smarty->_debug = new Smarty_Internal_Debug();
            }
            $this->smarty->_debug->start_compile($this->template);
        }
        $this->lex = new $this->lexer_class(str_replace(array("\r\n", "\r"), "\n", $template->source->getContent()) . "\n", $this);
        $this->parser = new $this->parser_class($this->lex, $this);
        if (function_exists("mb_internal_encoding") && function_exists("ini_get") && (int) ini_get("mbstring.func_overload") & 2) {
            $mbEncoding = mb_internal_encoding();
            mb_internal_encoding("ASCII");
        } else {
            $mbEncoding = NULL;
        }
        if ($this->smarty->_parserdebug) {
            $this->parser->PrintTrace();
        }
        while ($this->lex->yylex()) {
            if ($this->smarty->_parserdebug) {
                echo "<br>Parsing  " . $this->parser->yyTokenName[$this->lex->token] . " Token " . $this->lex->value . " Line " . $this->lex->line . " \n";
            }
            $this->parser->doParse($this->lex->token, $this->lex->value);
        }
        $this->parser->doParse(0, 0);
        if ($mbEncoding) {
            mb_internal_encoding($mbEncoding);
        }
        if ($this->smarty->debugging) {
            $this->smarty->_debug->end_compile($this->template);
        }
        $template_header = "<?php /* Smarty version " . Smarty::SMARTY_VERSION . ", created on " . strftime("%Y-%m-%d %H:%M:%S") . "\n";
        $template_header .= "         compiled from '" . $this->template->source->filepath . "' */ ?>\n";
        $code = "<?php \$_smarty_tpl->smarty->ext->configLoad->_loadConfigVars(\$_smarty_tpl, " . var_export($this->config_data, true) . "); ?>";
        return $template_header . $this->template->smarty->ext->_codeFrame->create($this->template, $code);
    }
    /**
     * display compiler error messages without dying
     * If parameter $args is empty it is a parser detected syntax error.
     * In this case the parser is called to obtain information about expected tokens.
     * If parameter $args contains a string this is used as error message
     *
     * @param string $args individual error message or null
     *
     * @throws SmartyCompilerException
     */
    public function trigger_config_file_error($args = NULL)
    {
        $line = $this->lex->line;
        if (isset($args)) {
        }
        $match = preg_split("/\n/", $this->lex->data);
        $error_text = "Syntax error in config file '" . $this->template->source->filepath . "' on line " . $line . " '" . $match[$line - 1] . "' ";
        if (isset($args)) {
            $error_text .= $args;
        } else {
            foreach ($this->parser->yy_get_expected_tokens($this->parser->yymajor) as $token) {
                $exp_token = $this->parser->yyTokenName[$token];
                if (isset($this->lex->smarty_token_names[$exp_token])) {
                    $expect[] = "\"" . $this->lex->smarty_token_names[$exp_token] . "\"";
                } else {
                    $expect[] = $this->parser->yyTokenName[$token];
                }
            }
            $error_text .= " - Unexpected \"" . $this->lex->value . "\", expected one of: " . implode(" , ", $expect);
        }
        throw new SmartyCompilerException($error_text);
    }
}

?>