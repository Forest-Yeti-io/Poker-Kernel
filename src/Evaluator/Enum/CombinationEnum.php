<?php

namespace ForestYeti\PokerKernel\Evaluator\Enum;

enum CombinationEnum: string
{
    case HighCard = 'HighCard';
    case Pair = 'Pair';
    case TwoPair = 'TwoPair';
    case ThreeOfKind = 'ThreeOfKind';
    case Straight = 'Straight';
    case Flash = 'Flash';
    case FullHouse = 'FullHouse';
    case FourOfKind = 'FourOfKind';
    case StraightFlash = 'StraightFlash';
    case RoyalFlash = 'RoyalFlash';
}
