<?php namespace System\Twig;

use Twig\Node\Node as TwigNode;
use Twig\Compiler as TwigCompiler;

/**
 * MailPartialNode
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class MailPartialNode extends TwigNode
{
    public function __construct(TwigNode $nodes, $paramNames, $body, $lineno, $tag = 'partial')
    {
        $nodes = ['nodes' => $nodes];

        if ($body) {
            $nodes['body'] = $body;
        }

        parent::__construct($nodes, ['names' => $paramNames], $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param TwigCompiler $compiler A TwigCompiler instance
     */
    public function compile(TwigCompiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $compiler->write("\$systemPartialParams = [];\n");

        if ($this->hasNode('body')) {
            $compiler
                ->addDebugInfo($this)
                ->write("\$systemPartialParams['body'] = implode('', iterator_to_array((function() use (\$context, \$blocks, \$macros) {")
                ->subcompile($this->getNode('body'))
                ->raw('return; yield "";})()));')
            ;
        }

        for ($i = 1; $i < count($this->getNode('nodes')); $i++) {
            $attrName = $this->getAttribute('names')[$i-1];
            $compiler->write("\$systemPartialParams['".$attrName."'] = ");
            $compiler->subcompile($this->getNode('nodes')->getNode($i));
            $compiler->write(";\n");
        }

        $compiler
            ->write("yield \System\Classes\MailManager::instance()->renderPartial(")
            ->subcompile($this->getNode('nodes')->getNode(0))
            ->write(", array_merge(\$context, ['__system_partial_params' => \$systemPartialParams], \$systemPartialParams)")
            ->write(");\n")
        ;
    }
}
