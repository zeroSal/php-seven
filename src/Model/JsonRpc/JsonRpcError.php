<?php

namespace Sal\Seven\Model\JsonRpc;

readonly class JsonRpcError
{
    public function __construct(
        private int $code,
        private string $message,
        private mixed $data = null,
    ) {
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
