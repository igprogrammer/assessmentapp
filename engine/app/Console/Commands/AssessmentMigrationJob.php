<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class AssessmentMigrationJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assessment:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        try {
            $request = Request::create('migrate', 'GET');
            $responseBody = app()->handle($request);
            $response = $responseBody->getContent();
            $this->info($response);
        }catch (\Exception $exception){
            $message = "Error: ".$exception->getMessage().' on line: '.$exception->getLine().' of file : '.$exception->getFile();
            dd($message);
        }
    }
}
