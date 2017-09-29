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
 * Class Time2_Customersecurity_Model_Observer Observer
 */
class Time2_Customersecurity_Model_Observer {

    /**
     * Customer loaded from email passed in event object
     *
     * @var Mage_Customer_Model_Customer
     */
    private $__customer;

    /**
     * Customersecurity main helper
     *
     * @var Time2_Customersecurity_Helper_Data
     */
    protected $_helper;

    /**
     * Add button to activate locked out customer account and set renderer on locked_until field
     *
     * @param Varien_Event_Observer $observer
     *
     * @return null
     */
    public function activateCustomerAccount(Varien_Event_Observer $observer) {

        if($customerId = Mage::app()->getRequest()->getParam('id')) {
            $customer = Mage::getModel('customer/customer')->load($customerId);

            if ($customer->isAccountLockedOut()) {
                $block = Mage::app()->getLayout()->getBlock("customer_edit");
                $block->addButton('activate', array(
                    'label' => $this->_getHelper()->__('Reactivate'),
                    'onclick' => 'setLocation(\'' . Mage::helper("adminhtml")->getUrl('*/customersecurity_lockout/activate', array('_current' => true)) . '\')',
                    'class' => 'go'
                ));
            }
        }

        return null;
    }

    /**
     * Check whether a customer account is locked when requesting a new password
     *
     * @param Varien_Event_Observer $observer
     *
     * @throws Time2_Customersecurity_Controller_Core_Varien_Exception
     */
    public function isCustomerAccountLockedOutForgotPassword(Varien_Event_Observer $observer) {

        if($this->__isCustomerAccountLockedOut($observer)) {
            Mage::getSingleton('customer/session')->addError($this->_getHelper()->getAccountLockedAlertMessage($this->__getCustomer($observer)));
            $base_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
            $url = str_replace($base_url, "", Mage::helper('core/http')->getHttpReferer());
            throw Mage::exception('Time2_Customersecurity_Controller_Core_Varien', $this->_getHelper()->getAccountLockedAlertMessage($this->__getCustomer($observer)))
                ->prepareRedirect($url, array("_current" => true))
            ;
        }
    }

    /**
     * Check whether a customer account is locked when requesting a new password in Ajax
     *
     * @param Varien_Event_Observer $observer
     */
    public function isCustomerAccountLockedOutForgotPasswordAjax(Varien_Event_Observer $observer) {

        if($this->__isCustomerAccountLockedOut($observer)) {
            $controller = $observer->getControllerAction();
            $controller->getResponse()->setBody($this->_getHelper()->getAccountLockedAlertMessage($this->__getCustomer($observer)));
            $controller->getResponse()->setHeader('HTTP/1.1','400 Bad Request');
            $controller->getRequest()->setDispatched(true);
            $controller->setFlag('', Mage_Core_Controller_Front_Action::FLAG_NO_DISPATCH, true);
        }
    }

    /**
     * Retrieve Customersecurity main helper
     *
     * @return Time2_Customersecurity_Helper_Data
     */
    protected function _getHelper() {
        if(!$this->_helper) {
            $this->_helper = Mage::helper('customersecurity');
        }
        return $this->_helper;
    }

    /**
     * Check whether a customer account is locked out
     *
     * @param Varien_Event_Observer $observer
     *
     * @return bool
     */
    private function __isCustomerAccountLockedOut(Varien_Event_Observer $observer) {

        if(!Mage::helper('customersecurity/config')->isLockoutEnabled()) {
            return false;
        }

        return $this->__getCustomer($observer)->isAccountLockedOut();
    }

    /**
     * Retrieve customer from email passed in event object
     *
     * @param Varien_Event_Observer $observer
     *
     * @return Mage_Customer_Model_Customer
     */
    private function __getCustomer(Varien_Event_Observer $observer) {

        if(!$this->__customer) {
            $controller = $observer->getControllerAction();
            $email = $controller->getRequest()->getParam('email');
            $this->__customer = Mage::getModel('customer/customer')->loadByEmail($email);
        }

        return $this->__customer;
    }
}