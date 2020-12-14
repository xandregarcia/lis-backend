<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ModelsIncrement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'increments:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset auto_increment for all models';

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
     * @return mixed
     */
    public function handle()
    {

      $excepts = [
        'places'
      ];

      $tables = DB::select('SHOW tables');

      $obj_table = "Tables_in_".env('DB_DATABASE');
      
      foreach ($tables as $table) {
        
        if (in_array($table->{"$obj_table"},$excepts)) continue;
        DB::statement('ALTER TABLE '.$table->{"$obj_table"}.' AUTO_INCREMENT = 1');
        $this->info('Reset table increment: '.$table->{"$obj_table"});	
        
      }

    }
}
