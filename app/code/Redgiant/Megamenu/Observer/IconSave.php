<?php

namespace Redgiant\Megamenu\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Megamenu observer
 */
class IconSave implements ObserverInterface
{
    const BASE_MEDIA_PATH = 'redgiant_megamenu';
    protected $category;

    protected $request;

    public function __construct(
        \Magento\Catalog\Model\Category $category,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->category = $category;
        $this->request = $request;
    }

    protected function _filterCategoryPostData(array $rawData)
    {
        $data = $rawData;
        // @todo It is a workaround to prevent saving this data in category model and it has to be refactored in future
        if (isset($data['rg_menu_icon_img']) && is_array($data['rg_menu_icon_img'])) {
            if (!empty($data['rg_menu_icon_img']['delete'])) {
                $data['rg_menu_icon_img'] = null;
            } else {
                if (isset($data['rg_menu_icon_img'][0]['name']) && isset($data['rg_menu_icon_img'][0]['tmp_name'])) {
                    $data['rg_menu_icon_img'] = $data['rg_menu_icon_img'][0]['name'];
                } else {
                    unset($data['rg_menu_icon_img']);
                }
            }
        }
        return $data;
    }

    public function imagePreprocessing($data)
    {
        if (empty($data['rg_menu_icon_img'])) {
            unset($data['rg_menu_icon_img']);
            $data['rg_menu_icon_img']['delete'] = true;
        }
        return $data;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $category = $this->category;           

        $categoryPostData = $this->request->getPostValue();
        $categoryPostData = $this->imagePreprocessing($categoryPostData);
        if ($categoryPostData) {
            if (isset($categoryPostData['rg_menu_icon_img'][0]['name']) && isset($categoryPostData['rg_menu_icon_img'][0]['tmp_name'])) {
                $image = $categoryPostData['rg_menu_icon_img'][0]['name'];                
                $imageUploader = \Magento\Framework\App\ObjectManager::getInstance()->get('Redgiant\Megamenu\ImageUpload');                                   
                $imageUploader->moveFileFromTmp($image);                                             
            }
            $category->addData($this->_filterCategoryPostData($categoryPostData));
            $category->save();
        }
    }
}
