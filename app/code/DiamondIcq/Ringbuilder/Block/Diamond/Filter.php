<?php

namespace DiamondIcq\Ringbuilder\Block\Diamond;

class Filter extends \Gemfind\Ringbuilder\Block\Diamond\Filter
{

    /**
     * @return mixed
     */
    public function getDiamondFilters()
    {
        $request = $this->getRequest()->getParams();

        // shapes filter
        // gemstone shape options
        $options = $this->_helper->getAttributeOptionByCode('gemstoneshape');
        $results['shapes'] = [];
        foreach ($options as $option) {
            $optionData = (object)$option->getData();
            if (!empty($optionData->value)) {
                $image_file = 'gemstone-shapes/' . strtolower($optionData->label) . '.jpeg';
                $results['shapes'][] = (object)[
                    'shapeName' => $optionData->label,
                    'shapeImage' => $this->getViewFileUrl('DiamondIcq_Ringbuilder::images/' . $image_file),
                ];
            }
        }

        // carat filter
        $minCarat = 1;
        $maxCarat = 20;
        $results['caratRange'] = [
            (object)['minCarat' => $minCarat, 'maxCarat' => $maxCarat],
        ];

        // price filter
        $diamond_type = 'White';
        if (!empty($request['filtermode']) && $request['filtermode'] == 'navfancycolored') {
            $diamond_type = 'Fancy';
        }
        $priceRange = $this->_helper->diamondPriceRange($diamond_type);
        $minPrice = 0;
        $maxPrice = $priceRange->max;
        $results['priceRange'] = [
            (object)['minPrice' => $minPrice, 'maxPrice' => $maxPrice],
        ];

        // color filter
        $results['colorRange'] = [
            (object)['colorId' => 68, 'colorName' => 'D'],
            (object)['colorId' => 69, 'colorName' => 'E'],
            (object)['colorId' => 70, 'colorName' => 'F'],
            (object)['colorId' => 71, 'colorName' => 'G'],
            (object)['colorId' => 72, 'colorName' => 'H'],
            (object)['colorId' => 73, 'colorName' => 'I'],
            (object)['colorId' => 74, 'colorName' => 'J'],
            (object)['colorId' => 75, 'colorName' => 'K'],
            (object)['colorId' => 76, 'colorName' => 'L'],
            (object)['colorId' => 77, 'colorName' => 'M'],
            (object)['colorId' => 78, 'colorName' => 'N'],
            (object)['colorId' => 79, 'colorName' => 'O'],
            (object)['colorId' => 80, 'colorName' => 'P+'],
        ];

        if (array_key_exists('filtermode', $request) && $request['filtermode'] == 'navfancycolored') {
            // ? fancy color filter
            $diamondColorRange = [
                'Blue', 'Red', 'Pink', 'Yellow', 'Champagne', 'Green', 'Grey', 'Purple', 'Chameleon', 'Violet',
            ];
            $results['diamondColorRange'] = [];
            foreach ($diamondColorRange as $color) {
                $image_file = "diamonds/{$color}.png";
                $results['diamondColorRange'][] = (object)[
                    'diamondColorId' => $color,
                    'diamondColorName' => $color,
                    'diamondColorImagePath' => $this->getViewFileUrl('DiamondIcq_Ringbuilder::images/' . $image_file),
                ];
            }
            // ? fancy intensity
            $results['intensity'] = [
                (object)['intensityName' => 'Faint'],
                (object)['intensityName' => 'V.Light'],
                (object)['intensityName' => 'Light'],
                (object)['intensityName' => 'F.Light'],
                (object)['intensityName' => 'Fancy'],
                (object)['intensityName' => 'Vivid'],
                (object)['intensityName' => 'Deep'],
                (object)['intensityName' => 'Dark'],
            ];
        }

        // clarity filter
        $results['clarityRange'] = [
            (object)['clarityId' => 1, 'clarityName' => 'FL'],
            (object)['clarityId' => 2, 'clarityName' => 'IF'],
            (object)['clarityId' => 3, 'clarityName' => 'VVS1'],
            (object)['clarityId' => 4, 'clarityName' => 'VVS2'],
            (object)['clarityId' => 5, 'clarityName' => 'VS1'],
            (object)['clarityId' => 6, 'clarityName' => 'VS2'],
            (object)['clarityId' => 7, 'clarityName' => 'SI1'],
            (object)['clarityId' => 8, 'clarityName' => 'SI2'],
            (object)['clarityId' => 9, 'clarityName' => 'SI3'],
            (object)['clarityId' => 10, 'clarityName' => 'I1'],
            (object)['clarityId' => 11, 'clarityName' => 'I2+'],
        ];

        // depth filter
        $minDepth = 0;
        $maxDepth = 100;
        $results['depthRange'] = [
            (object)['minDepth' => $minDepth, 'maxDepth' => $maxDepth],
        ];

        // table filter
        $minTable = 0;
        $maxTable = 100;
        $results['tableRange'] = [
            (object)['minTable' => $minTable, 'maxTable' => $maxTable],
        ];

        // cut range
        $results['cutRange'] = [
            (object)['cutId' => 1, 'cutName' => 'Ideal'],
            (object)['cutId' => 2, 'cutName' => 'Excellent'],
            (object)['cutId' => 3, 'cutName' => 'V.Good'],
            (object)['cutId' => 4, 'cutName' => 'Good'],
        ];

        // polish filter
        $results['polishRange'] = [
            (object)['polishId' => 1, 'polishName' => 'Excellent'],
            (object)['polishId' => 2, 'polishName' => 'Very good'],
            (object)['polishId' => 3, 'polishName' => 'Good'],
            (object)['polishId' => 4, 'polishName' => 'Fair'],
        ];

        // flourence filter
        $results['fluorescenceRange'] = [
            (object)['fluorescenceId' => 1, 'fluorescenceName' => 'N'],
            (object)['fluorescenceId' => 2, 'fluorescenceName' => 'FNT'],
            (object)['fluorescenceId' => 3, 'fluorescenceName' => 'Med'],
            (object)['fluorescenceId' => 4, 'fluorescenceName' => 'ST'],
            (object)['fluorescenceId' => 5, 'fluorescenceName' => 'VST'],
        ];

        // symmetry filter
        $results['symmetryRange'] = [
            (object)['symmetryId' => 1, 'symmteryName' => 'Excellent'],
            (object)['symmetryId' => 2, 'symmteryName' => 'Very good'],
            (object)['symmetryId' => 3, 'symmteryName' => 'Good'],
            (object)['symmetryId' => 4, 'symmteryName' => 'Fair'],
        ];

        // certificate filter
        $results['certificateRange'] = [
            (object)['certificateName' => 'Show All Cerificate'],
            (object)['certificateName' => 'AGS'],
            (object)['certificateName' => 'EGL'],
            (object)['certificateName' => 'GIA'],
            (object)['certificateName' => 'IGI'],
            (object)['certificateName' => 'HRD'],
            (object)['certificateName' => 'GCAL'],
            (object)['certificateName' => 'None'],
        ];

        return $results;
    }
}
