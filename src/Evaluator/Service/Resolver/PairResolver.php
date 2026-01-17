<?php

namespace ForestYeti\PokerKernel\Evaluator\Service\Resolver;

use ForestYeti\PokerKernel\CardDeck\Collection\CardDeck;
use ForestYeti\PokerKernel\Evaluator\Enum\CombinationEnum;
use ForestYeti\PokerKernel\Evaluator\Exception\CombinationNotResolvedException;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use ForestYeti\PokerKernel\Evaluator\ValueObject\ResolverResult;

class PairResolver extends AbstractResolver
{
    public function getName(): string
    {
        return CombinationEnum::Pair->value;
    }

    /**
     * @throws CombinationNotResolvedException
     */
    public function resolve(array $playingCards, Player $player): ResolverResult
    {
        $deck = new CardDeck($playingCards);

        $groups = $deck->groupByRank();

        $pairRankValue = null;
        foreach ($deck->getUniqueRankValuesDesc() as $rankValue) {
            if (!empty($groups[$rankValue]) && count($groups[$rankValue]) >= 2) {
                $pairRankValue = $rankValue;
                break;
            }
        }

        if ($pairRankValue === null) {
            throw new CombinationNotResolvedException('Pair not resolved');
        }

        $pairCards = $deck->takeOfRank($pairRankValue, 2);
        $kickers = $deck->takeKickersExcludingRanks([$pairRankValue], 3);

        return $this->result($player, array_merge($pairCards, $kickers));
    }
}
