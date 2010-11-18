<?php defined('SYSPATH') or die('No direct script access.');

return array(
	// Default  configuration
	'default' => array(
		'default_column' => array(
			'default'    => 'column_default',
			'type'       => 'text',
			'multiline'  => false,
			'maxlength'  => false,
		),
		'varchar' => array(
			'maxlength'  => 'character_maximum_length',
		),
		'char'    => array(
			'maxlength'  => 'character_maximum_length',
		),
		'int'     => array(),
		'double'  => array(),
		'decimal' => array(),
		'enum'    => array(
			'type'       => 'enum',
			'options'    => 'options',
			'format'     => 'radio',
		),
		'set'      => array(
			'type'       => 'enum',
			'options'    => 'options',
			'multiple'   => true,
			'separator'  => ',',
		),
		'tinyint' => array(
			'type'       => 'boolean',
		),
		'tinytext'   => array(
			'type'       => 'text',
			'multiline'  => 3,
		),
		'text'       => array(
			'type'       => 'text',
			'multiline'  => 5,
		),
		'mediumtext' => array(
			'type'       => 'text',
			'multiline'  => 7,
		),
		'longtext'   => array(
			'type'       => 'text',
			'multiline'  => 12,
		),
		'date'       => array(
			'type'       => 'temporal',
			'format'     => 'Y-m-d',
			'year'       => true,
			'month'      => true,
			'day'        => true,
			'fields'     => ':month :day :year',
		),
		'datetime'   => array(
			'type'       => 'temporal',
			'format'     => 'Y-m-d H:i:s',
			'year'       => true,
			'month'      => true,
			'day'        => true,
			'hour'       => true,
			'minute'     => true,
			'meridien'   => true,
			'fields'     => ':month :day :year :hour :minute :second :meridien',
		),
		'timestamp'   => array(
			'type'       => 'temporal',
			'format'     => 'Y-m-d H:i:s',
			'year'       => true,
			'month'      => true,
			'day'        => true,
			'hour'       => true,
			'minute'     => true,
			'meridien'   => true,
			'fields'     => ':month :day :year :hour :minute :second :meridien',
		),
		'time'        => array(
			'type'       => 'temporal',
			'format'     => 'H:i:s',
			'hour'       => true,
			'minute'     => true,
			'meridien'   => true,
			'fields'     => ':hour :minute :meridien',
		),
	),
);