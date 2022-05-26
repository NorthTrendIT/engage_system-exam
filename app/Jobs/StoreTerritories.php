<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Territory;

class StoreTerritories implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(!empty($this->data)){

            foreach ($this->data as $value) {

                $insert = array(
                            'territory_id' => @$value['TerritoryID'],
                            'parent' => @$value['Parent'],
                            'description' => @$value['Description'],
                            'location_index' => @$value['LocationIndex'],
                            'is_active' => @$value['Inactive'] == "tYES" ? false : true,
                            'last_sync_at' => current_datetime(),
                            //'response' => json_encode($value),
                        );

                Territory::updateOrCreate(
                                [
                                    'territory_id' => @$value['TerritoryID'],
                                ],
                                $insert
                            );
            }

        }
    }
}
