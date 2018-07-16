<?php

use system\components\Controller;

class SiteController extends Controller {

    public function actionIndex() {

        $this->render('template' , [
            'user' => 'john',
        ]);

    }
}