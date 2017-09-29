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
 * Class Time2_Customersecurity_Helper_Config
 */
class Time2_Customersecurity_Helper_Config extends Mage_Core_Helper_Abstract {

    /**
     * get current store timezone
     *
     * @return string
     */
    public function getStoreTimeZone() {
        return Mage::getStoreConfig('general/locale/timezone');
    }

    /**
     * Provide number of allowed attempts until customer account is locked out
     *
     * @return int
     */
    public function getAllowedAttempts() {
        return (int) Mage::getStoreConfig('customersecurity/login/login_attempt');
    }

    /**
     * Provide time (minutes) until customer account is locked out
     *
     * @return int
     */
    public function getLockoutPeriod() {
        return (int) Mage::getStoreConfig('customersecurity/login/lockout_period');
    }

    /**
     * Provide time (minutes) until customer try to login for ALLOWED_ATTEMPTS
     *
     * @return int
     */
    public function getLoginPeriod() {
        return (int) Mage::getStoreConfig('customersecurity/login/login_period');
    }

    /**
     * Get config value for module enabled
     *
     * @return bool
     */
    public function isLockoutEnabled() {
        return Mage::getStoreConfigFlag('customersecurity/login/lockout_enabled');
    }

}