<?php defined('SYSPATH') or die('No direct script access.');

/**
* ZForm field: The base class for all Zform field types
*
* @package    ZForm
* @author     Azuka Okuleye
* @copyright  (c) 2009 Azuka Okuleye
* @license    http://zahymaka.com/license.html
*/
abstract class Zahymaka_ZForm_Field {

	/**
	 * Form field attributes
	 * @var array
	 */
	protected $_attributes  = array();
	/**
	 * Valid configuration items
	 * @var array
	 */
	protected $_config      = array();
	/**
	 * Data about the column from ORM
	 * @var array
	 */
	protected $_extra       = array();
	/**
	 * Form field id
	 * @var string
	 */
	protected $_id          = NULL;
	/**
	 * Form field name
	 * @var string
	 */
	protected $_name        = NULL;
	/**
	 * Form field label
	 * @var string
	 */
	protected $_label       = NULL;
	/**
	 * Form field value
	 * @var string
	 */
	protected $_value       = NULL;
	/**
	 * Form field help text.
	 * @var string
	 */
	protected $_help_text   = NULL;
	/**
	 * Form field error.
	 * @var string
	 */
	protected $_error       = NULL;

	/**
	 * Wrapper
	 * @var string
	 */
	protected $_wrapper     = 'zform/wrappers/default';

	/**
	 * Render the field
	 * @return string
	 */
	abstract public function render();

	/**
	 * Create a new field
	 * @param string $name
	 * @param string $id
	 * @param string $label
	 * @param array $config
	 * @param array $attributes
	 * @param array $extra
	 */
	public function __construct($name, $id, $label, array $config = NULL, array $attributes = NULL, array $extra = NULL)
	{
		$this->_attributes = $this->_attributes + (array) $attributes;
		$this->_config     = Arr::overwrite($this->_config, (array) $config);
		$this->_extra      = (array) $extra;

		$this->_name       = $name;
		$this->_label      = $label;
		$this->_id         = $id;
	}

	/**
	 * Get the value
	 * @param mixed $name
	 * @return mixed
	 */
	public function  __get($name)
	{
		if ($name === 'value')
			return $this->_value;
		elseif ($name === 'label')
			return $this->_label;
		elseif ($name === 'wrapper')
			return $this->_wrapper;
		elseif ($name === 'id')
			return $this->_id;
		elseif ($name === 'help_text')
			return $this->_help_text;
		elseif ($name === 'error')
			return $this->_error;
		elseif (isset($this->_config[$name]))
			return $this->_config[$name];
		else
		{
			throw new Kohana_Exception('The :property: property does not exist in the :class: class',
				array(':property:' => $name, ':class:' => get_class($this)));
		}
	}

	/**
	 * Set the value
	 * @param mixed $name
	 * @param mixed $value
	 */
	public function  __set($name, $value)
	{
		if ($name === 'value')
			$this->_set_value($value);
		elseif ($name === 'label')
			$this->_label = $value;
		elseif ($name === 'wrapper')
			$this->_wrapper = $value;
		elseif ($name === 'help_text')
			$this->_help_text = $value;
		elseif ($name === 'error')
			$this->_error = $value;
		elseif (isset($this->_config[$name]))
			$this->_config[$name] = $value;
		else
		{
			throw new Kohana_Exception('The :property: property does not exist in the :class: class',
				array(':property:' => $name, ':class:' => get_class($this)));
		}
	}

	/**
	 * String representation
	 * @return string
	 */
	public function  __toString()
	{
		return $this->render();
	}

	/**
	 * Render the field
	 * @return string
	 */
	public function form_field()
	{
		return $this->render();
	}

	/**
	 * Render the field
	 * @return string
	 */
	public function form_label(array $attributes = NULL)
	{
		return Form::label($this->_id, $this->_label, $attributes);
	}

	/**
	 * Display single field (and optionally label) in a wrapper
	 * @return string
	 */
	public function single_field()
	{
		return View::factory($this->_wrapper)
			->set('field', $this)
			->set('help_text', $this->_help_text)
			->set('error', $this->error);
	}

	/**
	 * Value formatted for the database
	 * @return string
	 */
	public function db_value()
	{
		return $this->_value;
	}

	/**
	 * Set the value to the default
	 */
	public function set_default()
	{
		$this->value = Arr::get($this->_extra, Kohana::$config->load('zcolumns.default.default_column.default'));
	}

	/**
	 * Set the value. Override to handle array and other value types
	 * @param mixed $value
	 */
	protected function _set_value($value)
	{
		$this->_value = $value;
	}
}