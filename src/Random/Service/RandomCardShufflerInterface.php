<?php

namespace ForestYeti\PokerKernel\Random\Service;

use ForestYeti\PokerKernel\CardDeck\Collection\CardDeck;

interface RandomCardShufflerInterface
{
    public function shuffle(CardDeck $cardDeck): CardDeck;
}
