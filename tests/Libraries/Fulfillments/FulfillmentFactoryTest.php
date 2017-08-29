<?php

/**
 *    Copyright 2015-2017 ppy Pty. Ltd.
 *
 *    This file is part of osu!web. osu!web is distributed with the hope of
 *    attracting more community contributions to the core ecosystem of osu!.
 *
 *    osu!web is free software: you can redistribute it and/or modify
 *    it under the terms of the Affero GNU General Public License version 3
 *    as published by the Free Software Foundation.
 *
 *    osu!web is distributed WITHOUT ANY WARRANTY; without even the implied
 *    warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *    See the GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with osu!web.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Tests;

use App\Libraries\Fulfillments\FulfillmentFactory;
use App\Libraries\Fulfillments\SupporterTagFulfillment;
use App\Libraries\Fulfillments\UsernameChangeFulfillment;
use App\Models\Store\OrderItem;
use App\Models\Store\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Carbon\Carbon;
use TestCase;

class FulfillmentFactoryTests extends TestCase
{
    protected $connectionsToTransact = [
        'mysql',
        'mysql-store',
    ];

    /**
     * @expectedException \App\Exceptions\NotImplementedException
     */
    public function testGenericFulfillerIsNotImplementedYet()
    {
        $orderItem = factory(OrderItem::class)->create([
            'product_id' => factory(Product::class, 'master_tshirt')->create()->product_id,
        ]);
        $order = $orderItem->order;

        FulfillmentFactory::createFulfillersFor($order);
    }

    public function testCustomClassSupporterTag()
    {
        $orderItem = factory(OrderItem::class, 'supporter_tag')->create();
        $order = $orderItem->order;

        $fulfillers = FulfillmentFactory::createFulfillersFor($order);
        $this->assertSame(1, count($fulfillers));
        $this->assertSame(SupporterTagFulfillment::class, get_class($fulfillers[0]));
    }

    public function testCustomClassUsernameChange()
    {
        $orderItem = factory(OrderItem::class, 'username_change')->create();
        $order = $orderItem->order;

        $fulfillers = FulfillmentFactory::createFulfillersFor($order);
        $this->assertSame(1, count($fulfillers));
        $this->assertSame(UsernameChangeFulfillment::class, get_class($fulfillers[0]));
    }

    /**
     * @expectedException \App\Libraries\Fulfillments\InvalidFulfillerException
     */
    public function testCustomClassDoesNotExist()
    {
        $orderItem = factory(OrderItem::class)->create([
            'product_id' => factory(Product::class)->create(['custom_class' => 'derp-derp'])->product_id,
        ]);
        $order = $orderItem->order;

        FulfillmentFactory::createFulfillersFor($order);
    }
}
