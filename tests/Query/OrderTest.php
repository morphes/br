<?php
namespace App\Tests\Query;

use App\Tests\GraphQLTesting;

/**
 * Class OrderTest
 *
 * @package App\Tests\Query
 */
class OrderTest extends GraphQLTesting
{
    public function testOrder()
    {
        $token = $this->getToken();
        $this->graphqlQuery(
            $this->getMutationTestQuery('addBasket'), $this->getMutationTestData('addBasket'), $token
        );

        $createOrder = $this->graphqlQuery(
            $this->getMutationTestQuery('createOrder'), $this->getMutationTestData('createOrder'), $token
        );
        $secretKey = $createOrder['data']['order']['secret_key'];

        $order = $this->graphqlQuery($this->getQueryTestQuery('order', $secretKey));

        $this->assertArraySubset([
            'data' => [
                'order' => [
                    'orderItems' => [

                    ],
                    'payment' => [

                    ],
                    'delivery' => [

                    ]
                ]
            ]
        ], $order);

        $orderItems = $order['data']['order']['orderItems'];
        $this->assertNotEmpty($orderItems);

        $payment = $order['data']['order']['payment'];
        $this->assertNotEmpty($payment);
        $this->assertNotEmpty($payment['id']);

        $delivery = $order['data']['order']['delivery'];
        $this->assertNotEmpty($delivery);
        $this->assertNotEmpty($delivery['id']);

        $this->assertArraySubset([
            'item' => [
                'productItemTagItems' => []
            ]
        ], $orderItems[0]);
    }
}
