public function createOrder()
    {
        $this->scenario = self::SCENARIO_CREATE;

        if (!$this->load(\Yii::$app->request->post()) || !$this->validate()) {
            return false;
        }

        $this->order = new Order([
            'status'             => Order::STATUS_NEW,
            'customer_id'        => $this->user->id,
            'phone'              => $this->user->phone,
            'customer_confirmed' => true,
            'created'            => ($this->payment_method == Order::PAYMENT_METHOD_ON_PICKUP),
        ]);

        $this->order->load($this->attributes, '');

        $this->order->updateStatusHistory('status', Order::STATUS_NEW);

        $this->setBonusesAccrued();

        if (!$this->order->save()) {
            $this->addErrors($this->order->getErrors());

            return false;
        }

        $this->disposeBonuses($this->order);
        $addressFromReq = ArrayHelper::getValue($this->attributes, ['delivery_details', 'address',]);
        if (!$addressFromReq) {
            $this->addError('address', \Yii::t('app', 'Invalid address'));
            return false;
        }
        if(!empty($addressFromReq["restaurant_id"]))
        {
            $restaurant = Restaurant::findOne(['id' => $addressFromReq["restaurant_id"]]);
            if (!$restaurant) {
                $this->addError('address', \Yii::t('app', 'Invalid address'));
                return false;
            }
            $min_payment_amount = $restaurant->min_payment_amount;
        }
        else
        {
            $address = CustomerAddress::findOne(['id' => $addressFromReq['id']]);
            if (!$address || !$address->zone || !$address->zone->restaurant) {
                $this->addError('address', \Yii::t('app', 'Invalid address'));
                return false;
            }
            $min_payment_amount = $address->zone->min_payment_amount;
            $restaurant = $address->zone->restaurant;
        }

        $totalPrice = $this->order->total;
        // $totalPrice = array_reduce($this->order->items, function ($carry, $item) {return $carry + floatval($item['price']) * ($item['quantity'] ?: 1);}, 0);
        if ($totalPrice < ($min_payment_amount)) {
            $this->addError('items', \Yii::t('app', 'Total price is too low for chosen address.'));
            return false;
        }

        $this->createIikoOrder($this->order, $restaurant->iiko_terminal_id/*, $restaurant->iiko_login, $restaurant->iiko_password, $restaurant->iiko_organization*/);

        $this->order->refresh();

        $this->notifyManagers($this->order, ($restaurant->tlg_group_id ? ['chatIds' => $restaurant->tlg_group_id] : []));

        return $this->order;
    }