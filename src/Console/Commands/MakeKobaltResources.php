<?php

namespace Hoppermagic\Kobalt\Console\Commands;

use Illuminate\Console\Command;

class MakeKobaltResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:ko-resources 
                            {name : The basename of the resource eg Project}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold Kobalt resources, Admin Controller, Model, Form, Request';



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
        $this->call('make:ko-controller', [
            'name' => $this->argument('name')
        ]);

        $this->call('make:ko-model', [
            'name' => $this->argument('name')
        ]);

        $this->call('make:ko-form', [
            'name' => $this->argument('name')
        ]);

        $this->call('make:ko-request', [
            'name' => $this->argument('name')
        ]);
    }
}
