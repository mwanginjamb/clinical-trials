<?php

use common\library\FormUi;
use frontend\models\Country;
use frontend\widgets\TailwindActionColumn;
use frontend\widgets\TailwindGridView;
use yii\grid\SerialColumn;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var frontend\models\TownSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Towns';
$this->params['breadcrumbs'][] = ['label' => 'Lookups'];
$this->params['breadcrumbs'][] = $this->title;

$countries = ArrayHelper::map(Country::find()->select(['id', 'name'])->orderBy('name')->asArray()->all(), 'id', 'name');
?>

<?= $this->render('../lookups/_header', [
    'title' => 'Towns',
    'subtitle' => 'Towns and cities, each linked to a country.',
    'actions' => FormUi::ctaButton('New town', 'add', ['create']),
]) ?>

<?= TailwindGridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'emptyText' => 'No towns match your filters.',
    'columns' => [
        ['class' => SerialColumn::class],
        [
            'attribute' => 'name',
            'contentOptions' => ['class' => TailwindGridView::TD . ' font-headline font-bold text-primary'],
        ],
        [
            'attribute' => 'country_id',
            'label' => 'Country',
            'value' => 'country.name',
            'filter' => $countries,
            'filterInputOptions' => ['prompt' => 'All countries'],
        ],
        [
            'attribute' => 'code',
            'headerOptions' => ['class' => TailwindGridView::TH . ' w-[10rem]'],
        ],
        [
            'class' => TailwindActionColumn::class,
            'confirmMessage' => static fn($m) => 'Delete town "' . $m->name . '"? This cannot be undone.',
        ],
    ],
]) ?>
