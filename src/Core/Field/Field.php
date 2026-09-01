<?php
/**
 * Immutable checkout field value object.
 *
 * @package SCFW\Core\Field
 */

declare(strict_types=1);

namespace SCFW\Core\Field;

/**
 * Immutable value object describing a checkout field.
 *
 * All properties are set once via the constructor and exposed only through
 * getters. Constructing and validating Field objects should be delegated to
 * {@see FieldFactory}.
 *
 * @since 0.0.1
 */
final class Field {

	/**
	 * Field identifier.
	 *
	 * @var string
	 */
	private string $id;

	/**
	 * Human-readable label.
	 *
	 * @var string
	 */
	private string $label;

	/**
	 * Field type.
	 *
	 * @var FieldType
	 */
	private FieldType $type;

	/**
	 * Whether the field is required.
	 *
	 * @var bool
	 */
	private bool $required;

	/**
	 * Input placeholder text.
	 *
	 * @var string
	 */
	private string $placeholder;

	/**
	 * Field description.
	 *
	 * @var string
	 */
	private string $description;

	/**
	 * Selectable options for choice-based fields.
	 *
	 * @var array<int, string>
	 */
	private array $options;

	/**
	 * Default value.
	 *
	 * @var string|array<int, string>
	 */
	private string|array $defaultValue;

	/**
	 * Enabled presentation destinations.
	 *
	 * @var array<int, string>
	 */
	private array $destinations;

	/**
	 * Conditional visibility configuration.
	 *
	 * @var array{field: string, operator: string, value: string}|null
	 */
	private ?array $conditions;

	/**
	 * Create a new field.
	 *
	 * @since 0.0.1
	 *
	 * @param string              $id           Field identifier.
	 * @param string              $label        Human-readable label.
	 * @param FieldType           $type         Field type.
	 * @param bool                $required     Whether the field is required.
	 * @param string              $placeholder  Input placeholder text.
	 * @param string              $description  Field description.
	 * @param array<int, string>  $options      Selectable options for choice-based fields.
	 * @param string|array<int, string> $defaultValue Default value.
	 * @param array<int, string>  $destinations Enabled presentation destinations.
	 * @param array{field: string, operator: string, value: string}|null $conditions Conditional visibility rule.
	 */
	public function __construct(
		string $id,
		string $label,
		FieldType $type,
		bool $required = false,
		string $placeholder = '',
		string $description = '',
		array $options = array(),
		string|array $defaultValue = '',
		array $destinations = array(),
		?array $conditions = null
	) {
		$this->id           = $id;
		$this->label        = $label;
		$this->type         = $type;
		$this->required     = $required;
		$this->placeholder  = $placeholder;
		$this->description  = $description;
		$this->options      = $options;
		$this->defaultValue = $defaultValue;
		$this->destinations = empty( $destinations ) ? FieldDestination::all() : FieldDestination::normalize( $destinations );
		$this->conditions   = $conditions;
	}


	/**
	 * Get the field identifier.
	 *
	 * @since 0.0.1
	 *
	 * @return string Field identifier.
	 */
	public function getId(): string {
		return $this->id;
	}

	/**
	 * Get the human-readable label.
	 *
	 * @since 0.0.1
	 *
	 * @return string Human-readable label.
	 */
	public function getLabel(): string {
		return $this->label;
	}

	/**
	 * Get the field type.
	 *
	 * @since 0.0.1
	 *
	 * @return FieldType Field type.
	 */
	public function getType(): FieldType {
		return $this->type;
	}

	/**
	 * Get whether the field is required.
	 *
	 * @since 0.0.1
	 *
	 * @return bool True when the field is required, false otherwise.
	 */
	public function getRequired(): bool {
		return $this->required;
	}

	/**
	 * Get the input placeholder text.
	 *
	 * @since 0.0.1
	 *
	 * @return string Input placeholder text.
	 */
	public function getPlaceholder(): string {
		return $this->placeholder;
	}

	/**
	 * Get the field description.
	 *
	 * @since 0.0.1
	 *
	 * @return string Field description.
	 */
	public function getDescription(): string {
		return $this->description;
	}

	/**
	 * Get the selectable options.
	 *
	 * @since 0.0.1
	 *
	 * @return array<int, string> Selectable options.
	 */
	public function getOptions(): array {
		return $this->options;
	}

	/**
	 * Get the default value.
	 *
	 * @since 0.0.1
	 *
	 * @return string|array<int, string> Default value.
	 */
	public function getDefaultValue(): string|array {
		return $this->defaultValue;
	}

	/**
	 * Get the enabled presentation destinations.
	 *
	 * @since 0.0.1
	 *
	 * @return array<int, string> Enabled destination identifiers.
	 */
	public function getDestinations(): array {
		return $this->destinations;
	}

	/**
	 * Check whether this field is enabled for a given presentation destination.
	 *
	 * @since 0.0.1
	 *
	 * @param FieldDestination|string $destination Presentation destination to check.
	 *
	 * @return bool True when the destination is enabled.
	 */
	public function hasDestination( FieldDestination|string $destination ): bool {
		$value = $destination instanceof FieldDestination ? $destination->value : $destination;

		return in_array( $value, $this->destinations, true );
	}

	/**
	 * Get the conditional visibility rule.
	 *
	 * @since 0.0.1
	 *
	 * @return array{field: string, operator: string, value: string}|null Condition or null.
	 */
	public function getConditions(): ?array {
		return $this->conditions;
	}

	/**
	 * Determine whether this field has an active conditional visibility rule.
	 *
	 * @since 0.0.1
	 *
	 * @return bool True if a condition is set.
	 */
	public function hasConditions(): bool {
		return null !== $this->conditions && ! empty( $this->conditions['field'] );
	}
}