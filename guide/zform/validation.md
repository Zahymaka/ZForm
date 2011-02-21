# Validation

You can rely on [ORM validation](../orm/validation), or if you prefer to retrieve a list of errors without triggering a validation exception, you can call the [ZForm::errors] function

	$errors = $member->errors();
	
which accepts a callback, language and message directory (`models` by default) in that order.