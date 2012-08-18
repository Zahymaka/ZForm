<?php defined('SYSPATH') or die('No direct script access.');

/**
 * ZForm: a text field implementation. Displays a textbox
 *
 * @package    ZForm
 * @category   Field
 * @author     Azuka Okuleye
 * @copyright  (c) 2011 Azuka Okuleye
 * @license    http://zahymaka.com/license.html
 */
class Zahymaka_ZForm_Field_Text extends ZForm_Field {

	protected $_config = array(
		'multiline'  => false,
		'maxlength' => false,
	);

	public function render()
	{
		if ($this->_config['maxlength'] AND !isset($this->_attributes['maxlength']))
		{
			$this->_attributes['maxlength'] = $this->_extra[$this->_config['maxlength']];
		}

		if ($this->_config['multiline'])
		{
			return Form::textarea(
				$this->_name,
				$this->_value,
				$this->_attributes +
				array(
					'id' => $this->_id,
					'rows' => $this->_config['multiline'],
					'cols' => 120,
					'class' => 'xxlarge',
				)
			);
		}

		return Form::input(
				$this->_name,
				$this->_value,
				$this->_attributes +
				array(
					'id' => $this->_id,
					'size' => 40,
				)
			);
	}
}