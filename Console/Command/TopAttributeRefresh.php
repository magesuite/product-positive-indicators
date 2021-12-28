<?php

namespace MageSuite\ProductPositiveIndicators\Console\Command;

class TopAttributeRefresh extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \MageSuite\ProductPositiveIndicators\Cron\TopAttributeRefreshFactory
     */
    private $cronTopAttributeRefreshFactory;

    public function __construct(\MageSuite\ProductPositiveIndicators\Cron\TopAttributeRefreshFactory $cronTopAttributeRefreshFactory)
    {
        $this->cronTopAttributeRefreshFactory = $cronTopAttributeRefreshFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('indicator:topattribute:refresh');

        parent::configure();
    }

    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $stopwatch = new \Symfony\Component\Stopwatch\Stopwatch();
        $stopwatch->start('calculate_top_attribute');
        $returnStatus = \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        try {
            /** @var \MageSuite\ProductPositiveIndicators\Cron\TopAttributeRefresh $cronTopAttributeRefresh */
            $cronTopAttributeRefresh = $this->cronTopAttributeRefreshFactory->create();
            $cronTopAttributeRefresh->execute();
            $output->writeln('TopAttribute has been calculated successfully.');
        } catch (\Exception $e) {
            $output->writeln('An error has occurred while execute TopAttributeRefresh Cron task: ' . $e->getMessage());
            $output->writeln($e->getMessage());
            $returnStatus = \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        $event = $stopwatch->stop('calculate_top_attribute');
        $output->writeln('Duration: ' . $event->getDuration(). 'ms');
        $output->writeln('Max memory usage: ' . $event->getMemory()/1024/1024/8 . 'MB');
        return $returnStatus;
    }
}
