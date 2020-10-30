<?php
namespace Cminds\MultiUserAccounts\Model;

use Cminds\MultiUserAccounts\Model\SubaccountFactory as SubaccountFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerFactory;
use Magento\Framework\App\Request\Http as Request;
use Magento\Framework\Data\OptionSourceInterface;

class ParentAccount implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private $customerFactory;

    /**
     * @var Http
     */
    private $request;

    /**
     * @var SubaccountFactory
     */
    private $subAccountFactory;

    /**
     * ParentAccount constructor.
     *
     * @param CustomerFactory $customerFactory
     * @param Request $request
     * @param SubaccountFactory $subAccountFactory
     */
    public function __construct(
        CustomerFactory $customerFactory,
        Request $request,
        SubaccountFactory $subAccountFactory
    ) {
        $this->customerFactory = $customerFactory;
        $this->request = $request;
        $this->subAccountFactory = $subAccountFactory;
    }

    public function getCustomerCollection()
    {
        return $this->customerFactory->create();
    }

    /**
     * Get subaccount collection
     */
    public function getSubaccountCollection()
    {
        return $this->subAccountFactory->create()->getCollection();
    }

    public function getOptionArray()
    {
        $removeCustomerIds  = [];
        $this->_options = [];

        $currentCustomerId  = $this->request->getParam('id');
        if ($currentCustomerId) {
            $subAccountsArr = $this
                ->getSubaccountCollection()
                ->addFieldToFilter('parent_customer_id', $currentCustomerId)
                ->getData();

            if (count($subAccountsArr) > 0) {
                foreach ($subAccountsArr as $subAccount) {
                    array_push($removeCustomerIds, $subAccount['customer_id']);
                }
            }

            array_push($removeCustomerIds,$currentCustomerId);

            $customerCollection = $this
                ->getCustomerCollection()
                ->setOrder('email','ASC');

            $i = 1;

            $this->_options[0] = array('label' => 'Select Parent Account', 'value' => '0');
            foreach ($customerCollection as $customer) {
                if (!in_array($customer->getId(),$removeCustomerIds)) {
                    $this->_options[$i] = array('label' => $customer->getEmail(), 'value' => $customer->getId());
                    $i++;
                }
            }
        } else {
            $customerCollection  = $this->getCustomerCollection();
            
            $i = 1;
            $this->_options[0] = array('label' => 'Select Parent Account', 'value' => '0');
            foreach ($customerCollection as $customer) {
                $this->_options[$i] = array('label' => $customer->getEmail(), 'value' => $customer->getId());
                $i++;
            }
        }
       
        $parentOptions = array();
        foreach ($this->_options as $key => $value) {
            $parentOptions[$value['value']] = __($value['label']);    
        }

        return $parentOptions;
    }

    /**
     * Get Grid row status labels array with empty value for option element.
     *
     * @return array
     */
    public function getAllOptions()
    {
        $result = $this->getOptions();
        array_unshift($result, ['value' => '', 'label' => '']);

        return $result;
    }

    /**
     * Get Grid row type array for option element.
     * @return array
     */
    public function getOptions()
    {
        $result = [];
        foreach ($this->getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }
}