<?php

/**
 * PAYONE Magento 2 Connector is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PAYONE Magento 2 Connector is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with PAYONE Magento 2 Connector. If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category  Payone
 * @package   Payone_Magento2_Plugin
 * @author    FATCHIP GmbH <support@fatchip.de>
 * @copyright 2003 - 2017 Payone GmbH
 * @license   <http://www.gnu.org/licenses/> GNU Lesser General Public License
 * @link      http://www.payone.de
 */

namespace Payone\Core\Test\Unit\Model\Methods;

use Magento\Checkout\Model\Session;
use Payone\Core\Model\Methods\Paypal as ClassToTest;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Sales\Model\Order;
use Magento\Framework\Url;
use Payone\Core\Test\Unit\BaseTestCase;
use Payone\Core\Test\Unit\PayoneObjectManager;

class PaypalTest extends BaseTestCase
{
    /**
     * @var ClassToTest
     */
    private $classToTest;

    /**
     * @var ObjectManager|PayoneObjectManager
     */
    private $objectManager;

    /**
     * @var Session
     */
    private $checkoutSession;

    protected function setUp(): void
    {
        $this->objectManager = $this->getObjectManager();

        $this->checkoutSession = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPayoneWorkorderId', 'getIsPayonePayPalExpress'])
            ->getMock();
        $this->checkoutSession->method('getPayoneWorkorderId')->willReturn('12345');

        $url = $this->getMockBuilder(Url::class)->disableOriginalConstructor()->getMock();
        $url->method('getUrl')->willReturn('http://testdomain.org');

        $this->classToTest = $this->objectManager->getObject(ClassToTest::class, [
            'checkoutSession' => $this->checkoutSession,
            'url' => $url
        ]);
    }

    public function testGetPaymentSpecificParameters()
    {
        $order = $this->getMockBuilder(Order::class)->disableOriginalConstructor()->getMock();
        $this->checkoutSession->method('getIsPayonePayPalExpress')->willReturn(true);

        $result = $this->classToTest->getPaymentSpecificParameters($order);
        $expected = ['wallettype' => 'PPE', 'workorderid' => '12345'];
        $this->assertEquals($expected, $result);
    }

    public function testGetSuccessUrl()
    {
        $this->checkoutSession->method('getIsPayonePayPalExpress')->willReturn(true);
        $expected = 'http://testdomain.org';

        $result = $this->classToTest->getSuccessUrl();
        $this->assertEquals($expected, $result);
    }

    public function testGetSuccessUrlParent()
    {
        $this->checkoutSession->method('getIsPayonePayPalExpress')->willReturn(false);
        $expected = 'http://testdomain.org?incrementId=12345';

        $order = $this->getMockBuilder(Order::class)->disableOriginalConstructor()->getMock();
        $order->method('getIncrementId')->willReturn('12345');

        $result = $this->classToTest->getSuccessUrl($order);
        $this->assertEquals($expected, $result);
    }

    public function testGetCancelUrl()
    {
        $expected = 'http://testdomain.org';

        $result = $this->classToTest->getCancelUrl();
        $this->assertEquals($expected, $result);
    }

    public function testGetErrorUrl()
    {
        $expected = 'http://testdomain.org';

        $result = $this->classToTest->getErrorUrl();
        $this->assertEquals($expected, $result);
    }

    public function testFormatReferenceNumber()
    {
        $expected = 'prefix_000012345';
        $result = $this->classToTest->formatReferenceNumber($expected);
        $this->assertEquals($expected, $result);
    }
}
