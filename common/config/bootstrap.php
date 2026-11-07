<?php

Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
// Private file storage (outside the web root; served through access-checked actions).
Yii::setAlias('@uploads', dirname(dirname(__DIR__)) . '/frontend/uploads');
