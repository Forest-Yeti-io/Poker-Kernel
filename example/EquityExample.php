<?php

require_once  '../vendor/autoload.php';

use ForestYeti\PokerKernel\CardDeck\Enum\CardRankEnum;
use ForestYeti\PokerKernel\CardDeck\Enum\CardSuitEnum;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Service\HoldemEquityCalculator;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;

$equityCalculator = new HoldemEquityCalculator();

$firstPlayer = new Player('P1', [
    new Card(CardRankEnum::Ace, CardSuitEnum::Hearts),
    new Card(CardRankEnum::King, CardSuitEnum::Hearts),
]);

$secondPlayer = new Player('P2', [
    new Card(CardRankEnum::Ten, CardSuitEnum::Spades),
    new Card(CardRankEnum::Ten, CardSuitEnum::Diamonds),
]);

$board = [
    new Card(CardRankEnum::Ten, CardSuitEnum::Hearts),
    new Card(CardRankEnum::Two, CardSuitEnum::Hearts),
    new Card(CardRankEnum::Nine, CardSuitEnum::Spades),
];

$equityResult = $equityCalculator->calculate($board, [$firstPlayer, $secondPlayer]);

$firstPlayerEquity = $equityResult->getEquity($firstPlayer);
$secondPlayerEquity = $equityResult->getEquity($secondPlayer);

echo "{$firstPlayer->getIdentifier()} - $firstPlayerEquity" . PHP_EOL;
echo "{$secondPlayer->getIdentifier()} - $secondPlayerEquity" . PHP_EOL;
