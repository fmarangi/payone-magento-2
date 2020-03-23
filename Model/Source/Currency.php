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
 * @copyright 2003 - 2018 Payone GmbH
 * @license   <http://www.gnu.org/licenses/> GNU Lesser General Public License
 * @link      http://www.payone.de
 */

namespace Payone\Core\Model\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * Source class for currency to transmit
 */
class Currency implements ArrayInterface
{

    /**
     * Store object
     *
     * @var Context
     */
    private $context;

    /**
     * Constructor
     *
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Returns current base currency code
     * Seems like Magento2 framework doesn't have a reliable method to get the current scope in backend configuration..
     *
     * @return string
     */
    protected function getBaseCurrencyCode()
    {
        $aRequestParams = $this->context->getRequest()->getParams();
        $oStoreManager = $this->context->getStoreManager();
        if (isset($aRequestParams['website'])) {
            return $oStoreManager->getWebsite($aRequestParams['website'])->getBaseCurrencyCode();
        }

        if (isset($aRequestParams['store'])) {
            return $oStoreManager->getStore($aRequestParams['store'])->getBaseCurrencyCode();
        }

        return $oStoreManager->getStore()->getBaseCurrencyCode();
    }

    /**
     * Return currency options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'base',
                'label' => __('Base Currency').' ('.$this->getBaseCurrencyCode().')'
            ],
            [
                'value' => 'display',
                'label' => __('Display Currency')
            ]
        ];
    }
}
