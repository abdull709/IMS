<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class DiagnosticsController extends Controller
{
    public function errors(): void
    {
        $this->requireAdmin();

        header('Content-Type: text/plain; charset=UTF-8');
        header('Cache-Control: no-store, private');

        $logFile = STORAGE_PATH . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'app.log';
        if (!is_file($logFile) || !is_readable($logFile)) {
            echo "No readable application log was found.\n";
            return;
        }

        $lines = file($logFile, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            echo "The application log could not be read.\n";
            return;
        }

        echo implode(PHP_EOL, array_slice($lines, -160)) . PHP_EOL;
    }
}
