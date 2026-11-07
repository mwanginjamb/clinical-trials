<?php

use common\library\FormUi;
use common\models\User;
use frontend\widgets\TailwindActionColumn;
use frontend\widgets\TailwindGridView;
use yii\grid\SerialColumn;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var frontend\models\PublisherSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Publishers';
$this->params['breadcrumbs'][] = ['label' => 'Lookups'];
$this->params['breadcrumbs'][] = $this->title;

// usernames for the "Added by" column, one query for the page
$creatorIds = array_filter(array_unique(ArrayHelper::getColumn($dataProvider->getModels(), 'created_by')));
$usernames = $creatorIds
    ? ArrayHelper::map(User::find()->select(['id', 'username'])->where(['id' => $creatorIds])->asArray()->all(), 'id', 'username')
    : [];
?>

<?= $this->render('../lookups/_header', [
    'title' => 'Publishers',
    'subtitle' => 'Journals and publishers offered when recording trial publications.',
    'actions' => FormUi::ctaButton('New publisher', 'add', ['create']),
]) ?>

<?= TailwindGridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'emptyText' => 'No publishers match your filters.',
    'columns' => [
        ['class' => SerialColumn::class],
        [
            'attribute' => 'publisher',
            'contentOptions' => ['class' => TailwindGridView::TD . ' font-headline font-bold text-primary'],
        ],
        [
            'attribute' => 'created_at',
            'label' => 'Added on',
            'format' => 'date',
            'filter' => false,
            'contentOptions' => ['class' => TailwindGridView::TD . ' text-on-surface-variant whitespace-nowrap'],
        ],
        [
            'attribute' => 'created_by',
            'label' => 'Added by',
            'value' => static fn($m) => $usernames[$m->created_by] ?? null,
            'filter' => false,
            'contentOptions' => ['class' => TailwindGridView::TD . ' text-on-surface-variant'],
        ],
        [
            'class' => TailwindActionColumn::class,
            'confirmMessage' => static fn($m) => 'Delete publisher "' . $m->publisher . '"? This cannot be undone.',
        ],
    ],
]) ?>
