<?php

namespace ForestYeti\PokerKernel\Evaluator\Service\Resolver;

use ForestYeti\PokerKernel\CardDeck\Collection\CardDeck;
use ForestYeti\PokerKernel\Evaluator\Enum\CombinationEnum;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use ForestYeti\PokerKernel\Evaluator\ValueObject\ResolverResult;

class HighCardResolver extends AbstractResolver
{
    public function getName(): string
    {
        return CombinationEnum::HighCard->value;
    }

    public function resolve(array $playingCards, Player $player): ResolverResult
    {
        $playingCards = (new CardDeck($playingCards))
            ->getSortedDesc()
            ->slice()
            ->toArray();

        return $this->result($player, $playingCards);
    }
}
