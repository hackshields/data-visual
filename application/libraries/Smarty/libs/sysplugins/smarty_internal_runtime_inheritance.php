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
 * Inheritance Runtime Methods processBlock, endChild, init
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 *
 **/
class Smarty_Internal_Runtime_Inheritance
{
    /**
     * State machine
     * - 0 idle next extends will create a new inheritance tree
     * - 1 processing child template
     * - 2 wait for next inheritance template
     * - 3 assume parent template, if child will loaded goto state 1
     *     a call to a sub template resets the state to 0
     *
     * @var int
     */
    public $state = 0;
    /**
     * Array of root child {block} objects
     *
     * @var Smarty_Internal_Block[]
     */
    public $childRoot = array();
    /**
     * inheritance template nesting level
     *
     * @var int
     */
    public $inheritanceLevel = 0;
    /**
     * inheritance template index
     *
     * @var int
     */
    public $tplIndex = -1;
    /**
     * Array of template source objects
     *
     * @var Smarty_Template_Source[]
     */
    public $sources = array();
    /**
     * Stack of source objects while executing block code
     *
     * @var Smarty_Template_Source[]
     */
    public $sourceStack = array();
    /**
     * Initialize inheritance
     *
     * @param \Smarty_Internal_Template $tpl        template object of caller
     * @param bool                      $initChild  if true init for child template
     * @param array                     $blockNames outer level block name
     *
     */
    public function init(Smarty_Internal_Template $tpl, $initChild, $blockNames = array())
    {
        if ($initChild && $this->state === 3 && strpos($tpl->template_resource, "extendsall") === false) {
            $tpl->inheritance = new Smarty_Internal_Runtime_Inheritance();
            $tpl->inheritance->init($tpl, $initChild, $blockNames);
        } else {
            $this->tplIndex++;
            $this->sources[$this->tplIndex] = $tpl->source;
            if ($initChild) {
                $this->state = 1;
                if (!$this->inheritanceLevel) {
                    ob_start();
                }
                $this->inheritanceLevel++;
            }
            if ($this->state === 2) {
                $this->state = 3;
            }
        }
    }
    /**
     * End of child template(s)
     * - if outer level is reached flush output buffer and switch to wait for parent template state
     *
     * @param \Smarty_Internal_Template $tpl
     * @param null|string               $template optional name of inheritance parent template
     * @param null|string               $uid      uid of inline template
     * @param null|string               $func     function call name of inline template
     *
     * @throws \Exception
     * @throws \SmartyException
     */
    public function endChild(Smarty_Internal_Template $tpl, $template = NULL, $uid = NULL, $func = NULL)
    {
        $this->inheritanceLevel--;
        if (!$this->inheritanceLevel) {
            ob_end_clean();
            $this->state = 2;
        }
        if (isset($template) && ($tpl->parent->_isTplObj() && $tpl->parent->source->type !== "extends" || $tpl->smarty->extends_recursion)) {
            $tpl->_subTemplateRender($template, $tpl->cache_id, $tpl->compile_id, $tpl->caching ? 9999 : 0, $tpl->cache_lifetime, array(), 2, false, $uid, $func);
        }
    }
    /**
     * Smarty_Internal_Block constructor.
     * - if outer level {block} of child template ($state === 1) save it as child root block
     * - otherwise process inheritance and render
     *
     * @param \Smarty_Internal_Template $tpl
     * @param                           $className
     * @param string                    $name
     * @param int|null                  $tplIndex index of outer level {block} if nested
     *
     * @throws \SmartyException
     */
    public function instanceBlock(Smarty_Internal_Template $tpl, $className, $name, $tplIndex = NULL)
    {
        $block = new $className($name, isset($tplIndex) ? $tplIndex : $this->tplIndex);
        if (isset($this->childRoot[$name])) {
            $block->child = $this->childRoot[$name];
        }
        if ($this->state === 1) {
            $this->childRoot[$name] = $block;
        } else {
            while ($block->child && $block->tplIndex <= $block->child->tplIndex) {
                $block->child = $block->child->child;
            }
            $this->process($tpl, $block);
        }
    }
    /**
     * Goto child block or render this
     *
     * @param \Smarty_Internal_Template   $tpl
     * @param \Smarty_Internal_Block      $block
     * @param \Smarty_Internal_Block|null $parent
     *
     * @throws \SmartyException
     */
    public function process(Smarty_Internal_Template $tpl, Smarty_Internal_Block $block, Smarty_Internal_Block $parent = NULL)
    {
        if ($block->hide && !isset($block->child)) {
            return NULL;
        }
        if (isset($block->child) && $block->child->hide && !isset($block->child->child)) {
            $block->child = NULL;
        }
        $block->parent = $parent;
        if ($block->append && !$block->prepend && isset($parent)) {
            $this->callParent($tpl, $block, "'{block append}'");
        }
        if ($block->callsChild || !isset($block->child) || $block->child->hide && !isset($block->child->child)) {
            $this->callBlock($block, $tpl);
        } else {
            $this->process($tpl, $block->child, $block);
        }
        if ($block->prepend && isset($parent)) {
            $this->callParent($tpl, $block, "{block prepend}");
            if ($block->append) {
                if ($block->callsChild || !isset($block->child) || $block->child->hide && !isset($block->child->child)) {
                    $this->callBlock($block, $tpl);
                } else {
                    $this->process($tpl, $block->child, $block);
                }
            }
        }
        $block->parent = NULL;
    }
    /**
     * Render child on \$smarty.block.child
     *
     * @param \Smarty_Internal_Template $tpl
     * @param \Smarty_Internal_Block    $block
     *
     * @return null|string block content
     * @throws \SmartyException
     */
    public function callChild(Smarty_Internal_Template $tpl, Smarty_Internal_Block $block)
    {
        if (isset($block->child)) {
            $this->process($tpl, $block->child, $block);
        }
    }
    /**
     * Render parent block on \$smarty.block.parent or {block append/prepend}
     *
     * @param \Smarty_Internal_Template $tpl
     * @param \Smarty_Internal_Block    $block
     * @param string                    $tag
     *
     * @return null|string  block content
     * @throws \SmartyException
     */
    public function callParent(Smarty_Internal_Template $tpl, Smarty_Internal_Block $block, $tag)
    {
        if (isset($block->parent)) {
            $this->callBlock($block->parent, $tpl);
        } else {
            throw new SmartyException("inheritance: illegal '" . $tag . "' used in child template '" . $tpl->inheritance->sources[$block->tplIndex]->filepath . "' block '" . $block->name . "'");
        }
    }
    /**
     * render block
     *
     * @param \Smarty_Internal_Block    $block
     * @param \Smarty_Internal_Template $tpl
     */
    public function callBlock(Smarty_Internal_Block $block, Smarty_Internal_Template $tpl)
    {
        $this->sourceStack[] = $tpl->source;
        $tpl->source = $this->sources[$block->tplIndex];
        $block->callBlock($tpl);
        $tpl->source = array_pop($this->sourceStack);
    }
}

?>