<?php

use yii\helpers\Html;

/** @var \app\services\TrialImportResult $result */

$this->title = 'Import Results';
?>
<h1>
    <?= Html::encode($this->title) ?>
</h1>

<?php if ($result->getFatals()): ?>
    <div class="alert alert-danger">
        <?= implode('<br>', array_map('Html::encode', $result->getFatals())) ?>
    </div>
<?php endif; ?>

<h3>Imported (
    <?= $result->successCount() ?>)
</h3>
<table class="table table-sm">
    <thead>
        <tr>
            <th>trial_ref</th>
            <th>New trial ID</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($result->getSuccesses() as $row): ?>
            <tr>
                <td>
                    <?= Html::encode($row['ref']) ?>
                </td>
                <td>
                    <?= Html::a($row['id'], ['clinical-trial/view', 'id' => $row['id']]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h3>Failed (
    <?= count($result->getErrors()) ?>)
</h3>
<table class="table table-sm">
    <thead>
        <tr>
            <th>Row</th>
            <th>Reason</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($result->getErrors() as $row): ?>
            <tr>
                <td>
                    <?= Html::encode($row['context']) ?>
                </td>
                <td class="text-danger">
                    <?= Html::encode($row['message']) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>