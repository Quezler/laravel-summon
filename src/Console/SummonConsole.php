<?php

namespace Quezler\Laravel_Summon\Console;


use Illuminate\Console\Command;

class SummonConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'summon:console';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Summon a genuine Laravel console command.';

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
        //
    }
}
