<?php
namespace PHPDublin\Rag\Model\Llm;

use Magento\Framework\App\Config\ScopeConfigInterface;
use OpenAI\Client;
use PHPDublin\Rag\Model\LlmInterface;

class OpenAi implements LlmInterface
{
    private const KEY = 'rag/llm/openai';

    public function __construct(private readonly ScopeConfigInterface $config)
    {
    }

    private ?Client $client = null;

    public function embedding(array $data): array
    {
        $embeddings = $this->getClient()->embeddings()->create(
            [
                'model' => 'text-embedding-3-small',
                'input' => implode(' ', $data)
            ]
        )->embeddings;

        return reset($embeddings)->embedding;
    }

    public function prompt(string $query): string
    {
        $response = $this->getClient()->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $query],
            ],
        ]);
        return $response->choices[0]->message->content;
    }

    private function getClient(): Client
    {
        if (!$this->client) {
            $this->client = \OpenAI::client($this->config->getValue(self::KEY));
        }
        return $this->client;
    }
}
