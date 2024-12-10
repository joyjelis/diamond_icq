<?php
namespace Travash\Education\Controller\Adminhtml\Educationcat;

/**
 * Class Save
 * @package Travash\Education\Controller\Adminhtml\Educationcat
 */
class Save extends \Travash\Education\Controller\Adminhtml\Educationcat
{
    /**
     * @return mixed
     */
    public function execute()
    {
        /* @phpstan-ignore-next-line */
        $formPostValues = $this->getRequest()->getPostValue();
        if (isset($formPostValues)) {
            $educationCategoryData = $formPostValues;
            if (!empty($educationCategoryData)) {
                if (isset($formPostValues[
                        'store_id'
                        ]) && !empty(
                            $formPostValues['store_id']
                        )
                ) {
                    $storeId = implode(
                        ',',
                        $formPostValues['store_id']
                    );
                    $educationCategoryData['store_id'] = $storeId;
                }
            }

            $educationCategoryId = isset($educationCategoryData[
                'education_cat_id'
                ]) ? $educationCategoryData[
            'education_cat_id'
            ] : null;
            $model = $this->_educationcatFactory->create();
            $modelUrl = $this->_educationcatFactory->create();
            if ($educationCategoryId) {
                /* @phpstan-ignore-next-line */
                $modelUrl->getCollection()
                    ->addFieldToFilter(
                        'education_cat_id',
                        [
                            'neq' => $educationCategoryId
                        ]
                    );
            } else {
                /* @phpstan-ignore-next-line */
                $modelUrl->getCollection();
            }
            if ($educationCategoryData['url_key']) {
                $educationCategoryData['url_key'] = preg_replace(
                    '/^-+|-+$/',
                    '',
                    strtolower(
                        preg_replace(
                            '/[^a-zA-Z0-9]+/',
                            '-',
                            $educationCategoryData[
                            'url_key'
                            ]
                        )
                    )
                );
            }
            $model->setData($educationCategoryData);
            $data['created_at'] = date('Y-m-d');
            $model->setCreatedAt($data['created_at']);
            try {
                /* @phpstan-ignore-next-line */
                $model->save();
                $this->messageManager->addSuccess(__(
                    'The category has been saved.'
                ));
                $this->_getSession()->setFormData(false);

                if ($this->getRequest()->getParam('back') === 'edit') {
                    $this->_redirect(
                        '*/*/edit',
                        [
                            'education_cat_id' => $model->getEducationCatId(),
                            '_current' => true
                        ]
                    );
                    return;
                } elseif ($this->getRequest()->getParam('back') === "new") {
                    $this->_redirect(
                        '*/*/new',
                        [
                            '_current' => true
                        ]
                    );
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __(
                        'Something went wrong while saving the Category.'
                    )
                );
            }

            $this->_getSession()->setFormData($formPostValues);
            $this->_redirect(
                '*/*/edit',
                [
                    'education_cat_id' => $this->getRequest()->getParam(
                        'education_cat_id'
                    )
                ]
            );
            return;
        }
        /* @phpstan-ignore-next-line */
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Travash_Education::education');
    }
}
