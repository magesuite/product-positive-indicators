<?php
namespace MageSuite\ProductPositiveIndicators\Console\Command;

class RecentlyBought extends \Symfony\Component\Console\Command\Command
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
     * @var \MageSuite\ProductPositiveIndicators\Model\RecentlyBoughtProductsFactory
     */
    protected $recentlyBoughtProductsFactory;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Framework\Config\ScopeInterface $scope,
        \MageSuite\ProductPositiveIndicators\Model\RecentlyBoughtProductsFactory $recentlyBoughtProductsFactory
    ) {
        parent::__construct();

        $this->state = $state;
        $this->scope = $scope;
        $this->recentlyBoughtProductsFactory = $recentlyBoughtProductsFactory;
    }

    protected function configure()
    {
        $this->setName('indicator:recentlybought:refresh');
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        if ($this->scope->getCurrentScope() !== 'frontend') {
            $this->state->setAreaCode('frontend');
        }

        $recentlyBoughtProducts = $this->recentlyBoughtProductsFactory->create();

        $recentlyBoughtProducts->execute();
    }
}
