<?php

namespace ForestYeti\PokerKernel\Evaluator\Service\Resolver;

use ForestYeti\PokerKernel\CardDeck\Collection\CardDeck;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Enum\CombinationEnum;
use ForestYeti\PokerKernel\Evaluator\Exception\CombinationNotResolvedException;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use ForestYeti\PokerKernel\Evaluator\ValueObject\ResolverResult;

class FlashResolver extends AbstractResolver
{
    public function getName(): string
    {
        return CombinationEnum::Flash->value;
    }

    /**
     * @param Card[] $playingCards
     * @throws CombinationNotResolvedException
     */
    public function resolve(array $playingCards, Player $player): ResolverResult
    {
        $deck = (new CardDeck($playingCards))->getSortedDesc();
        $groups = $deck->groupBySuit();

        $bestFlush = [];
        foreach ($groups as $cards) {
            if (count($cards) < 5) {
                continue;
            }

            $candidate = (new CardDeck($cards))
                ->getSortedDesc()
                ->slice(0, 5)
                ->toArray();

            if (empty($bestFlush) || $this->isBetterFlush($candidate, $bestFlush)) {
                $bestFlush = $candidate;
            }
        }

        if (empty($bestFlush)) {
            throw new CombinationNotResolvedException('Flash not resolved');
        }

        return $this->result($player, $bestFlush);
    }

    /**
     * @param Card[] $candidate
     * @param Card[] $bestFlush
     */
    private function isBetterFlush(array $candidate, array $bestFlush): bool
    {
        $candidateRanks = array_map(
            static fn (Card $card) => $card->getRank()->value,
            $candidate
        );
        $bestRanks = array_map(
            static fn (Card $card) => $card->getRank()->value,
            $bestFlush
        );

        foreach ($candidateRanks as $index => $rank) {
            if ($rank === $bestRanks[$index]) {
                continue;
            }

            return $rank > $bestRanks[$index];
        }

        return false;
    }
}
