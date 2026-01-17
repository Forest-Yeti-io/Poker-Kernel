<?php

namespace ForestYeti\PokerKernel\Evaluator\Service;

use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\ValueObject\GameResult;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;

interface EvaluatorInterface
{
    /**
     * @param Player[] $players
     * @param Card[] $boardCards
     */
    public function evaluate(array $players, array $boardCards): GameResult;
}