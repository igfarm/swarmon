<?php
namespace Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

use Console\AppConsoleLogger;

class RunCommand extends SymfonyCommand
{
	private $dotenv = null;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
  		// create logger 
        $this->logger = new AppConsoleLogger($output, $this->getName());
 
        // Load .env file if it exists
        $envdir = __DIR__ . "/../";
		$this->dotenv = \Dotenv\Dotenv::create($envdir);
		if (file_exists($envfile . ".env"))
			$this->dotenv->load();
	
        // We are ready to go
        $this->logger->notice("Starting...");
    }
    
    public function configure()
    {
        $this->setName('swarmon')
            ->setDescription('Start swarm monitoring');
    }
        
    public function execute(InputInterface $input, OutputInterface $output)
    {
    	// Make sure we have a variables we use
		$this->dotenv->required('SLACK_WEBHOOK');
		$this->dotenv->required('NODE_COUNT')->isInteger();

		// Create slack
		$slack = new \Maknz\Slack\Client(getenv('SLACK_WEBHOOK'));

		// Get the expected node count
		$expected_node_count = getenv('NODE_COUNT');
		if ($expected_node_count < 1)
			throw new \Exception("Node count should be greater than zero.");
		
		// Set check interval
		$check_inverval_min = getenv('CHECK_INTEVAL_MIN') ?: 5;
		if ($check_inverval_min < 1)
		{
			throw new \Exception("Check interval should be greater than 1 minute, was '$check_inverval_min'.");
		}

		// Check docker is around
		if (!preg_match("/Swarm: active/", `docker info`))
		{
			throw new \Exception("Docker is not running or not connected to a swarm.");
		}

		// sent an initial slack to confirm all is working on startup if requested	
		if (getenv('SLACK_ON_START') == "yes")
		{
			$slack->send("swarmon is starting.");
		}
		
        //	Loop forverver and ever
	    $this->logger->notice("Will check every $check_inverval_min minutes.");
        while (true) 
        {
	        $this->logger->notice("Checking swarm nodes...");

        	$real_node_count = trim(`docker node ls | grep Active | grep Ready | wc -l`);
        	if ($real_node_count == $expected_node_count)
        	{
				$this->logger->notice("All is well.");
        	}
        	else
        	{
        		$message = "Swarm problem: expected $expected_node_count nodes but found $real_node_count";
	        	$this->logger->warning($message);
				$slack->send($message);
        	}

        	sleep($check_inverval_min * 60);
        }
    }
}

