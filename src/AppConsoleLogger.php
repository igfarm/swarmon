<?php
namespace Console;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Psr\Log\LogLevel;

class AppConsoleLogger extends ConsoleLogger
{
    private $app_name = "";
    
    public function __construct(OutputInterface $output, $app_name)
    {
        $this->app_name = $app_name;
        
        parent::__construct($output, [ LogLevel::NOTICE => OutputInterface::VERBOSITY_NORMAL]);
    }

    public function log($level, $message, array $context = [])
    {
        parent::log($level, "[" . date("Y-m-d:H:i:s") . " " . $this->app_name .  " "  . getmypid() . "] " . $message, $context);
    }
}
