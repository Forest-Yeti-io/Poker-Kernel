<?php

namespace ForestYeti\PokerKernel\Evaluator\Service;

use ForestYeti\PokerKernel\CardDeck\Service\Factory\HoldemCardDeckFactory;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Dto\EquityResult;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;

class HoldemEquityCalculator implements EquityCalculatorInterface
{
    private HoldemEvaluator $evaluator;
    private HoldemCardDeckFactory $deckFactory;

    public function __construct(?HoldemEvaluator $evaluator = null, ?HoldemCardDeckFactory $deckFactory = null)
    {
        $this->evaluator = $evaluator ?? new HoldemEvaluator();
        $this->deckFactory = $deckFactory ?? new HoldemCardDeckFactory();
    }

    public function calculate(array $boardCards, array $players): EquityResult
    {
        $deck = $this->deckFactory->factory();
        $usedCards = $this->collectUsedCards($boardCards, $players);
        $usedHashes = array_fill_keys(
            array_map(static fn (Card $card) => $card->getHash(), $usedCards),
            true
        );

        $remainingBoardCards = 5 - count($boardCards);
        $deckCards = array_values(
            array_filter(
                $deck->toArray(),
                static fn (Card $card) => !isset($usedHashes[$card->getHash()])
            )
        );
        $totalOutcomes = 0;
        $wins = $this->initializeWinCounters($players);

        foreach ($this->generateCombinations($deckCards, $remainingBoardCards) as $completion) {
            $fullBoard = array_merge($boardCards, $completion);
            $gameResult = $this->evaluator->evaluate($players, $fullBoard);
            $winners = $gameResult->getWinners();

            $totalOutcomes++;
            if (empty($winners)) {
                continue;
            }

            $share = 1.0 / count($winners);
            foreach ($winners as $winner) {
                $identifier = $winner->getPlayer()->getIdentifier();
                $wins[$identifier] += $share;
            }
        }

        if ($totalOutcomes === 0) {
            return new EquityResult($this->initializeEquities($players));
        }

        $equities = [];
        foreach ($wins as $identifier => $winCount) {
            $equities[$identifier] = ($winCount / $totalOutcomes) * 100;
        }

        return new EquityResult($equities);
    }

    /**
     * @param Card[] $boardCards
     * @param Player[] $players
     *
     * @return Card[]
     */
    private function collectUsedCards(array $boardCards, array $players): array
    {
        $usedCards = $boardCards;
        foreach ($players as $player) {
            $usedCards = array_merge($usedCards, $player->getHandCards());
        }

        return $usedCards;
    }

    /**
     * @param Player[] $players
     * @return array<string, float>
     */
    private function initializeWinCounters(array $players): array
    {
        $wins = [];
        foreach ($players as $player) {
            $wins[$player->getIdentifier()] = 0.0;
        }

        return $wins;
    }

    /**
     * @param Player[] $players
     * @return array<string, float>
     */
    private function initializeEquities(array $players): array
    {
        $equities = [];
        foreach ($players as $player) {
            $equities[$player->getIdentifier()] = 0.0;
        }

        return $equities;
    }

    /**
     * @param Card[] $items
     * @return iterable<Card[]>
     */
    private function generateCombinations(array $items, int $length): iterable
    {
        $count = count($items);

        if ($length === 0) {
            yield [];
            return;
        }

        for ($i = 0; $i <= $count - $length; $i++) {
            $head = $items[$i];
            $tail = array_slice($items, $i + 1);

            foreach ($this->generateCombinations($tail, $length - 1) as $combination) {
                array_unshift($combination, $head);
                yield $combination;
            }
        }
    }
}
