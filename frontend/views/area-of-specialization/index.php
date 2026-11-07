<?php

use common\library\FormUi;
use frontend\widgets\TailwindActionColumn;
use frontend\widgets\TailwindGridView;
use yii\grid\SerialColumn;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var frontend\models\AreaOfSpecializationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Areas of Specialization';
$this->params['breadcrumbs'][] = ['label' => 'Lookups'];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('../lookups/_header', [
    'title' => 'Areas of Specialization',
    'subtitle' => 'Specialities used to classify trials.',
    'actions' => FormUi::ctaButton('New area of specialization', 'add', ['create']),
]) ?>

<?= TailwindGridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'emptyText' => 'No areas of specialization match your filters.',
    'columns' => [
        ['class' => SerialColumn::class],
        [
            'attribute' => 'title',
            'contentOptions' => ['class' => TailwindGridView::TD . ' font-headline font-bold text-primary'],
        ],
        [
            'attribute' => 'description',
            'value' => static fn($m) => StringHelper::truncate((string) $m->description, 90),
            'contentOptions' => ['class' => TailwindGridView::TD . ' text-on-surface-variant max-w-md'],
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
            'confirmMessage' => static fn($m) => 'Delete area of specialization "' . $m->title . '"? This cannot be undone.',
        ],
    ],
]) ?>
