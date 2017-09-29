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
 * Class Time2_Customersecurity_Block_Adminhtml_Customer_Lockout_Renderer
 */
class Time2_Customersecurity_Block_Adminhtml_Customer_Lockout_Renderer extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element {

    /**
     * Render field as disabled
     *
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element) {
        $this->_element = $element;
        if($this->_element->getValue()) {
            $lockDateTime = Mage::getSingleton('core/date');
            $lockDateTimeValue = $lockDateTime->date(Varien_Date::DATETIME_PHP_FORMAT, $this->_element->getValue());
            $this->_element->setValue($lockDateTimeValue);
        }
        $this->_element->setDisabled('disabled');
        return $this->toHtml();
    }
}