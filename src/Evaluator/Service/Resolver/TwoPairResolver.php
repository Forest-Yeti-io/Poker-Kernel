<?php

namespace ForestYeti\PokerKernel\Evaluator\Service\Resolver;

use ForestYeti\PokerKernel\CardDeck\Collection\CardDeck;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Enum\CombinationEnum;
use ForestYeti\PokerKernel\Evaluator\Exception\CombinationNotResolvedException;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use ForestYeti\PokerKernel\Evaluator\ValueObject\ResolverResult;

class TwoPairResolver extends AbstractResolver
{
    public function getName(): string
    {
        return CombinationEnum::TwoPair->value;
    }

    /**
     * @param Card[] $playingCards
     * @throws CombinationNotResolvedException
     */
    public function resolve(array $playingCards, Player $player): ResolverResult
    {
        $deck = (new CardDeck($playingCards))->getSortedDesc();
        $groups = $deck->groupByRank();

        $pairRankValues = [];
        foreach ($deck->getUniqueRankValuesDesc() as $rankValue) {
            if (isset($groups[$rankValue]) && count($groups[$rankValue]) >= 2) {
                $pairRankValues[] = $rankValue;
                if (count($pairRankValues) === 2) {
                    break;
                }
            }
        }

        if (count($pairRankValues) < 2) {
            throw new CombinationNotResolvedException('Two pair not resolved');
        }

        [$highPairRank, $lowPairRank] = $pairRankValues;

        $pairCards = array_merge(
            array_slice($groups[$highPairRank], 0, 2),
            array_slice($groups[$lowPairRank], 0, 2),
        );

        $kicker = $deck->takeKickersExcludingRanks([$highPairRank, $lowPairRank], 1);

        return $this->result($player, array_merge($pairCards, $kicker));
    }
}
