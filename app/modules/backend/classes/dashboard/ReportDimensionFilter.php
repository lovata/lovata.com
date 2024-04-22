<?php namespace Backend\Classes\Dashboard;

use SystemException;

/**
 * ReportDimensionFilter represents a report data source dimension filter.
 *
 * Dimension filters allow to limit the data source results by applying
 * a filter to a specific dimension or a dimension field values.
 */
class ReportDimensionFilter
{
    const ATTR_TYPE_DIMENSION = 'dimension';
    const ATTR_TYPE_DIMENSION_FIELD = 'dimension_field';

    const OPERATION_EQUALS = '=';
    const OPERATION_MORE_OR_EQUALS = '>=';
    const OPERATION_LESS_OR_EQUALS = '<=';
    const OPERATION_MORE = '>';
    const OPERATION_LESS = '<';
    const OPERATION_STARTS_WITH = 'string_starts_with';
    const OPERATION_STRING_INCLUDES = 'string_includes';
    const OPERATION_ONE_OF = 'one_of';

    private $dataAttributeType;
    private $attributeName;
    private $operation;
    private $value;

    /**
     * Creates a report data dimension filter.
     * @param string $dataAttributeType Specifies the data attribute type.
     * One of the ATTR_TYPE_* constants.
     * @param ?string $attributeName Specifies the attribute name, e.g. a dimension field code.
     * Must not be null if the data attribute type is ATTR_TYPE_DIMENSION_FIELD.
     * Must be null if the data attribute type is ATTR_TYPE_DIMENSION.
     * @param bool $operation Specifies the filter operation.
     * One of the OPERATION_* constants.
     * @param string|array|int|float|bool $value Specifies the filter value.
     */
    public function __construct(string $dataAttributeType, ?string $attributeName, string $operation, string|array|int|float|bool $value)
    {
        $knownAttributeTypes = [
            self::ATTR_TYPE_DIMENSION,
            self::ATTR_TYPE_DIMENSION_FIELD
        ];
        if (!in_array($dataAttributeType, $knownAttributeTypes)) {
            throw new SystemException('Invalid data attribute type. Supported types: ' . implode(', ', $knownAttributeTypes));
        }

        if (!strlen($attributeName) && $dataAttributeType !== self::ATTR_TYPE_DIMENSION) {
            throw new SystemException('Attribute name cannot be empty for ' . $dataAttributeType . ' data attribute type.');
        }

        if (strlen($attributeName) && $dataAttributeType === self::ATTR_TYPE_DIMENSION) {
            throw new SystemException('Attribute name must be empty for ' . self::ATTR_TYPE_DIMENSION . ' data attribute type.');
        }

        if ($dataAttributeType === self::ATTR_TYPE_DIMENSION_FIELD) {
            ReportDimensionField::validateCode($attributeName);
        }

        $knownOperations = [
            self::OPERATION_EQUALS,
            self::OPERATION_MORE_OR_EQUALS,
            self::OPERATION_LESS_OR_EQUALS,
            self::OPERATION_MORE,
            self::OPERATION_LESS,
            self::OPERATION_STARTS_WITH,
            self::OPERATION_STRING_INCLUDES,
            self::OPERATION_ONE_OF
        ];

        if (!in_array($operation, $knownOperations)) {
            throw new SystemException('Invalid filter operation. Supported types: ' . implode(', ', $knownOperations));
        }

        if ($operation === self::OPERATION_ONE_OF && !is_array($value)) {
            throw new SystemException('Value must be of type array for ' . self::OPERATION_ONE_OF . ' operation.');
        } elseif ($operation !== self::OPERATION_ONE_OF && is_array($value)) {
            throw new SystemException('Value cannot be of type array for ' . $operation . ' operation.');
        }

        $this->dataAttributeType = $dataAttributeType;
        $this->attributeName = $attributeName;
        $this->operation = $operation;
        $this->value = $value;
    }

    /**
     * Get the data attribute type
     *
     * @return string
     */
    public function getDataAttributeType(): string
    {
        return $this->dataAttributeType;
    }

    /**
     * Get the attribute name
     *
     * @return string
     */
    public function getAttributeName(): ?string
    {
        return $this->attributeName;
    }

    /**
     * Get the operation
     *
     * @return string
     */
    public function getOperation(): string
    {
        return $this->operation;
    }

    /**
     * Get the value
     *
     * @return string|array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns code unique for this filter to be used as a part of a cache key.
     * @return string
     */
    public function getCacheUniqueCode(): string
    {
        $result = $this->getDataAttributeType() . $this->getDataAttributeType() . $this->getOperation();

        if (!is_array($this->value)) {
            $result .= $this->value;
        }
        else {
            foreach ($this->value as $value) {
                $result .= $value;
            }
        }

        return $result;
    }
}
