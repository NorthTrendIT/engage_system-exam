<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Imports\CustomerSalesSpecialistAssignImport;
use Excel;

class ImportCustomerSalesSpecialistAssign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $filename;
    protected $log_id;

    public function __construct($filename, $log_id = null)
    {
        $this->filename = $filename;
        $this->log_id = $log_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{

            if(get_valid_file_url('/sitebucket/files/', $this->filename)){

                $file = public_path() . '/sitebucket/files/'. $this->filename;

                Excel::import(new CustomerSalesSpecialistAssignImport,$file);

                unlink($file);

                add_sap_log([
                                'status' => "completed",
                            ], $this->log_id);
            }else{
                add_sap_log([
                            'status' => "error",
                            'error_data' => "This file ".$this->filename. " not found",
                        ], $this->log_id);
            }

        }catch (\Exception $e) {

            add_sap_log([
                            'status' => "error",
                            'error_data' => $e->getMessage(),
                        ], $this->log_id);

        }
    }
}
