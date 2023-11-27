<?php namespace Cms\Twig;

use Twig\Token as TwigToken;
use Twig\TokenParser\AbstractTokenParser as TwigTokenParser;

/**
 * MetaTokenParser for the `{% meta %}` Twig tag.
 *
 *     {% meta %}
 *
 * @package october\cms
 * @author Alexey Bobkov, Samuel Georges
 */
class MetaTokenParser extends TwigTokenParser
{
    /**
     * parse a token and returns a node.
     * @return Twig\Node\Node A Twig\Node\Node instance
     */
    public function parse(TwigToken $token)
    {
        $stream = $this->parser->getStream();
        $stream->expect(TwigToken::BLOCK_END_TYPE);
        return new MetaNode($token->getLine(), $this->getTag());
    }

    /**
     * getTag name associated with this token parser.
     * @return string The tag name
     */
    public function getTag()
    {
        return 'meta';
    }
}
