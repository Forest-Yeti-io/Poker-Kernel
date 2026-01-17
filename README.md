# Poker Kernel

Библиотека для работы с покерными правилами и расчётами в стиле Texas Hold’em:

- определение комбинаций и победителей раздачи;
- расчёт equity для игроков;
- генерация и перемешивание колоды.

Пакет опубликован в Composer как [`forest-yeti/poker-kernel`](https://packagist.org/packages/forest-yeti/poker-kernel).

## Требования

- PHP **8.1+** (используются `enum`).

## Установка

```bash
composer require forest-yeti/poker-kernel
```

## Быстрый старт

### 1. Определение победителя раздачи

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use ForestYeti\PokerKernel\CardDeck\Service\CardPresenter;
use ForestYeti\PokerKernel\CardDeck\Service\Factory\HoldemCardDeckFactory;
use ForestYeti\PokerKernel\Evaluator\Service\HoldemEvaluator;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;
use ForestYeti\PokerKernel\Random\Service\SimpleRandomCardShuffler;

$evaluator = new HoldemEvaluator();
$shuffler = new SimpleRandomCardShuffler();
$presenter = new CardPresenter();

$deck = (new HoldemCardDeckFactory())->factory();
$deck = $shuffler->shuffle($deck);

$firstPlayer = new Player('P1', [$deck->pop(), $deck->pop()]);
$secondPlayer = new Player('P2', [$deck->pop(), $deck->pop()]);
$boardCards = [$deck->pop(), $deck->pop(), $deck->pop(), $deck->pop(), $deck->pop()];

$gameResult = $evaluator->evaluate([$firstPlayer, $secondPlayer], $boardCards);

foreach ($gameResult->getWinners() as $winner) {
    echo "Winner: {$winner->getPlayer()->getIdentifier()}" . PHP_EOL;
}

foreach ($boardCards as $card) {
    echo $presenter->preset($card) . ' ';
}
```

### 2. Расчёт equity

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use ForestYeti\PokerKernel\CardDeck\Enum\CardRankEnum;
use ForestYeti\PokerKernel\CardDeck\Enum\CardSuitEnum;
use ForestYeti\PokerKernel\CardDeck\ValueObject\Card;
use ForestYeti\PokerKernel\Evaluator\Service\HoldemEquityCalculator;
use ForestYeti\PokerKernel\Evaluator\ValueObject\Player;

$calculator = new HoldemEquityCalculator();

$playerOne = new Player('P1', [
    new Card(CardRankEnum::Ace, CardSuitEnum::Hearts),
    new Card(CardRankEnum::King, CardSuitEnum::Hearts),
]);

$playerTwo = new Player('P2', [
    new Card(CardRankEnum::Ten, CardSuitEnum::Spades),
    new Card(CardRankEnum::Ten, CardSuitEnum::Diamonds),
]);

$board = [
    new Card(CardRankEnum::Ten, CardSuitEnum::Hearts),
    new Card(CardRankEnum::Two, CardSuitEnum::Hearts),
    new Card(CardRankEnum::Nine, CardSuitEnum::Spades),
];

$result = $calculator->calculate($board, [$playerOne, $playerTwo]);

echo "P1 equity: {$result->getEquity($playerOne)}" . PHP_EOL;
echo "P2 equity: {$result->getEquity($playerTwo)}" . PHP_EOL;
```

### 3. Примеры из репозитория

В папке `example/` лежат готовые сценарии:

- `WinnerPlayerExample.php` — определение победителя;
- `RandomCombinationExample.php` — генерация случайной комбинации;
- `EquityExample.php` — расчёт equity.

Запуск:

```bash
php example/WinnerPlayerExample.php
```

## Расширение и кастомизация

### 1. Новый тасовщик колоды

Реализуйте интерфейс `RandomCardShufflerInterface` и подайте его в код, где требуется перемешивание:

```php
use ForestYeti\PokerKernel\CardDeck\Collection\CardDeck;
use ForestYeti\PokerKernel\Random\Service\RandomCardShufflerInterface;

final class MyShuffler implements RandomCardShufflerInterface
{
    public function shuffle(CardDeck $cardDeck): CardDeck
    {
        // Ваша логика перемешивания
        return $cardDeck;
    }
}
```

### 2. Своя фабрика колоды

Создайте класс, реализующий `CardDeckFactoryInterface`, если нужна нестандартная колода:

```php
use ForestYeti\PokerKernel\CardDeck\Collection\CardDeck;
use ForestYeti\PokerKernel\CardDeck\Service\Factory\CardDeckFactoryInterface;

final class ShortDeckFactory implements CardDeckFactoryInterface
{
    public function factory(): CardDeck
    {
        $deck = new CardDeck();
        // Заполнение укороченной колоды
        return $deck;
    }
}
```

### 3. Новый расчётчик/оценщик

Для другой разновидности игры можно реализовать:

- `EvaluatorInterface` — определение победителей и комбинаций;
- `EquityCalculatorInterface` — расчёт вероятностей (equity).

Это позволяет подключать альтернативную логику, не меняя текущие классы.

## Лицензия

MIT.
