<?php

namespace ForestYeti\PokerKernel\Evaluator\Service;

use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Enum\HoldemCombinationScoreEnum;
use ForestYeti\PokerKernel\Evaluator\Exception\CombinationNotResolvedException;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\FlashResolver;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\FourOfKindResolver;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\FullHouseResolver;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\HighCardResolver;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\PairResolver;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\ResolverInterface;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\RoyalFlashResolver;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\StraightFlashResolver;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\StraightResolver;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\ThreeOfKindResolver;
use ForestYeti\PokerKernel\Evaluator\Service\Resolver\TwoPairResolver;
use ForestYeti\PokerKernel\Evaluator\Dto\GameResult;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use ForestYeti\PokerKernel\Evaluator\ValueObject\ResolverResult;

class HoldemEvaluator implements EvaluatorInterface
{
    /**
     * @var ResolverInterface[]
     */
    private array $resolvers;

    public function __construct()
    {
        $this->resolvers[] = (new RoyalFlashResolver())->setBaseScore(HoldemCombinationScoreEnum::RoyalFlash->value);
        $this->resolvers[] = (new StraightFlashResolver())->setBaseScore(HoldemCombinationScoreEnum::StraightFlash->value);
        $this->resolvers[] = (new FourOfKindResolver())->setBaseScore(HoldemCombinationScoreEnum::FourOfKind->value);
        $this->resolvers[] = (new FullHouseResolver())->setBaseScore(HoldemCombinationScoreEnum::FullHouse->value);
        $this->resolvers[] = (new FlashResolver())->setBaseScore(HoldemCombinationScoreEnum::Flash->value);
        $this->resolvers[] = (new StraightResolver())->setBaseScore(HoldemCombinationScoreEnum::Straight->value);
        $this->resolvers[] = (new ThreeOfKindResolver())->setBaseScore(HoldemCombinationScoreEnum::ThreeOfKind->value);
        $this->resolvers[] = (new TwoPairResolver())->setBaseScore(HoldemCombinationScoreEnum::TwoPair->value);
        $this->resolvers[] = (new PairResolver())->setBaseScore(HoldemCombinationScoreEnum::Pair->value);
        $this->resolvers[] = (new HighCardResolver())->setBaseScore(HoldemCombinationScoreEnum::HighCard->value);
    }

    /**
     * @param Player[] $players
     * @param Card[] $boardCards
     */
    public function evaluate(array $players, array $boardCards): GameResult
    {
        $gameResult = new GameResult();
        $bestResults = [];

        foreach ($players as $player) {
            $playingCards = array_merge($player->getHandCards(), $boardCards);

            foreach ($this->resolvers as $resolver) {
                try {
                    $resolverResult = $resolver->resolve($playingCards, $player);
                    $gameResult->addResolverResult($resolverResult);

                    if (empty($bestResults)) {
                        $bestResults = [$resolverResult];
                        break;
                    }

                    $comparison = $this->compareResolverResults($resolverResult, $bestResults[0]);
                    if ($comparison > 0) {
                        $bestResults = [$resolverResult];
                    } elseif ($comparison === 0) {
                        $bestResults[] = $resolverResult;
                    }

                    break;
                } catch (CombinationNotResolvedException) {
                    continue;
                }
            }
        }

        $gameResult->setWinners($bestResults);

        return $gameResult;
    }

    private function compareResolverResults(ResolverResult $left, ResolverResult $right): int
    {
        if ($left->getCombinationScore() !== $right->getCombinationScore()) {
            return $left->getCombinationScore() <=> $right->getCombinationScore();
        }

        $leftRanks = $this->getComparisonRanks($left);
        $rightRanks = $this->getComparisonRanks($right);

        foreach ($leftRanks as $index => $rank) {
            $rightRank = $rightRanks[$index] ?? 0;
            if ($rank === $rightRank) {
                continue;
            }

            return $rank <=> $rightRank;
        }

        return 0;
    }

    /**
     * @return int[]
     */
    private function getComparisonRanks(ResolverResult $result): array
    {
        return array_map(
            static fn (Card $card) => $card->getRank()->value,
            $result->getPlayingCards()
        );
    }
}
