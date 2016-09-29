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
        $to_summon = $this->choice('Which to summon?', $this->summonableCommands());

        $this->info("$to_summon ey? a fine choice!");
        $this->prepare();

        $this->line(''); // spacer
        $this->section('Summoning your selected console command...');

        $linux = str_replace('\\', '/', $to_summon);

        $explode = explode('/', $linux);

        $filename = array_pop($explode);
        $path = implode('/', $explode);

        $this->copy(
            $filename . '.php',
            "vendor/laravel/framework/src/$path/",
            'app/Console/Commands/'
        );

        $namespace = str_replace('/', '\\', $path);

        $this->patch(
            "app/Console/Commands/$filename.php",
            "namespace $namespace;",
            'namespace App\Console\Commands;'
        );

        // update namespace of summoned console command
        $this->patch(
            'app/Providers/ArtisanServiceProvider.php',
            "use $to_summon;",
            "use App\\Console\\Commands\\$filename;"
        );
    }

    public function section($string) {
        $this->getOutput()->writeln("<bg=yellow;options=bold>$string</>");
    }

    private function copy($filename, $vendor, $summon) {

        if (file_exists($summon . $filename)) {
            $this->info("$filename summoned.");

        } else {
            $this->comment("$filename is being summoned...");

            File::copy($vendor.$filename, $summon.$filename);
        }

    }

    private function summonableCommands() {
        $file = 'vendor/laravel/framework/src/Illuminate/Foundation/Providers/ArtisanServiceProvider.php';
        preg_match_all("/use (.*Command);/", file_get_contents($file), $array);
        return $array[1];
    }

    private function patch($filePath, $search, $replace) {

        $file = file_get_contents($filePath);

        if (Str::contains($file, $search)) {
            $this->comment("$filePath is being patched...");

            $file = str_replace(
                $search,
                $replace,
                $file
            );

            file_put_contents($filePath, $file);
        } else {
            $this->info("$filePath patched.");
        }

    }

    /**
     * Prepare environment for the relocation of a console command.
     */
    private function prepare() {
        $this->line(''); // spacer
        $this->section('Copying ServiceProviders from vendor...');

        $this->copy(
            'ConsoleSupportServiceProvider.php',
            'vendor/laravel/framework/src/Illuminate/Foundation/Providers/',
            'app/Providers/'
        );

        $this->copy(
            'ArtisanServiceProvider.php',
            'vendor/laravel/framework/src/Illuminate/Foundation/Providers/',
            'app/Providers/'
        );

        $summonPath = [
            'ConsoleSupportServiceProvider.php' => 'app/Providers/',
            'ArtisanServiceProvider.php'        => 'app/Providers/',
        ];

        $this->line(''); // spacer
        $this->section('Updating namespaces...');

        // update namespace of summoned ConsoleSupportServiceProvider
        $this->patch(
            $summonPath['ConsoleSupportServiceProvider.php'] . 'ConsoleSupportServiceProvider.php',
            'namespace Illuminate\Foundation\Providers;',
            'namespace App\Providers;'
        );

        // update namespace of summoned ArtisanServiceProvider
        $this->patch(
            $summonPath['ArtisanServiceProvider.php'] . 'ArtisanServiceProvider.php',
            'namespace Illuminate\Foundation\Providers;',
            'namespace App\Providers;'
        );

        $this->line(''); // spacer
        $this->section('Linking files...');

        // update ArtisanServiceProvider pointer in summoned ConsoleSupportServiceProvider
        $this->patch(
            $summonPath['ConsoleSupportServiceProvider.php'] . 'ConsoleSupportServiceProvider.php',
            'Illuminate\Foundation\Providers\ArtisanServiceProvider',
            'App\Providers\ArtisanServiceProvider'
        );

        // update ConsoleSupportServiceProvider pointer in config.app
        $this->patch(
            base_path('config/') . 'app.php',
            'Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class',
            'App\Providers\ConsoleSupportServiceProvider::class'
        );

        $this->line(''); // spacer
    }
}
