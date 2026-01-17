<?php

namespace App\Tests\Evaluator;

use ForestYeti\PokerKernel\CardDeck\Enum\CardRankEnum;
use ForestYeti\PokerKernel\CardDeck\Enum\CardSuitEnum;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Enum\CombinationScoreEnum;
use ForestYeti\PokerKernel\Evaluator\Exception\CombinationNotResolvedException;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\SetResolver;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\ThreeOfKindResolver;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ThreeOfKindResolverTest extends TestCase
{
    /**
     * @param Card[] $playingCards
     * @param int[] $expectedKickersRankValues
     */
    #[DataProvider('setProvider')]
    public function testSetResolver(array $playingCards, CardRankEnum $expectedSetRank, array $expectedKickersRankValues): void
    {
        $result = (new ThreeOfKindResolver())
            ->setBaseScore(CombinationScoreEnum::ThreeOfKind->value)
            ->resolve($playingCards, new Player('Mock'));

        self::assertCount(5, $result->getPlayingCards());

        $cards = $result->getPlayingCards();

        // Сет (3 карты нужного ранга)
        $setCards = array_values(array_filter(
            $cards,
            static fn (Card $c) => $c->getRank() === $expectedSetRank
        ));
        self::assertCount(3, $setCards);

        // Кикеры (2 карты не этого ранга)
        $kickers = array_values(array_filter(
            $cards,
            static fn (Card $c) => $c->getRank() !== $expectedSetRank
        ));
        self::assertCount(2, $kickers);

        $kickerRanks = array_values(array_map(
            static fn (Card $c) => $c->getRank()->value,
            $kickers
        ));

        self::assertSame($expectedKickersRankValues, $kickerRanks);
    }

    public function testSetNotResolvedThrowsException(): void
    {
        $this->expectException(CombinationNotResolvedException::class);

        $playingCards = [
            // Нет сета, только пара
            new Card(CardRankEnum::Ace,  CardSuitEnum::Hearts),
            new Card(CardRankEnum::Ace,  CardSuitEnum::Clubs),
            new Card(CardRankEnum::King, CardSuitEnum::Diamonds),
            new Card(CardRankEnum::Ten,  CardSuitEnum::Spades),
            new Card(CardRankEnum::Eight,CardSuitEnum::Diamonds),
            new Card(CardRankEnum::Four, CardSuitEnum::Diamonds),
            new Card(CardRankEnum::Two,  CardSuitEnum::Hearts),
        ];

        (new ThreeOfKindResolver())
            ->setBaseScore(CombinationScoreEnum::ThreeOfKind->value)
            ->resolve($playingCards, new Player('Mock'));
    }

    public static function setProvider(): array
    {
        return [
            // Three of kind aces, kickers K Q
            [
                [
                    new Card(CardRankEnum::Ace,   CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Ace,   CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Ace,   CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::King,  CardSuitEnum::Spades),
                    new Card(CardRankEnum::Queen, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Ten,   CardSuitEnum::Spades),
                    new Card(CardRankEnum::Two,   CardSuitEnum::Clubs),
                ],
                CardRankEnum::Ace,
                [CardRankEnum::King->value, CardRankEnum::Queen->value],
            ],
            // Three of kind jacks, kickers A K
            [
                [
                    new Card(CardRankEnum::Jack,  CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Jack,  CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Jack,  CardSuitEnum::Spades),
                    new Card(CardRankEnum::Ace,   CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::King,  CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Nine,  CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Two,   CardSuitEnum::Spades),
                ],
                CardRankEnum::Jack,
                [CardRankEnum::Ace->value, CardRankEnum::King->value],
            ],

            // Кейс: два сета в 7 картах — берём старший сет
            // Two three of kind -> take top (QQQ over 777), kickers A K
            [
                [
                    new Card(CardRankEnum::Queen, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Queen, CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Queen, CardSuitEnum::Diamonds),
                    new Card(CardRankEnum::Seven, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Seven, CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Seven, CardSuitEnum::Spades),
                    new Card(CardRankEnum::Ace,   CardSuitEnum::Spades),
                ],
                CardRankEnum::Queen,
                // Kickers rules: A + (лучший из оставшихся, т.е. 7)
                [CardRankEnum::Ace->value, CardRankEnum::Seven->value],
            ],
        ];
    }
}
