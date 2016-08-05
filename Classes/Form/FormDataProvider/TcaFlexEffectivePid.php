<?php
namespace CPSIT\Multicolumn\Form\FormDataProvider;

use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Set effective pid in flexform result
 */
class TcaFlexEffectivePid implements FormDataProviderInterface
{
    /**
     * Effective pid is used to determine entry point for page ts and is also
     * the pid where new records are stored later.
     *
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        if (empty($result['flexParentDatabaseRow']['pid']) || $result['flexParentDatabaseRow']['pid'] > 0) {
            return $result;
        }

        $recordUid = abs($result['flexParentDatabaseRow']['pid']);
        $record = BackendUtility::getRecord($result['tableName'], $recordUid, 'pid');

        if (!empty($record['pid'])) {
            $result['flexParentDatabaseRow']['pid'] = $record['pid'];
        }

        return $result;
    }
}
