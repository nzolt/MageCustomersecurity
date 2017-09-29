<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Time2
 * @package    Time2_Customersecurity
 * @author     Time2 Digital Limited <zoltan.nagy@time2.digital>
 * @copyright  Copyright (c) 2016 Time2 Digital Limited (http://www.visiondirect.co.uk)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Time2_Customersecurity_Model_Login_Fail
 */
class Time2_Customersecurity_Model_Login_Fail extends Mage_Core_Model_Abstract
{
    /**
     * Time2_Customersecurity_Model_Login_Fail constructor
     */
    protected function _construct()
    {
        $this->_init('customersecurity/login_fail');
    }

    /**
     * @param $customer Time2_Customersecurity_Model_Customer
     * @param $email mixed string|null
     * @return $this Time2_Customersecurity_Model_Login_Fail
     */
    public function setCustomerData($customer, $email = null) {

        if(!$email) {
            $email = $customer->getEmail();
        }

        $this->setLoginName($email)
            ->setCustomerId($customer->getId())
        ;

        return $this;
    }

    /**
     * Processing object before save data
     *
     * @return Time2_Customersecurity_Model_Login
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->setIp(Mage::helper('core/http')->getRemoteAddr());
        $this->setCreatedAt(Mage::getSingleton('core/date')->gmtDate(Varien_Date::DATETIME_PHP_FORMAT));
        $this->setStoreId(Mage::app()->getStore()->getStoreId());

        return $this;
    }

}