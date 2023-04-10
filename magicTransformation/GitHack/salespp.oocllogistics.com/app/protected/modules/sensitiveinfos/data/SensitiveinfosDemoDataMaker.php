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

    /**
     * Class that builds demo sensitiveinfos.
     */
    class SensitiveinfosDemoDataMaker extends DemoDataMaker
    {
        protected $ratioToLoad = 2;

        public static function getDependencies()
        {
            return array('contacts');
        }

        public function makeAll(& $demoDataHelper)
        {
            assert('$demoDataHelper instanceof DemoDataHelper');
            assert('$demoDataHelper->isSetRange("User")');
            assert('$demoDataHelper->isSetRange("Account")');
            assert('$demoDataHelper->isSetRange("Contact")');
            $currencies = Currency::getAll('id');
            $sensitiveinfos = array();
            for ($i = 0; $i < $this->resolveQuantityToLoad(); $i++)
            {
                $sensitiveinfo = new Sensitiveinfo();
                $sensitiveinfo->contacts->add($demoDataHelper->getRandomByModelName('Contact'));
                $sensitiveinfo->account      = $sensitiveinfo->contacts[0]->account;
                $sensitiveinfo->owner        = $sensitiveinfo->contacts[0]->owner;
                $currencyValue             = new CurrencyValue();
                $currencyValue->currency   = $currencies[array_rand($currencies)];
                $sensitiveinfo->amount       = $currencyValue;
                $this->populateModel($sensitiveinfo);
                $saved = $sensitiveinfo->save();
                assert('$saved');
                $sensitiveinfos[]           = $sensitiveinfo->id;
            }
            $demoDataHelper->setRangeByModelName('Sensitiveinfo', $sensitiveinfos[0], $sensitiveinfos[count($sensitiveinfos)-1]);
        }

        public function populateModel(& $model)
        {
            assert('$model instanceof Sensitiveinfo');
            $sensitiveinfoRandomData = ZurmoRandomDataUtil::
                                     getRandomDataByModuleAndModelClassNames('SensitiveinfosModule', 'Sensitiveinfo');

            parent::populateModel($model);
            $name        = RandomDataUtil::getRandomValueFromArray($sensitiveinfoRandomData['names']);
            $model->name = $name;
            $stage       = RandomDataUtil::getRandomValueFromArray(static::getCustomFieldDataByName('SalesStages'));
            $source      = RandomDataUtil::getRandomValueFromArray(static::getCustomFieldDataByName('LeadSources'));
            $model->stage->value  = $stage;
            $model->source->value = $source;
            $futureTimeStamp      = time() + (mt_rand(1, 200) * 60 * 60 * 24);
            $closeDate            = Yii::app()->dateFormatter->format(
                                    DatabaseCompatibilityUtil::getDateFormat(), $futureTimeStamp);
            $model->closeDate     = $closeDate;
            $model->amount->value = mt_rand(5, 350) * 1000;
        }
    }
?>