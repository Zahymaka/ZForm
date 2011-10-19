<?php defined('SYSPATH') or die('No direct script access.');

/**
 * ZForm: a select/multiselect implementation. Displays a select, group of radios or group of checkboxes
 *
 * @package    ZForm
 * @category   Field
 * @author     Azuka Okuleye
 * @copyright  (c) 2011 Azuka Okuleye
 * @license    http://zahymaka.com/license.html
 */
class Zahymaka_ZForm_Field_Enum extends ZForm_Field
{
	protected $_config = array(
		'multiple'  => false,
		'multichoice' => false,
		'options'   => array(),
		'format'    => 'select',
		'separator' => '',
	);

	protected function _set_value($value)
	{
		if (!is_array($value) && $this->_config['multiple'])
		{
			$value = explode($this->_config['separator'], $value);
		}

		parent::_set_value($value);
	}

	public function  db_value()
	{
		if (is_array($this->_value))
			return join($this->_config['separator'], $this->_value);

		return parent::db_value();
	}

	public function render()
	{
		$render = '';

		if (!is_array($this->_config['options']))
		{
			$this->_config['options'] = array_combine($this->_extra[$this->_config['options']], $this->_extra[$this->_config['options']]);
		}

		switch ($this->_config['format'])
		{
			case 'select':
				// Multiple
				if ($this->_config['multiple'])
				{
					$render = Form::select(
						$this->_name . '[]',
						$this->_config['options'],
						(array) $this->_value,
						$this->_attributes +
						array(
							'id' => $this->_id,
						)
					);
					break;
				}

				// Multichoice
				if ($this->_config['multichoice'])
				{
					$render = ZForm::multichoice(
						$this->_name,
						$this->_config['options'],
						$this->_value,
						$this->_attributes +
						array(
							'id' => $this->_id,
						)
					);
					break;
				}

				//  Single
				$render = Form::select(
					$this->_name,
					$this->_config['options'],
					$this->_value,
					$this->_attributes +
					array(
						'id' => $this->_id,
					)
				);
				break;
			default :

				$first = true;
				$i     = 0;

				foreach ($this->_config['options'] as $option => $label)
				{
					$render .= '<span class="enum-item">';

					$id      = $first ? $this->_id : $this->_id . '_' . $i++;

					// Multiple (checkboxes)
					if ($this->_config['multiple'])
					{
						$render .= Form::checkbox(
							$this->_name.'['.$option.']',
							$option,
							in_array($option, $this->_value),
							$this->_attributes +
							array(
								'id' => $id,
							)
						);
					}
					// Single value (radio)
					else
					{
						$render .= Form::radio(
							$this->_name,
							$option,
							// Always choose the first if nothing is set
							$this->_value == $option OR ($first AND !$this->_value),
							$this->_attributes +
							array(
								'id' => $id,
							)
						);
					}

					$render .= Form::label($id, $label);

					$render .= '</span>';

					$first = false;
				}


				$render = '<div class="enum">' . $render . '</div>';

		}

		return $render;
	}
}