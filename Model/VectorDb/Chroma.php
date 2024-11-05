<?php
namespace PHPDublin\Rag\Model\VectorDb;

use Codewithkyrian\ChromaDB\ChromaDB;
use Codewithkyrian\ChromaDB\Client;
use PHPDublin\Rag\Model\VectorDbClientInterface;

class Chroma implements VectorDbClientInterface
{
    private ?Client $client = null;

    public function reset()
    {
        $this->getClient()->deleteCollection('products');
    }

    public function save(int $productId, array $embedding, array $metadata): void
    {
        $collection = $this->getClient()->getOrCreateCollection('products');
        $collection->add([$productId], [$embedding], [$metadata]);
    }

    public function get(array $embedding): array
    {
        $collection = $this->getClient()->getCollection('products');
        $queryResponse = $collection->query(
            queryEmbeddings: [
                $embedding
            ],
            nResults: 3
        );
        return $queryResponse->toArray();
    }

    private function getClient(): Client
    {
        if (!$this->client) {
            $this->client = ChromaDB::client();
        }
        return $this->client;
    }
}
