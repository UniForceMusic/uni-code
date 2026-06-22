<?php

declare(strict_types=1);

use PhpTui\Term\Terminal;
use PhpTui\Tui\DisplayBuilder;
use Src\UniCode;

require 'vendor/autoload.php';

$errorReporting = error_reporting(E_ERROR);

$terminal = Terminal::new();
$display = DisplayBuilder::default()->build();

error_reporting($errorReporting);

$uniCode = UniCode::fromPhpTui($terminal, $display);

$uniCode->execute(60);
