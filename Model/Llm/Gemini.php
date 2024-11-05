<?php
namespace PHPDublin\Rag\Model\Llm;

use Gemini\Client;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPDublin\Rag\Model\LlmInterface;

class Gemini implements LlmInterface
{
    private const KEY = 'rag/llm/gemini';

    public function __construct(private readonly ScopeConfigInterface $config)
    {
    }

    private ?Client $client = null;

    public function embedding(array $data): array
    {
        return $this->getClient()->embeddingModel()->embedContent($data)->toArray();
    }

    public function prompt(string $query): string
    {
        return $this->getClient()->geminiFlash()->generateContent($query)->text();
    }

    private function getClient(): Client
    {
        if (!$this->client) {
            $this->client = \Gemini::client($this->config->getValue(self::KEY));
        }
        return $this->client;
    }
}
