<?php
    /*********************************************************************************
     * Zurmo is a customer relationship management program developed by
     * Zurmo, Inc. Copyright (C) 2012 Zurmo Inc.
     *
     * Zurmo is free software; you can redistribute it and/or modify it under
     * the terms of the GNU General Public License version 3 as published by the
     * Free Software Foundation with the addition of the following permission added
     * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
     * IN WHICH THE COPYRIGHT IS OWNED BY ZURMO, ZURMO DISCLAIMS THE WARRANTY
     * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
     *
     * Zurmo is distributed in the hope that it will be useful, but WITHOUT
     * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
     * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
     * details.
     *
     * You should have received a copy of the GNU General Public License along with
     * this program; if not, see http://www.gnu.org/licenses or write to the Free
     * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
     * 02110-1301 USA.
     *
     * You can contact Zurmo, Inc. with a mailing address at 113 McHenry Road Suite 207,
     * Buffalo Grove, IL 60089, USA. or at email address contact@zurmo.com.
     ********************************************************************************/

    class SensitiveinfoTestHelper
    {
        public static function createSensitiveinfoByNameForOwner($name, $owner)
        {
            $currencies    = Currency::getAll();
            $currencyValue = new CurrencyValue();
            $currencyValue->value = 500.54;
            $currencyValue->currency = $currencies[0];
            $sensitiveinfo = new Sensitiveinfo();
            $sensitiveinfo->owner        = $owner;
            $sensitiveinfo->name         = $name;
            $sensitiveinfo->amount       = $currencyValue;
            $sensitiveinfo->closeDate    = '2011-01-01'; //eventually fix to make correct format
            $sensitiveinfo->stage->value = 'Negotiating';
            $saved                     = $sensitiveinfo->save();
            assert('$saved');
            return $sensitiveinfo;
        }

        public static function createSensitiveinfoWithAccountByNameForOwner($name, $owner, $account)
        {
            $currencies    = Currency::getAll();
            $currencyValue = new CurrencyValue();
            $currencyValue->value = 500.54;
            $currencyValue->currency = $currencies[0];
            $sensitiveinfo = new Sensitiveinfo();
            $sensitiveinfo->owner        = $owner;
            $sensitiveinfo->name         = $name;
            $sensitiveinfo->account      = $account;
            $sensitiveinfo->amount       = $currencyValue;
            $sensitiveinfo->closeDate    = '2011-01-01'; //eventually fix to make correct format
            $sensitiveinfo->stage->value = 'Negotiating';
            $saved                     = $sensitiveinfo->save();
            assert('$saved');
            return $sensitiveinfo;
        }

        public static function createSensitiveinfoStagesIfDoesNotExist()
        {
            $salesStagesFieldData = CustomFieldData::getByName('SalesStages');
            $stageValues = array(
                'Prospecting',
                'Qualification',
                'Negotiating',
                'Verbal',
                'Closed Won',
                'Closed Lost',
            );
            if (count(unserialize($salesStagesFieldData->serializedData)) == 0)
            {
                $salesStagesFieldData = CustomFieldData::getByName('SalesStages');
                $salesStagesFieldData->defaultValue = $stageValues[0];
                $salesStagesFieldData->serializedData = serialize($stageValues);
                $saved = $salesStagesFieldData->save();
                assert('$saved');
            }
        }

            public static function createSensitiveinfoSourcesIfDoesNotExist()
        {
            $sourceFieldData = CustomFieldData::getByName('LeadSources');
            $sourceValues = array(
                'Word of Mouth',
                'Outbound',
                'Trade Show',
            );
            if (count(unserialize($sourceFieldData->serializedData)) == 0)
            {
                $sourceFieldData = CustomFieldData::getByName('LeadSources');
                $sourceFieldData->defaultValue = $sourceValues[0];
                $sourceFieldData->serializedData = serialize($sourceValues);
                $saved = $sourceFieldData->save();
                assert('$saved');
            }
        }
    }
?>