<?php

namespace Quezler\Laravel_Summon\Console;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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
        $console_provider_summon = base_path('app/Providers/')            . 'ConsoleSupportServiceProvider.php';
        $console_provider_vendor = base_path('vendor/laravel/framework/') . 'src/Illuminate/Foundation/Providers/ConsoleSupportServiceProvider.php';

        if (file_exists($console_provider_summon)) {
            $this->info('ConsoleSupportServiceProvider found in app/Providers.');
        } else {
            $this->comment('ConsoleSupportServiceProvider not found in app/Providers.');

            File::copy($console_provider_vendor, $console_provider_summon);
        }

        // possible locations of ArtisanServiceProvider
        $artisan_provider_summon = base_path('app/Providers/')            . 'ArtisanServiceProvider.php';
        $artisan_provider_vendor = base_path('vendor/laravel/framework/') . 'src/Illuminate/Foundation/Providers/ArtisanServiceProvider.php';

        if (file_exists($artisan_provider_summon)) {
            $this->info('ArtisanServiceProvider found in app/Providers.');
        } else {
            $this->comment('ArtisanServiceProvider not found in app/Providers.');

            File::copy($artisan_provider_vendor, $artisan_provider_summon);
        }

        $this->line(''); // spacer

        // update namespace of summoned ConsoleSupportServiceProvider
        $file = file_get_contents($console_provider_summon);
        if (Str::contains($file, 'namespace App\Providers;')) {
            $this->info('ConsoleSupportServiceProvider namespace has been updated.');
        } else {
            $this->comment('ConsoleSupportServiceProvider namespace has not yet been updated.');
            $file = str_replace(
                'namespace Illuminate\Foundation\Providers;',
                'namespace App\Providers;',
                $file
            );
            file_put_contents($console_provider_summon, $file);
        }

        // update namespace of summoned ArtisanServiceProvider
        $file = file_get_contents($artisan_provider_summon);
        if (Str::contains($file, 'namespace App\Providers;')) {
            $this->info('ArtisanServiceProvider namespace has been updated.');
        } else {
            $this->comment('ArtisanServiceProvider namespace has not yet been updated.');
            $file = str_replace(
                'namespace Illuminate\Foundation\Providers;',
                'namespace App\Providers;',
                $file
            );
            file_put_contents($artisan_provider_summon, $file);
        }

        $this->line(''); // spacer

        // update ArtisanServiceProvider pointer in summoned ConsoleSupportServiceProvider
        $file = file_get_contents($console_provider_summon);
        if (Str::contains($file, 'App\Providers\ArtisanServiceProvider')) {
            $this->info('ConsoleSupportServiceProvider\'s ArtisanServiceProvider pointer has been updated.');
        } else {
            $this->comment('ConsoleSupportServiceProvider\'s ArtisanServiceProvider pointer has not yet been updated.');
            $file = str_replace(
                'Illuminate\Foundation\Providers\ArtisanServiceProvider',
                'App\Providers\ArtisanServiceProvider',
                $file
            );
            file_put_contents($console_provider_summon, $file);
        }

        // update ConsoleSupportServiceProvider pointer in config.app
        $config_app = base_path('config/') . 'app.php';
        $file = file_get_contents($config_app);
        if (Str::contains($file, 'App\Providers\ConsoleSupportServiceProvider::class')) {
            $this->info('config.app\'s ConsoleSupportServiceProvider pointer has been updated.');
        } else {
            $this->comment('config.app\'s ConsoleSupportServiceProvider pointer has not yet been updated.');
            $file = str_replace(
                'Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class',
                'App\Providers\ConsoleSupportServiceProvider::class',
                $file
            );
            file_put_contents($config_app, $file);
        }

        $this->line(''); // spacer

    }
}
