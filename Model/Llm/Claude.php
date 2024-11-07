<?php
namespace PHPDublin\Rag\Model\Llm;

use Claude\Claude3Api\Client;
use Claude\Claude3Api\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPDublin\Rag\Model\LlmInterface;

class Claude implements LlmInterface
{
    private const KEY = 'rag/llm/claude';

    public function __construct(private readonly ScopeConfigInterface $config)
    {
    }

    private ?Client $client = null;

    public function embedding(array $data): array
    {
        throw new \Exception('Embeddings are available with Claude.');
    }

    public function prompt(string $query): string
    {
        return $this->getClient()->chat($query)->getContent()[0]['text'];
    }

    private function getClient(): Client
    {
        if (!$this->client) {
            $this->client = new Client(new Config($this->config->getValue(self::KEY)));
        }
        return $this->client;
    }
}
