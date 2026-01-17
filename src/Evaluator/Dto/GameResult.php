<?php

namespace ForestYeti\PokerKernel\Evaluator\Dto;

use ForestYeti\PokerKernel\Evaluator\ValueObject\ResolverResult;

class GameResult
{
    /**
     * @var ResolverResult[]
     */
    private array $resolverResults = [];

    /**
     * @var ResolverResult[]
     */
    private array $winners = [];

    public function addResolverResult(ResolverResult $resolverResult): self
    {
        $this->resolverResults[] = $resolverResult;

        return $this;
    }

    public function getResolverResults(): array
    {
        return $this->resolverResults;
    }

    /**
     * @return ResolverResult[]
     */
    public function getWinners(): array
    {
        return $this->winners;
    }

    /**
     * @param ResolverResult[] $winners
     */
    public function setWinners(array $winners): self
    {
        $this->winners = $winners;

        return $this;
    }
}
