<?php

class Dtn_Mobiapp_DeliveryController extends Mage_Core_Controller_Front_Action
{
    public function slotAction()
    {
        $date = $this->getRequest()->getParam('date');
        $datetimestamp = strtotime($date);
        $date_array = getdate();
        $today = date('d-m-Y');
        $today = strtotime($today);
        $delivery = array("14-15","15-16","16-17","17-18","19-20","20-21");
        $dayofweek=array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
        if($today > $datetimestamp)
        {
            $this->getResponse()->setBody(json_encode(array('status'=>'error','message'=>'Date in past')));
            
        }
        elseif($today < $datetimestamp)
        {
            $date_array = getdate($datetimestamp);
            $daydate = $date_array['weekday'];
            if($daydate == "Sunday")
            {
                $this->getResponse()->setBody(json_encode(array('status'=>'error','message'=>'The day is Sunday')));
            }
            else
            {
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($delivery));
            }
        }
        else
        {
            $date_array = getdate();
            $daydate = $date_array['weekday'];            
            if($daydate == "Sunday")
            {
                $this->getResponse()->setBody(json_encode(array('status'=>'error','message'=>'Today is Sunday')));
            }
            else
            {
                $hours = $date_array['hours']+7;            
                if($hours<14)
                {
                    $this->getResponse()->setBody(json_encode(array('status'=>'error','message'=>'Too early')));
                }
                elseif($hours>21)
                {
                    $this->getResponse()->setBody(json_encode(array('status'=>'error','message'=>'Too late')));
                }
                else
                {
                    $p = array();
                    for($i=$hours;$i<21;$i++)
                    {
                        $p[] = ($i)."-".($i+1);
                    }
                    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($p));
                }
            }
        }
    }
}
