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

    class SensitiveinfoTest extends ZurmoBaseTest
    {
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            SecurityTestHelper::createSuperAdmin();
        }

        public function testCreateStageValues()
        {
            $stageValues = array(
                'Prospecting',
                'Negotiating',
                'Close Won',
            );
            $stageFieldData = CustomFieldData::getByName('SalesStages');
            $stageFieldData->serializedData = serialize($stageValues);
            $this->assertTrue($stageFieldData->save());
        }

        /**
         * @depends testCreateStageValues
         */
        public function testVariousCurrencyValues()
        {
            $super                      = User::getByUsername('super');
            Yii::app()->user->userModel = $super;
            $currencies                 = Currency::getAll();
            $currencyValue              = new CurrencyValue();
            $currencyValue->value       = 100;
            $currencyValue->currency    = $currencies[0];
            $this->assertEquals('USD', $currencyValue->currency->code);
            $sensitiveinfo = new Sensitiveinfo();
            $sensitiveinfo->owner          = $super;
            $sensitiveinfo->name           = 'test';
            $sensitiveinfo->amount         = $currencyValue;
            $sensitiveinfo->closeDate      = '2011-01-01';
            $sensitiveinfo->stage->value   = 'Verbal';
            $saved                       = $sensitiveinfo->save();
            $this->assertTrue($saved);
            $sensitiveinfo1Id              = $sensitiveinfo->id;
            $sensitiveinfo->forget();

            $currencyValue              = new CurrencyValue();
            $currencyValue->value       = 800;
            $currencyValue->currency    = $currencies[0];
            $this->assertEquals('USD', $currencyValue->currency->code);
            $sensitiveinfo = new Sensitiveinfo();
            $sensitiveinfo->owner          = $super;
            $sensitiveinfo->name           = 'test';
            $sensitiveinfo->amount         = $currencyValue;
            $sensitiveinfo->closeDate      = '2011-01-01';
            $sensitiveinfo->stage->value   = 'Verbal';
            $saved                       = $sensitiveinfo->save();
            $this->assertTrue($saved);
            $sensitiveinfo2Id              = $sensitiveinfo->id;
            $sensitiveinfo->forget();
            $currencyValue->forget(); //need to forget this to pull the accurate value from the database

            $sensitiveinfo1 = Sensitiveinfo::getById($sensitiveinfo1Id);
            $this->assertEquals(100, $sensitiveinfo1->amount->value);

            $sensitiveinfo2 = Sensitiveinfo::getById($sensitiveinfo2Id);
            $this->assertEquals(800, $sensitiveinfo2->amount->value);

            $sensitiveinfo1->delete();
            $sensitiveinfo2->delete();
        }

        /**
         * @depends testVariousCurrencyValues
         */
        public function testCreateAndGetSensitiveinfoById()
        {
            Yii::app()->user->userModel = User::getByUsername('super');

            $user = UserTestHelper::createBasicUser('Billy');
            $currencies    = Currency::getAll();
            $currencyValue = new CurrencyValue();
            $currencyValue->value = 500.54;
            $currencyValue->currency = $currencies[0];
            $sensitiveinfo = new Sensitiveinfo();
            $sensitiveinfo->owner        = $user;
            $sensitiveinfo->name         = 'Test Sensitiveinfo';
            $sensitiveinfo->amount       = $currencyValue;
            $sensitiveinfo->closeDate    = '2011-01-01'; //eventually fix to make correct format
            $sensitiveinfo->stage->value = 'Negotiating';
            $this->assertTrue($sensitiveinfo->save());
            $id = $sensitiveinfo->id;
            unset($sensitiveinfo);
            $sensitiveinfo = Sensitiveinfo::getById($id);
            $this->assertEquals('Test Sensitiveinfo', $sensitiveinfo->name);
            $this->assertEquals('500.54',      $sensitiveinfo->amount->value);
            $this->assertEquals('Negotiating', $sensitiveinfo->stage->value);
            $this->assertEquals('2011-01-01',    $sensitiveinfo->closeDate);
            $this->assertEquals(1, $currencies[0]->rateToBase);
        }

        /**
         * @depends testCreateAndGetSensitiveinfoById
         */
        public function testGetSensitiveinfosByName()
        {
            Yii::app()->user->userModel = User::getByUsername('super');

            $sensitiveinfos = Sensitiveinfo::getByName('Test Sensitiveinfo');
            $this->assertEquals(1, count($sensitiveinfos));
            $this->assertEquals('Test Sensitiveinfo', $sensitiveinfos[0]->name);
        }

        /**
         * @depends testCreateAndGetSensitiveinfoById
         */
        public function testGetLabel()
        {
            Yii::app()->user->userModel = User::getByUsername('super');
            $sensitiveinfos = Sensitiveinfo::getByName('Test Sensitiveinfo');
            $this->assertEquals(1, count($sensitiveinfos));
            $this->assertEquals('Sensitiveinfo',   $sensitiveinfos[0]::getModelLabelByTypeAndLanguage('Singular'));
            $this->assertEquals('Sensitiveinfos', $sensitiveinfos[0]::getModelLabelByTypeAndLanguage('Plural'));
        }

        /**
         * @depends testGetSensitiveinfosByName
         */
        public function testGetSensitiveinfosByNameForNonExistentName()
        {
            Yii::app()->user->userModel = User::getByUsername('super');
            $sensitiveinfos = Sensitiveinfo::getByName('Test Sensitiveinfo 69');
            $this->assertEquals(0, count($sensitiveinfos));
        }

        /**
         * @depends testCreateAndGetSensitiveinfoById
         */
        public function testGetAll()
        {
            Yii::app()->user->userModel = User::getByUsername('super');

            $user = User::getByUsername('billy');
            $currencies    = Currency::getAll();
            $currencyValue = new CurrencyValue();
            $currencyValue->value = 500.54;
            $currencyValue->currency = $currencies[0];
            $sensitiveinfo = new Sensitiveinfo();
            $sensitiveinfo->owner        = $user;
            $sensitiveinfo->name         = 'Test Sensitiveinfo 2';
            $sensitiveinfo->amount       = $currencyValue;
            $sensitiveinfo->closeDate    = '2011-01-01'; //eventually fix to make correct format
            $sensitiveinfo->stage->value = 'Negotiating';
            $this->assertTrue($sensitiveinfo->save());
            $sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(2, count($sensitiveinfos));
            $this->assertTrue('Test Sensitiveinfo'   == $sensitiveinfos[0]->name &&
                              'Test Sensitiveinfo 2' == $sensitiveinfos[1]->name ||
                              'Test Sensitiveinfo 2' == $sensitiveinfos[0]->name &&
                              'Test Sensitiveinfo'   == $sensitiveinfos[1]->name);
            $this->assertEquals(1, $currencies[0]->rateToBase);
        }

        /**
         * @depends testCreateAndGetSensitiveinfoById
         */
        public function testSetAndGetOwner()
        {
            Yii::app()->user->userModel = User::getByUsername('super');

            $user = UserTestHelper::createBasicUser('Dicky');

            $sensitiveinfos = Sensitiveinfo::getByName('Test Sensitiveinfo');
            $this->assertEquals(1, count($sensitiveinfos));
            $sensitiveinfo = $sensitiveinfos[0];
            $sensitiveinfo->owner = $user;
            $this->assertTrue($sensitiveinfo->save());
            unset($user);
            $this->assertTrue($sensitiveinfo->owner !== null);
            $sensitiveinfo->owner = null;
            $this->assertFalse($sensitiveinfo->validate());
            $sensitiveinfo->forget();
            unset($sensitiveinfo);
        }

        /**
         * @depends testSetAndGetOwner
         */
        public function testReplaceOwner()
        {
            Yii::app()->user->userModel = User::getByUsername('super');

            $sensitiveinfos = Sensitiveinfo::getByName('Test Sensitiveinfo');
            $this->assertEquals(1, count($sensitiveinfos));
            $sensitiveinfo = $sensitiveinfos[0];
            $user = User::getByUsername('dicky');
            $this->assertEquals($user->id, $sensitiveinfo->owner->id);
            unset($user);
            $user2 = UserTestHelper::createBasicUser('Benny');
            $sensitiveinfo->owner = $user2;
            unset($user2);
            $this->assertTrue($sensitiveinfo->owner !== null);
            $user = $sensitiveinfo->owner;
            $this->assertEquals('benny', $user->username);
            unset($user);
        }

        /**
         * @depends testCreateAndGetSensitiveinfoById
         */
        public function testUpdateSensitiveinfoFromForm()
        {
            Yii::app()->user->userModel = User::getByUsername('super');

            $user = User::getByUsername('billy');
            $sensitiveinfos = Sensitiveinfo::getByName('Test Sensitiveinfo');
            $sensitiveinfo = $sensitiveinfos[0];
            $this->assertEquals($sensitiveinfo->name, 'Test Sensitiveinfo');
            $currencies    = Currency::getAll();
            $postData = array(
                'owner' => array(
                    'id' => $user->id,
                ),
                'name' => 'New Name',
                'amount' => array('value' => '500.54', 'currency' => array('id' => $currencies[0]->id)),
                'closeDate' => '2011-01-01',
                'stage' => array(
                    'value' => 'Negotiating'
                ),
            );
            $sensitiveinfo->setAttributes($postData);
            $this->assertTrue($sensitiveinfo->save());

            $id = $sensitiveinfo->id;
            unset($sensitiveinfo);
            $sensitiveinfo = Sensitiveinfo::getById($id);
            $this->assertEquals('New Name', $sensitiveinfo->name);
            $this->assertEquals(500.54,     $sensitiveinfo->amount->value);
            $this->assertEquals(1, $currencies[0]->rateToBase);
        }

        public function testDeleteSensitiveinfo()
        {
            Yii::app()->user->userModel = User::getByUsername('super');

            $sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(2, count($sensitiveinfos));
            $sensitiveinfos[0]->delete();
            $sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(1, count($sensitiveinfos));
            $sensitiveinfos[0]->delete();
            $sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(0, count($sensitiveinfos));
            $currencies    = Currency::getAll();
            $this->assertEquals(1, $currencies[0]->rateToBase);
        }

        public function testGetAllWhenThereAreNone()
        {
            Yii::app()->user->userModel = User::getByUsername('super');
            $sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(0, count($sensitiveinfos));
        }

        /**
         * @depends testCreateAndGetSensitiveinfoById
         */
        public function testSetStageAndSourceAndRetrieveDisplayName()
        {
            Yii::app()->user->userModel = User::getByUsername('super');

            $user = User::getByUsername('billy');

            $stageValues = array(
                'Prospecting',
                'Negotiating',
                'Close Won',
            );
            $stageFieldData = CustomFieldData::getByName('SalesStages');
            $stageFieldData->serializedData = serialize($stageValues);
            $this->assertTrue($stageFieldData->save());

            $sourceValues = array(
                'Word of Mouth',
                'Outbound',
                'Trade Show',
            );
            $sourceFieldData = CustomFieldData::getByName('LeadSources');
            $sourceFieldData->serializedData = serialize($sourceValues);
            $this->assertTrue($sourceFieldData->save());

            $currencies    = Currency::getAll();
            $currencyValue = new CurrencyValue();
            $currencyValue->value = 500.54;
            $currencyValue->currency = $currencies[0];
            $sensitiveinfo = new Sensitiveinfo();
            $sensitiveinfo->owner        = $user;
            $sensitiveinfo->name         = '1000 Widgets';
            $sensitiveinfo->amount       = $currencyValue;
            $sensitiveinfo->closeDate    = '2011-01-01'; //eventually fix to make correct format
            $sensitiveinfo->stage->value = $stageValues[1];
            $sensitiveinfo->source->value = $sourceValues[1];
            $saved = $sensitiveinfo->save();
            $this->assertTrue($saved);
            $this->assertTrue($sensitiveinfo->id !== null);
            $id = $sensitiveinfo->id;
            unset($sensitiveinfo);
            $sensitiveinfo = Sensitiveinfo::getById($id);
            $this->assertEquals('Negotiating', $sensitiveinfo->stage->value);
            $this->assertEquals('Outbound', $sensitiveinfo->source->value);
            $this->assertEquals(1, $currencies[0]->rateToBase);
        }

        public function testGetModelClassNames()
        {
            $modelClassNames = SensitiveinfosModule::getModelClassNames();
            $this->assertEquals(1, count($modelClassNames));
            $this->assertEquals('Sensitiveinfo', $modelClassNames[0]);
        }
    }
?>
