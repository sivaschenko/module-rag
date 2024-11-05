<?php
namespace PHPDublin\Rag\Console\Command;

use Magento\Framework\Console\Cli;
use PHPDublin\Rag\Model\Llm;
use PHPDublin\Rag\Model\Source\GetProducts;
use PHPDublin\Rag\Model\VectorDb;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Index extends Command
{
    public function __construct(
        private readonly Llm $llm,
        private readonly GetProducts $getProducts,
        private readonly VectorDb $vectorDb
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('rag:index');
        $this->setDescription('RAG index.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $products = $this->getProducts->execute();
        $productsWithEmbeddings = array_map(
            function (array $product): array {
                $product['embedding'] = $this->llm->getClient()
                    ->embedding([$product['description']])['embedding']['values'];
                return $product;
            },
            $products
        );

        $this->vectorDb->getClient()->reset();
        foreach ($productsWithEmbeddings as $product) {
            $this->vectorDb->getClient()->save(
                $product['id'],
                $product['embedding'],
                [
                    'name' => $product['name'],
                    'link' => $product['link'],
                    'description' => $product['description'],
                ]
            );
        }

        return Cli::RETURN_SUCCESS;
    }
}
