<?php

namespace frontend\services;

/**
 * Collects a per-batch report so the controller/view can show the user exactly what
 * happened: which trial_refs imported (with the new trial IDs) and which failed and why.
 */
class TrialImportResult
{
    /** @var array<int, array{ref:string,id:int}> */
    private array $successes = [];

    /** @var array<int, array{context:string,message:string}> */
    private array $errors = [];

    /** @var string[] */
    private array $fatals = [];

    public function addSuccess(string $ref, int $trialId): void
    {
        $this->successes[] = ['ref' => $ref, 'id' => $trialId];
    }

    public function addError(string $context, string $message): void
    {
        $this->errors[] = ['context' => $context, 'message' => $message];
    }

    public function addFatal(string $message): void
    {
        $this->fatals[] = $message;
    }

    public function getSuccesses(): array
    {
        return $this->successes;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFatals(): array
    {
        return $this->fatals;
    }

    public function successCount(): int
    {
        return count($this->successes);
    }

    public function errorCount(): int
    {
        return count($this->errors) + count($this->fatals);
    }

    public function hasErrors(): bool
    {
        return $this->errorCount() > 0;
    }
}
