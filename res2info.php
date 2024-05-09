<?php

$xx = $_GET['para'];

exec('python sequence.getinfo.py', $output, $return_var);

foreach ($output as $value) {
	echo $value;
}

?>