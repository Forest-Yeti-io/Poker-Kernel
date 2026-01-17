<?php

namespace ForestYeti\PokerKernel\Evaluator\Service\Resolver;

use ForestYeti\PokerKernel\CardDeck\Collection\CardDeck;
use ForestYeti\PokerKernel\CardDeck\Enum\CardRankEnum;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Enum\CombinationEnum;
use ForestYeti\PokerKernel\Evaluator\Exception\CombinationNotResolvedException;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use ForestYeti\PokerKernel\Evaluator\ValueObject\ResolverResult;

final class RoyalFlashResolver extends AbstractResolver
{
    public function getName(): string
    {
        return CombinationEnum::RoyalFlash->value;
    }

    /**
     * @param Card[] $playingCards
     * @throws CombinationNotResolvedException
     */
    public function resolve(array $playingCards, Player $player): ResolverResult
    {
        $deck = new CardDeck($playingCards);
        $groups = $deck->groupBySuit();

        $royalRanks = [
            CardRankEnum::Ten->value,
            CardRankEnum::Jack->value,
            CardRankEnum::Queen->value,
            CardRankEnum::King->value,
            CardRankEnum::Ace->value,
        ];

        foreach ($groups as $cards) {
            if (count($cards) < 5) {
                continue;
            }

            $rankMap = [];
            foreach ($cards as $card) {
                $rankMap[$card->getRank()->value] = $card;
            }

            $hasAll = true;
            foreach ($royalRanks as $rankValue) {
                if (!isset($rankMap[$rankValue])) {
                    $hasAll = false;
                    break;
                }
            }

            if (!$hasAll) {
                continue;
            }

            $resultCards = [];
            foreach ($royalRanks as $rankValue) {
                $resultCards[] = $rankMap[$rankValue];
            }

            return $this->result($player, $resultCards);
        }

        throw new CombinationNotResolvedException('Royal flash not resolved');
    }
}
