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
 * Controller exception for redirect
 *
 * @see Mage_Core_Controller_Varien_Exception
 *
 */
class Time2_Customersecurity_Controller_Core_Varien_Exception extends Mage_Core_Controller_Varien_Exception
{

    /**
     * Prepare data for redirecting
     *
     * @param string $path
     * @param array $arguments
     *
     * @return Time2_Customersecurity_Controller_Core_Varien_Exception
     */
    public function prepareRedirect($path, $arguments = array()) {
        $this->_resultCallback = self::RESULT_REDIRECT;
        $this->_resultCallbackParams = array($path, $arguments);
        return $this;
    }
}
