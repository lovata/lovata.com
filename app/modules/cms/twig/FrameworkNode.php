<?php namespace Cms\Twig;

use Twig\Node\Node as TwigNode;
use Twig\Compiler as TwigCompiler;

/**
 * FrameworkNode represents a "framework" node
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class FrameworkNode extends TwigNode
{
    /**
     * __construct
     */
    public function __construct($options, $lineno, $tag = 'framework')
    {
        parent::__construct([], ['options' => $options], $lineno, $tag);
    }

    /**
     * compile the node to PHP.
     */
    public function compile(TwigCompiler $compiler)
    {
        $options = $this->getAttribute('options');
        $includeExtras = in_array('extras', $options);
        $includeTurbo = in_array('turbo', $options);

        $compiler
            ->addDebugInfo($this)
            ->write("\$_minify = System\Classes\CombineAssets::instance()->useMinify;" . PHP_EOL);

        // Default
        $cssFile = null;
        $jsScript = 'framework';

        // Options
        if ($includeExtras && $includeTurbo) {
            $jsScript = 'framework-bundle';
            $cssFile = 'framework-extras';
        }
        elseif ($includeExtras) {
            $jsScript = 'framework-extras';
            $cssFile = 'framework-extras';
        }
        elseif ($includeTurbo) {
            $jsScript = 'framework-turbo';
        }

        $compiler->write("echo '<script src=\"' . Request::getBasePath() . '/modules/system/assets/js/".$jsScript."'.(\$_minify ? '.min' : '').'.js\"></script>'.PHP_EOL;" . PHP_EOL);

        if ($cssFile) {
            $compiler->write("echo '<link rel=\"stylesheet\" property=\"stylesheet\" href=\"' . Request::getBasePath() .'/modules/system/assets/css/".$cssFile.".css\">'.PHP_EOL;" . PHP_EOL);
        }

        $compiler->write('unset($_minify);' . PHP_EOL);
    }
}
