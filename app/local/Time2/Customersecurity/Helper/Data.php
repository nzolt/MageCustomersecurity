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
 * Class Time2_Customersecurity_Helper_Data
 */
class Time2_Customersecurity_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Log failed login attempt if CustomerSecurity enabled
     *
     * @param $customer Time2_Customersecurity_Model_Customer
     * @param $email mixed string|null
     *
     * @return Time2_Customersecurity_Helper_Data
     */
    public function logFailedLoginAttempt($customer, $email = null)
    {
        if (Mage::helper('customersecurity/config')->isLockoutEnabled()) {
            $login_fail = Mage::getModel('customersecurity/login_fail');
            $login_fail->setCustomerData($customer, $email)->save();

            $customer->lockOutAccount();
        }

        return $this;
    }

    /**
     * Retrieve the phrase displayed on the website to let customers know how many minutes they have to wait until their account is unlocked
     *
     * @param $customer
     *
     * @return string
     */
    public function getAccountLockedAlertMessage($customer) {

        $message = "";
        if($customer->getLockedUntil()) {

            $locked_for = $customer->getLockedFor();
            if(!is_null($locked_for)) {
                if($locked_for <= 0) {
                    $message = $this->__("You've entered wrong password too many times. Less than a minute remaining until your account is unlocked.");
                } else if($locked_for == 1) {
                    $message = $this->__("You've entered wrong password too many times. 1 minute remaining until your account is unlocked.");
                } else {
                    $message = $this->__("You've entered wrong password too many times. %s minutes remaining until your account is unlocked.", $locked_for);
                }
            }
        }

        return $message;
    }

    /**
     * Clean locked_until for customer
     *
     * @param Mage_Customer_Model_Customer $customer
     *
     * @return Customersecurity_Model_Customer
     */
    public function cleanLockOut(Mage_Customer_Model_Customer $customer) {
        return $customer->setLockedUntil(null)
            ->save();
    }
}