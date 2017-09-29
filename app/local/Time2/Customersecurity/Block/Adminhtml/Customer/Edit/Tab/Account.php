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
 * Class Time2_Customersecurity_Block_Adminhtml_Customer_Edit_Tab_Account
 *
 * @see Mage_Adminhtml_Block_Customer_Edit_Tab_Account
 *
 */
class Time2_Customersecurity_Block_Adminhtml_Customer_Edit_Tab_Account extends Mage_Adminhtml_Block_Customer_Edit_Tab_Account
{
    /**
     * Initialize form
     *
     * @return $this|Mage_Adminhtml_Block_Customer_Edit_Tab_Account
     */
    public function initForm()
    {
        parent::initForm();
        $form = $this->getForm();
        $fieldset = $form->getElement('locked_until');
        $renderer = $this->getLayout()->createBlock('customersecurity/adminhtml_customer_lockout_renderer');
        $fieldset->setRenderer($renderer);

        return $this;
    }
}