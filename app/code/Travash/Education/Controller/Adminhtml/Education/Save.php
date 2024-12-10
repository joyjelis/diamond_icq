<?php
namespace Travash\Education\Controller\Adminhtml\Education;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Save
 * @package Travash\Education\Controller\Adminhtml\Education
 */
class Save extends \Travash\Education\Controller\Adminhtml\Education
{
    

    /**
     * @return mixed
     */
    public function execute()
    {
        /* @phpstan-ignore-next-line */
        $formPostValues = $data = $this->getRequest()->getPostValue();

        if (isset($formPostValues)) {
            $educationData = $formPostValues;
            if (!empty($educationData)) {
                if (isset($formPostValues[
                        'store_id'
                        ]) && !empty(
                            $formPostValues['store_id']
                        )
                ) {
                    $storeids = implode(',', $educationData['store_id']);
                    $educationData['store_id'] = $storeids;
                }
            }
            if ((isset($_FILES['featured_img']['name'])) && ($_FILES['featured_img']['name'] != '') && (!isset($educationData['featured_img']['delete']))) {
                try {
                        $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'featured_img']);
                        $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                        $imageAdapter = $this->adapterFactory->create();
                        $uploaderFactory->setAllowRenameFiles(true);
                        $uploaderFactory->setFilesDispersion(true);
                        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

                        $destinationPath = $mediaDirectory->getAbsolutePath('travash_education');
                        $result = $uploaderFactory->save($destinationPath);

                    if (!$result) {
                        $this->messageManager->addError(__('File cannot be saved to path: $1', $destinationPath));
                    }

                        $imagePath = 'travash_education' . $result['file'];

                        $educationData['featured_img'] = $imagePath;

                } catch (\Exception $e) {

                        $this->messageManager->addError(__("Image not Upload Pleae Try Again"));
                }
            }

            if (isset($educationData['featured_img']) && is_array($educationData['featured_img'])) {
                $educationData['featured_img'] = $educationData['featured_img']['value'];
            }

            if (isset($educationData['education_category'])) {
                $educationData['education_cat_id'] = implode(',', $educationData['education_category']);
            }

            $educationId = isset($educationData['education_id']) ? $educationData['education_id'] : null;
            $model = $this->_educationFactory->create();
            if ($educationId) {
                /* @phpstan-ignore-next-line */
                $model->load($educationId);
            }

            if ($educationData['url_key']) {
                $educationData['url_key'] = preg_replace(
                    '/^-+|-+$/',
                    '',
                    strtolower(
                        preg_replace(
                            '/[^a-zA-Z0-9]+/',
                            '-',
                            $educationData['url_key']
                        )
                    )
                );
            } else {
                $educationData['url_key'] = preg_replace(
                    '/^-+|-+$/',
                    '',
                    strtolower(
                        preg_replace(
                            '/[^a-zA-Z0-9]+/',
                            '-',
                            $educationData['title']
                        )
                    )
                );
            }

            $modelUrl = $this->_educationFactory->create();
            if ($educationId) {
                /* @phpstan-ignore-next-line */
                $modelURL = $modelUrl->getCollection()
                    ->addFieldToFilter(
                        'education_id',
                        [
                            'neq' => $educationId
                        ]
                    );
            } else {
                /* @phpstan-ignore-next-line */
                $modelURL = $modelUrl->getCollection();
            }
            $count = 0;
            foreach ($modelURL->getData() as $url) {
                if ($url['url_key'] == $educationData['url_key']) {
                    if ($count === 1) {
                        $random = rand(1, 99);
                        $educationData['url_key'] = $educationData[
                            'url_key'
                            ] .
                            '-' .
                            $random;
                        break;
                    }
                    $count++;
                }
            }

            $model->setData($educationData);
             /* @phpstan-ignore-next-line */
            $model->setEducationCategory($educationData['education_cat_id']);
            try {
                /* @phpstan-ignore-next-line */
                $model->save();
                $this->messageManager->addSuccess(__(
                    'The Education has been saved.'
                ));
                $this->_getSession()->setFormData(false);

                if ($this->getRequest()->getParam('back') === 'edit') {
                    $this->_redirect(
                        '*/*/edit',
                        [
                            'education_id' => $model->getEducationId(),
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
                $this->messageManager->addException($e, __(
                    'Something went wrong while saving the Category.'
                ));

            }

            $this->_getSession()->setFormData($formPostValues);
            $this->_redirect(
                '*/*/edit',
                [
                    'education_id' => $this->getRequest()->getParam('education_id')
                ]
            );
            return;
        }
        /* @phpstan-ignore-next-line */
        $this->_redirect('*/*/');
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Travash_Education::education');
    }
}
