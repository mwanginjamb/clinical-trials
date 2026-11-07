<?php

namespace frontend\widgets;

use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\grid\DataColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;

/**
 * GridView with the app's Tailwind look (card shell, header row, filter inputs, sort links,
 * pager and summary), so index pages only declare their columns.
 *
 * Every column gets default th/td classes unless it sets its own `class` in
 * headerOptions/contentOptions. Data columns also get Tailwind filter inputs and sort links.
 *
 * Usage:
 *   <?= TailwindGridView::widget([
 *       'dataProvider' => $dataProvider,
 *       'filterModel'  => $searchModel,
 *       'columns'      => [['class' => SerialColumn::class], 'name', ['class' => TailwindActionColumn::class]],
 *   ]) ?>
 *
 * Classes are spelled out here (not built from FormUi) because Tailwind only scans
 * frontend/views, frontend/widgets and frontend/web/js. Avoid bare p/m/gap-3|4|5 while
 * bootstrap.css is still loaded (its !important utilities win).
 */
class TailwindGridView extends GridView
{
    public const TH = 'px-6 py-[0.875rem] text-[11px] font-bold text-on-surface-variant uppercase tracking-widest whitespace-nowrap align-middle';
    public const TD = 'px-6 py-[1rem] text-sm text-on-surface align-middle';
    public const INPUT = 'w-full min-w-[6rem] rounded-lg border border-outline-variant/30 bg-surface-container-lowest '
        . 'px-[0.75rem] py-[0.5rem] text-xs font-normal normal-case tracking-normal text-on-surface '
        . 'focus:border-primary focus:ring-1 focus:ring-primary';

    public $options = ['class' => 'tw-grid min-w-0 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm overflow-hidden'];

    public $tableOptions = ['class' => 'w-full text-left min-w-[640px]'];

    public $headerRowOptions = ['class' => 'bg-surface-container-low'];

    public $filterRowOptions = ['class' => 'bg-surface-container-low border-t border-outline-variant/10'];

    public $rowOptions = ['class' => 'border-t border-outline-variant/10 hover:bg-surface-container-low/60 transition-colors'];

    public $filterErrorOptions = ['class' => 'mt-1 text-[11px] font-medium normal-case tracking-normal text-error'];

    public $emptyTextOptions = ['class' => 'px-6 py-[2.5rem] text-center text-sm text-on-surface-variant'];

    public $summaryOptions = ['class' => 'text-xs text-on-surface-variant'];

    public $layout = "<div class=\"overflow-x-auto\">{items}</div>\n"
        . "<div class=\"flex flex-col sm:flex-row items-center justify-between gap-2 px-6 py-[0.875rem] border-t border-outline-variant/15\">{summary}{pager}</div>";

    public $pager = [
        'options' => ['class' => 'tw-pager flex flex-wrap items-center gap-1'],
        'linkContainerOptions' => ['class' => 'list-none'],
        'linkOptions' => ['class' => 'block min-w-[2rem] text-center px-2.5 py-1.5 rounded-lg text-xs font-semibold text-on-surface-variant hover:bg-surface-container'],
        'activePageCssClass' => 'tw-pager-active',
        'disabledPageCssClass' => 'opacity-40 pointer-events-none',
        'disabledListItemSubTagOptions' => ['class' => 'block min-w-[2rem] text-center px-2.5 py-1.5 text-xs text-on-surface-variant'],
        'prevPageLabel' => '‹',
        'nextPageLabel' => '›',
        'maxButtonCount' => 5,
    ];

    public function init()
    {
        if ($this->summary === null) {
            $this->summary = 'Showing <b>{begin, number}–{end, number}</b> of <b>{totalCount, number}</b> {totalCount, plural, one{record} other{records}}';
        }
        if ($this->emptyText === null) {
            $this->emptyText = 'No records found.';
        }
        parent::init();
    }

    protected function initColumns()
    {
        parent::initColumns();

        foreach ($this->columns as $column) {
            $right = $column instanceof ActionColumn;
            $narrow = $column instanceof SerialColumn || $column instanceof CheckboxColumn;

            if (!isset($column->headerOptions['class'])) {
                Html::addCssClass($column->headerOptions, self::TH . ($right ? ' text-right' : '') . ($narrow ? ' w-[4rem]' : ''));
            }
            if (!isset($column->filterOptions['class'])) {
                Html::addCssClass($column->filterOptions, 'px-6 py-[0.625rem] align-top');
            }
            if (!isset($column->contentOptions['class']) && !is_callable($column->contentOptions)) {
                Html::addCssClass($column->contentOptions, self::TD
                    . ($right ? ' text-right whitespace-nowrap' : '')
                    . ($narrow ? ' text-on-surface-variant' : ''));
            }

            if ($column instanceof DataColumn) {
                $column->filterInputOptions = array_merge($column->filterInputOptions, ['class' => self::INPUT]);
                Html::addCssClass($column->sortLinkOptions, 'inline-flex items-center gap-1 hover:text-primary');
            }
        }
    }
}
