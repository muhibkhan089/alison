<?php

namespace Redgiant\Megamenu\Model\Category;

/**
 * Class DataProvider
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{
    /**
     * @return array
     */
    protected $loadedData;


    public function getData() 
    {
        $this->loadedData = parent::getData();
        $category = parent::getCurrentCategory();

        if ($category) {
            $categoryData = $this->loadedData[$category->getId()];            
            if (isset($categoryData['rg_menu_icon_img'])) {
                unset($categoryData['rg_menu_icon_img']);
                $categoryData['rg_menu_icon_img'][0]['name'] = $category->getData('rg_menu_icon_img');
                $categoryData['rg_menu_icon_img'][0]['url'] = \Magento\Framework\App\ObjectManager::getInstance()->get('Redgiant\Megamenu\Helper\Data')
            ->getImageUrl($category->getData('rg_menu_icon_img'));
            }            

            $this->loadedData[$category->getId()] = $categoryData;         
        }
        return $this->loadedData;
    }

    protected function getFieldsMap()
    {
        return [
            'general' =>
                [
                    'parent',
                    'path',
                    'is_active',
                    'include_in_menu',
                    'name',
                ],
            'content' =>
                [
                    'image',
                    'description',
                    'landing_page',
                ],
            'display_settings' =>
                [
                    'display_mode',
                    'is_anchor',
                    'available_sort_by',
                    'use_config.available_sort_by',
                    'default_sort_by',
                    'use_config.default_sort_by',
                    'filter_price_range',
                    'use_config.filter_price_range',
                ],
            'search_engine_optimization' =>
                [
                    'url_key',
                    'url_key_create_redirect',
                    'use_default.url_key',
                    'url_key_group',
                    'meta_title',
                    'meta_keywords',
                    'meta_description',
                ],
            'assign_products' =>
                [
                ],
            'design' =>
                [
                    'custom_use_parent_settings',
                    'custom_apply_to_products',
                    'custom_design',
                    'page_layout',
                    'custom_layout_update',
                ],
            'schedule_design_update' =>
                [
                    'custom_design_from',
                    'custom_design_to',
                ],
            'rg-menu' =>
                [
                    'rg_menu_hide_item',
                    'rg_menu_type',
                    'rg_menu_static_width',
                    'rg_menu_cat_columns',
                    'rg_menu_float_type',
                    'rg_menu_cat_label',
                    'rg_menu_icon_img',
                    'rg_menu_font_icon',
                    'rg_menu_block_top_content',
                    'rg_menu_block_left_width',
                    'rg_menu_block_left_content',
                    'rg_menu_block_right_width',
                    'rg_menu_block_right_content',
                    'rg_menu_block_bottom_content',
                ],
            'category_view_optimization' =>
                [
                ],
            'category_permissions' =>
                [
                ],
        ];
    }
}
