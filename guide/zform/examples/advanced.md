# Advanced

The model below has most of the options you're likely to use with ZForm.

	<?php defined('SYSPATH') or die('No direct access allowed.');
	/// Columns
	/// id - Primary Key
	/// first_name varchar
	/// last_name  varchar
	/// gender     tinyint(1)
	/// email      varchar
	/// address    text
	/// city       varchar
	/// state      varchar
	/// zip        varchar
	/// phone      varchar
	/// status     varchar
	/// company_id int foreign_key
	/// spouse_id  int foreign_key
	/// active     tinyint(1)

	class Model_Person extends ZForm {
		const GENDER_MALE   = 0;
		const GENDER_FEMALE = 1;

		protected $_belongs_to = array(
			'spouse' => array(
				'zform_pk'    => array(),
				'zform_label' => array('CONCAT_WS(\', \', "last_name", "first_name")', 'name'), // Really arcane way of  doing things, but you get the idea
																								// Leave this blank and primary_key and primary_val will be used instead
				'zform_order' => array(),
			),
		);

		protected $_z_attributes = array(
			'address' => array(
				'cols' => 200,
				'rows' => 5,
			),
		);

		protected $_z_labels = array(
			'spouse_id' => 'Partner',
		);

		protected $_z_exclude = array('phone',);

		protected $_z_field_config = array(
			'gender' => array(
				'type' => 'enum',
				'options' => array(
					self::GENDER_MALE   => 'Male',
					self::GENDER_FEMALE => 'Female',
				),
			),
		);

		public function rules()
		{
			return array(
				'first_name' => array(
					array('not_empty'),
				),
				'last_name' => array(
					array('not_empty'),
				),
				'email' => array(
					array('not_empty'),
					array('email'),
				),
			);
		}

		public function initialize()
		{
			$this->_z_field_config['state']['type'] = 'enum';
		}

		public function finalize()
		{
			$this->_z_fields['state']->options = array('AL' => 'Alabama', 'AK' => 'Arkansas',...);
		}

	}

## Labels
Your labels are formatted using a call to [Inflector::humanize()] and [ucfirst](http://php.net/ucfirst), so `first_name` becomes `First Name`. You can override a label by setting the appropriate item in 	[$_z_labels](../api/ZForm#property:_z_labels).

## Relationships

ZForm will automatically create fields for `$_belongs_to` relationships except that column has been excluded. The label name is derived from the alias, so the label for the `company_id` column above will be `Company`. For these, an `enum` field type is used. The columns queried are from ORM's `$_primary_key` and `$_primary_val` (typically `id` and `name`), but you can specify yours by adding `zform_pk` (option value), `zform_label` (option text), `zform_order` (array of sort options).

Even better, build yours in [initialize()](../api/ZForm#initialize):

	public function initialize()
	{
		$this->_z_fields[<foreign key>] = new ZForm_Field_Enum(
			$this->field_name(<foreign key>),
			$this->field_id(<foreign key>),
			...

A simpler way of doing the above is:

	public function initialize()
	{
		$this->_z_field_config[<foreign key>]['type'] = 'enum';
			...

	public function finalize()
	{
		$this->_z_fields[<foreign key>]->options = <options>;
			...

## Field exclusion

Some fields are excluded by default -- your primary key, created and updated columns.

To exclude a field, you can call the [exclude()](../api/ZForm#exclude) function, either in [initialize()](../api/ZForm#initialize) or directly on the object, but it has to be called before [setup_form()](../api/ZForm#setup_form) You can also use the [$_z_exclude](../api/ZForm#property:_z_exclude) property in ZForm (see above).

## Field types

There are four field types that come with ZForm, although you are free to extend [ZForm_Field] and create yours.

### Text
[ZForm_Field_Text] handles both text and textarea fields. Text fields automatically get a maxlength added for them

### Boolean
[ZForm_Field_Boolean] is for checkboxes.

### Temporal
[ZForm_Field_Temporal] handles fields that have to do with date and time. May not be reliable with PHP versions < 5.3.0.

### Enum
[ZForm_Field_Enum] handles pretty much everything else - selects, multiselects, radio, multiple checkboxes and a special [multichoice](../api/Zform#multichoice) field, which lets you repeat select options e.g.:

	United States
	Canada
	Mexico
	---
	Afghanistan
	...
	Canada
	...
	Mexico
	...
	United States

## Conclusion

For other help, please browse the api. You may also explore the contents of the **zdata** and **zcolumns** config files.

