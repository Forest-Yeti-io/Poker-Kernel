<?php

namespace ForestYeti\PokerKernel\Evaluator\Service\Resolver;

use ForestYeti\PokerKernel\CardDeck\Collection\CardDeck;
use ForestYeti\PokerKernel\CardDeck\Enum\CardRankEnum;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Enum\CombinationEnum;
use ForestYeti\PokerKernel\Evaluator\Exception\CombinationNotResolvedException;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use ForestYeti\PokerKernel\Evaluator\ValueObject\ResolverResult;

final class StraightFlashResolver extends AbstractResolver
{
    public function getName(): string
    {
        return CombinationEnum::StraightFlash->value;
    }

    /**
     * @param Card[] $playingCards
     * @throws CombinationNotResolvedException
     */
    public function resolve(array $playingCards, Player $player): ResolverResult
    {
        $bestStraightFlush = $this->resolveBestStraightFlush($playingCards);

        if (empty($bestStraightFlush)) {
            throw new CombinationNotResolvedException('Straight flash not resolved');
        }

        if ($this->isRoyal($bestStraightFlush)) {
            throw new CombinationNotResolvedException('Straight flash not resolved');
        }

        return $this->result($player, $bestStraightFlush);
    }

    /**
     * @param Card[] $playingCards
     * @return Card[]
     */
    private function resolveBestStraightFlush(array $playingCards): array
    {
        $deck = new CardDeck($playingCards);
        $groups = $deck->groupBySuit();

        $bestStraightFlush = [];
        $bestHigh = 0;

        foreach ($groups as $cards) {
            if (count($cards) < 5) {
                continue;
            }

            $straightCards = $this->resolveStraightFromCards($cards);
            if (empty($straightCards)) {
                continue;
            }

            $highValue = $this->getStraightHighValue($straightCards);
            if ($highValue > $bestHigh) {
                $bestHigh = $highValue;
                $bestStraightFlush = $straightCards;
            }
        }

        return $bestStraightFlush;
    }

    /**
     * @param Card[] $cards
     * @return Card[]
     */
    private function resolveStraightFromCards(array $cards): array
    {
        $deck = new CardDeck($cards);
        $groups = $deck->groupByRank();

        $rankMap = [];
        foreach ($groups as $rankValue => $groupCards) {
            $rankMap[(int)$rankValue] = $groupCards[0];
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
            return [];
        }

        $resultCards = [];
        $bestSequence = array_reverse($bestSequence);
        foreach ($bestSequence as $rankValue) {
            if ($rankValue === CardRankEnum::LowAce->value) {
                $rankValue = CardRankEnum::Ace->value;
            }
            $resultCards[] = $rankMap[$rankValue];
        }

        return $resultCards;
    }

    /**
     * @param Card[] $cards
     */
    private function getStraightHighValue(array $cards): int
    {
        $ranks = array_map(
            static fn (Card $card) => $card->getRank()->value,
            $cards
        );
        sort($ranks);

        if ($ranks === [
            CardRankEnum::Two->value,
            CardRankEnum::Three->value,
            CardRankEnum::Four->value,
            CardRankEnum::Five->value,
            CardRankEnum::Ace->value,
        ]) {
            return CardRankEnum::Five->value;
        }

        return max($ranks);
    }

    /**
     * @param Card[] $cards
     */
    private function isRoyal(array $cards): bool
    {
        $ranks = array_map(
            static fn (Card $card) => $card->getRank()->value,
            $cards
        );
        sort($ranks);

        return $ranks === [
            CardRankEnum::Ten->value,
            CardRankEnum::Jack->value,
            CardRankEnum::Queen->value,
            CardRankEnum::King->value,
            CardRankEnum::Ace->value,
        ];
    }
}
