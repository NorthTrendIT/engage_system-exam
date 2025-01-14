<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Support\SAPTestAPI;

class TestApiHostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $url;
    protected $database;
    protected $username;
    protected $password;

    public function __construct($url, $database, $username, $password)
    {
        $this->url = $url;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $testAPI = new SAPTestAPI($this->database, $this->username, $this->password, $this->url);
        $testAPI->checkLogin(true);

    }
}
