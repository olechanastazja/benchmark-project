<?php

namespace App\Command;

use App\Service\BenchmarkingService;
use App\Service\NotificationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BenchmarkCommand extends Command
{
    /**
     * @var BenchmarkingService
     */
    private $benchmarkingService;

    /**
     * @var string
     */
    protected static $defaultName = 'app:benchmark';

    /**
     * BenchmarkCommand constructor.
     * @param BenchmarkingService $benchmarkingService
     */
    public function __construct(BenchmarkingService $benchmarkingService)
    {
        $this->benchmarkingService = $benchmarkingService;
        parent::__construct();
    }

    /**
     * Configuration of a command
     */
    protected function configure()
    {
        $this
            ->addArgument('main-url', InputArgument::REQUIRED, 'The main website to be compared')
            ->addArgument('others-urls', InputArgument::IS_ARRAY, 'Competitors');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $validate  = $this->inputValidator(array_merge([$input->getArgument('main-url')],
            $input->getArgument('others-urls')));
        if(!$validate){
            $result = '<info>Remember that all arguments has to be urls in a proper form.</info>';
        }else{
            $result = $this->benchmarkingService->handleProcess($input->getArgument('main-url'),
                $input->getArgument('others-urls'));
        }
        $output->writeln($result);
    }

    /**
     * Checks if inserted url is valid
     * @param array $inputUrls
     * @return bool
     */
    private function inputValidator(array $inputUrls)
    {
        foreach ($inputUrls as $inputUrl){
            if (!filter_var($inputUrl, FILTER_VALIDATE_URL) ||
                !preg_match("/\b(?:(?:https?):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",
                    $inputUrl)) {
                return false;
            }
        }
        return true;
    }
}