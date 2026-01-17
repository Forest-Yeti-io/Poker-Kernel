<?php

namespace ForestYeti\PokerKernel\Evaluator\Service\Resolver;

use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use ForestYeti\PokerKernel\Evaluator\ValueObject\ResolverResult;

interface ResolverInterface
{
    public function getName(): string;
    public function getBaseScore(): int;
    public function setBaseScore(int $baseScore): self;

    /**
     * @param Card[] $playingCards
     */
    public function resolve(array $playingCards, Player $player): ResolverResult;
}
