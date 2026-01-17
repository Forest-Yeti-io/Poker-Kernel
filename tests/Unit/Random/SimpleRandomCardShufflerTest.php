<?php

namespace App\Tests\Random;

use ForestYeti\PokerKernel\CardDeck\Service\Factory\HoldemCardDeckFactory;
use ForestYeti\PokerKernel\Random\Service\SimpleRandomCardShuffler;
use PHPUnit\Framework\TestCase;

class SimpleRandomCardShufflerTest extends TestCase
{
    private const int SHUFFLE_ITERATION = 512;

    public function testShuffles(): void
    {
        $cardDeck = (new HoldemCardDeckFactory())->factory();
        $simpleShuffler = new SimpleRandomCardShuffler();

        $prevShuffle = $cardDeck->getSeed();
        for ($i = 0; $i < self::SHUFFLE_ITERATION; $i++) {
            $cardDeck = $simpleShuffler->shuffle($cardDeck);

            $this->assertNotEquals($prevShuffle, $cardDeck->getSeed());
            $prevShuffle = $cardDeck->getSeed();
        }
    }
}
