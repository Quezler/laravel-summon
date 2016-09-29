<?php

namespace Quezler\Laravel_Summon\Console;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;

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
        $table = new Table($this->getOutput());
        $table
            ->setHeaders([
                new TableCell('ConsoleSupportServiceProvider', ['colspan' => 2])
            ])
            ->setRows(array(
                ['app/Providers/',
                    wrap('green', 'ConsoleSupportServiceProvider.php')],
                ['vendor/laravel/framework/src/Illuminate/Foundation/Providers/',
                    wrap('yellow', 'ConsoleSupportServiceProvider.php')],
                new TableSeparator(),
                [
                    new TableCell(wrap('green', 'File coppied'), ['colspan' => 2])
                ]
            ))
        ;
        $table->render();

        return;
        // possible locations of ConsoleSupportServiceProvider
        $console_provider_summon = base_path('app/Providers/')            . 'ConsoleSupportServiceProvider.php';
        $console_provider_vendor = base_path('vendor/laravel/framework/') . 'src/Illuminate/Foundation/Providers/ConsoleSupportServiceProvider.php';

        $this->replicate(
            $console_provider_summon,
            $console_provider_vendor
        );

        // possible locations of ArtisanServiceProvider
        $artisan_provider_summon = base_path('app/Providers/')            . 'ArtisanServiceProvider.php';
        $artisan_provider_vendor = base_path('vendor/laravel/framework/') . 'src/Illuminate/Foundation/Providers/ArtisanServiceProvider.php';

        $this->replicate(
            $artisan_provider_summon,
            $artisan_provider_vendor
        );

        $this->line(''); // spacer

        // update namespace of summoned ConsoleSupportServiceProvider
        $this->patch(
            $console_provider_summon,
            'namespace Illuminate\Foundation\Providers;',
            'namespace App\Providers;'
        );

        // update namespace of summoned ArtisanServiceProvider
        $this->patch(
            $artisan_provider_summon,
            'namespace Illuminate\Foundation\Providers;',
            'namespace App\Providers;'
        );

        $this->line(''); // spacer

        // update ArtisanServiceProvider pointer in summoned ConsoleSupportServiceProvider
        $this->patch(
            $console_provider_summon,
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
        $this->question("if you see this, you're good to go! (っ◕‿◕)っ");
        $this->line(''); // spacer

//        return $this->summonableCommands($artisan_provider_summon);
    }

    private function summonableCommands($file) {
        preg_match_all("/use (.*Command);/", file_get_contents($file), $array);
        $foo = $this->choice('Which to summon?', $array[1]);
        dd($foo);
    }

    private function replicate($to, $fro) {
        if (file_exists($to)) {
            $this->info(   sprintf('Found        %s', $to));
        } else {
            $this->comment(sprintf('Not found    %s', $to));
            $this->comment(sprintf('Copying from %s', $fro));

            File::copy($fro, $to);

            $this->replicate($to, $fro);
        }

    }

    private function patch($filePath, $search, $replace) {
        $file = file_get_contents($filePath);

        if (Str::contains($file, $replace)) {
            $this->info("$filePath contains [$replace]");
        } else {
            $this->comment("$filePath contains [$search]");
            $this->comment("Updating file...");

            $file = str_replace(
                $search,
                $replace,
                $file
            );

            file_put_contents($filePath, $file);
            $this->patch($filePath, $search, $replace);
        }

    }

}


function wrap($color, $string) {
    $resolve = [
        'green' => 'info',
        'yellow' => 'comment',
    ];

    return sprintf('<%s>%s</%s>',
        $resolve[$color],
        $string,
        $resolve[$color]
    );
}
