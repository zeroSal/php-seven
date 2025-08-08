<?php

namespace Sal\Seven\Model\Http\Header;

/**
 * @author Luca Saladino <sal65535@protonmail.com>
 */
class HttpHeader
{
    public function __construct(
        private string $name,
        private string $value,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
