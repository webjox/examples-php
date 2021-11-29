<?php

use common\helpers\FormHelper;
use common\models\data\Order;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\data\Order */

$isPaid = $model->payment_status == Order::PAYMENT_STATUS_COMPLETE;

$html = '<b>' . Yii::t('app', 'Order') . ' № ' . ($model->iiko_order_number ?? $model->order_number) . ' от ' . $model->created_at . '</b>' . "\n";
$html .= "\n";
$html .= ('<b>' . Yii::t('app', $isPaid ? 'Is paid' : 'Is not paid') . '</b> <i>' . ArrayHelper::getValue(Order::paymentMethodOptions(), $model->payment_method, '') . '</i>' . "\n");
$html .= "\n";
$html .= ('+' . $model->phone . ' ' . '<b>' . ($model->customer->firstname ?? '') . ' ' . ($model->customer->email ?? '') . '</b>' . "\n");
$html .= "\n";
if (!empty($model->delivery_details['time'])) {
    $html .= (Yii::t('app', 'Delivery time') . ': <b>' . $model->delivery_details['date'] . ' ' . $model->delivery_details['time'] . '</b>');
} else {
    $html .= Yii::t('app', 'As soon as possible');
}
$html .= "\n";
if (!empty($model->delivery_details['address'])) {
    $html .= FormHelper::formatAddress($model->delivery_details['address']);
    $html .= "\n";
}
if (!empty($model->delivery_details['comments'])) {
    $html .= (Yii::t('app', 'Order comment') . ': ' . Html::encode($model->delivery_details['comments']));
    $html .= "\n";
}
$html .= "\n";
if($model->promocode_discount)
{
    $promocode = $model->promocode;
$html .= (Yii::t('app', 'Total') . ': <b>' . (int)$model->total . '</b>' . "\n");
    $html .= "Скидка по промокоду <b>$promocode: ". $model->promocode_discount.'</b>'. "\n";
}
$html .= (Yii::t('app', 'Total due') . ': <b>' . (int)$model->total_due . '</b>' . "\n");
$html .= "\n";
foreach ($model->items as $i => $item) {
    $html .= (($i + 1) . ' | ' . '<b>' . Html::encode($item['title']) . '</b>' . ' | ' . '<i>' . (isset($item['price']) ? str_pad((int)$item['price'], 5, ' ', STR_PAD_LEFT) : '     ') . '</i>' . ' | ' . str_pad($item['quantity'] ?? 1, 3, ' ', STR_PAD_LEFT) . ' | ' . '<b>' . $item['amount'] . '</b>' . "\n");
    foreach ($item['toppings'] as $topping){
        $html.= ("Топпинг".'|'.'<b>'.Html::encode($topping['name']).'</b>'.'|'.'<i>'.(isset($topping['price']) ? str_pad((int)$topping['price'], 5, ' ', STR_PAD_LEFT) : '     ') . '</i>'.'|'. str_pad($topping['amount'] ?? 1, 3, ' ', STR_PAD_LEFT) . "\n");
    }
    if ($item['type'] == 'set') {
        foreach ($item['groups'] as $group) {
            foreach ($group['ingredients'] as $ingredient) {
                $html .= ('   <i> - ' . Html::encode($ingredient['title']) . ' | ' . (int)$ingredient['price'] . ' | ' . ($ingredient['count'] ?? 1) . ' | ' . (isset($ingredient['paid']) ? (int)($ingredient['paid'] * $ingredient['price']) : 0) . '</i>' . "\n");
            }
        }
    }
}
$html .= "\n";
$html .= ('<b>' . Yii::t('app', 'Cuttlery quantity') . '</b>: ' . $model->cuttlery_quantity . "\n");
echo $html;
