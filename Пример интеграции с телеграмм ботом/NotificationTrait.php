<?php

namespace common\traits;

use common\components\telegram\SendMessageTrait;
use common\models\data\Order;

/**
 * Trait NotificationTrait
 * @package common\traits
 */
trait NotificationTrait
{
    use SendMessageTrait;

    /**
     * @param \common\models\data\Order $order
     */
    private function notifyManagers(Order $order, $override)
    {
        if ($order->created === true) {
            \Yii::$app->setViewPath('@backend/views');
            $html = \Yii::$app->view->render('//orders/_order_tg_notification', [
                'model' => $order,
            ]);

            $data = [
                'message' => $html,
                'params'  => [
                    'parse_mode' => 'HTML',
                ],
            ];

            $this->sendViaBot('notify-orders', $override + $data);
        }
    }
}
