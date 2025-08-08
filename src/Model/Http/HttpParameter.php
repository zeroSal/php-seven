<?php

namespace Sal\Seven\Model\Http;

/**
 * @author Luca Saladino <sal65535@protonmail.com>
 */
class HttpParameter
{
    public function __construct(
        private string $name,
        private string|bool|int|null $value,
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

    public function getValue(): string|bool|int|null
    {
        return $this->value;
    }

    public function setValue(string|bool|int|null $value): void
    {
        $this->value = $value;
    }
}
