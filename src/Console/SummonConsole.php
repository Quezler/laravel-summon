<?php

namespace Quezler\Laravel_Summon\Console;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

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
        // possible locations of ArtisanServiceProvider
        $provider_summon = base_path('app/Providers/')            . 'ArtisanServiceProvider.php';
        $provider_vendor = base_path('vendor/laravel/framework/') . 'src/Illuminate/Foundation/Providers/ArtisanServiceProvider.php';

        if (file_exists($provider_summon)) {
            $this->info('ArtisanServiceProvider found in app/Providers.');
        } else {
            $this->comment('ArtisanServiceProvider not found in app/Providers.');

            File::copy($provider_vendor, $provider_summon);
        }
    }
}
