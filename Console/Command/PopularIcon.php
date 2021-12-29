<?php
namespace MageSuite\ProductPositiveIndicators\Console\Command;

class PopularIcon extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Magento\Framework\Config\ScopeInterface $scope
     */
    protected $scope;

    /**
     * @var \MageSuite\ProductPositiveIndicators\Model\PopularIconProductsFactory
     */
    protected $popularIconProductsFactory;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Framework\Config\ScopeInterface $scope,
        \MageSuite\ProductPositiveIndicators\Model\PopularIconProductsFactory $popularIconProductsFactory
    ) {
        parent::__construct();

        $this->state = $state;
        $this->scope = $scope;
        $this->popularIconProductsFactory = $popularIconProductsFactory;
    }

    protected function configure()
    {
        $this->setName('indicator:popularicon:refresh');
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        if ($this->scope->getCurrentScope() !== 'frontend') {
            $this->state->setAreaCode('frontend');
        }

        $popularIconProducts = $this->popularIconProductsFactory->create();

        $popularIconProducts->execute();
    }
}
