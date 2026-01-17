<?php

namespace ForestYeti\PokerKernel\Evaluator\ValueObject;

readonly class Player
{
    public function __construct(
        private string $identifier,
        private array $handCards
    ) {
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getHandCards(): array
    {
        return $this->handCards;
    }
}
