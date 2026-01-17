<?php

require_once  '../vendor/autoload.php';

use ForestYeti\PokerKernel\CardDeck\Service\CardPresenter;
use ForestYeti\PokerKernel\CardDeck\Service\Factory\HoldemCardDeckFactory;
use ForestYeti\PokerKernel\Evaluator\Service\HoldemEvaluator;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use ForestYeti\PokerKernel\Random\Service\SimpleRandomCardShuffler;

$holdemEvaluator = new HoldemEvaluator();
$simpleRandomCardShuffler = new SimpleRandomCardShuffler();
$cardPresenter = new CardPresenter();

$iterations = 10;
for ($i = 0; $i < $iterations; $i++) {
    $cardDeck = (new HoldemCardDeckFactory())->factory();
    $cardDeck = $simpleRandomCardShuffler->shuffle($cardDeck);

    $handCards = [$cardDeck->pop(), $cardDeck->pop()];
    $player = new Player('Mock', $handCards);

    $boardCards = [$cardDeck->pop(), $cardDeck->pop(), $cardDeck->pop(), $cardDeck->pop(), $cardDeck->pop()];
    $gameResult = $holdemEvaluator->evaluate([$player], $boardCards);

    $resolverResult = $gameResult->getResolverResults()[0];

    $content = 'Board Cards - |';
    foreach ($boardCards as $card) {
        $content .= $cardPresenter->preset($card) . '|';
    }
    $content .= PHP_EOL;
    echo $content;

    $content = 'Hand Cards - |';
    foreach ($handCards as $card) {
        $content .= $cardPresenter->preset($card) . '|';
    }
    $content .= PHP_EOL;
    echo $content;

    echo 'Combination Score - ' . $resolverResult->getCombinationScore() . PHP_EOL;

    $content = 'Playing Cards - |';
    foreach ($resolverResult->getPlayingCards() as $card) {
        $content .= $cardPresenter->preset($card) . '|';
    }
    $content .= PHP_EOL;
    echo $content;
    echo '====================' . PHP_EOL;
}