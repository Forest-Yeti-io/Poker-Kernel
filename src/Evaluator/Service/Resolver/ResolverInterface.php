<?php

namespace ForestYeti\PokerKernel\Evaluator\Service\Resolver;

use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\ValueObject\ResolverResult;

interface ResolverInterface
{
    /**
     * @param Card[] $playingCards
     */
    public function resolve(array $playingCards): ResolverResult;
}
