<?php

define('ENV', 'DEV'); // DEV | PROD

// do not change anything below if you are unsure what it does
if (ENV == 'DEV') {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
} else {
    error_reporting(E_ERROR);
    ini_set('display_errors', false);
}
