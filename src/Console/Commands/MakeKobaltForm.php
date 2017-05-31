<?php

namespace Hoppermagic\Kobalt\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;

class MakeKobaltForm extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:ko-form 
                            {name : The basename of the resource eg Project}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Kobalt form';



    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct($files);
    }



    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function fire()
    {
        $name = $this->getNameInput() . 'Form';

        $path = $this->getPath('App\Http\Forms\Admin\\' . $name);

        // First we will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ($this->alreadyExists($name)) {
            $this->error('Form already exists!');
            return false;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($this->getNameInput()));

        $this->info('Admin form created successfully.');
    }



    /**
    * Get the stub file for the generator.
    *
    * @return string
    */
    protected function getStub()
    {
        return __DIR__ . '/stubs/admin-form-template.stub';
    }



    /**
     * Replace the class name for the given stub.
     *
     * @param  string $stub
     * @param  string $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $stub = str_replace(
            '{{name}}',
            $name,
            $stub
        );

        return $stub;
    }
}
