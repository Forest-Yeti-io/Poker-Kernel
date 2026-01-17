<?php

namespace App\Tests\CardDeck;

use ForestYeti\PokerKernel\CardDeck\Enum\CardRankEnum;
use ForestYeti\PokerKernel\CardDeck\Enum\CardSuitEnum;
use ForestYeti\PokerKernel\CardDeck\Service\Factory\HoldemCardDeckFactory;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use PHPUnit\Framework\TestCase;

class HoldemCardDeckTest extends TestCase
{
    public function testCardDeckSize(): void
    {
        $cardDeck = (new HoldemCardDeckFactory())->factory();

        $this->assertEquals(52, $cardDeck->count());
    }

    public function testCardDeckDuplicatePolicy(): void
    {
        $cardDeck = (new HoldemCardDeckFactory())->factory();

        // Проверяем, что карта "Туз Червей" существует
        $aceOfHearts = $cardDeck->get(new Card(CardRankEnum::Ace, CardSuitEnum::Hearts));
        $this->assertNotNull($aceOfHearts);

        // Если карта существует, добавление такой же карты ни к чему не приведет
        $cardDeck->push(new Card(CardRankEnum::Ace, CardSuitEnum::Hearts));
        $this->assertEquals(52, $cardDeck->count());
    }

    public function testCardDeckCorrectly(): void
    {
        $cardDeck = (new HoldemCardDeckFactory())->factory();

        // Каждая карта должна быть
        foreach (CardSuitEnum::cases() as $suit) {
            foreach (CardRankEnum::getHoldemRanks() as $rank) {
                $targetCard = new Card($rank, $suit);

                $this->assertNotNull($cardDeck->get($targetCard));
            }
        }
    }
}
