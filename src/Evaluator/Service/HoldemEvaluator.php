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

        foreach ($players as $player) {
            $playingCards = array_merge($player->getHandCards(), $boardCards);

            foreach ($this->resolvers as $resolver) {
                try {
                    $gameResult->addResolverResult($resolver->resolve($playingCards, $player));
                } catch (CombinationNotResolvedException) {
                    continue;
                }
            }
        }

        return $gameResult;
    }
}
