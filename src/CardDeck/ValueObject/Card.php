<?php

namespace ForestYeti\PokerKernel\CardDeck\ValueObject;

use ForestYeti\PokerKernel\CardDeck\Enum\CardRankEnum;
use ForestYeti\PokerKernel\CardDeck\Enum\CardSuitEnum;

readonly class Card
{
    public function __construct(
        private CardRankEnum $rank,
        private CardSuitEnum $suit,
    ) {
    }

    public function getRank(): CardRankEnum
    {
        return $this->rank;
    }

    public function getSuit(): CardSuitEnum
    {
        return $this->suit;
    }

    public function getHash(): string
    {
        $payload = "CardHash_{$this->getRank()->value}_{$this->getSuit()->value}";
        return md5($payload);
    }

    public function __toString(): string
    {
        return $this->getHash();
    }
}
