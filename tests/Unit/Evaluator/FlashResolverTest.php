<?php

namespace App\Tests\Evaluator;

use ForestYeti\PokerKernel\CardDeck\Enum\CardRankEnum;
use ForestYeti\PokerKernel\CardDeck\Enum\CardSuitEnum;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Enum\CombinationScoreEnum;
use ForestYeti\PokerKernel\Evaluator\Exception\CombinationNotResolvedException;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\FlashResolver;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FlashResolverTest extends TestCase
{
    /**
     * @param Card[] $playingCards
     * @param int[] $expectedRanks
     */
    #[DataProvider('flashProvider')]
    public function testFlashResolver(array $playingCards, array $expectedRanks): void
    {
        $result = (new FlashResolver())
            ->setBaseScore(CombinationScoreEnum::Flash->value)
            ->resolve($playingCards, new Player('Mock'));

        self::assertCount(5, $result->getPlayingCards());

        $ranks = array_map(
            static fn (Card $card) => $card->getRank()->value,
            $result->getPlayingCards()
        );

        self::assertSame($expectedRanks, $ranks);
    }

    public function testFlashNotResolved(): void
    {
        $this->expectException(CombinationNotResolvedException::class);

        $cards = [
            new Card(CardRankEnum::Ace, CardSuitEnum::Hearts),
            new Card(CardRankEnum::King, CardSuitEnum::Diamonds),
            new Card(CardRankEnum::Queen, CardSuitEnum::Clubs),
            new Card(CardRankEnum::Jack, CardSuitEnum::Spades),
            new Card(CardRankEnum::Ten, CardSuitEnum::Hearts),
            new Card(CardRankEnum::Eight, CardSuitEnum::Diamonds),
            new Card(CardRankEnum::Two, CardSuitEnum::Clubs),
        ];

        (new FlashResolver())
            ->setBaseScore(CombinationScoreEnum::Flash->value)
            ->resolve($cards, new Player('Mock'));
    }

    public static function flashProvider(): array
    {
        return [
            [
                [
                    new Card(CardRankEnum::Ace, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::King, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Ten, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Eight, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Four, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Two, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Nine, CardSuitEnum::Clubs),
                ],
                [
                    CardRankEnum::Ace->value,
                    CardRankEnum::King->value,
                    CardRankEnum::Ten->value,
                    CardRankEnum::Eight->value,
                    CardRankEnum::Four->value,
                ],
            ],
        ];
    }
}
