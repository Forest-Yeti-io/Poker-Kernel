<?php

namespace ForestYeti\PokerKernel\Evaluator\Service\Resolver;

use ForestYeti\PokerKernel\CardDeck\Collection\CardDeck;
use ForestYeti\PokerKernel\CardDeck\Enum\CardRankEnum;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Enum\CombinationEnum;
use ForestYeti\PokerKernel\Evaluator\Exception\CombinationNotResolvedException;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use ForestYeti\PokerKernel\Evaluator\ValueObject\ResolverResult;

final class StraightResolver extends AbstractResolver
{
    public function getName(): string
    {
        return CombinationEnum::Straight->value;
    }

    /**
     * @param Card[] $playingCards
     * @throws CombinationNotResolvedException
     */
    public function resolve(array $playingCards, Player $player): ResolverResult
    {
        $deck = new CardDeck($playingCards);

        $groups = $deck->groupByRank();

        $rankMap = [];
        foreach ($groups as $rankValue => $cards) {
            $rankMap[(int)$rankValue] = $cards[0];
        }

        if (isset($rankMap[CardRankEnum::Ace->value])) {
            $rankMap[CardRankEnum::LowAce->value] =
                $rankMap[CardRankEnum::Ace->value];
        }

        $ranks = array_keys($rankMap);
        sort($ranks);

        $bestSequence = [];
        for ($i = 0; $i <= count($ranks) - 5; $i++) {
            $sequence = [$ranks[$i]];

            for ($j = $i + 1, $jMax = count($ranks); $j < $jMax; $j++) {
                if ($ranks[$j] === end($sequence) + 1) {
                    $sequence[] = $ranks[$j];
                } elseif ($ranks[$j] > end($sequence) + 1) {
                    break;
                }

                if (count($sequence) === 5) {
                    $bestSequence = $sequence;
                }
            }
        }

        if (count($bestSequence) !== 5) {
            throw new CombinationNotResolvedException('Straight not resolved');
        }

        $resultCards = [];
        foreach ($bestSequence as $rankValue) {
            if ($rankValue === CardRankEnum::LowAce->value) {
                $rankValue = CardRankEnum::Ace->value;
            }
            $resultCards[] = $rankMap[$rankValue];
        }

        return $this->result($player, $resultCards);
    }
}
