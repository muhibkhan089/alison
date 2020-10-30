<?php

namespace Cminds\Multiwishlist\Controller\Wishlist\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Session\Generic as WishlistSession;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Customer\Model\Session;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Wishlist\Model\Config;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Customer\Helper\View;
use Magento\Wishlist\Controller\Index\Send as WushlistSend;
use Magento\Framework\Validator\EmailAddress;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Area;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\View\Result\Layout;
/**
 * Class Send
 *
 * @package Index
 */
class Send extends WushlistSend
{
    /**
     * Send constructor.
     *
     * @param Context $context
     * @param Validator $formKeyValidator
     * @param Session $customerSession
     * @param WishlistProviderInterface $wishlistProvider
     * @param Config $wishlistConfig
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param View $customerHelperView
     * @param WishlistSession $wishlistSession
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        Session $customerSession,
        WishlistProviderInterface $wishlistProvider,
        Config $wishlistConfig,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        View $customerHelperView,
        WishlistSession $wishlistSession,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    )
    {
        parent::__construct(
            $context,
            $formKeyValidator,
            $customerSession,
            $wishlistProvider,
            $wishlistConfig,
            $transportBuilder,
            $inlineTranslation,
            $customerHelperView,
            $wishlistSession,
            $scopeConfig,
            $storeManager
        );
    }
    /**
     * @return Redirect
     *
     * @throws NotFoundException
     * @throws \Zend_Validate_Exception
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $resultRedirect->setPath('*/*/');

            return $resultRedirect;
        }
        $wishlistId = $this->getRequest()->getParam('wishlist_id');
        $wishlist = $this->wishlistProvider->getWishlist($wishlistId);
        if (!$wishlist) {
            throw new NotFoundException(__('Page not found.'));
        }

        $sharingLimit = $this->_wishlistConfig->getSharingEmailLimit();
        $textLimit = $this->_wishlistConfig->getSharingTextLimit();
        $emailsLeft = $sharingLimit - $wishlist->getShared();

        $emails = $this->getRequest()->getPost('emails');
        $emails = empty($emails) ? $emails : explode(',', $emails);

        $error = false;
        $message = (string)$this->getRequest()->getPost('message');
        if (strlen($message) > $textLimit) {
            $error = __('Message length must not exceed %1 symbols', $textLimit);
        } else {
            $message = nl2br(htmlspecialchars($message));
            if (empty($emails)) {
                $error = __('Please enter an email address.');
            } else {
                if (count($emails) > $emailsLeft) {
                    $error = __('This wish list can be shared %1 more times.', $emailsLeft);
                } else {
                    foreach ($emails as $index => $email) {
                        $email = trim($email);
                        if (!\Zend_Validate::is($email, EmailAddress::class)) {
                            $error = __('Please enter a valid email address.');
                            break;
                        }
                        $emails[$index] = $email;
                    }
                }
            }
        }

        if ($error) {
            $this->messageManager->addError($error);
            $this->wishlistSession->setSharingForm($this->getRequest()->getPostValue());
            $resultRedirect->setPath('*/*/share');

            return $resultRedirect;
        }
        /** @var Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        $this->addLayoutHandles($resultLayout);
        $this->inlineTranslation->suspend();

        $sent = 0;

        try {
            $customer = $this->_customerSession->getCustomerDataObject();
            $customerName = $this->_customerHelperView->getCustomerName($customer);

            $message .= $this->getRssLink($wishlist->getId(), $resultLayout);
            $emails = array_unique($emails);
            $sharingCode = $wishlist->getSharingCode();

            try {
                foreach ($emails as $email) {
                    $transport = $this->_transportBuilder->setTemplateIdentifier(
                        $this->scopeConfig->getValue(
                            'wishlist/email/email_template',
                            ScopeInterface::SCOPE_STORE
                        )
                    )->setTemplateOptions(
                        [
                            'area' => Area::AREA_FRONTEND,
                            'store' => $this->storeManager->getStore()->getStoreId(),
                        ]
                    )->setTemplateVars(
                        [
                            'customer' => $customer,
                            'customerName' => $customerName,
                            'salable' => $wishlist->isSalable() ? 'yes' : '',
                            'items' => $this->getWishlistItems($resultLayout),
                            'viewOnSiteLink' => $this->_url->getUrl('*/shared/index', ['code' => $sharingCode]),
                            'message' => $message,
                            'store' => $this->storeManager->getStore(),
                        ]
                    )->setFrom(
                        $this->scopeConfig->getValue(
                            'wishlist/email/email_identity',
                            ScopeInterface::SCOPE_STORE
                        )
                    )->addTo(
                        $email
                    )->getTransport();

                    $transport->sendMessage();

                    $sent++;
                }
            } catch (\Exception $e) {
                $wishlist->setShared($wishlist->getShared() + $sent);
                $wishlist->save();
                throw $e;
            }
            $wishlist->setShared($wishlist->getShared() + $sent);
            $wishlist->save();

            $this->inlineTranslation->resume();

            $this->_eventManager->dispatch('wishlist_share', ['wishlist' => $wishlist]);
            $this->messageManager->addSuccess(__('Your wish list has been shared.'));
            $resultRedirect->setPath('*/*', ['wishlist_id' => $wishlist->getId()]);

            return $resultRedirect;
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError($e->getMessage());
            $this->wishlistSession->setSharingForm($this->getRequest()->getPostValue());
            $resultRedirect->setPath('*/*/share');

            return $resultRedirect;
        }
    }
}
