<?php

namespace Gemfind\Ringbuilder\Block\Diamond\Search;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Gemfind\Ringbuilder\Helper\Data as Helper;
use Magento\Framework\Session\SessionManagerInterface;

class AbstractProduct extends Template
{
    /**
     * @var SessionManagerInterface
     */
    protected $sessionManager;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * AbstractProduct constructor.
     * @param Context $context
     * @param SessionManagerInterface $sessionManager
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        SessionManagerInterface $sessionManager,
        Helper $helper,
        array $data = []
    ) {

        $this->sessionManager = $sessionManager;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getSearchUrl()
    {
        return $this->getUrl('ringbuilder/diamond/diamondsearch', ['_secure' => true]);
    }

     /**
      * @return string
      */
    public function getSiteUrl()
    {
        return $this->helper->getSiteUrl();
    }

     /**
      * @return string
      */
    public function getDiamondViewUrl($param, $type)
    {
        return $this->getUrl('ringbuilder/diamond/view', ['path' => $param, 'type' => $type, '_secure' => true]);
    }

    /**
     * @return array
     */
    public function getProductCollection()
    {
        $request = $this->getRequest()->getParams();
        return $this->getDiamonds($request);
    }

    /**
     * @return mixed
     */
    public function getUrlString()
    {
        return $this->getRequest()->getUriString();
    }

    /**
     * @param $request
     * @return array
     */
    protected function getDiamonds($request)
    {
        if ($request == null) {
            $diamond = [
                'meta' => ['code' => 400, 'message' => __('No arguments supplied.')],
                'data' => [],
                'pagination' => [],
                'perpage'       => $this->helper->getResultPerPage()
            ];
            return $diamond;
        }
        if (!is_array($request)) {
            $diamond = [
                'meta' => ['code' => 400, 'message' => $request],
                'data' => [],
                'pagination' => [],
                'perpage'       => $this->helper->getResultPerPage()
            ];
            return $diamond;
        }

        $shapeValue =  $certificate = $fluorescence = $fancycolor = $colorcontent = $claritycontent =
        $cutcontent = $polishcontent = $symmetrycontent = $fancycolorcontent = $intintensitycontent = [];
        $shapesContent = $symmetrycontentContent = $certificatesContent = $fluorescenceContent =
        $fancycolorContent = $colorcontentContent = $claritycontentContent = $cutcontentContent =
        $polishcontentContent = $symmetrycontentContent = $fancycolorcontentContent =
        $intintensitycontentContent = $itemperpage = '';
        $hasvideo = 'Yes' ;
        // Convert the Shapes list into gemfind form

        if (array_key_exists('diamond_shape', $request)) {
            foreach ($request["diamond_shape"] as $value) {
                $shapeValue[] = strtolower($value);
            }
            $shapesContent = implode(',', $shapeValue);
        }

        // Convert the Certificate array into gemfind form

        if (array_key_exists('diamond_certificates', $request)) {
            foreach ($request["diamond_certificates"] as $values) {
                    $certificate[] = $values;
            }
                $certificatesContent = implode(',', $certificate);
        }

        // Convert the Fluorescence list into gemfind form

        if (array_key_exists('diamond_fluorescence', $request)) {
            foreach ($request["diamond_fluorescence"] as $value) {
                $fluorescence[] = strtolower($value);
            }
            $fluorescenceContent = implode(',', $fluorescence);
        }

        // Convert the color list into gemfind form

        if (array_key_exists('diamond_color', $request)) {
            foreach ($request["diamond_color"] as $value) {
                $colorcontent[] = strtolower($value);
            }
            $colorcontentContent = implode(',', $colorcontent);
        }

        // Convert the clarity list into gemfind form

        if (array_key_exists('diamond_clarity', $request)) {
            foreach ($request["diamond_clarity"] as $value) {
                $claritycontent[] = strtolower($value);
            }
            $claritycontentContent = implode(',', $claritycontent);
        }

        // Convert the Cut list into gemfind form

        if (array_key_exists('diamond_cut', $request)) {
            foreach ($request["diamond_cut"] as $value) {
                $cutcontent[] = strtolower($value);
            }
            $cutcontentContent = implode(',', $cutcontent);
        }

        // Convert the Polish list into gemfind form

        if (array_key_exists('diamond_polish', $request)) {
            foreach ($request["diamond_polish"] as $value) {
                $polishcontent[] = strtolower($value);
            }
            $polishcontentContent = implode(',', $polishcontent);
        }

        // Convert the Symmetry list into gemfind form

        if (array_key_exists('diamond_symmetry', $request)) {
            foreach ($request["diamond_symmetry"] as $value) {
                $symmetrycontent[] = strtolower($value);
            }
            $symmetrycontentContent = implode(',', $symmetrycontent);
        }

        // Convert the DiamondId list into gemfind form

        if (isset($request['did'])) {
            $did = $request['did'];
        } else {
            $did = '';
        }

        if (!isset($request['gemfind_diamond_origin'])) {
            $request['gemfind_diamond_origin'] = "";
        }

        $price_from = '';
        $price_to = '';

        if (isset($request['price'])) {
            $price_from = (intval($request["price"]["from"])) ? intval(str_replace(',', '', $request["price"]["from"])) : '';
            $price_to = (intval($request["price"]["to"])) ? intval(str_replace(',', '', $request["price"]["to"])) : '';
        }

        // Create the request array to sumbit to gemfind
        $requestData = [
            'shapes' => $shapesContent,
            'fluorescence_intensities' => $fluorescenceContent,
            'size_from' => isset($request["diamond_carats"]["from"]) ? $request["diamond_carats"]["from"] : '',
            'size_to' => isset($request["diamond_carats"]["to"]) ? $request["diamond_carats"]["to"] : '',
            'color' => $colorcontentContent,
            'clarity' => $claritycontentContent,
            'cut' => $cutcontentContent,
            'polish' => $polishcontentContent,
            'symmetry' => $symmetrycontentContent,
            'price_from' => $price_from,
            'price_to' => $price_to,
            'diamond_table_from' => (isset($request["diamond_table"]["from"])) ? intval($request["diamond_table"]["from"]) : '',
            'diamond_table_to' => (isset($request["diamond_table"]["to"])) ? intval($request["diamond_table"]["to"]) : '',
            'depth_percent_from' => (isset($request["diamond_depth"]["from"])) ? intval($request["diamond_depth"]["from"]) : '',
            'depth_percent_to' => (isset($request["diamond_depth"]["to"])) ? intval($request["diamond_depth"]["to"]) : '',
            'labs' => $certificatesContent,
            'origin' => isset($request["gemfind_diamond_origin"]) ? $request["gemfind_diamond_origin"] : '',
            'page_number' => isset($request['currentpage']) ? $request['currentpage'] : '',
            'page_size' => isset($request['itemperpage']) ? $request['itemperpage'] : $this->helper->getResultPerPage(),
            'sort_by' => isset($request['orderby']) ? $request['orderby'] : '',
            'sort_direction' => isset($request['direction']) ? $request['direction'] : '',
            'did' => $did,
            'hasvideo' => $hasvideo,
            'Filtermode' => isset($request['filtermode'])? $request['filtermode'] : 'navstandard'
        ];


        if (isset($request['filtermode'])) {
            if ($request['filtermode'] != 'navstandard' && $request['filtermode'] != 'navlabgrown') {
                // Convert the Symmetry list into gemfind form

                if (array_key_exists('diamond_fancycolor', $request)) {
                    foreach ($request["diamond_fancycolor"] as $value) {
                        $fancycolorcontent[] = strtolower($value);
                    }
                    $fancycolorcontentContent = implode(',', $fancycolorcontent);
                }

                // Convert the Symmetry list into gemfind form

                if (array_key_exists('diamond_intintensity', $request)) {
                    foreach ($request["diamond_intintensity"] as $value) {
                        $intintensitycontent[] = strtolower($value);
                    }
                    $intintensitycontentContent = implode(',', $intintensitycontent);
                }

                $fancyData = ['FancyColor' =>$fancycolorcontentContent,'intIntensity' =>
                $intintensitycontentContent];

                $requestData = array_merge($requestData, $fancyData);
            }
        }


        $result = $this->helper->sendRequest($requestData);
        $num = ceil($result['total'] / $this->helper->getResultPerPage());
        if (isset($request['currentpage'])) {
            if ($request['currentpage'] > $num) {
                $requestData['page_number'] = 1;
                $request['currentpage'] = 1;
                $result = $this->helper->sendRequest($requestData);
            }
        }
        if ($result['diamonds'] != null || $result['total'] != 0) {
            $count = 0;
            if ($request['currentpage'] > 1) {
                $count = ($request['itemperpage']) ? $request['itemperpage'] : $this->helper->getResultPerPage() * ($request['currentpage'] - 1);
            }

            $diamond = [
                'meta' => ['code' => 200],
                'data' => $result['diamonds'],
                'pagination' => [
                    'currentpage' => $request['currentpage'],
                    'count'     => $count,
                    'limit'     => count($result['diamonds']),
                    'total'     => $result['total']
                ],
                'perpage'       => ($request['itemperpage']) ? $request['itemperpage'] : $this->helper->getResultPerPage()
            ];
        } else {
            $diamond = [
                'meta' =>['code' => 404, 'message' => "No Product Found"],
                'data' => [],
                'pagination' =>['total' => $result['total']],
                'perpage'       => $this->helper->getResultPerPage()
            ];
        }

        return $diamond;
    }

    /**
     * @return mixed
     */
    public function getAdditionalFilters()
    {
        $dealerID = $this->helper->getUsername();
        $requestUrl = $this->helper->getJcOptionsapi().'DealerID='.$dealerID;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $requestUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->helper->getApiTimeout());
        $responce = curl_exec($curl);
        $results = (array) json_decode($responce);
        if (sizeof($results) > 0) {
            foreach ($results[0] as $value) {
                return $value = (array) $value;
            }
        }
        curl_close($curl);
    }

    /**
     * @return bool
     */
    public function getGemfindEnabledPoweredBy()
    {
        return $this->helper->isGemfindEnabledPoweredBy();
    }

    /**
     * @return bool
     */
    public function getGemfindEnabledStickyHeaderRB()
    {
        return $this->helper->isGemfindEnabledStickyHeaderRB();
    }
}
