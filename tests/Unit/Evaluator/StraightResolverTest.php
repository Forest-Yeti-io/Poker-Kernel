<?php

namespace App\Tests\Evaluator;

use ForestYeti\PokerKernel\CardDeck\Enum\CardRankEnum;
use ForestYeti\PokerKernel\CardDeck\Enum\CardSuitEnum;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Enum\HoldemCombinationScoreEnum;
use ForestYeti\PokerKernel\Evaluator\Exception\CombinationNotResolvedException;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\StraightResolver;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class StraightResolverTest extends TestCase
{
    /**
     * @param Card[] $playingCards
     * @param int[]  $expectedRanks
     */
    #[DataProvider('straightProvider')]
    public function testStraightResolver(array $playingCards, array $expectedRanks): void
    {
        $result = (new StraightResolver())
            ->setBaseScore(HoldemCombinationScoreEnum::Straight->value)
            ->resolve($playingCards, new Player('Mock'));

        self::assertCount(5, $result->getPlayingCards());

        $ranks = array_map(
            static fn (Card $c) => $c->getRank()->value,
            $result->getPlayingCards()
        );

        self::assertSame($expectedRanks, $ranks);
    }

    public function testStraightNotResolved(): void
    {
        $this->expectException(CombinationNotResolvedException::class);

        $cards = [
            new Card(CardRankEnum::Ace, CardSuitEnum::Hearts),
            new Card(CardRankEnum::King, CardSuitEnum::Diamonds),
            new Card(CardRankEnum::Ten, CardSuitEnum::Spades),
            new Card(CardRankEnum::Eight, CardSuitEnum::Clubs),
            new Card(CardRankEnum::Four, CardSuitEnum::Hearts),
            new Card(CardRankEnum::Two, CardSuitEnum::Clubs),
            new Card(CardRankEnum::Nine, CardSuitEnum::Diamonds),
        ];

        (new StraightResolver())
            ->setBaseScore(HoldemCombinationScoreEnum::Straight->value)
            ->resolve($cards, new Player('Mock'));
    }

    public static function straightProvider(): array
    {
        return [
            // Broadway straight, from Ten to Ace
            [
                [
                    new Card(CardRankEnum::Ace, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::King, CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::Queen, CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Jack, CardSuitEnum::Spades),
                    new Card(CardRankEnum::Ten, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Two, CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Nine, CardSuitEnum::Diamonds),
                ],
                [CardRankEnum::Ace->value, CardRankEnum::King->value, CardRankEnum::Queen->value, CardRankEnum::Jack->value, CardRankEnum::Ten->value],
            ],
            // Wheel straight, from A to Five
            [
                [
                    new Card(CardRankEnum::Ace, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Two, CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::Three, CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Four, CardSuitEnum::Spades),
                    new Card(CardRankEnum::Five, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Nine, CardSuitEnum::Clubs),
                    new Card(CardRankEnum::King, CardSuitEnum::Diamonds),
                ],
                [CardRankEnum::Five->value, CardRankEnum::Four->value, CardRankEnum::Three->value, CardRankEnum::Two->value, CardRankEnum::Ace->value],
            ],
            // Middle straight, from Nine to King
            [
                [
                    new Card(CardRankEnum::Nine, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Ten, CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::Jack, CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Queen, CardSuitEnum::Spades),
                    new Card(CardRankEnum::King, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Two, CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Four, CardSuitEnum::Diamonds),
                ],
                [CardRankEnum::King->value, CardRankEnum::Queen->value, CardRankEnum::Jack->value, CardRankEnum::Ten->value, CardRankEnum::Nine->value],
            ],
        ];
    }
}
