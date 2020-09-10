<?php

declare(strict_types=1);

namespace IchHabRecht\Multicolumn\Evaluation;

/*
 * This file is part of the TYPO3 Multicolumn project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read
 * LICENSE file that was distributed with this source code.
 */

use IchHabRecht\Multicolumn\Utility\MulticolumnUtility;

class MaxColumnsEvaluator
{
    /**
     * Returns input value
     *
     * @return    mixed        set value or null
     */
    public function returnFieldJS()
    {
        return 'return (value ? value : null);';
    }

    /**
     * Checks if input value of advanced layout column is greater than $returnValue
     *
     * @param int $inputValue
     * @return    int|null        max column value
     */
    public function evaluateFieldValue($inputValue)
    {
        if ($id = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('popViewId')) {
            $conf = MulticolumnUtility::getTSConfig($id, 'config');
            $maxNumberOfColumns = $conf['advancedLayouts.']['maxNumberOfColumns'];

            $returnValue = ($inputValue > $maxNumberOfColumns) ? $maxNumberOfColumns : $inputValue;
        }

        return $returnValue ? $returnValue : ($inputValue ? $inputValue : null);
    }
}
