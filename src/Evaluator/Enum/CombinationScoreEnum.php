<?php

namespace ForestYeti\PokerKernel\Evaluator\Enum;

enum CombinationScoreEnum: int
{
    case HighCard = 1;
    case Pair = 100;
    case TwoPair = 200;
    case ThreeOfKind = 300;
    case Straight = 400;
    case Flash = 500;
}
