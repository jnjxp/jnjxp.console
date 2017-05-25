# jnjxp.console
Really simple commandline interface

## Usage

### Console

```php
/**
 * Some Commands
 */

class HelloWorld
{
    public function __invoke($name = 'World')
    {
        echo "Hello $name" . PHP_EOL;
        return \Jnjxp\Console\Status::SUCCESS;
    }
}

class ByeWorld
{
    public function __invoke($name = 'World')
    {
        echo "Bye $name" . PHP_EOL;
        return \Jnjxp\Console\Status::SUCCESS;
    }
}


/**
 * Create an application
 */
$console = new Jnjxp\Console\Console(
    [
        'hi'  => HelloWorld::class,
        'bye' => ByeWorld::class
    ]
);


// Execute based on input and exit
$status = $console($argv);
exit($status);
```

### StdLog
See: http://paul-m-jones.com/archives/6552

```php
$log = new \Jnjxp\Console\StdLog;
$log->setLevel(\Psr\Log\LogLevel::INFO);

$log->debug('This wont print cuz level');
$log->info('Prints to stdout');
$log->error('Prints to STDERR');

```

