<?php

namespace ForestYeti\PokerKernel\CardDeck\Service;

use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;

class CardPresenter
{
    public function preset(Card $card): string
    {
        return "{$card->getRank()->value}-{$card->getSuit()->value}";
    }
}
