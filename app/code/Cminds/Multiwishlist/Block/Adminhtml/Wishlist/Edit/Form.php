<?php

namespace Cminds\Multiwishlist\Block\Adminhtml\Wishlist\Edit;

use Cminds\Multiwishlist\Block\Adminhtml\Wishlist\Edit;
use \Magento\Backend\Block\Widget\Form\Generic;

class Form extends Generic
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('wishlist_form');
        $this->setTitle(__('Wishlist Information'));
    }

    /**
     * @return Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Wishlist\Model\Wishlist $model */
        $model = $this->_coreRegistry->registry(Edit::WISHLIST_REGISTRY_KEY);

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );

        $form->setHtmlIdPrefix('wishlist_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Wishlist Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField('wishlist_id', 'hidden', ['name' => 'wishlist_id']);
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true
            ]
        );
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
