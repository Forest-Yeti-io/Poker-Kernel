<?php

namespace ForestYeti\PokerKernel\Evaluator\Service\Resolver;

use ForestYeti\PokerKernel\CardDeck\Collection\CardDeck;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Enum\CombinationEnum;
use ForestYeti\PokerKernel\Evaluator\Exception\CombinationNotResolvedException;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use ForestYeti\PokerKernel\Evaluator\ValueObject\ResolverResult;

class ThreeOfKindResolver extends AbstractResolver
{
    public function getName(): string
    {
        return CombinationEnum::ThreeOfKind->value;
    }

    /**
     * @param Card[] $playingCards
     * @throws CombinationNotResolvedException
     */
    public function resolve(array $playingCards, Player $player): ResolverResult
    {
        $deck = (new CardDeck($playingCards))->getSortedDesc();
        $groups = $deck->groupByRank();

        $threeRankValue = null;
        foreach ($deck->getUniqueRankValuesDesc() as $rankValue) {
            if (isset($groups[$rankValue]) && count($groups[$rankValue]) >= 3) {
                $threeRankValue = $rankValue;
                break;
            }
        }

        if ($threeRankValue === null) {
            throw new CombinationNotResolvedException('Three of a Kind not resolved');
        }

        $threeCards = array_slice($groups[$threeRankValue], 0, 3);
        $kickers = $deck->takeKickersExcludingRanks([$threeRankValue], 2);

        return $this->result($player, array_merge($threeCards, $kickers));
    }
}
