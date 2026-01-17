<?php

namespace App\Tests\Evaluator;

use ForestYeti\PokerKernel\CardDeck\Enum\CardRankEnum;
use ForestYeti\PokerKernel\CardDeck\Enum\CardSuitEnum;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Enum\CombinationScoreEnum;
use ForestYeti\PokerKernel\Evaluator\Exception\CombinationNotResolvedException;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\RoyalFlashResolver;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RoyalFlashResolverTest extends TestCase
{
    /**
     * @param Card[] $playingCards
     */
    #[DataProvider('royalFlashProvider')]
    public function testRoyalFlashResolver(array $playingCards): void
    {
        $result = (new RoyalFlashResolver())
            ->setBaseScore(CombinationScoreEnum::RoyalFlash->value)
            ->resolve($playingCards, new Player('Mock'));

        self::assertCount(5, $result->getPlayingCards());

        $ranks = array_map(
            static fn (Card $card) => $card->getRank()->value,
            $result->getPlayingCards()
        );
        sort($ranks);

        self::assertSame(
            [
                CardRankEnum::Ten->value,
                CardRankEnum::Jack->value,
                CardRankEnum::Queen->value,
                CardRankEnum::King->value,
                CardRankEnum::Ace->value,
            ],
            $ranks
        );
    }

    public function testRoyalFlashNotResolved(): void
    {
        $this->expectException(CombinationNotResolvedException::class);

        $cards = [
            new Card(CardRankEnum::Nine, CardSuitEnum::Hearts),
            new Card(CardRankEnum::Eight, CardSuitEnum::Hearts),
            new Card(CardRankEnum::Seven, CardSuitEnum::Hearts),
            new Card(CardRankEnum::Six, CardSuitEnum::Hearts),
            new Card(CardRankEnum::Five, CardSuitEnum::Hearts),
            new Card(CardRankEnum::Ace, CardSuitEnum::Diamonds),
            new Card(CardRankEnum::King, CardSuitEnum::Clubs),
        ];

        (new RoyalFlashResolver())
            ->setBaseScore(CombinationScoreEnum::RoyalFlash->value)
            ->resolve($cards, new Player('Mock'));
    }

    public static function royalFlashProvider(): array
    {
        return [
            [
                [
                    new Card(CardRankEnum::Ace, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::King, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Queen, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Jack, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Ten, CardSuitEnum::Hearts),
                    new Card(CardRankEnum::Two, CardSuitEnum::Clubs),
                    new Card(CardRankEnum::Three, CardSuitEnum::Diamonds),
                ],
            ],
        ];
    }
}
