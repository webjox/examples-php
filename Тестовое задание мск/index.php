<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/entity/RevertClass.php';

use entity\RevertClass;

$str = "Привет! Давно не виделись.";
$result = RevertClass::revertCharacters($str);

echo "Искомая строка - " . "<strong>" . $str . "</strong>" . "<br>";
echo "Результат выполнения функции - " . "<strong>" . $result . "</strong>";
