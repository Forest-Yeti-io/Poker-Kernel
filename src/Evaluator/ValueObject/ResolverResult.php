<?php

namespace ForestYeti\PokerKernel\Evaluator\ValueObject;

readonly class ResolverResult
{
    public function __construct(
        private string $playerIdentifier,
        private array $playingCards,
        private int $combinationScore
    ) {
    }

    public function getPlayerIdentifier(): string
    {
        return $this->playerIdentifier;
    }

    public function getPlayingCards(): array
    {
        return $this->playingCards;
    }

    public function getCombinationScore(): int
    {
        return $this->combinationScore;
    }
}
