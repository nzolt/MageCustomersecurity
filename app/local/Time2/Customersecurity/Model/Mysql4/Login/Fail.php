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
 * Class Time2_Customersecurity_Model_Mysql4_Login_Fail
 */
class Time2_Customersecurity_Model_Mysql4_Login_Fail extends Mage_Core_Model_Mysql4_Abstract {

    /**
     * Time2_Customersecurity_Model_Mysql4_Login_Fail constructor
     */
    protected function _construct()
    {
        $this->_init('customersecurity/login_fail', 'login_fail_id');
    }
}