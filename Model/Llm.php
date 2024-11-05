<?php
namespace PHPDublin\Rag\Model;

use Magento\Framework\Exception\RuntimeException;

class Llm
{
    public function __construct(private readonly array $adapters, private readonly string $default) {}

    public function getClient(string $name = null): LlmInterface
    {
        $name = $name ?? $this->default;

        if (!isset($this->adapters[$name])) {
            throw new RuntimeException(__('Could not found the LLM adapter named "%1"', $name));
        }
        if (!$this->adapters[$name] instanceof LlmInterface) {
            throw new RuntimeException(
                __('The LLM adapter "%1" must implement ' . LlmInterface::class, $name)
            );
        }
        return $this->adapters[$name];
    }
}
