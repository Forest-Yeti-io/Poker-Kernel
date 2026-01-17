<?php

namespace ForestYeti\PokerKernel\CardDeck\Collection;

use ForestYeti\PokerKernel\CardDeck\Exception\CardDeckException;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;

class CardDeck
{
    /**
     * @param Card[] $cards
     */
    private array $cards = [];

    /**
     * @param Card[] $cards
     */
    public function __construct(array $cards = [])
    {
        foreach ($cards as $card) {
            $this->push($card);
        }
    }

    /**
     * @throws CardDeckException
     */
    public function pop(): Card
    {
        if ($this->empty()) {
            throw new CardDeckException('Пустая игровая колода');
        }

        return array_shift($this->cards);
    }

    public function push(Card $card): CardDeck
    {
        $this->cards[$card->getHash()] = $card;

        return $this;
    }

    public function empty(): bool
    {
        return empty($this->cards);
    }

    public function count(): int
    {
        return count($this->cards);
    }

    public function get(Card $targetCard): ?Card
    {
        return $this->cards[$targetCard->getHash()] ?? null;
    }

    public function toArray(): array
    {
        return array_values($this->cards);
    }

    public function getSeed(): string
    {
        $seed = array_reduce(
            $this->cards,
            static fn (string $carry, Card $card) => "{$carry}:{$card->getHash()}}",
            ''
        );

        return md5($seed);
    }

    public function getSortedDesc(): CardDeck
    {
        $cards = $this->toArray();

        usort(
            $cards, static fn (Card $a, Card $b) => $b->getRank()->value <=> $a->getRank()->value
        );

        return new self($cards);
    }

    public function slice(int $offset = 0, int $length = 5): CardDeck
    {
        return new self(
            array_slice($this->cards, $offset, $length)
        );
    }

    public function groupByRank(): array
    {
        $groups = [];

        foreach ($this->toArray() as $card) {
            $groups[$card->getRank()->value][] = $card;
        }

        return $groups;
    }

    public function groupBySuit(): array
    {
        $groups = [];

        foreach ($this->toArray() as $card) {
            $groups[$card->getSuit()->value][] = $card;
        }

        return $groups;
    }

    /**
     * Уникальные rank values по убыванию (основано на сортировке по рангу).
     *
     * @return int[]
     */
    public function getUniqueRankValuesDesc(): array
    {
        $sorted = $this->getSortedDesc()->toArray();

        $result = [];
        foreach ($sorted as $card) {
            $rank = $card->getRank()->value;
            if (isset($result[$rank])) {
                continue;
            }

            $result[$rank] = $rank;
        }

        return array_values($result);
    }

    /**
     * Взять $count карт конкретного ранга
     *
     * @return Card[]
     */
    public function takeOfRank(string $rankValue, int $count): array
    {
        $groups = $this->groupByRank();
        if (!isset($groups[$rankValue])) {
            return [];
        }

        return array_slice($groups[$rankValue], 0, $count);
    }

    /**
     * Берём лучшие кикеры (по убыванию ранга), исключив заданные ранги.
     *
     * @param int[] $excludedRankValues
     * @return Card[]
     */
    public function takeKickersExcludingRanks(array $excludedRankValues, int $count): array
    {
        $excluded = array_fill_keys($excludedRankValues, true);

        $sorted = $this->getSortedDesc()->toArray();

        $kickers = [];
        foreach ($sorted as $card) {
            if (isset($excluded[$card->getRank()->value])) {
                continue;
            }

            $kickers[] = $card;
            if (count($kickers) === $count) {
                break;
            }
        }

        return $kickers;
    }
}
