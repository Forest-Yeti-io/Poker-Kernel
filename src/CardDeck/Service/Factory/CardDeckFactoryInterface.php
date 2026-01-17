<?php

namespace ForestYeti\PokerKernel\CardDeck\Service\Factory;

use ForestYeti\PokerKernel\CardDeck\Collection\CardDeck;

interface CardDeckFactoryInterface
{
    public function factory(): CardDeck;
}