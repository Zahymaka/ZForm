<?php defined('SYSPATH') or die('No direct script access.');

/**
* ZForm: a form generation library for Kohana 3 ORM
*
* @package    ZForm
* @author     Azuka Okuleye
* @copyright  (c) 2011 Azuka Okuleye
* @license    http://zahymaka.com/license.html
* @property   array $zfields
*/
class Zahymaka_ZForm extends ORM {

	/**
	 * Individual field configurations
	 * @var array
	 */
	protected $_z_field_config = array();
	/**
	 * Form labels
	 * @var array
	 */
	protected $_z_labels       = array();
	/**
	 * Fields to exclude from the form
	 * @var array
	 */
	protected $_z_exclude      = array();
	/**
	 * Additional field configuration.
	 * @todo remove or use
	 * @var array
	 */
	protected $_z_fields      = array();
	/**
	 * Extra attributes for form fields
	 * @var array
	 */
	protected $_z_attributes  = array();
	/**
	 * Help text path.
	 * @var boolean
	 */
	protected $_z_help_path   = NULL;
	/**
	 * Name of object (post key). So for a user model you'll get user[username]
	 * @var string
	 */
	protected $_z_orm_name    = NULL;
	/**
	 * Errors
	 * @var string
	 */
	protected $_z_errors      = NULL;
	/**
	 * Init called.
	 * @var boolean
	 */
	protected $_z_inited      = false;
	/**
	 * Primary val
	 * @var string
	 */
	protected $_z_primary_val = 'name';

	/**
	 * Setup. Initializes all non-hidden fields
	 * @return $this
	 */
	public function setup_form()
	{
		if ($this->_z_inited)
			return $this;

		$this->_z_initialize();
		$this->_z_create_fields();

		foreach ($this->_z_fields as $column => $field)
		{
			/* @var $field ZForm_Field */
			// Loaded or changed, set field value
			if (isset($this->_changed[$column]) || $this->loaded())
			{
				$field->value = $this->$column;
			}
			// Use default value
			else
			{
				$field->set_default();
			}
		}

		$this->finalize();

		// Help text
		$messages = (array) Kohana::message($this->_z_help_path);

		foreach ($this->_z_fields as $column => $field)
		{
			$field->help_text = Arr::get($messages, $column, '');
		}

		$this->_z_inited = true;

		return $this;
	}

	/**
	 * Set form key
	 * @param string $orm_name
	 * @return Zahymaka_ZForm
	 */
	public function set_name($orm_name)
	{
		$this->_z_orm_name = $orm_name;

		return $this;
	}

	/**
	 * Exclude column
	 * @param array|string $column,...
	 * @return Zahymaka_ZForm
	 */
	public function exclude($columns)
	{
		if (!is_array($columns))
		{
			$columns = func_get_args();
		}

		foreach ($columns as $column)
		{
			$this->_z_exclude[$column] = $column;
		}

		return $this;
	}

	/**
	 * Include column
	 * @param string $column
	 * @return Zahymaka_ZForm
	 */
	public function zinclude($column)
	{
		if (isset($this->_z_exclude[$column]))
			unset($this->_z_exclude[$column]);
		return $this;
	}

	/**
	 *
	 * @param mixed $columns,...
	 * @return string
	 */
	public function generate_form($columns = NULL)
	{
		$this->setup_form();

		if (!$columns)
		{
			// All columns except excluded
			$columns = array_keys(Arr::extract($this->_table_columns, array_filter(array_keys($this->_table_columns), array($this, '_z_filter'))));
		}
		elseif (!is_array($columns))
		{
			$columns = func_get_args();
		}

		$render = '';

		foreach ($columns as $column)
		{
			if (!isset($this->_z_fields[$column]))
				continue;

			$render .= $this->_z_fields[$column]->single_field();
		}

		return $render;
	}

	/**
	 * Get the form label for a specific field
	 * @param string $field
	 * @throws ErrorException
	 * @return string
	 */
	public function form_label($field)
	{
		return $this->zfields[$field]->form_label();
	}

	/**
	 * Get the form field for a specific field
	 * @param string $field
	 * @throws ErrorException
	 * @return string
	 */
	public function form_field($field)
	{
		return $this->zfields[$field]->form_field();
	}

	/**
	 *
	 * @param type $array
	 * @param type $columns
	 * @return Zahymaka_ZForm
	 */
	public function get_form($array = NULL, $columns = NULL)
	{
		$this->setup_form();

		if (!$array)
			$array = Request::current()->post();

		if (!$columns)
		{
			// All columns except excluded
			$columns = array_keys(Arr::extract($this->_table_columns, array_filter(array_keys($this->_table_columns), array($this, '_z_filter'))));
		}
		elseif (!is_array($columns))
		{
			$columns = func_get_args();
			array_shift($columns);
		}

		foreach ($columns as $column)
		{
			if (!isset($this->_z_fields[$column]))
				continue;

			$this->_z_fields[$column]->value = Arr::path($array, $this->field_path($column));
			$this->$column                   = $this->_z_fields[$column]->db_value();
		}

		return $this;
	}

	/**
	 * Overloaded __get() for zfields
	 * @param string $column
	 * @return mixed
	 */
	public function  __get($column)
	{
		if ($column == 'zfields')
		{
			$this->setup_form();
			return $this->_z_fields;
		}
		elseif ($column == 'z_orm_name')
		{
			return $this->_z_orm_name ? $this->_z_orm_name : $this->_object_name;
		}

		return parent::__get($column);
	}

	public function labels()
	{
		if (!$this->_z_inited)
			return parent::labels();
		return $this->_z_labels;
	}

	/**
	 * Sync field values with
	 * @param Validation $extra_validation
	 * @todo Clean this up and enable error passing directly to fields
	 */
	public function check(Validation $extra_validation = NULL)
	{
		$ex     = NULL;
		$errors = array();

		// Reapply filters by setting objects again
		foreach ($this->table_columns() as $column => $definition)
		{
			if ($column == $this->primary_key())
			{
				continue;
			}

			$this->$column = $this->$column;
		}

		try
		{
			parent::check($extra_validation);
		}
		catch (ORM_Validation_Exception $ex)
		{
			$errors = $ex->errors('models', TRUE);
		}

		// After applying filters, set field values back.
		foreach ($this->_z_fields as $column => $field)
		{
			$field->value = $this->$column;
			$field->error = Arr::get($errors, $column);
		}

		// Rethrow exception
		if ($ex instanceof ORM_Validation_Exception)
		{
			throw $ex;
		}
	}

	/**
	 * Get a list of errors after validating. Parse using error config
	 * @return string Error message
	 * @usage <code>$this->errors();</code>
	 * @usage <code>$this->errors('Alert::error', 'es', 'models');</code>
	 */
	public function errors($callback = NULL, $language = TRUE, $directory = 'models')
	{
		try
		{
			$this->check();
			return array();
		}
		catch (ORM_Validation_Exception $ex)
		{
			$messages = $ex->errors($directory, $language);

			if ($callback && is_callable($callback))
			{
				return join("\n", array_map($callback, $messages));
			}

			return $messages;
		}
	}

	/**
	 * Set any field options beforehand
	 */
	public function initialize(){}

	/**
	 * Finish up after fields are loaded
	 */
	public function finalize(){}

	/**
	 * Field form name
	 * @param string $field
	 * @return string
	 */
	public function field_name($column)
	{
		return $this->loaded()
				?
				$this->z_orm_name . '[' . $this->pk() . '][' . $column . ']'
				:
				$this->z_orm_name . '[' . $column . ']';
	}

	/**
	 * Path in form array
	 * @param string $column
	 * @return string
	 */
	public function field_path($column)
	{
		return $this->loaded()
				?
				$this->z_orm_name . '.' . $this->pk() . '.' . $column
				:
				$this->z_orm_name . '.' . $column;
	}

	/**
	 * Form ID
	 * @param string $field
	 * @return string
	 */
	public function field_id($column)
	{
		return str_replace('.', '_', $this->field_path($column));
	}

	/**
	 * Set path to help
	 * @param string $message_path
	 * @return $this
	 */
	public function set_help($message_path)
	{
		$this->_z_help_path = $message_path;

		return $this;
	}

	/**
	 * I think this is specifically for the days dropdown
	 * @param array $array
	 * @param int $length
	 * @return array
	 */
	public static function zerofill($array, $length = 2)
	{
		foreach ($array as $key => $value)
		{
			$array[$key] = str_pad($value, $length, '0', STR_PAD_LEFT);
		}

		return $array;
	}

	/**
	 * Select function with weighted items and separators e.g. Countries: [United States][Canada][------][Afghanistan]...[United States]...
	 * @param string $name
	 * @param array $options
	 * @param mixed $selected
	 * @param array $attributes
	 * @param string $separator
	 * @return string
	 */
	public static function multichoice($name, array $options = NULL, $selected = NULL, array $attributes = NULL, $separator = NULL)
	{
		$iselected = false;
		$first     = true;
		$aoptions  = '';
		$attributes['name'] = $name;

		if ($separator === NULL)
			$separator = '----------';

		foreach ((array)$options as $loptions)
		{
			if (!$first AND $separator AND !empty($loptions))
			{
				$aoptions .= '<option disabled="disabled">'.$separator.'</option>';
			}
			else
			{
				$first = false;
			}

			if (empty($loptions))
			{
				// There are no options
				$loptions = '';
			}
			else
			{
				foreach ($loptions as $value => $name)
				{
					if (is_array($name))
					{
						// Create a new optgroup
						$group = array('label' => $value);

						// Create a new list of options
						$_options = array();

						foreach ($name as $_value => $_name)
						{
							// Create a new attribute set for this option
							$option = array('value' => $_value);

							if ($_value == $selected && !$iselected)
							{
								// This option is selected
								$option['selected'] = 'selected';
								$iselected          = true;
							}

							// Sanitize the option title
							$title = htmlspecialchars($_name, ENT_NOQUOTES, Kohana::$charset, FALSE);

							// Change the option to the HTML string
							$_options[] = '<option'.HTML::attributes($option).'>'.$title.'</option>';
						}

						// Compile the options into a string
						$_options = "\n".implode("\n", $_options)."\n";

						$loptions[$value] = '<optgroup'.HTML::attributes($group).'>'.$_options.'</optgroup>';
					}
					else
					{
						// Create a new attribute set for this option
						$option = array('value' => $value);

						if ($value == $selected && !$iselected)
						{
							// This option is selected
							$option['selected'] = 'selected';
							$iselected          = true;
						}

						// Sanitize the option title
						$title = htmlspecialchars($name, ENT_NOQUOTES, Kohana::$charset, FALSE);

						// Change the option to the HTML string
						$loptions[$value] = '<option'.HTML::attributes($option).'>'.$title.'</option>';
					}
				}

				// Compile the options into a single string
				$loptions = "\n".implode("\n", $loptions)."\n";
			}

			$aoptions .= $loptions;
		}

		return '<select'.HTML::attributes($attributes).'>'.$aoptions.'</select>';
	}

	/**
	 * Used to filter out all excluded columns
	 * @param string $column
	 * @return string
	 */
	protected function _z_filter($column)
	{
		return !in_array($column, $this->_z_exclude);
	}

	/**
	 * Initialize basic properties: excluded fields, form name
	 */
	protected function _z_initialize()
	{
		$this->initialize();

		// Exclude primary key
		$this->exclude($this->_primary_key);

		// Exclude created and updated columns
		if (isset($this->_updated_column['column']))
		{
			$this->_z_exclude[] = $this->_updated_column['column'];
		}
		if (isset($this->_created_column['column']))
		{
			$this->_z_exclude[] = $this->_created_column['column'];
		}

		// Use object name for form fields
		if (empty($this->_z_orm_name))
		{
			$this->_z_orm_name = $this->_object_name;
		}

		// Help path
		if ($this->_z_help_path === NULL)
		{
			$this->_z_help_path = 'help/model/'.$this->_object_name;
		}
	}

	/**
	 * Create fields automatically
	 */
	protected function _z_create_fields()
	{

		foreach ($this->_belongs_to as $column => $data)
		{
			// Field has already been set in
			if (isset($this->_z_fields[$data['foreign_key']]) OR in_array($data['foreign_key'], $this->_z_exclude))
				continue;

			// Assign label
			if (!isset($this->_z_labels[$data['foreign_key']]))
			{
				$this->_z_labels[$data['foreign_key']] = ucfirst(Inflector::humanize($column));
			}

			// Field type is set
			if (isset($this->_z_field_config[$data['foreign_key']]['type']))
				continue;


			$options    = ORM::factory($data['model']);

			$pk         = Arr::overwrite(array($options->primary_key(), $options->primary_key()), (array) Arr::get($data, 'zform_pk'));
			$label      = Arr::overwrite(array('name', 'name'), (array) Arr::get($data, 'zform_label'));

			$options    = $options->select($pk)->select($label);
			$options    = $options->find_all()->as_array($pk[1], $label[1]);

			$attributes = Arr::get($this->_z_attributes, $data['foreign_key']);

			$name       = $this->field_name($data['foreign_key']);
			$id         = $this->field_id($data['foreign_key']);
			$label      = Arr::get($this->_z_labels, $data['foreign_key'], ucfirst(Inflector::humanize($column)));
			$config     = array('options' => $options);

			$this->_z_fields[$data['foreign_key']] = new ZForm_Field_Enum($name, $id, $label, $config, $attributes, $data);

		}

		foreach ($this->_table_columns as $column => $field)
		{
			// Field has already been set or has been excluded
			if (isset($this->_z_fields[$column]) OR in_array($column, $this->_z_exclude))
			{
				$this->_z_labels[$column] = Arr::get($this->_z_labels, $column, ucfirst(Inflector::humanize($column)));
				continue;
			}

			$data_type  = explode(' ', $field['data_type']);
			$data_type  = $data_type[0];


			// Get additional config items, and add the default data
			$config     = Arr::merge(Kohana::$config->load('zcolumns.default.default_column'), (array) Kohana::$config->load('zcolumns.default.'.$data_type));
			$config     = Arr::merge($config, (array) Arr::get($this->_z_field_config, $column));

			$type       = 'ZForm_Field_' . $config['type'];

			$attributes = Arr::get($this->_z_attributes, $column);

			$name       = $this->field_name($column);
			$id         = $this->field_id($column);
			$label      = Arr::get($this->_z_labels, $column, ucfirst(Inflector::humanize($column)));

			$this->_z_labels[$column] = $label;
			$this->_z_fields[$column] = new $type($name, $id, $label, $config, $attributes, $field);
		}
	}

}