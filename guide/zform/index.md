# ZForm

When you use Kohana's ORM for a while, you find yourself using the same patterns over and over again.
This isn't strange, but if you can do your validation in ORM, why not create and retrieve your forms using in ORM?

## Installation

Extract to a module folder 'zform' under your modules directory and enable in your bootstrap.php file.

The database and orm modules are required.

	Kohana::modules(array(
		...
		'database' => MODPATH.'database',
		'orm' => MODPATH.'orm',
		'zform' => MODPATH.'zform',
		...
	));

You can now create your [models](models) and [use ZForm](using).
