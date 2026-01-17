<?php

namespace ForestYeti\PokerKernel\Evaluator\Service\Resolver;

use ForestYeti\PokerKernel\CardDeck\Collection\CardDeck;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Enum\CombinationEnum;
use ForestYeti\PokerKernel\Evaluator\Exception\CombinationNotResolvedException;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use ForestYeti\PokerKernel\Evaluator\ValueObject\ResolverResult;

class FourOfKindResolver extends AbstractResolver
{
    public function getName(): string
    {
        return CombinationEnum::FourOfKind->value;
    }

    /**
     * @param Card[] $playingCards
     * @throws CombinationNotResolvedException
     */
    public function resolve(array $playingCards, Player $player): ResolverResult
    {
        $deck = (new CardDeck($playingCards))->getSortedDesc();
        $groups = $deck->groupByRank();

        $fourRankValue = null;
        foreach ($deck->getUniqueRankValuesDesc() as $rankValue) {
            if (isset($groups[$rankValue]) && count($groups[$rankValue]) >= 4) {
                $fourRankValue = $rankValue;
                break;
            }
        }

        if ($fourRankValue === null) {
            throw new CombinationNotResolvedException('Four of kind not resolved');
        }

        $fourCards = array_slice($groups[$fourRankValue], 0, 4);
        $kicker = $deck->takeKickersExcludingRanks([$fourRankValue], 1);

        return $this->result($player, array_merge($fourCards, $kicker));
    }
}
