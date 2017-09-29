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
 * Class Time2_Customersecurity_Adminhtml_Customersecurity_LockoutController
 */
class Time2_Customersecurity_Adminhtml_Customersecurity_LockoutController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Activate (customer) action
     *
     * @return void
     */
    public function activateAction() {
        try {
            if($customerId = (int)$this->getRequest()->getParam('id')) {
                $customer = Mage::getModel('customer/customer')->load($customerId);
                $helper = Mage::helper('customersecurity');
                $helper->cleanLockOut($customer);
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $helper->__('Customer account activated.'));
            }

        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

        $this->_redirect(
            '*/customer/edit',
            array(
                '_current' => true,
                'tab' => 'customer_info_tabs_account',
            )
        );
    }
}