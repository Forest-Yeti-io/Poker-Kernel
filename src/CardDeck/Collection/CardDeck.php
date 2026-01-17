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
    public function __construct(
        array $cards = []
    ) {
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
}
