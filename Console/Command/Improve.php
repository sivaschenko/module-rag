<?php
namespace PHPDublin\Rag\Console\Command;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Console\Cli;
use PHPDublin\Rag\Model\Llm;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Improve extends Command
{
    public function __construct(
        private readonly Llm $llm,
        private readonly ProductRepositoryInterface $repository
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('rag:improve');
        $this->setDescription('RAG improve.');
        $this->addArgument('sku', InputArgument::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sku = $input->getArgument('sku');
        $product = $this->repository->get($sku);

        $productNameOutput = 'Product name: ' . "\n" . $product->getName() . "\n";
        $productDescriptionOutput = 'Description: ' . "\n". $product->getDescription() . "\n\n";

        $prompt = 'Improve the description for the following product: ' . "\n"
            . $productNameOutput . $productDescriptionOutput;
        $prompt .= 'The response has to include only an improved description for the product, nothing else.';

        $output->writeln($productNameOutput);
        $output->writeln($productDescriptionOutput);
        $output->writeln('Suggested Description:' . "\n");
        $output->writeln($this->llm->getClient()->prompt($prompt));

        return Cli::RETURN_SUCCESS;
    }
}
