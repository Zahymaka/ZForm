# Basic Usage

For the most part ZForm stays out of the way of ORM, so usage should be the same.

	ORM::factory('member')

should work just as well for everything you want.

## Generating a form

On your object, call [ZForm::generate_form] on your object to output all visible form fields in the order they appear in the database.

	$member = ORM::factory();
	echo $member->generate_form();

For finer control, you can call the method with either an array of column names:

	...
	echo $member->generate_form(array('first_name', 'last_name', 'email'));

or with as many arguments as needed.

	...
	echo $member->generate_form('first_name', 'last_name', 'email');

to control the order the items appear in.

## Retrieving form values

[get_form](../api/ZForm#get_form) will retrieve all visible form fields from `$_POST` if called with no arguments.

	$member->get_form();

You can also retrieve the fields from another array (not $_POST) by calling

	$member->get_form($array);

Similar to *get_form()*, you can retrieve a custom list of values by calling

	$member->get_form(NULL, array('first_name', 'last_name', 'email'));

or

	$member->get_form(NULL, 'first_name', 'last_name', 'email');

after which you can perform your [validation](validation) and checking for errors.

## The initialize() and finalize() functions

[initialize](../api/ZForm#initialize) just as it sounds, is a function called just before creating the form fields. It's the last place you can 
