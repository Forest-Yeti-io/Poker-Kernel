<?php

namespace App\Tests\Evaluator;

use ForestYeti\PokerKernel\CardDeck\Enum\CardRankEnum;
use ForestYeti\PokerKernel\CardDeck\Enum\CardSuitEnum;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Enum\CombinationScoreEnum;
use ForestYeti\PokerKernel\Evaluator\Exception\CombinationNotResolvedException;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\FullHouseResolver;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FullHouseResolverTest extends TestCase
{
    /**
     * @param Card[] $playingCards
     */
    #[DataProvider('fullHouseProvider')]
    public function testFullHouseResolver(array $playingCards, CardRankEnum $expectedThreeRank, CardRankEnum $expectedPairRank): void
    {
        $result = (new FullHouseResolver())
            ->setBaseScore(CombinationScoreEnum::FullHouse->value)
            ->resolve($playingCards, new Player('Mock'));

        self::assertCount(5, $result->getPlayingCards());

        $cards = $result->getPlayingCards();

        $threeCards = array_values(array_filter(
            $cards,
            static fn (Card $card) => $card->getRank() === $expectedThreeRank
        ));
        self::assertCount(3, $threeCards);

        $pairCards = array_values(array_filter(
            $cards,
            static fn (Card $card) => $card->getRank() === $expectedPairRank
        ));
        self::assertCount(2, $pairCards);
    }

    public function testFullHouseNotResolved(): void
    {
        $this->expectException(CombinationNotResolvedException::class);

        $cards = [
            new Card(CardRankEnum::Ace, CardSuitEnum::Hearts),
            new Card(CardRankEnum::Ace, CardSuitEnum::Clubs),
            new Card(CardRankEnum::Ace, CardSuitEnum::Diamonds),
            new Card(CardRankEnum::King, CardSuitEnum::Spades),
            new Card(CardRankEnum::Ten, CardSuitEnum::Hearts),
            new Card(CardRankEnum::Nine, CardSuitEnum::Clubs),
            new Card(CardRankEnum::Two, CardSuitEnum::Diamonds),
        ];

        (new FullHouseResolver())
            ->setBaseScore(CombinationScoreEnum::FullHouse->value)
            ->resolve($cards, new Player('Mock'));
    }

    public static function fullHouseProvider(): array
    {
        return [
            [
                [
                    new Card(CardRankEnum::Ace, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Ace, CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Ace, CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::King, CardSuitEnum::Spades),
                    new Card(CardRankEnum::King, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Ten, CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Four, CardSuitEnum::Diamonds),
                ],
                CardRankEnum::Ace,
                CardRankEnum::King,
            ],
            [
                [
                    new Card(CardRankEnum::Queen, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Queen, CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Queen, CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::Seven, CardSuitEnum::Spades),
                    new Card(CardRankEnum::Seven, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Seven, CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Ace, CardSuitEnum::Diamonds),
                ],
                CardRankEnum::Queen,
                CardRankEnum::Seven,
            ],
        ];
    }
}
