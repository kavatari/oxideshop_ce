<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

require_once 'oxerptype.php';

/**
 * user erp type subclass
 */
class oxERPType_User extends oxERPType
{
    protected $_sTableName = 'oxuser';
    protected $_sShopObjectName = 'oxuser';


    /**
     * Imports user. Returns import status
     *
     * @param array $aRow db row array
     *
     * @return string $oxid on success, bool FALSE on failure
     */
    public function import($aRow)
    {
        // Special check for user
        if (isset($aRow['OXUSERNAME'])) {
            $sID = $aRow['OXID'];
            $sUserName = $aRow['OXUSERNAME'];

            $oUser = oxNew("oxuser", "core");
            $oUser->oxuser__oxusername = new oxField($sUserName, oxField::T_RAW);

            //If user exists with and modifies OXID, throw an axception
            //throw new Exception( "USER {$sUserName} already exists!");
            if ($oUser->exists($sID) && $sID != $oUser->getId()) {
                throw new Exception("USER $sUserName already exists!");
            }
        }

        $sResult = parent::import($aRow);

        return $sResult;
    }

    /**
     * returns SQL string for this type
     *
     * @param string $sWhere    where part of sql
     * @param int    $iLanguage language id
     * @param int    $iShopId   shop id
     *
     * @return string
     */
    public function getSQL($sWhere, $iLanguage = 0, $iShopId = 1)
    {
        $myConfig = oxRegistry::getConfig();

        // add type 'user' for security reasons
        if (strstr($sWhere, 'where')) {
            $sWhere .= ' and ';
        } else {
            $sWhere .= ' where ';
        }

        $sWhere .= ' oxrights = \'user\'';
        //MAFI also check for shopid to restrict access


        return parent::getSQL($sWhere, $iLanguage, $iShopId);
    }

    /**
     * Basic access check for writing data, checks for same shopid, should be overridden if field oxshopid does not exist
     *
     * @param oxBase $oObj  loaded shop object
     * @param array  $aData fields to be written, null for default
     *
     * @throws Exception on now access
     *
     * @return null
     */
    public function checkWriteAccess($oObj, $aData = null)
    {
        return;

        $myConfig = oxRegistry::getConfig();

        if (!$myConfig->getConfigParam('blMallUsers')) {
            parent::checkWriteAccess($oObj, $aData);
        }
    }
}