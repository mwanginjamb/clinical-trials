<?php

namespace frontend\services;

/**
 * Report of one validation run or import: one entry per Trials row plus file-level issues.
 */
class TrialImportResult
{
    public const STATUS_OK = 'ok';            // valid (dry run) or imported
    public const STATUS_ERROR = 'error';      // not imported: fix the messages
    public const STATUS_SKIPPED = 'skipped';  // duplicate protocol number: deliberately not imported

    /** @var bool true = nothing was saved (validation run) */
    public bool $dryRun;

    /**
     * @var array<string, array{ref:string,row:int,title:string,status:string,messages:string[],trialId:?int,children:array<string,int>}>
     *      keyed by trial_ref
     */
    private array $trials = [];

    /** @var array<int, array{level:string,message:string}> problems not tied to one trial */
    private array $fileIssues = [];

    public function __construct(bool $dryRun)
    {
        $this->dryRun = $dryRun;
    }

    public function addTrial(string $ref, int $row, string $title): void
    {
        $this->trials[$ref] = [
            'ref' => $ref,
            'row' => $row,
            'title' => $title,
            'status' => self::STATUS_OK,
            'messages' => [],
            'trialId' => null,
            'children' => [],
        ];
    }

    public function hasTrial(string $ref): bool
    {
        return isset($this->trials[$ref]);
    }

    public function addTrialError(string $ref, string $message): void
    {
        $this->trials[$ref]['messages'][] = $message;
        if ($this->trials[$ref]['status'] !== self::STATUS_SKIPPED) {
            $this->trials[$ref]['status'] = self::STATUS_ERROR;
        }
    }

    public function skipTrial(string $ref, string $reason): void
    {
        $this->trials[$ref]['status'] = self::STATUS_SKIPPED;
        $this->trials[$ref]['messages'] = [$reason];
    }

    public function trialStatus(string $ref): string
    {
        return $this->trials[$ref]['status'];
    }

    public function setSaved(string $ref, ?int $trialId, array $children): void
    {
        $this->trials[$ref]['trialId'] = $this->dryRun ? null : $trialId;
        $this->trials[$ref]['children'] = array_filter($children);
    }

    public function addFileIssue(string $message, string $level = 'error'): void
    {
        $this->fileIssues[] = ['level' => $level, 'message' => $message];
    }

    /** @return array<int, array> trials in sheet order */
    public function getTrials(): array
    {
        return array_values($this->trials);
    }

    public function getFileIssues(): array
    {
        return $this->fileIssues;
    }

    public function count(string $status): int
    {
        return count(array_filter($this->trials, fn($t) => $t['status'] === $status));
    }

    public function okCount(): int
    {
        return $this->count(self::STATUS_OK);
    }

    /** A fatal file problem (bad file, missing Trials sheet…) stops everything. */
    public function hasFatal(): bool
    {
        return (bool) array_filter($this->fileIssues, fn($i) => $i['level'] === 'fatal');
    }

    public function hasProblems(): bool
    {
        return $this->count(self::STATUS_ERROR) > 0
            || (bool) array_filter($this->fileIssues, fn($i) => $i['level'] !== 'info');
    }
}
