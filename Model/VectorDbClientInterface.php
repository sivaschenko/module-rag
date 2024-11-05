<?php
namespace PHPDublin\Rag\Model;

interface VectorDbClientInterface
{
    public function reset();

    public function save(int $productId, array $embedding, array $metadata);

    public function get(array $embedding): array;
}
