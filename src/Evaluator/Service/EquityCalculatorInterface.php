<?php

namespace ForestYeti\PokerKernel\Evaluator\Service;

use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Dto\EquityResult;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;

interface EquityCalculatorInterface
{
    /**
     * @param Card[] $boardCards
     * @param Player[] $players
     */
    public function calculate(array $boardCards, array $players): EquityResult;
}
