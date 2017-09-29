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
 * Customer model override
 *
 * Class Time2_Customersecurity_Model_Customer
 *
 * @see Mage_Customer_Model_Customer
 */
class Time2_Customersecurity_Model_Customer extends Mage_Customer_Model_Customer {

    /**
     * Codes of exceptions related to customer model
     */
    const EXCEPTION_SECURITY_ACCOUNT_LOCKED = 5;

    /**
     * Authenticate customer
     *
     * @param  string $login
     * @param  string $password
     * @throws Mage_Core_Exception
     *
     * @return boolean
     *
     */
    public function authenticate($login, $password) {

        $this->loadByEmail($login);
        if ($this->getConfirmation() && $this->isConfirmationRequired()) {
            throw Mage::exception('Mage_Core', Mage::helper('customer')->__('This account is not confirmed.'),
                self::EXCEPTION_EMAIL_NOT_CONFIRMED
            );
        }

        /** @var Time2_Customersecurity_Helper_Data */
        $_helper = Mage::helper('customersecurity');
        $is_password_validated = $this->validatePassword($password);

        if (!$is_password_validated) {
            $errors = array(Mage::helper('customer')->__('Invalid login or password.'));
        }

        if(Mage::helper('customersecurity/config')->isLockoutEnabled()) {

            if($this->isAccountLockedOut()) {
                throw Mage::exception('Mage_Core', $_helper->getAccountLockedAlertMessage($this),
                    self::EXCEPTION_SECURITY_ACCOUNT_LOCKED
                );
            }

            if(!$is_password_validated) {
                // Log the failed login on wrong password
                $_helper->logFailedLoginAttempt($this, $login);

                if($this->getId()) {
                    $number_of_attempts = $this->getNumberOfAttemptsBeforeLocking();
                    if($number_of_attempts == 0) {
                        $errors[] = $_helper->getAccountLockedAlertMessage($this);
                    } else {
                        $lock_out_period = Mage::helper("customersecurity/config")->getLockoutPeriod();
                        if($number_of_attempts > 1) {
                            $message = Mage::helper('customersecurity')->__('<b>%s</b> attempts remaining before %s minute lockout.', $number_of_attempts, $lock_out_period);
                        } else {
                            $message = Mage::helper('customersecurity')->__('1 attempt remaining before %s minute lockout.', $lock_out_period);
                        }
                        $errors[] = "<span class=\"bar-message__description\">{$message}</span>";
                    }
                }
            }
        }

        if(!empty($errors)) {
            $message = join("<br>", $errors);
            throw Mage::exception('Mage_Core', $message, self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD);
        }

        Mage::dispatchEvent('customer_customer_authenticated', array(
            'model'    => $this,
            'password' => $password,
        ));

        return true;
    }

    /**
     * Check if account is locked out
     *
     * @return bool
     */
    public function isAccountLockedOut() {

        if(!$this->getId()) {
            return false;
        }

        $now = Mage::getSingleton('core/date')->date(Varien_Date::DATETIME_PHP_FORMAT);
        $lockedUntil = Mage::getSingleton('core/date')->date(Varien_Date::DATETIME_PHP_FORMAT, $this->getLockedUntil());

        $nowTimestamp = Mage::getSingleton('core/date')->gmtTimestamp($now);
        $lockedUntilTimestamp = Mage::getSingleton('core/date')->gmtTimestamp($lockedUntil);

        if($this->getLockedUntil()) {
            if($lockedUntilTimestamp > $nowTimestamp) {
                return true;
            } else {
                // Clean Lock if expired
                Mage::helper("customersecurity")->cleanLockOut($this);
            }
        }

        return false;
    }

    /**
     * Check number of login attempts and lock out the account if exceeded
     *
     * @return Mage_Customer_Model_Customer
     */
    public function lockOutAccount() {

        $_helper = Mage::helper("customersecurity/config");

        if(!$this->getId() OR !$_helper->isLockoutEnabled()) {
            return $this;
        }

        if($this->getNumberOfAttemptsBeforeLocking() == 0) {
            $nowGMT = Mage::getSingleton('core/date')->gmtDate();
            $now = new Zend_Date($nowGMT);
            $lockedUntil = $now->addMinute($_helper->getLockoutPeriod())->toString('YYYY-MM-dd HH:mm:ss');
            $this->setLockedUntil($lockedUntil)->save();
        }

        return $this;
    }

    /**
     * Retrieve the number of login attempts
     *
     * @return int
     */
    public function getNumberOfLoginAttempts() {

        if(!$this->getId()) {
            return 0;
        }

        if(!$this->getData("number_of_login_attempts")) {

            $fromGMT = Mage::getSingleton('core/date')->gmtDate();
            $from = new Zend_Date($fromGMT);
            $fromDate = $from->subMinute(Mage::helper("customersecurity/config")->getLoginPeriod())->toString('YYYY-MM-dd HH:mm:ss');
            $toDate = Mage::getSingleton('core/date')->gmtDate(Varien_Date::DATETIME_PHP_FORMAT);

            $number_of_attempts = Mage::getModel('customersecurity/login_fail')->getCollection()
                ->addFieldToFilter('customer_id', $this->getId())
                ->addFieldToFilter('created_at', array('from' => $fromDate, 'to' => $toDate))
                ->count()
            ;

            $this->setData("number_of_login_attempts", $number_of_attempts);
        }

        return $this->getData("number_of_login_attempts");
    }

    /**
     * Retrieve the number of login attempts before locking the account
     *
     * @return int
     */
    public function getNumberOfAttemptsBeforeLocking() {

        $total_number_of_attempts = Mage::helper("customersecurity/config")->getAllowedAttempts();

        if(!$this->getId()) {
            return $total_number_of_attempts;
        }

        $number_of_attempts = $this->getNumberOfLoginAttempts();
        $remaining_attempts = $total_number_of_attempts - $number_of_attempts;

        return $remaining_attempts <= 0 ? 0 : $remaining_attempts;
    }

    /**
     * Retrieve the number of minutes before unlocking the account
     *
     * @return mixed int|null
     */
    public function getLockedFor() {

        if(!$this->getLockedUntil()) {
            return null;
        }

        $now = new Zend_Date(Mage::getSingleton('core/date')->date(Varien_Date::DATETIME_PHP_FORMAT));
        $locked_until = new Zend_Date(Mage::getSingleton('core/date')->date(Varien_Date::DATETIME_PHP_FORMAT, $this->getLockedUntil()));

        if($locked_until->compareDate($now) === 0 AND $locked_until->compareTime($now) === 1) {
            $diff = $locked_until->subTime($now);
            $minutes_remaining = intval($diff->toString(Zend_Date::MINUTE_SHORT));
            return $minutes_remaining;
        }

        return null;
    }
}