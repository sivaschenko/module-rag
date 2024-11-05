<?php
namespace PHPDublin\Rag\Console\Command;

use Magento\Framework\Console\Cli;
use PHPDublin\Rag\Model\Llm;
use PHPDublin\Rag\Model\VectorDb;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Prompt extends Command
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
        $this->setName('rag:prompt');
        $this->setDescription('RAG index.');
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

        $prompt = 'Write an answer to the following question of our customer: ' . $text . "\n";
        $prompt .= 'Given the following information about the products we think must be relevant: ' . "\n";
        foreach ($similar['metadatas'][0] as $metadata) {
            $prompt .= 'Product name: ' . "\n" . $metadata['name'] . "\n";
            $prompt .= 'Link: ' . "\n" . $metadata['link'] . "\n";
            $prompt .= 'Description: ' . "\n". $metadata['description'] . "\n\n";
        }
        $prompt .= 'The references to the products must be wrapped in the corresponding html links';

        $output->writeln($this->llm->getClient()->prompt($prompt));

        return Cli::RETURN_SUCCESS;
    }
}
