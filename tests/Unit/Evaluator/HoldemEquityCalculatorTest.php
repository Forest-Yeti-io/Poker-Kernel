<?php

namespace App\Tests\Evaluator;

use ForestYeti\PokerKernel\CardDeck\Enum\CardRankEnum;
use ForestYeti\PokerKernel\CardDeck\Enum\CardSuitEnum;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Service\HoldemEquityCalculator;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use PHPUnit\Framework\TestCase;

final class HoldemEquityCalculatorTest extends TestCase
{
    public function testCalculateReturnsEquitiesForSplitBoard(): void
    {
        $board = [
            new Card(CardRankEnum::Ace, CardSuitEnum::Hearts),
            new Card(CardRankEnum::King, CardSuitEnum::Diamonds),
            new Card(CardRankEnum::Queen, CardSuitEnum::Spades),
            new Card(CardRankEnum::Jack, CardSuitEnum::Clubs),
            new Card(CardRankEnum::Ten, CardSuitEnum::Hearts),
        ];

        $playerOne = new Player('P1', [
            new Card(CardRankEnum::Two, CardSuitEnum::Clubs),
            new Card(CardRankEnum::Three, CardSuitEnum::Diamonds),
        ]);
        $playerTwo = new Player('P2', [
            new Card(CardRankEnum::Four, CardSuitEnum::Clubs),
            new Card(CardRankEnum::Five, CardSuitEnum::Diamonds),
        ]);

        $equityResult = (new HoldemEquityCalculator())->calculate($board, [$playerOne, $playerTwo]);

        self::assertSame(50.0, $equityResult->getEquity($playerOne));
        self::assertSame(50.0, $equityResult->getEquity($playerTwo));
    }

    public function testCalculateHandlesSingleRiverCard(): void
    {
        $board = [
            new Card(CardRankEnum::Queen, CardSuitEnum::Hearts),
            new Card(CardRankEnum::Jack, CardSuitEnum::Hearts),
            new Card(CardRankEnum::Ten, CardSuitEnum::Hearts),
            new Card(CardRankEnum::Two, CardSuitEnum::Clubs),
        ];

        $playerOne = new Player('P1', [
            new Card(CardRankEnum::Ace, CardSuitEnum::Spades),
            new Card(CardRankEnum::King, CardSuitEnum::Diamonds),
        ]);
        $playerTwo = new Player('P2', [
            new Card(CardRankEnum::Queen, CardSuitEnum::Spades),
            new Card(CardRankEnum::Queen, CardSuitEnum::Diamonds),
        ]);

        $equityResult = (new HoldemEquityCalculator())->calculate($board, [$playerOne, $playerTwo]);

        self::assertEqualsWithDelta(77.2727, $equityResult->getEquity($playerOne), 0.01);
        self::assertEqualsWithDelta(22.7272, $equityResult->getEquity($playerTwo), 0.01);
    }
}
