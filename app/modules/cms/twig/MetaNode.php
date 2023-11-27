<?php namespace Cms\Twig;

use Twig\Node\Node as TwigNode;
use Twig\Compiler as TwigCompiler;

/**
 * MetaNode represents a "meta" node
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class MetaNode extends TwigNode
{
    /**
     * __construct
     */
    public function __construct($lineno, $tag = 'meta')
    {
        parent::__construct([], [], $lineno, $tag);
    }

    /**
     * compile the node to PHP.
     */
    public function compile(TwigCompiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write("echo \$this->env->getExtension(\Cms\Twig\Extension::class)->displayBlock('meta');\n")
        ;
    }
}
