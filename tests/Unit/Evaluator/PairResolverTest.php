<?php

namespace App\Tests\Evaluator;

use ForestYeti\PokerKernel\CardDeck\Enum\CardRankEnum;
use ForestYeti\PokerKernel\CardDeck\Enum\CardSuitEnum;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Enum\CombinationScoreEnum;
use ForestYeti\PokerKernel\Evaluator\Exception\CombinationNotResolvedException;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\PairResolver;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PairResolverTest extends TestCase
{
    /**
     * @param Card[] $playingCards
     * @param int[] $expectedKickersRankValues
     */
    #[DataProvider('pairProvider')]
    public function testPairResolver(array $playingCards, CardRankEnum $expectedPairRank, array $expectedKickersRankValues): void
    {
        $resolveResult = (new PairResolver())
            ->setBaseScore(CombinationScoreEnum::Pair->value)
            ->resolve($playingCards, new Player('Mock'));

        // Пара
        self::assertCount(5, $resolveResult->getPlayingCards());
        $pairCards = array_values(array_filter(
            $resolveResult->getPlayingCards(),
            static fn (Card $c) => $c->getRank() === $expectedPairRank
        ));
        self::assertCount(2, $pairCards);

        // Кикеры
        $kickers = array_filter(
            $resolveResult->getPlayingCards(),
            static fn (Card $c) => $c->getRank() !== $expectedPairRank
        );

        self::assertCount(3, $kickers);

        $kickerRanks = array_values(array_map(
            static fn (Card $c) => $c->getRank()->value,
            array_values($kickers)
        ));

        self::assertSame($expectedKickersRankValues, $kickerRanks);
    }

    /**
     * @param Card[] $playingCards
     */
    #[DataProvider('noPairProvider')]
    public function testPairNotResolvedThrowsException(array $playingCards): void
    {
        $this->expectException(CombinationNotResolvedException::class);

        (new PairResolver())
            ->setBaseScore(CombinationScoreEnum::Pair->value)
            ->resolve($playingCards, new Player('Mock'));
    }

    public static function pairProvider(): array
    {
        return [
            // Pair of Aces
            [
                [
                    new Card(CardRankEnum::Ace,   CardSuitEnum::Hearts),
                    new Card(CardRankEnum::King,  CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::Four,  CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::Ace,   CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Two,   CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Ten,   CardSuitEnum::Spades),
                    new Card(CardRankEnum::Eight, CardSuitEnum::Diamonds),
                ],
                CardRankEnum::Ace,
                [CardRankEnum::King->value, CardRankEnum::Ten->value, CardRankEnum::Eight->value],
            ],
            // Pair of Jacks
            [
                [
                    new Card(CardRankEnum::Jack,  CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Jack,  CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Ace,   CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::King,  CardSuitEnum::Spades),
                    new Card(CardRankEnum::Nine,  CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Four,  CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::Two,   CardSuitEnum::Clubs),
                ],
                CardRankEnum::Jack,
                [CardRankEnum::Ace->value, CardRankEnum::King->value, CardRankEnum::Nine->value],
            ],
            // Pair of twos
            [
                [
                    new Card(CardRankEnum::Two,   CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Two,   CardSuitEnum::Spades),
                    new Card(CardRankEnum::Ace,   CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Queen, CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::Ten,   CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Nine,  CardSuitEnum::Spades),
                    new Card(CardRankEnum::Five,  CardSuitEnum::Clubs),
                ],
                CardRankEnum::Two,
                [CardRankEnum::Ace->value, CardRankEnum::Queen->value, CardRankEnum::Ten->value],
            ],
        ];
    }

    public static function noPairProvider(): array
    {
        return [
            [
                [
                    new Card(CardRankEnum::Ace,   CardSuitEnum::Hearts),
                    new Card(CardRankEnum::King,  CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::Queen, CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Jack,  CardSuitEnum::Spades),
                    new Card(CardRankEnum::Ten,   CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Eight, CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::Two,   CardSuitEnum::Clubs),
                ],
            ],
            [
                [
                    new Card(CardRankEnum::Ace,   CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Nine,  CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Seven, CardSuitEnum::Spades),
                    new Card(CardRankEnum::Six,   CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::Five,  CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Four,  CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Three, CardSuitEnum::Diamonds),
                ],
            ],
        ];
    }
}
