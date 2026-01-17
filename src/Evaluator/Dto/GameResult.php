<?php

namespace ForestYeti\PokerKernel\Evaluator\ValueObject;

class GameResult
{
    /**
     * @var ResolverResult[]
     */
    private array $resolverResults = [];

    private ?ResolverResult $winner = null;

    public function addResolverResult(ResolverResult $resolverResult): self
    {
        $this->resolverResults[] = $resolverResult;

        return $this;
    }

    public function getResolverResults(): array
    {
        return $this->resolverResults;
    }

    public function getWinner(): ?ResolverResult
    {
        return $this->winner;
    }

    public function setWinner(?ResolverResult $winner): self
    {
        $this->winner = $winner;

        return $this;
    }
}
