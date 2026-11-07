<?php

namespace frontend\widgets;

use Yii;
use yii\grid\ActionColumn;
use yii\helpers\Html;

/**
 * ActionColumn rendering Material Symbols icon buttons (view / edit / delete) in the
 * app's Tailwind style. Delete posts with a confirmation, as before.
 *
 * Set {@see $confirmMessage} to a callable `fn($model): string` for a per-row prompt.
 */
class TailwindActionColumn extends ActionColumn
{
    /** @var string|callable|null delete confirmation; callable receives the row model */
    public $confirmMessage;

    public $header = 'Actions';

    protected function initDefaultButtons()
    {
        $this->addButton('view', 'visibility', Yii::t('app', 'View'), 'hover:text-primary');
        $this->addButton('update', 'edit_square', Yii::t('app', 'Edit'), 'hover:text-secondary');
        $this->addButton('delete', 'delete', Yii::t('app', 'Delete'), 'hover:text-error', true);
    }

    private function addButton(string $name, string $icon, string $title, string $hover, bool $isDelete = false): void
    {
        if (isset($this->buttons[$name]) || strpos($this->template, '{' . $name . '}') === false) {
            return;
        }

        $this->buttons[$name] = function ($url, $model) use ($icon, $title, $hover, $isDelete) {
            $options = [
                'title' => $title,
                'aria-label' => $title,
                'data-pjax' => '0',
                'class' => 'inline-flex p-[0.5rem] rounded-lg text-outline hover:bg-surface-container transition-colors ' . $hover,
            ];
            if ($isDelete) {
                $message = is_callable($this->confirmMessage)
                    ? call_user_func($this->confirmMessage, $model)
                    : ($this->confirmMessage ?? Yii::t('app', 'Are you sure you want to delete this record?'));
                $options['data'] = ['confirm' => $message, 'method' => 'post'];
            }

            return Html::a(
                Html::tag('span', $icon, ['class' => 'material-symbols-outlined text-[20px]']),
                $url,
                array_merge($options, $this->buttonOptions)
            );
        };
    }
}
