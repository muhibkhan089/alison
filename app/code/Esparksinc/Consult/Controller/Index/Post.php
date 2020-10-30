<?php
namespace Esparksinc\Consult\Controller\Index;

use Esparksinc\Consult\Model\QueryFactory;

class Post extends \Magento\Framework\App\Action\Action
{

    const XML_PATH_EMAIL_SENDER = 'consult/email/sender_email_identity';
    const XML_PATH_ENABLED = 'consult/consult_email/enabled';
    const XML_PATH_NOTIFICATION = 'consult/email/notification_email';

    private $_query;
    private $_transportBuilder;
    private $inlineTranslation;
    private $scopeConfig;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        QueryFactory $query
    ) 
    {
        $this->_query = $query;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $customerId = $data['customerId'];
        $customerName = $data['customerName'];
        $customerEmail = $data['customerEmail'];
        $ergoId = $data['ergoId'];
        $ergoName = $data['ergoName'];
        $ergoEmail = $data['ergoEmail'];
        $query = $data['query'];

        

        $question = $this->_query->create();
        $question->setData($data);
        $question->setCustomerId($customerId);
        $question->setCustomerName($customerName);
        $question->setCustomerEmail($customerEmail);
        $question->setErgoId($ergoId);
        $question->setErgoName($ergoName);
        $question->setErgoEmail($ergoEmail);
        $question->setQuery($query);
        if (!$query) {
            $this->messageManager->addError(__('Please describe your query'));
        }

        // Email Sending Code Start
    $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    if($this->scopeConfig->getValue(self::XML_PATH_ENABLED, $storeScope)){
        $this->inlineTranslation->suspend();
        try {
            $recipientEmail = $data['ergoEmail'];
            $requestData = array();
            $requestData['message'] = $this->scopeConfig->getValue(self::XML_PATH_NOTIFICATION, $storeScope);
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($requestData);

            $error = false;

            $sender = $this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope);

            $transport = $this->_transportBuilder
            ->setTemplateIdentifier('consult_email_email_template') // this code we have mentioned in the email_templates.xml
            ->setTemplateOptions(
                [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ]
        )
            ->setTemplateVars(['data' => $postObject])
            ->setFrom($sender)
            ->addTo($recipientEmail)
            ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            $this->messageManager->addSuccess(__('Thanks for your message!  Our Ergonomist will get back to you as soon as possible.'));
            $question->save();
            $this->_redirect('*/*/');
            return;
            
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.'.$e->getMessage())
            );
            $this->_redirect('*/*/');
            return;
        }
    }
    else{
        $this->messageManager->addSuccess('Thanks for your message!  Our Ergonomist will get back to you as soon as possible.');
        $question->save();
        $this->_redirect('*/*/');
        return;
    }
    }
}
