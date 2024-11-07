<?php
namespace PHPDublin\Rag\Console\Command;

use Magento\Framework\Console\Cli;
use PHPDublin\Rag\Model\Llm;
use PHPDublin\Rag\Model\VectorDb;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Search extends Command
{
    public function __construct(
        private readonly Llm $llm,
        private readonly VectorDb $vectorDb
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('rag:search');
        $this->setDescription('RAG search.');
        $this->addArgument('text', InputArgument::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $text = $input->getArgument('text');
        $embedding = $this->llm->getClient()->embedding([$text]);
        $similar = $this->vectorDb->getClient()->get($embedding['embedding']['values']);

        $output->writeln("\n" . 'Here are some products relevant to your query:' ."\n");
        foreach ($similar['metadatas'][0] as $metadata) {
            $output->writeln('Product name: ' . "\n" . $metadata['name'] . "\n");
            $output->writeln('Link: ' . "\n" . $metadata['link'] . "\n");
            $output->writeln('Description: ' . "\n". $metadata['description'] . "\n\n");
        }

        return Cli::RETURN_SUCCESS;
    }
}
