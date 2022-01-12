<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Support\SAPOrderPost;

class SAPAllOrderPost implements ShouldQueue
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
    protected $order_id;

    public function __construct($database, $username, $password, $order_id)
    {
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->order_id = $order_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sap = new SAPOrderPost($this->database, $this->username, $this->password);

        $sap->pushOrder($this->order_id);
    }
}
