<?php

namespace ForestYeti\PokerKernel\Evaluator\ValueObject;

readonly class ResolverResult
{
    public function __construct(
        private Player $player,
        private array $playingCards,
        private int $combinationScore
    ) {
    }

    public function getPlayer(): Player
    {
        return $this->player;
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
