<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function getPhpInfo(): View
    {
        // Capture phpinfo() output
        ob_start();
        phpinfo(INFO_GENERAL | INFO_CREDITS | INFO_CONFIGURATION | INFO_MODULES);
        $phpInfo = ob_get_clean();

        // Extract body contents to not break the template
        $phpInfo = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $phpInfo);

        return view('backpack::metrics.phpinfo', [
            'phpInfo' => $phpInfo,
        ]);
    }

    public function getPhpFpmStatus(): View
    {
        $fpmStatusData = fpm_get_status();

        return view('backpack::metrics.php-fpm', [
            'data' => $fpmStatusData,
        ]);
    }

    public function getMysqlVars(): View
    {
        $vars = DB::select('SHOW VARIABLES');
        $items = collect($vars)
            ->map('get_object_vars')
            ->map('array_values')
            ->all();

        return view('backpack::metrics.mysql', [
            'items' => $items,
        ]);
    }
}
