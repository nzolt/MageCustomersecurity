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

$installer = $this;

$installer->startSetup();

$attribute  = array(
    'type' => 'datetime',
    'input' => 'text',
    'label' => 'Locked until',
    'global' => 1,
    'visible' => 1,
    'default' => null,
    'required' => 0,
    'user_defined' => 0,
    'searchable' => 0,
    'filterable' => 0,
    'comparable' => 0,
    'disabled' => 1,
    'used_for_sort_by' => 1,
    'is_configurable' => 0,
    'visible_on_front' => 1,
    'visible_in_advanced_search' => 0,
    'note' => 'Date and time until the account is locked.',
);

$installer->addAttribute('customer', 'locked_until', $attribute);

Mage::getSingleton('eav/config')
    ->getAttribute('customer', 'locked_until')
    ->setData('used_in_forms', array('adminhtml_customer'))
    ->save();

// CREATE table "customersecurity_login_fail"
$installer->run("
CREATE TABLE `{$this->getTable("customersecurity/login_fail")}` (
    `login_fail_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `customer_id` INT(11) UNSIGNED,
    `store_id` SMALLINT(5) UNSIGNED NOT NULL,
    `login_name` VARCHAR(64) NOT NULL,
    `ip` VARCHAR (16) NOT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`login_fail_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

// Add INDEX to table
// Index for Customer id
$installer->run("
    CREATE INDEX IDX_CUSTOMERSECURITY_LOGIN_FAIL_CUSTOMER_ID ON `{$this->getTable('customersecurity/login_fail')}` (`customer_id`);
");
// Index for Customer name
$installer->run("
    CREATE INDEX IDX_CUSTOMERSECURITY_LOGIN_FAIL_LODIN_NAME ON `{$this->getTable('customersecurity/login_fail')}` (`login_name`);
");
// Index for Sore id
$installer->run("
    CREATE INDEX IDX_CUSTOMERSECURITY_LOGIN_FAIL_STORE_ID ON `{$this->getTable('customersecurity/login_fail')}` (`store_id`);
");
// Index for IP number
$installer->run("
    CREATE INDEX IDX_CUSTOMERSECURITY_LOGIN_FAIL_IP ON `{$this->getTable('customersecurity/login_fail')}` (`ip`);
");

// Add Foreign keys
// Foreign key to Customer
$installer->run("
    ALTER TABLE `{$this->getTable('customersecurity/login_fail')}` 
        ADD CONSTRAINT FK_CUSTOMERSECURITY_LOGIN_FAIL_CUSTOMER_ENTITY_ENTITY_ID 
        FOREIGN KEY (`customer_id`) 
        REFERENCES `{$this->getTable('customer_entity')}` (`entity_id`) 
        ON DELETE CASCADE ON UPDATE CASCADE;
    ");
// Foreign key to Store
$installer->run("
    ALTER TABLE `{$this->getTable('customersecurity/login_fail')}` 
    ADD CONSTRAINT FK_CUSTOMERSECURITY_LOGIN_FAIL_CORE_STORE_STORE_ID 
    FOREIGN KEY (`store_id`) 
    REFERENCES `{$this->getTable('core_store')}` (`store_id`) 
    ON DELETE CASCADE ON UPDATE CASCADE;
");

// Terminate setup
$installer->endSetup();