<?php

namespace common\library;

use Yii;
use yii\base\Component;
use yii\helpers\Url;

class WizardComponent extends Component
{
    /**
     * Session key used to store wizard model IDs
     */
    public string $sessionKey = 'wizardModels';

    /**
     * Return all configured steps
     */
    public function getSteps(): array
    {
        return Yii::$app->params['steps'] ?? [];
    }

    /**
     * Get current active step index
     */
    public function getActiveIndex(): int
    {
        $steps = $this->getSteps();

        $currentController = Yii::$app->controller->id;
        $currentAction = Yii::$app->controller->action->id;

        foreach ($steps as $i => $step) {

            if (
                $step['controller'] === $currentController
                &&
                in_array(
                    $currentAction,
                    ['create', 'update']
                )
            ) {
                return $i;
            }
        }

        return 0;
    }

    /**
     * Save model ID for a step
     */
    public function registerModel(
        string $controller,
        int|string $id
    ): void {

        $models = Yii::$app->session->get(
            $this->sessionKey,
            []
        );

        $models[$controller] = $id;

        Yii::$app->session->set(
            $this->sessionKey,
            $models
        );
    }

    /**
     * Get stored model ID
     */
    public function getModelId(
        string $controller
    ): int|string|null {

        $models = Yii::$app->session->get(
            $this->sessionKey,
            []
        );

        return $models[$controller] ?? null;
    }

    /**
     * Determine if step is completed
     */
    public function isCompleted(
        int $stepIndex
    ): bool {

        return $stepIndex < $this->getActiveIndex();
    }

    /**
     * Determine if step is active
     */
    public function isActive(
        int $stepIndex
    ): bool {

        return $stepIndex === $this->getActiveIndex();
    }

    /**
     * Determine if step is upcoming
     */
    public function isUpcoming(
        int $stepIndex
    ): bool {

        return $stepIndex > $this->getActiveIndex();
    }

    /**
     * Generate URL for a step
     */
    public function getStepUrl(
        array $step,
        int $stepIndex
    ): ?string {

        $isCompleted = $this->isCompleted($stepIndex);

        $isActive = $this->isActive($stepIndex);

        // Completed steps -> update
        if ($isCompleted) {

            $id = $this->getModelId(
                $step['controller']
            );

            if (!$id) {
                return null;
            }

            return Url::to(array_merge(
                $step['updateUrl'],
                [
                    'id' => $id,
                ]
            ));
        }

        // Current step
        if ($isActive) {
            return Url::current();
        }

        // Future step disabled
        return null;
    }

    /**
     * Get visible steps window
     */
    public function getVisibleSteps(
        int $maxVisible = 3
    ): array {

        $steps = $this->getSteps();

        $activeIndex = $this->getActiveIndex();

        $totalSteps = count($steps);

        $start = max(0, $activeIndex - 1);

        $end = min(
            $totalSteps - 1,
            $start + ($maxVisible - 1)
        );

        $start = max(
            0,
            $end - ($maxVisible - 1)
        );

        return array_slice(
            $steps,
            $start,
            $maxVisible,
            true
        );
    }

    /**
     * Progress percentage
     */
    public function getProgressPercentage(): int
    {
        $steps = $this->getSteps();

        $total = count($steps);

        if ($total === 0) {
            return 0;
        }

        return (int) round(
            (
                ($this->getActiveIndex() + 1)
                / $total
            ) * 100
        );
    }
}