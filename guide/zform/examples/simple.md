# Simple Examples

## Model, form and retrieval
This is a simple example of a single ORM model, that has no relationships, but uses validation on the fields. 

### Model
	
	<?php defined('SYSPATH') or die('No direct access allowed.');

	class Model_Person extends ORM {

		public function rules()
		{
			return array(
				'first_name' => array(
					array('not_empty'),
					array('min_length', array(':value', 4)),
					array('max_length', array(':value', 32)),
					array('regex', array(':value', '/^[-\pL\pN_.]++$/uD')),
				),
				'last_name' => array(
					array('not_empty'),
					array('min_length', array(':value', 4)),
					array('max_length', array(':value', 32)),
					array('regex', array(':value', '/^[-\pL\pN_.]++$/uD')),
				),
				'email' => array(
					array('not_empty'),
					array('min_length', array(':value', 4)),
					array('max_length', array(':value', 127)),
					array('email'),
				),
			);
		}
	}

### Controller

	<?php defined('SYSPATH') or die('No direct access allowed.');

	class Controller_Example extends Controller {
		public function action_index()
		{
			$person   = ORM::factory('person');

			$template = View::factory('person/save');

			if ($this->request->method() == 'POST')
			{
				try
				{
					$person->get_form()->save();					
				}
				catch (ORM_Validation_Exception $ex)
				{
					echo join('<br />', $person->errors());
				}
			}

			$view->person = $person;
		}
	}

### View

	<?php defined('SYSPATH') or die('No direct access allowed.');

	echo Form::open();
	echo $person->generate_form();
	echo '<input type="submit" value="Submit" />';
	echo Form::close();
