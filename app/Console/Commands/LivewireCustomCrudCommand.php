<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class LivewireCustomCrudCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:livewire:crud
    {nameOfTheClass? : The name of the class.},
    {nameOfTheModelClass? : The name of the model class.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a custom livewire CRUD';

    /**
     * Our custom class properties here
     */
    protected $nameOfTheClass;
    protected $nameOfTheModelClass;
    protected $file;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->file = new Filesystem();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Gathers all parameters
        $this->gatherParameters();

        // Generates the Livewire Class File
        $this->generateLivewireCrudClassfile();

        // Generates the Livewire View File
    }

    /**
     * Gather all necessary parameters
     */
    protected function gatherParameters()
    {
        $this->nameOfTheClass = $this->argument('nameOfTheClass');
        $this->nameOfTheModelClass = $this->argument('nameOfTheModelClass');

        // If you didn't input the name of the class
        if (!$this->nameOfTheClass) {
            $this->nameOfTheClass = $this->ask('Enter class name');
        }

        // If you didn't input the name of the model class
        if (!$this->nameOfTheModelClass) {
            $this->nameOfTheModelClass = $this->ask('Enter model name');
        }

        // Converto to studly case
        $this->nameOfTheClass = Str::studly($this->nameOfTheClass);
        $this->nameOfTheModelClass = Str::studly($this->nameOfTheModelClass);
    }

    /**
     * Generates the CRUD class file
     */
    protected function generateLivewireCrudClassfile()
    {
        // Set the origin and destination for the livewire class file
        $fileOrigin = base_path('/stubs/custom.livewire.crud.stub');
        $fileDestination = base_path('/app/Http/Livewire/' . $this->nameOfTheClass . '.php');

        // Get the original string content of the file
        $fileOriginalString = $this->file->get($fileOrigin);

        // Put the content into the destination directory
        $this->file->put($fileDestination, $fileOriginalString);
        $this->info('Livewire class file created: '. $fileDestination);
    }
}
