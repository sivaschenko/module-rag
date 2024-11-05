<?php
namespace PHPDublin\Rag\Model;

interface LlmInterface
{
    public function embedding(array $data);

    public function prompt(string $query);
}
