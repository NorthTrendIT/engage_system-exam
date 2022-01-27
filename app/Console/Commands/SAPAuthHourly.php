<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SapConnection;
use App\Models\SapCompanySession;
use App\Support\SAPAuthentication;

class SAPAuthHourly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sapauth:hourly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is used for login into sap and save records into database.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sap_connections = SapConnection::all();

        foreach($sap_connections as $value){
            $sap_auth = new SAPAuthentication($value->db_name, $value->user_name , $value->password);
            $sap_auth->forceLogin();
        }

        echo "SAP Authentication records updated successfully";
        return 0;
    }
}
