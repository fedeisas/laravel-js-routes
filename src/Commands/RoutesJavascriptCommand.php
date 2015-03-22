<?php namespace Fedeisas\LaravelJsRoutes\Commands;

use Fedeisas\LaravelJsRoutes\Generators\RoutesJavascriptGenerator;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RoutesJavascriptCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'routes:javascript';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Javascript routes file';

    /**
     * Javascript generator instance.
     *
     * @var Fedeisas\LaravelJsRoutes\Generators\RoutesJavascriptGenerator
     */
    protected $generator;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RoutesJavascriptGenerator $generator)
    {
        $this->generator = $generator;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $path = $this->getPath();

        $options = [
            'filter' => $this->option('filter'),
            'object' => $this->option('object'),
            'prefix' => $this->option('prefix'),
        ];

        if ($this->generator->make($this->option('path'), $this->argument('name'), $options)) {
            return $this->info("Created {$path}");
        }

        $this->error("Could not create {$path}");
    }

    /**
     * Get the path to the file that should be generated.
     *
     * @return string
     */
    protected function getPath()
    {
        return $this->option('path') . '/' . $this->argument('name');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::OPTIONAL, 'Filename', 'routes.js'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
           ['path', 'p', InputOption::VALUE_OPTIONAL, 'Path to assets directory.', base_path()],
           ['filter', 'f', InputOption::VALUE_OPTIONAL, 'Custom route filter.', null],
           ['object', 'o', InputOption::VALUE_OPTIONAL, 'Custom JS object.', 'Router'],
           ['prefix', 'prefix', InputOption::VALUE_OPTIONAL, 'Custom route prefix.', null]
        ];
    }
}
