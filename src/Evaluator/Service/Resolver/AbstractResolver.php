<?php

namespace ForestYeti\PokerKernel\Evaluator\Service\Resolver;

use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use ForestYeti\PokerKernel\Evaluator\ValueObject\ResolverResult;

abstract class AbstractResolver implements ResolverInterface
{
    private int $baseScore;

    public function getBaseScore(): int
    {
        return $this->baseScore;
    }

    public function setBaseScore(int $baseScore): ResolverInterface
    {
        $this->baseScore = $baseScore;

        return $this;
    }

    public function result(Player $player, array $playingCards): ResolverResult
    {
        return new ResolverResult($player, $playingCards, $this->getBaseScore());
    }
}
