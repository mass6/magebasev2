<?php

class Dtn_Mobiapp_Model_Cron {

    public function pushMessages() {
        $apn = Mage::getModel('mobiapp/abstract');
        $apn->processQueue();
    }

}
