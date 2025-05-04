<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Response;

class DataExportController extends Controller
{
    public function download()
    {
        $tables = DB::select('SHOW TABLES');
        $databaseKey = 'Tables_in_' . env('DB_DATABASE');

        $exportData = [];

        foreach ($tables as $table) {
            $tableName = $table->$databaseKey;
            $exportData[$tableName] = DB::table($tableName)->get();
        }

        $jsonContent = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return Response::make($jsonContent, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="all_database_data.json"',
        ]);
    }
}
