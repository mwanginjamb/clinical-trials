<?php

namespace common\library;

use frontend\models\ClinicalTrial;
use yii\base\Component;

class DashboardComponent extends Component {
    
    public function getAllTrials()
    {
        $count = ClinicalTrial::find()->count();
        return $count;
    }
}
