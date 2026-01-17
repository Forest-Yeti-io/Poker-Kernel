<?php

namespace ForestYeti\PokerKernel\CardDeck\Service\Factory;

use ForestYeti\PokerKernel\CardDeck\Collection\CardDeck;
use ForestYeti\PokerKernel\CardDeck\Enum\CardRankEnum;
use ForestYeti\PokerKernel\CardDeck\Enum\CardSuitEnum;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;

class HoldemCardDeckFactory implements CardDeckFactoryInterface
{
    public function factory(): CardDeck
    {
        $cardDeck = new CardDeck();

        foreach ($this->getSuits() as $suit) {
            foreach ($this->getRanks() as $rank) {
                $cardDeck->push(new Card($rank, $suit));
            }
        }

        return $cardDeck;
    }

    /**
     * @return CardRankEnum[]
     */
    private function getRanks(): array
    {
        return CardRankEnum::getHoldemRanks();
    }

    /**
     * @return CardSuitEnum[]
     */
    private function getSuits(): array
    {
        return CardSuitEnum::cases();
    }
}
