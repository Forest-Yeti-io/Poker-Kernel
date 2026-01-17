<?php

namespace App\Tests\Evaluator;

use ForestYeti\PokerKernel\CardDeck\Enum\CardRankEnum;
use ForestYeti\PokerKernel\CardDeck\Enum\CardSuitEnum;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Enum\HoldemCombinationScoreEnum;
use ForestYeti\PokerKernel\Evaluator\Exception\CombinationNotResolvedException;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\FourOfKindResolver;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FourOfKindResolverTest extends TestCase
{
    /**
     * @param Card[] $playingCards
     */
    #[DataProvider('fourOfKindProvider')]
    public function testFourOfKindResolver(array $playingCards, CardRankEnum $expectedFourRank, CardRankEnum $expectedKickerRank): void
    {
        $result = (new FourOfKindResolver())
            ->setBaseScore(HoldemCombinationScoreEnum::FourOfKind->value)
            ->resolve($playingCards, new Player('Mock'));

        self::assertCount(5, $result->getPlayingCards());

        $cards = $result->getPlayingCards();
        $fourCards = array_values(array_filter(
            $cards,
            static fn (Card $card) => $card->getRank() === $expectedFourRank
        ));

        self::assertCount(4, $fourCards);

        $kickerCards = array_values(array_filter(
            $cards,
            static fn (Card $card) => $card->getRank() === $expectedKickerRank
        ));
        self::assertCount(1, $kickerCards);
    }

    public function testFourOfKindNotResolved(): void
    {
        $this->expectException(CombinationNotResolvedException::class);

        $cards = [
            new Card(CardRankEnum::Ace, CardSuitEnum::Hearts),
            new Card(CardRankEnum::Ace, CardSuitEnum::Clubs),
            new Card(CardRankEnum::King, CardSuitEnum::Diamonds),
            new Card(CardRankEnum::King, CardSuitEnum::Spades),
            new Card(CardRankEnum::Queen, CardSuitEnum::Hearts),
            new Card(CardRankEnum::Ten, CardSuitEnum::Clubs),
            new Card(CardRankEnum::Two, CardSuitEnum::Diamonds),
        ];

        (new FourOfKindResolver())
            ->setBaseScore(HoldemCombinationScoreEnum::FourOfKind->value)
            ->resolve($cards, new Player('Mock'));
    }

    public static function fourOfKindProvider(): array
    {
        return [
            [
                [
                    new Card(CardRankEnum::King, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::King, CardSuitEnum::Clubs),
                    new Card(CardRankEnum::King, CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::King, CardSuitEnum::Spades),
                    new Card(CardRankEnum::Ace, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Ten, CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Two, CardSuitEnum::Diamonds),
                ],
                CardRankEnum::King,
                CardRankEnum::Ace,
            ],
        ];
    }
}
