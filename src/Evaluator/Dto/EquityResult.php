<?php

namespace ForestYeti\PokerKernel\Evaluator\Dto;

use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;

class EquityResult
{
    /**
     * @var array<string, float>
     */
    private array $equities;

    /**
     * @param array<string, float> $equities
     */
    public function __construct(array $equities)
    {
        $this->equities = $equities;
    }

    public function getEquity(Player $player): float
    {
        return $this->equities[$player->getIdentifier()] ?? 0.0;
    }

    /**
     * @return array<string, float>
     */
    public function getEquities(): array
    {
        return $this->equities;
    }
}
