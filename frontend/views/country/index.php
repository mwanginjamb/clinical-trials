<?php

use common\library\FormUi;
use frontend\widgets\TailwindActionColumn;
use frontend\widgets\TailwindGridView;
use yii\grid\SerialColumn;

/** @var yii\web\View $this */
/** @var frontend\models\CountrySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Countries';
$this->params['breadcrumbs'][] = ['label' => 'Lookups'];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('../lookups/_header', [
    'title' => 'Countries',
    'subtitle' => 'Countries offered in trial, investigator and funding forms.',
    'actions' => FormUi::ctaButton('New country', 'add', ['create']),
]) ?>

<?= TailwindGridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'emptyText' => 'No countries match your filters.',
    'columns' => [
        ['class' => SerialColumn::class],
        [
            'attribute' => 'name',
            'contentOptions' => ['class' => TailwindGridView::TD . ' font-headline font-bold text-primary'],
        ],
        [
            'attribute' => 'code',
            'headerOptions' => ['class' => TailwindGridView::TH . ' w-[10rem]'],
        ],
        [
            'attribute' => 'updated_at',
            'label' => 'Last updated',
            'format' => 'date',
            'filter' => false,
            'contentOptions' => ['class' => TailwindGridView::TD . ' text-on-surface-variant whitespace-nowrap'],
        ],
        [
            'class' => TailwindActionColumn::class,
            'confirmMessage' => static fn($m) => 'Delete country "' . $m->name . '"? Its towns will be deleted too. This cannot be undone.',
        ],
    ],
]) ?>
