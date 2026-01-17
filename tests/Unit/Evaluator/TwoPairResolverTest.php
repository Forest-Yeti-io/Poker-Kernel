<?php

namespace App\Tests\Evaluator;

use ForestYeti\PokerKernel\CardDeck\Enum\CardRankEnum;
use ForestYeti\PokerKernel\CardDeck\Enum\CardSuitEnum;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Enum\HoldemCombinationScoreEnum;
use ForestYeti\PokerKernel\Evaluator\Exception\CombinationNotResolvedException;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\TwoPairResolver;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TwoPairResolverTest extends TestCase
{
    /**
     * @param Card[] $playingCards
     * @param int[] $expectedPairRanksDesc
     */
    #[DataProvider('twoPairProvider')]
    public function testTwoPairResolver(array $playingCards, array $expectedPairRanksDesc, int $expectedKickerRank): void
    {
        $result = (new TwoPairResolver())
            ->setBaseScore(HoldemCombinationScoreEnum::TwoPair->value)
            ->resolve($playingCards, new Player('Mock'));

        self::assertCount(5, $result->getPlayingCards());

        $rankCounts = [];
        foreach ($result->getPlayingCards() as $card) {
            $value = $card->getRank()->value;
            $rankCounts[$value] = ($rankCounts[$value] ?? 0) + 1;
        }

        $pairs = [];
        $kicker = null;
        foreach ($rankCounts as $rankValue => $count) {
            if ($count === 2) {
                $pairs[] = $rankValue;
            } elseif ($count === 1) {
                $kicker = $rankValue;
            }
        }

        sort($pairs);
        $pairsDesc = array_reverse($pairs);

        self::assertSame($expectedPairRanksDesc, $pairsDesc, 'Two pair ranks must match (high to low)');
        self::assertSame($expectedKickerRank, $kicker, 'Kicker rank must match');
    }

    public function testTwoPairNotResolvedThrowsException(): void
    {
        $this->expectException(CombinationNotResolvedException::class);

        $playingCards = [
            // Тут только одна пара (AA)
            new Card(CardRankEnum::Ace,  CardSuitEnum::Hearts),
            new Card(CardRankEnum::Ace,  CardSuitEnum::Clubs),
            new Card(CardRankEnum::King, CardSuitEnum::Diamonds),
            new Card(CardRankEnum::Ten,  CardSuitEnum::Spades),
            new Card(CardRankEnum::Eight,CardSuitEnum::Diamonds),
            new Card(CardRankEnum::Four, CardSuitEnum::Diamonds),
            new Card(CardRankEnum::Two,  CardSuitEnum::Hearts),
        ];

        (new TwoPairResolver())
            ->setBaseScore(HoldemCombinationScoreEnum::TwoPair->value)
            ->resolve($playingCards, new Player('Mock'));
    }

    public static function twoPairProvider(): array
    {
        return [
            // AA + KK, Kicker T
            [
                [
                    new Card(CardRankEnum::Ace,  CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Ace,  CardSuitEnum::Clubs),
                    new Card(CardRankEnum::King, CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::King, CardSuitEnum::Spades),
                    new Card(CardRankEnum::Ten,  CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Eight,CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::Two,  CardSuitEnum::Clubs),
                ],
                [CardRankEnum::Ace->value, CardRankEnum::King->value],
                CardRankEnum::Ten->value,
            ],
            // JJ + 22, Kicker A
            [
                [
                    new Card(CardRankEnum::Jack, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Jack, CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Two,  CardSuitEnum::Spades),
                    new Card(CardRankEnum::Two,  CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Ace,  CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::Nine, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Four, CardSuitEnum::Clubs),
                ],
                [CardRankEnum::Jack->value, CardRankEnum::Two->value],
                CardRankEnum::Ace->value,
            ],
            // Кейс: на столе три пары — берём две старшие
            // QQ + TT + 55, Take QQ + TT, Kicker A
            [
                [
                    new Card(CardRankEnum::Queen, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Queen, CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Ten,   CardSuitEnum::Spades),
                    new Card(CardRankEnum::Ten,   CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::Four,  CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Four,  CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Ace,   CardSuitEnum::Diamonds),
                ],
                [CardRankEnum::Queen->value, CardRankEnum::Ten->value],
                CardRankEnum::Ace->value,
            ],
        ];
    }
}
