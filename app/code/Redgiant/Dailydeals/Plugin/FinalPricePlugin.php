<?php
namespace Redgiant\Dailydeals\Plugin;

class FinalPricePlugin
{
    public function beforeSetTemplate(\Magento\Catalog\Pricing\Render\FinalPriceBox $subject, $template)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $enable=$objectManager->create('Redgiant\Dailydeals\Helper\Data')->chkEnableDailydeals();
        if ($enable) {
            if ($template == 'Magento_Catalog::product/price/final_price.phtml') {
                return ['Redgiant_Dailydeals::product/price/final_price.phtml'];
            } else {
                return [$template];
            }
        } else {
            return[$template];
        }
    }
}
