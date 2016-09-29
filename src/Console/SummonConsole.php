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
        $this->question("if you see this, you're good to go! (っ◕‿◕)っ");
        $this->line(''); // spacer

//        return $this->summonableCommands($artisan_provider_summon);
    }

    private function copy($filename, $vendor, $summon) {
        $table = new Table($this->getOutput());

        $rows = [];

        $rows[] = [$vendor, wrap('green', $filename)];

        if (file_exists($summon . $filename)) {
            $rows[] = [$summon, wrap('green', $filename)];

            $rows[] = new TableSeparator();
            $rows[] = [new TableCell(wrap('yellow', 'No action required'), ['colspan' => 2])];
        } else {
            $rows[] = [$summon, wrap('yellow', $filename)];

            File::copy($vendor.$filename, $summon.$filename);

            $rows[] = new TableSeparator();
            $rows[] = [new TableCell(wrap('green', 'File coppied'), ['colspan' => 2])];
        }

        $table
            ->setHeaders([
                new TableCell($filename, ['colspan' => 2])
            ])
            ->setRows($rows);

        $table->render();
    }

    private function summonableCommands($file) {
        preg_match_all("/use (.*Command);/", file_get_contents($file), $array);
        $foo = $this->choice('Which to summon?', $array[1]);
        dd($foo);
    }

    private function patch($filePath, $search, $replace) {

        $table = new Table($this->getOutput());

        $rows = [];

        $file = file_get_contents($filePath);

        if (Str::contains($file, $search)) {
            $rows[] = [wrap('yellow', $search), wrap('green', $replace)];

            $file = str_replace(
                $search,
                $replace,
                $file
            );

            file_put_contents($filePath, $file);

            $rows[] = new TableSeparator();
            $rows[] = [new TableCell(wrap('green', 'Updating lines'), ['colspan' => 2])];
        } else {
            $rows[] = [wrap('green', $replace), wrap('green', $replace)];


            $rows[] = new TableSeparator();
            $rows[] = [new TableCell(wrap('yellow', 'No action required'), ['colspan' => 2])];
        }

        $table
            ->setHeaders([
                new TableCell($filePath, ['colspan' => 2])
            ])
            ->setRows($rows);

        $table->render();

//        }

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
