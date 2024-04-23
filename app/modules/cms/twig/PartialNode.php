<?php namespace Cms\Twig;

use Twig\Node\Node as TwigNode;
use Twig\Compiler as TwigCompiler;

/**
 * PartialNode represents a "partial" node
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class PartialNode extends TwigNode
{
    /**
     * __construct
     */
    public function __construct(TwigNode $nodes, $body, $options, $lineno, $tag = 'partial')
    {
        $nodes = ['nodes' => $nodes];

        if ($body) {
            $nodes['body'] = $body;
        }

        parent::__construct($nodes, ['options' => $options], $lineno, $tag);
    }

    /**
     * compile the node to PHP.
     */
    public function compile(TwigCompiler $compiler)
    {
        $options = $this->getAttribute('options');

        $compiler->addDebugInfo($this);

        $compiler->write("\$cmsPartialParams = [];\n");

        if ($this->hasNode('body')) {
            $compiler
                ->addDebugInfo($this)
                ->write("\$cmsPartialParams['body'] = implode('', iterator_to_array((function() use (\$context, \$blocks, \$macros) {")
                ->subcompile($this->getNode('body'))
                ->raw('return; yield "";})()));')
            ;
        }

        for ($i = 1; $i < count($this->getNode('nodes')); $i++) {
            $attrName = $options['paramNames'][$i-1];
            $compiler->write("\$cmsPartialParams['".$attrName."'] = ");
            $compiler->subcompile($this->getNode('nodes')->getNode($i));
            $compiler->write(";\n");
        }

        $isAjax = $options['isAjax'] ?? false;
        if ($isAjax) {
            $compiler->write("yield '<div data-ajax-partial=\"'.")
                ->subcompile($this->getNode('nodes')->getNode(0))
                ->write(".'\">';".PHP_EOL);
        }

        $isLazy = $options['hasLazy'] ?? false;
        if ($isLazy) {
            $compiler->write("yield '<div data-request=\"onAjax\" data-request-update=\"_self: true\" data-auto-submit>'.(\$cmsPartialParams['body'] ?? '').'</div>';");
        }
        else {
            $compiler
                ->write("yield \$this->env->getExtension(\Cms\Twig\Extension::class)->partialFunction(")
                ->subcompile($this->getNode('nodes')->getNode(0))
            ;

            if ($options['hasOnly']) {
                $compiler->write(", array_merge(['__cms_partial_params' => \$cmsPartialParams], \$cmsPartialParams)");
            }
            else {
                $compiler->write(", array_merge(\$context, ['__cms_partial_params' => \$cmsPartialParams], \$cmsPartialParams)");
            }

            $compiler
                ->write(", true")
                ->write(");\n")
            ;
        }

        if ($isAjax) {
            $compiler->write("yield '</div>';".PHP_EOL);
        }
    }
}
