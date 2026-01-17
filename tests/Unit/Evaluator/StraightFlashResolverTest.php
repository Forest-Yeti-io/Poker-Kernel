<?php

namespace App\Tests\Evaluator;

use ForestYeti\PokerKernel\CardDeck\Enum\CardRankEnum;
use ForestYeti\PokerKernel\CardDeck\Enum\CardSuitEnum;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Enum\HoldemCombinationScoreEnum;
use ForestYeti\PokerKernel\Evaluator\Exception\CombinationNotResolvedException;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\StraightFlashResolver;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class StraightFlashResolverTest extends TestCase
{
    /**
     * @param Card[] $playingCards
     * @param int[] $expectedRanks
     */
    #[DataProvider('straightFlashProvider')]
    public function testStraightFlashResolver(array $playingCards, array $expectedRanks): void
    {
        $result = (new StraightFlashResolver())
            ->setBaseScore(HoldemCombinationScoreEnum::StraightFlash->value)
            ->resolve($playingCards, new Player('Mock'));

        self::assertCount(5, $result->getPlayingCards());

        $ranks = array_map(
            static fn (Card $card) => $card->getRank()->value,
            $result->getPlayingCards()
        );

        self::assertSame($expectedRanks, $ranks);
    }

    public function testStraightFlashNotResolved(): void
    {
        $this->expectException(CombinationNotResolvedException::class);

        $cards = [
            new Card(CardRankEnum::Ace, CardSuitEnum::Hearts),
            new Card(CardRankEnum::King, CardSuitEnum::Hearts),
            new Card(CardRankEnum::Queen, CardSuitEnum::Hearts),
            new Card(CardRankEnum::Jack, CardSuitEnum::Hearts),
            new Card(CardRankEnum::Ten, CardSuitEnum::Hearts),
            new Card(CardRankEnum::Nine, CardSuitEnum::Clubs),
            new Card(CardRankEnum::Eight, CardSuitEnum::Diamonds),
        ];

        (new StraightFlashResolver())
            ->setBaseScore(HoldemCombinationScoreEnum::StraightFlash->value)
            ->resolve($cards, new Player('Mock'));
    }

    public static function straightFlashProvider(): array
    {
        return [
            [
                [
                    new Card(CardRankEnum::Nine, CardSuitEnum::Spades),
                    new Card(CardRankEnum::Eight, CardSuitEnum::Spades),
                    new Card(CardRankEnum::Seven, CardSuitEnum::Spades),
                    new Card(CardRankEnum::Six, CardSuitEnum::Spades),
                    new Card(CardRankEnum::Five, CardSuitEnum::Spades),
                    new Card(CardRankEnum::Ace, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Two, CardSuitEnum::Diamonds),
                ],
                [
                    CardRankEnum::Nine->value,
                    CardRankEnum::Eight->value,
                    CardRankEnum::Seven->value,
                    CardRankEnum::Six->value,
                    CardRankEnum::Five->value,
                ],
            ],
        ];
    }
}
