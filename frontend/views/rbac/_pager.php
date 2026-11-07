<?php

/**
 * Tailwind pagination footer for a data provider.
 *
 * @var yii\web\View $this
 * @var yii\data\DataProviderInterface $dataProvider
 */

use yii\widgets\LinkPager;

$pagination = $dataProvider->getPagination();
if ($pagination === false || $pagination->getPageCount() <= 1) {
    echo '<p class="px-6 py-[0.875rem] text-xs text-on-surface-variant">' . $dataProvider->getTotalCount() . ' total</p>';
    return;
}
?>
<div class="flex flex-col sm:flex-row items-center justify-between gap-2 px-6 py-[0.875rem] border-t border-outline-variant/15">
    <p class="text-xs text-on-surface-variant">
        <?= $pagination->getOffset() + 1 ?>–<?= min($pagination->getOffset() + $pagination->getLimit(), $dataProvider->getTotalCount()) ?>
        of <?= $dataProvider->getTotalCount() ?>
    </p>
    <?= LinkPager::widget([
        'pagination' => $pagination,
        'options' => ['class' => 'tw-pager flex flex-wrap items-center gap-1'],
        'linkContainerOptions' => ['class' => 'list-none'],
        'linkOptions' => ['class' => 'block min-w-[2rem] text-center px-2.5 py-1.5 rounded-lg text-xs font-semibold text-on-surface-variant hover:bg-surface-container'],
        'activePageCssClass' => 'tw-pager-active',
        'disabledPageCssClass' => 'opacity-40 pointer-events-none',
        'disabledListItemSubTagOptions' => ['class' => 'block min-w-[2rem] text-center px-2.5 py-1.5 text-xs text-on-surface-variant'],
        'prevPageLabel' => '‹',
        'nextPageLabel' => '›',
        'maxButtonCount' => 5,
    ]) ?>
</div>
