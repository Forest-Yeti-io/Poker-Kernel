<?php

namespace App\Tests\Evaluator;

use ForestYeti\PokerKernel\CardDeck\Enum\CardRankEnum;
use ForestYeti\PokerKernel\CardDeck\Enum\CardSuitEnum;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Service\HoldemEvaluator;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use PHPUnit\Framework\TestCase;

final class HoldemEvaluatorTest extends TestCase
{
    public function testEvaluateUsesKickersToBreakTies(): void
    {
        $board = [
            new Card(CardRankEnum::Ace, CardSuitEnum::Hearts),
            new Card(CardRankEnum::King, CardSuitEnum::Diamonds),
            new Card(CardRankEnum::Queen, CardSuitEnum::Spades),
            new Card(CardRankEnum::Seven, CardSuitEnum::Clubs),
            new Card(CardRankEnum::Two, CardSuitEnum::Hearts),
        ];

        $players = [
            new Player('P1', [
                new Card(CardRankEnum::Ace, CardSuitEnum::Clubs),
                new Card(CardRankEnum::Nine, CardSuitEnum::Diamonds),
            ]),
            new Player('P2', [
                new Card(CardRankEnum::Ace, CardSuitEnum::Diamonds),
                new Card(CardRankEnum::Jack, CardSuitEnum::Hearts),
            ]),
        ];

        $gameResult = (new HoldemEvaluator())->evaluate($players, $board);

        self::assertCount(2, $gameResult->getResolverResults());
        self::assertCount(1, $gameResult->getWinners());
        self::assertSame('P2', $gameResult->getWinners()[0]?->getPlayer()->getIdentifier());
    }

    public function testEvaluateStraightUsesKickersToBreakTies(): void
    {
        $board = [
            new Card(CardRankEnum::Ten, CardSuitEnum::Hearts),
            new Card(CardRankEnum::King, CardSuitEnum::Diamonds),
            new Card(CardRankEnum::Queen, CardSuitEnum::Spades),
            new Card(CardRankEnum::Three, CardSuitEnum::Clubs),
            new Card(CardRankEnum::Jack, CardSuitEnum::Hearts),
        ];

        $players = [
            new Player('P1', [
                new Card(CardRankEnum::Ace, CardSuitEnum::Clubs),
                new Card(CardRankEnum::Two, CardSuitEnum::Diamonds),
            ]),
            new Player('P2', [
                new Card(CardRankEnum::Nine, CardSuitEnum::Diamonds),
                new Card(CardRankEnum::Two, CardSuitEnum::Hearts),
            ]),
        ];

        $gameResult = (new HoldemEvaluator())->evaluate($players, $board);

        self::assertCount(2, $gameResult->getResolverResults());
        self::assertCount(1, $gameResult->getWinners());
        self::assertSame('P1', $gameResult->getWinners()[0]?->getPlayer()->getIdentifier());
    }

    public function testEvaluateTracksSplitPotWinners(): void
    {
        $board = [
            new Card(CardRankEnum::Ace, CardSuitEnum::Hearts),
            new Card(CardRankEnum::King, CardSuitEnum::Diamonds),
            new Card(CardRankEnum::Queen, CardSuitEnum::Clubs),
            new Card(CardRankEnum::Seven, CardSuitEnum::Spades),
            new Card(CardRankEnum::Two, CardSuitEnum::Hearts),
        ];

        $players = [
            new Player('P1', [
                new Card(CardRankEnum::Jack, CardSuitEnum::Hearts),
                new Card(CardRankEnum::Nine, CardSuitEnum::Diamonds),
            ]),
            new Player('P2', [
                new Card(CardRankEnum::Jack, CardSuitEnum::Spades),
                new Card(CardRankEnum::Nine, CardSuitEnum::Clubs),
            ]),
        ];

        $gameResult = (new HoldemEvaluator())->evaluate($players, $board);

        $winnerIds = array_map(
            static fn (Player $player) => $player->getIdentifier(),
            array_map(
                static fn ($result) => $result->getPlayer(),
                $gameResult->getWinners()
            )
        );
        sort($winnerIds);

        self::assertCount(2, $gameResult->getWinners());
        self::assertSame(['P1', 'P2'], $winnerIds);
    }
}
