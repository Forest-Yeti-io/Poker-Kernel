<?php

namespace ForestYeti\PokerKernel\CardDeck\Enum;

enum CardRankEnum: int
{
    case LowAce = 1;
    case Two = 2;
    case Three = 3;
    case Four = 4;
    case Five = 5;
    case Six = 6;
    case Seven = 7;
    case Eight = 8;
    case Nine = 9;
    case Ten = 10;
    case Jack = 11;
    case Queen = 12;
    case King = 13;
    case Ace = 14;

    public static function getHoldemRanks(): array
    {
        return [
            self::Two,
            self::Three,
            self::Four,
            self::Five,
            self::Six,
            self::Seven,
            self::Eight,
            self::Nine,
            self::Ten,
            self::Jack,
            self::Queen,
            self::King,
            self::Ace,
        ];
    }
}
