<?php defined('SYSPATH') or die('No direct script access.');

$config['date']['months']     = array_combine(range(1, 12), array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'));
$config['date']['days']       = array_combine(range(1, 31), range(1, 31));
$config['date']['years']      = array_combine(range(date('Y') - 60, date('Y')+5), range(date('Y') - 60, date('Y')+5));
$config['date']['hours']      = array_combine(range(1, 12), ZForm::zerofill(range(1, 12)));
$config['date']['minutes']    = array_combine(range(0, 59), ZForm::zerofill(range(0, 59)));
$config['date']['seconds']    = array_combine(range(0, 59), ZForm::zerofill(range(0, 59)));
$config['date']['meridiens']  = array('AM'=>'AM', 'PM'=>'PM');

return $config;