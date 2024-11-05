<?php
namespace PHPDublin\Rag\Model;

use Magento\Framework\Exception\RuntimeException;

class VectorDb
{
    public function __construct(private readonly array $adapters, private readonly string $default) {}

    public function getClient(string $name = null): VectorDbClientInterface
    {
        $name = $name ?? $this->default;

        if (!isset($this->adapters[$name])) {
            throw new RuntimeException(__('Could not found the vector DB adapter named "%1"', $name));
        }
        if (!$this->adapters[$name] instanceof VectorDbClientInterface) {
            throw new RuntimeException(
                __('The vector DB adapter "%1" must implement ' . VectorDbClientInterface::class, $name)
            );
        }
        return $this->adapters[$name];
    }
}
