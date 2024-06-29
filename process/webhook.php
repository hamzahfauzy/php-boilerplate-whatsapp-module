<?php

use Core\Log;

$data = file_get_contents('php://input');

Log::write($data);