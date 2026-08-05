<?php
/**
 * Field type enum.
 *
 * @package SCFW\Core\Field
 */

declare(strict_types=1);

namespace SCFW\Core\Field;

/**
 * Supported checkout field types.
 *
 * @since 0.0.1
 */
enum FieldType : string {

	case TEXT = 'text';

	case TEXTAREA = 'textarea';

	case EMAIL = 'email';

	case TEL = 'tel';

	case NUMBER = 'number';

	case DATE = 'date';

	case CHECKBOX = 'checkbox';

	case RADIO = 'radio';

	case SELECT = 'select';
}