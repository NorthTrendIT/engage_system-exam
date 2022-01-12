<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Support\SAPCustomerPromotion;

class SAPAllCustomerPromotionPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $database;
    protected $username;
    protected $password;
    protected $promotion_id;

    public function __construct($database, $username, $password, $promotion_id)
    {
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->promotion_id = $promotion_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sap = new SAPCustomerPromotion($this->database, $this->username, $this->password);
        $sap->createOrder($this->promotion_id);
    }
}
