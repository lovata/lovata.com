<?php namespace Cms\Twig;

use Twig\Token as TwigToken;
use Twig\TokenParser\AbstractTokenParser as TwigTokenParser;
use Twig\Error\SyntaxError as TwigErrorSyntax;

/**
 * FrameworkTokenParser for the `{% framework %}` Twig tag.
 *
 *     {% framework %}
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class FrameworkTokenParser extends TwigTokenParser
{
    /**
     * parse a token and returns a node.
     * @return Twig\Node\Node A Twig\Node\Node instance
     */
    public function parse(TwigToken $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $options = [];
        $end = false;
        while (!$end) {
            $current = $stream->next();

            switch ($current->getType()) {
                case TwigToken::NAME_TYPE:
                    $options[] = strtolower(trim((string) $current->getValue()));
                    break;

                case TwigToken::BLOCK_END_TYPE:
                    $end = true;
                    break;

                default:
                    throw new TwigErrorSyntax(
                        sprintf('Invalid syntax in the framework tag. Line %s', $lineno),
                        $stream->getCurrent()->getLine(),
                        $stream->getSourceContext()
                    );
                    break;
            }
        }

        return new FrameworkNode($options, $lineno, $this->getTag());
    }

    /**
     * getTag name associated with this token parser.
     * @return string The tag name
     */
    public function getTag()
    {
        return 'framework';
    }
}
