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

$iterations = 3;
for ($i = 0; $i < $iterations; $i++) {
    $cardDeck = (new HoldemCardDeckFactory())->factory();
    $cardDeck = $simpleRandomCardShuffler->shuffle($cardDeck);

    $firstPlayerHandCards = [$cardDeck->pop(), $cardDeck->pop()];
    $firstPlayer = new Player('P1', $firstPlayerHandCards);

    $secondPlayerHandCards = [$cardDeck->pop(), $cardDeck->pop()];
    $secondPlayer = new Player('P2', $secondPlayerHandCards);

    $players = [$firstPlayer, $secondPlayer];
    $boardCards = [$cardDeck->pop(), $cardDeck->pop(), $cardDeck->pop(), $cardDeck->pop(), $cardDeck->pop()];
    $gameResult = $holdemEvaluator->evaluate($players, $boardCards);

    $content = 'Board Cards - |';
    foreach ($boardCards as $card) {
        $content .= $cardPresenter->preset($card) . '|';
    }
    $content .= PHP_EOL;
    echo $content;

    foreach ($players as $player) {
        $content = "Hand Cards {$player->getIdentifier()} - |";
        foreach ($player->getHandCards() as $card) {
            $content .= $cardPresenter->preset($card) . '|';
        }
        $content .= PHP_EOL;

        echo $content;
    }

    foreach ($gameResult->getResolverResults() as $resolverResult) {
        echo "Combination Score {$resolverResult->getPlayer()->getIdentifier()} - " . $resolverResult->getCombinationScore() . PHP_EOL;

        $content = 'Playing Cards - |';
        foreach ($resolverResult->getPlayingCards() as $card) {
            $content .= $cardPresenter->preset($card) . '|';
        }
        $content .= PHP_EOL;

        echo $content;
    }

    foreach ($gameResult->getWinners() as $winner) {
        echo "Winner - {$winner->getPlayer()->getIdentifier()}" . PHP_EOL;
    }

    echo '====================' . PHP_EOL;
}