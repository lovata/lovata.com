<?php namespace Tailor\Classes;

use App;
use Yaml;
use Tailor\Classes\Blueprint\EntryBlueprint;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * BlueprintVerifier super class responsible for validating blueprints
 *
 * @todo List
 * - Duplicate field names (including mixins)
 * - Duplicate handles
 * - Duplicate UUIDs
 * - Missing source references
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class BlueprintVerifier
{
    /**
     * @var array reservedFieldNames are field names that cannot be used as field names.
     * @see Tailor\Classes\SchemaBuilder
     */
    protected $reservedFieldNames = [
        // Properties
        'attributes',

        // Columns
        'site_id',
        'site_root_id',
        'created_user_id',
        'updated_user_id',
        'deleted_user_id',
        'relation_id',
        'relation_type',
        'field_name',
        'nest_left',
        'nest_right',
        'nest_depth',
        'blueprint_uuid',
        'is_version',
        'primary_id',
        'primary_attrs',
        'content_group',
        'draft_mode',
        'published_at',
        'expired_at',

        // Relations
        'primaryRecord',
        'drafts',
        'versions',
        'parent',
        'children',
    ];

    /**
     * instance creates a new instance of this singleton
     */
    public static function instance(): static
    {
        return App::make('tailor.blueprint.verifier');
    }

    /**
     * verifyBlueprint
     */
    public function verifyBlueprint(Blueprint $blueprint)
    {
        $this->validateYamlSyntax($blueprint);
        $this->validateSupportedTypes($blueprint);
        $this->validateFieldset($blueprint);
    }

    /**
     * validateYamlSyntax checks the YAML syntax and parses attributes
     */
    protected function validateYamlSyntax(Blueprint $blueprint)
    {
        try {
            $blueprint->attributes = (array) Yaml::parse($blueprint->content);
        }
        catch (ParseException $ex) {
            $this->yamlToBlueprintException($blueprint, $ex);
        }
    }

    /**
     * validateSupportedTypes checks for valid blueprint types
     */
    protected function validateSupportedTypes(Blueprint $blueprint)
    {
        $supportedTypes = ['entry', 'stream', 'structure', 'single', 'mixin', 'global'];

        if (in_array($blueprint->type, $supportedTypes)) {
            return;
        }

        $lineNo = $this->findLineFromKeyValPair($blueprint->content, 'type', $blueprint->type);

        $typeAsString = implode(', ', $supportedTypes);
        throw new BlueprintException($blueprint, "Type must be one of: {$typeAsString}.", $lineNo);
    }

    /**
     * validateFieldset
     */
    protected function validateFieldset(Blueprint $blueprint)
    {
        $fields = $blueprint->fields ?? [];

        if ($blueprint instanceof EntryBlueprint && is_array($blueprint->groups)) {
            foreach ($blueprint->groups as $group) {
                $fields += $group['fields'] ?? [];
            }
        }

        $fieldset = FieldManager::instance()->makeFieldset(['fields' => $fields]);
        $fieldset->validate();

        // Check reserved field names
        foreach ($fieldset->getAllFields() as $fieldName => $fieldObj) {
            if (in_array($fieldName, $this->reservedFieldNames)) {
                $lineNo = $this->findLineFromKeyValPair($blueprint->content, $fieldName, '');
                throw new BlueprintException($blueprint, "Field name is reserved: {$fieldName}.", $lineNo);
            }
        }
    }

    /**
     * findLineFromKeyValPair
     */
    protected function findLineFromKeyValPair($content, $key, $val)
    {
        $content = PHP_EOL.$content;
        $regex = '/\n\s*'.preg_quote($key, '/').':\s*'.preg_quote($val, '/').'\s*\n/';

        if (preg_match($regex, $content, $matches, PREG_OFFSET_CAPTURE)) {
            $charPos = $matches[0][1];

            // Find line number from char position
            list($before) = str_split($content, $charPos);
            return strlen($before) - strlen(str_replace("\n", "", $before)) + 1;
        }

        return 0;
    }

    /**
     * yamlToBlueprintException is a workaround to access protected property `rawMessage`
     */
    protected function yamlToBlueprintException($blueprint, $ex)
    {
        $lineNo = $ex->getParsedLine();
        $ex->setSnippet('');
        $ex->setParsedLine(-1);

        throw new BlueprintException(
            $blueprint,
            $ex->getMessage(),
            $lineNo,
            $ex
        );
    }
}
