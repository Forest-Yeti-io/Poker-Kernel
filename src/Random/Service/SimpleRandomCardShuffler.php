<?php

namespace ForestYeti\PokerKernel\Random\Service;

use ForestYeti\PokerKernel\CardDeck\Collection\CardDeck;

class SimpleRandomCardShuffler implements RandomCardShufflerInterface
{
    public function shuffle(CardDeck $cardDeck): CardDeck
    {
        $cards = $cardDeck->toArray();
        shuffle($cards);

        return new CardDeck($cards);
    }
}
