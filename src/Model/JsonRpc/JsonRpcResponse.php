<?php

namespace Sal\Seven\Model\JsonRpc;

class JsonRpcResponse
{
    public function __construct(
        private ?string $id,
        private mixed $result,
        private ?JsonRpcError $error = null,
    ) {
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getResult(): mixed
    {
        return $this->result;
    }

    public function getError(): ?JsonRpcError
    {
        return $this->error;
    }

    public function isSuccessful(): bool
    {
        return null === $this->error;
    }
}
