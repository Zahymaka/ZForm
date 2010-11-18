Introduction
============

I started using Kohana's ORM in 2.3 just after making the move from CodeIgniter. When I moved on to KO3 (Kohana v3), I tried a hand at Sprig. Unfortunately, I didn't like it. I disliked having to define column types for every field, so I went back to Kohana ORM.

One of the things that stood out from my experience with Sprig was the auto form generation. I also ran into situations where I needed to have multiple forms on one page. After working on ORMForm, I realized some things were clunky and decided to rewrite everything while keeping things as simple as possible.

Instead of filling the model definition with config items, very little is needed.

Installation
============
Extract to a module folder 'zform' under your modules directory and enable in your bootstrap.php file.

Usage
============
I have tried to add as many hints on usage to the class documentation which should show up if you have the userguide module enabled.

Examples
===========
Controller
----------
<pre>
&lt;?php
class Controller_Example extends Controller
{
	public function action_index()
	{
		$person   = ORM::factory('person');

		$template = View::factory('person/save');

		$person->setup_form();

		if (Request::$method === 'POST')
		{
			$person->get_form();
			if (!$person->check())
				echo join('&lt;br /&gt;', $person->errors(NULL));
			else
				$person->save();
		}

		$view->person = $person;
	}
}
</pre>

View
------
<pre>&lt;?php

echo Form::open().$person->generate_form().Form::close();</pre>

Model (Partial)
--------------
<pre>&lt;?php
/// Columns
/// id - Primary Key
/// first_name varchar
/// last_name  varchar
/// gender     tinyint(1)
/// email      varchar
/// address    text
/// company_id int foreign_key
/// spouse_id int foreign_key
/// active     tinyint(1)
class Model_Person extends ORM_Form
{
	const GENDER_MALE   = 0;
	const GENDER_FEMALE = 1;

	protected $_rules = array(
		'first_name' => array(
			'not_empty' => NULL,
		),
		'last_name' => array(
			'not_empty' => NULL,
		),
		'email' => array(
			'not_empty' => NULL,
			'email' => NULL,
		),
	);

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

	protected $_z_field_config = array(
		'gender' => array(
			'type' => 'enum',
			'options' => array(
				self::GENDER_MALE   => 'Male',
				self::GENDER_FEMALE => 'Female',
			),
		),
	);

}</pre>


Suggestions
============
Shoot me an email to azuka [at] zatechcorp.com.