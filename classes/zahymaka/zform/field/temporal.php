<?php defined('SYSPATH') or die('No direct script access.');

/**
 * ZForm: a date and time field implementation. Displays select dropdowns
 *
 * @package    ZForm
 * @category   Field
 * @author     Azuka Okuleye
 * @copyright  (c) 2011 Azuka Okuleye
 * @license    http://zahymaka.com/license.html
 * @todo       Fix the parsing. This is the messiest class in this suite
 */
class Zahymaka_ZForm_Field_Temporal extends ZForm_Field {

	protected $_config = array(
		'format'     => '',
		'year'       => false,
		'month'      => false,
		'day'        => false,
		'hour'       => false,
		'minute'     => false,
		'second'     => false,
		'meridien'   => false,
		'fields'     => '',
	);

	/**
	 * The current stored date
	 * @var array
	 */
	protected $_date = array(
		'year'       => 0,
		'month'      => 0,
		'day'        => 0,
		'hour'       => 0,
		'minute'     => 0,
		'second'     => 0,
	);

	protected $_matches = array();

	public function  __construct($name, $id, $label, array $config = NULL, array $attributes = NULL, array $extra = NULL)
	{
		parent::__construct($name, $id, $label, $config, $attributes, $extra);

		$this->_date = array(
			'year'       => date('Y'),
			'month'      => date('n'),
			'day'        => date('j'),
			'hour'       => date('h'),
			'minute'     => date('i'),
			'second'     => 0, //date('s'),
			'meridien'   => date('A'),
		);
	}

	/**
	 * Year field
	 * @return string
	 */
	protected function _field_year()
	{
		if (!$this->_config['year'])
			return '';

		return Form::select(
			$this->_name.'[year]',
			Kohana::$config->load('zdata.date.years'),
			$this->_date['year'],
			(array) Arr::get($this->_attributes, 'year') +
			array(
				'id' => $this->_matches[0] == 'year' ? $this->_id : $this->_id . '_year'
			)
		);
	}

	/**
	 * Month field
	 * @return string
	 */
	protected function _field_month()
	{
		if (!$this->_config['month'])
			return '';

		return Form::select(
			$this->_name.'[month]',
			Kohana::$config->load('zdata.date.months'),
			$this->_date['month'],
			(array) Arr::get($this->_attributes, 'month') +
			array(
				'id' => $this->_matches[0] == 'month' ? $this->_id : $this->_id . '_month'
			)
		);
	}

	/**
	 * Day field
	 * @return string
	 */
	protected function _field_day()
	{
		if (!$this->_config['day'])
			return '';

		return Form::select(
			$this->_name.'[day]',
			Kohana::$config->load('zdata.date.days'),
			$this->_date['day'],
			(array) Arr::get($this->_attributes, 'day') +
			array(
				'id' => $this->_matches[0] == 'day' ? $this->_id : $this->_id . '_day'
			)
		);
	}

	/**
	 * Hour field
	 * @return string
	 */
	protected function _field_hour()
	{
		if (!$this->_config['hour'])
			return '';

		return Form::select(
			$this->_name.'[hour]',
			Kohana::$config->load('zdata.date.hours'),
			$this->_date['hour'],
			(array) Arr::get($this->_attributes, 'hour') +
			array(
				'id' => $this->_matches[0] == 'hour' ? $this->_id : $this->_id . '_hour'
			)
		);
	}

	/**
	 * Minute field
	 * @return string
	 */
	protected function _field_minute()
	{
		if (!$this->_config['minute'])
			return '';

		return Form::select(
			$this->_name.'[minute]',
			Kohana::$config->load('zdata.date.minutes'),
			$this->_date['minute'],
			(array) Arr::get($this->_attributes, 'minute') +
			array(
				'id' => $this->_matches[0] == 'minute' ? $this->_id : $this->_id . '_minute',
			)
		);
	}

	/**
	 * Second field
	 * @return string
	 */
	protected function _field_second()
	{
		if (!$this->_config['second'])
			return '';

		return Form::select(
			$this->_name.'[second]',
			Kohana::$config->load('zdata.date.seconds'),
			$this->_date['second'],
			(array) Arr::get($this->_attributes, 'second') +
			array(
				'id' => $this->_matches[0] == 'second' ? $this->_id : $this->_id . '_second'
			)
		);
	}

	/**
	 * Meridien (AM/PM) field
	 * @return string
	 */
	protected function _field_meridien()
	{
		if (!$this->_config['meridien'])
			return '';

		return Form::select(
			$this->_name.'[meridien]',
			Kohana::$config->load('zdata.date.meridiens'),
			$this->_date['meridien'],
			(array) Arr::get($this->_attributes, 'meridien') +
			array(
				'id' => $this->_matches[0] == 'meridien' ? $this->_id : $this->_id . '_meridien'
			)
		);
	}

	public function db_value()
	{
		return date($this->_config['format'], $this->value);
	}

	public function render()
	{
		preg_match_all('/\:([\w]+)\b/i', $this->_config['fields'], $this->_matches);

		$this->_matches = $this->_matches[1];

		$values = array(
			':year'     => $this->_field_year(),
			':month'    => $this->_field_month(),
			':day'      => $this->_field_day(),
			':hour'     => $this->_field_hour(),
			':minute'   => $this->_field_minute(),
			':second'   => $this->_field_second(),
			':meridien' => $this->_field_meridien(),
		);

		return __($this->_config['fields'], $values);
	}

	/**
	 * Parse settings and return date
	 * @return int
	 */
	protected function _value_from_date()
	{
		$meridien    = $this->_date['meridien'];

		$this->_date = array_map('intval', $this->_date);

		$this->_date['meridien'] = $meridien;

		$hour = $this->_date['hour'];

		if ($this->_date['meridien'] == 'PM')
			$hour += 12;

		if ($this->_date['hour'] === 12)
		{
			if ($this->_date['meridien'] == 'AM')
			{
				$hour = 0;
			}
			else
			{
				$hour = 12;
			}
		}

		return mktime($hour, $this->_date['minute'], $this->_date['second'], $this->_date['month'], $this->_date['day'], $this->_date['year']);
	}

	protected function  _set_value($value)
	{
		if (!$value)
			return;
		// Coming in from $_POST
		if (is_array($value))
		{
			$this->_date = Arr::overwrite ($this->_date, $value);
		}
		// No further processing needed
		elseif (is_numeric($value))
		{
			// do nothing
		}
		// PHP 5.3 and above are more reliable
		elseif (method_exists('DateTime', 'createFromFormat'))
		{
			$date = DateTime::createFromFormat($this->_config['format'], $value);

			if ($date instanceof DateTime)
				$value = $date->format('U');
			else
				$value = 0;
		}
		// Awkwardly parse MySQL TIME fields
		elseif (preg_match("/([0-9]{2}):([0-9]{2}):([0-9]{2})/", $value))
		{
			$value = strtotime(date('Y-m-d ').$value);
		}
		// All others
		else
		{
			$value = strtotime($value);
		}

		if (is_numeric($value))
		{
			$this->_date = array(
				'year'       => date('Y', $value),
				'month'      => date('n', $value),
				'day'        => date('j', $value),
				'hour'       => date('h', $value),
				'minute'     => date('i', $value),
				'second'     => date('s', $value),
				'meridien'   => date('A', $value),
			);
		}

		$value  = $this->_value_from_date();

		parent::_set_value($value);
	}
}