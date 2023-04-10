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

    class ProjectactivityTest extends ZurmoBaseTest
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
            $projectactivity = new Projectactivity();
            $projectactivity->owner          = $super;
            $projectactivity->name           = 'test';
            $projectactivity->amount         = $currencyValue;
            $projectactivity->closeDate      = '2011-01-01';
            $projectactivity->stage->value   = 'Verbal';
            $saved                       = $projectactivity->save();
            $this->assertTrue($saved);
            $projectactivity1Id              = $projectactivity->id;
            $projectactivity->forget();

            $currencyValue              = new CurrencyValue();
            $currencyValue->value       = 800;
            $currencyValue->currency    = $currencies[0];
            $this->assertEquals('USD', $currencyValue->currency->code);
            $projectactivity = new Projectactivity();
            $projectactivity->owner          = $super;
            $projectactivity->name           = 'test';
            $projectactivity->amount         = $currencyValue;
            $projectactivity->closeDate      = '2011-01-01';
            $projectactivity->stage->value   = 'Verbal';
            $saved                       = $projectactivity->save();
            $this->assertTrue($saved);
            $projectactivity2Id              = $projectactivity->id;
            $projectactivity->forget();
            $currencyValue->forget(); //need to forget this to pull the accurate value from the database

            $projectactivity1 = Projectactivity::getById($projectactivity1Id);
            $this->assertEquals(100, $projectactivity1->amount->value);

            $projectactivity2 = Projectactivity::getById($projectactivity2Id);
            $this->assertEquals(800, $projectactivity2->amount->value);

            $projectactivity1->delete();
            $projectactivity2->delete();
        }

        /**
         * @depends testVariousCurrencyValues
         */
        public function testCreateAndGetProjectactivityById()
        {
            Yii::app()->user->userModel = User::getByUsername('super');

            $user = UserTestHelper::createBasicUser('Billy');
            $currencies    = Currency::getAll();
            $currencyValue = new CurrencyValue();
            $currencyValue->value = 500.54;
            $currencyValue->currency = $currencies[0];
            $projectactivity = new Projectactivity();
            $projectactivity->owner        = $user;
            $projectactivity->name         = 'Test Projectactivity';
            $projectactivity->amount       = $currencyValue;
            $projectactivity->closeDate    = '2011-01-01'; //eventually fix to make correct format
            $projectactivity->stage->value = 'Negotiating';
            $this->assertTrue($projectactivity->save());
            $id = $projectactivity->id;
            unset($projectactivity);
            $projectactivity = Projectactivity::getById($id);
            $this->assertEquals('Test Projectactivity', $projectactivity->name);
            $this->assertEquals('500.54',      $projectactivity->amount->value);
            $this->assertEquals('Negotiating', $projectactivity->stage->value);
            $this->assertEquals('2011-01-01',    $projectactivity->closeDate);
            $this->assertEquals(1, $currencies[0]->rateToBase);
        }

        /**
         * @depends testCreateAndGetProjectactivityById
         */
        public function testGetProjectactivitiesByName()
        {
            Yii::app()->user->userModel = User::getByUsername('super');

            $projectactivities = Projectactivity::getByName('Test Projectactivity');
            $this->assertEquals(1, count($projectactivities));
            $this->assertEquals('Test Projectactivity', $projectactivities[0]->name);
        }

        /**
         * @depends testCreateAndGetProjectactivityById
         */
        public function testGetLabel()
        {
            Yii::app()->user->userModel = User::getByUsername('super');
            $projectactivities = Projectactivity::getByName('Test Projectactivity');
            $this->assertEquals(1, count($projectactivities));
            $this->assertEquals('Projectactivity',   $projectactivities[0]::getModelLabelByTypeAndLanguage('Singular'));
            $this->assertEquals('Projectactivities', $projectactivities[0]::getModelLabelByTypeAndLanguage('Plural'));
        }

        /**
         * @depends testGetProjectactivitiesByName
         */
        public function testGetProjectactivitiesByNameForNonExistentName()
        {
            Yii::app()->user->userModel = User::getByUsername('super');
            $projectactivities = Projectactivity::getByName('Test Projectactivity 69');
            $this->assertEquals(0, count($projectactivities));
        }

        /**
         * @depends testCreateAndGetProjectactivityById
         */
        public function testGetAll()
        {
            Yii::app()->user->userModel = User::getByUsername('super');

            $user = User::getByUsername('billy');
            $currencies    = Currency::getAll();
            $currencyValue = new CurrencyValue();
            $currencyValue->value = 500.54;
            $currencyValue->currency = $currencies[0];
            $projectactivity = new Projectactivity();
            $projectactivity->owner        = $user;
            $projectactivity->name         = 'Test Projectactivity 2';
            $projectactivity->amount       = $currencyValue;
            $projectactivity->closeDate    = '2011-01-01'; //eventually fix to make correct format
            $projectactivity->stage->value = 'Negotiating';
            $this->assertTrue($projectactivity->save());
            $projectactivities = Projectactivity::getAll();
            $this->assertEquals(2, count($projectactivities));
            $this->assertTrue('Test Projectactivity'   == $projectactivities[0]->name &&
                              'Test Projectactivity 2' == $projectactivities[1]->name ||
                              'Test Projectactivity 2' == $projectactivities[0]->name &&
                              'Test Projectactivity'   == $projectactivities[1]->name);
            $this->assertEquals(1, $currencies[0]->rateToBase);
        }

        /**
         * @depends testCreateAndGetProjectactivityById
         */
        public function testSetAndGetOwner()
        {
            Yii::app()->user->userModel = User::getByUsername('super');

            $user = UserTestHelper::createBasicUser('Dicky');

            $projectactivities = Projectactivity::getByName('Test Projectactivity');
            $this->assertEquals(1, count($projectactivities));
            $projectactivity = $projectactivities[0];
            $projectactivity->owner = $user;
            $this->assertTrue($projectactivity->save());
            unset($user);
            $this->assertTrue($projectactivity->owner !== null);
            $projectactivity->owner = null;
            $this->assertFalse($projectactivity->validate());
            $projectactivity->forget();
            unset($projectactivity);
        }

        /**
         * @depends testSetAndGetOwner
         */
        public function testReplaceOwner()
        {
            Yii::app()->user->userModel = User::getByUsername('super');

            $projectactivities = Projectactivity::getByName('Test Projectactivity');
            $this->assertEquals(1, count($projectactivities));
            $projectactivity = $projectactivities[0];
            $user = User::getByUsername('dicky');
            $this->assertEquals($user->id, $projectactivity->owner->id);
            unset($user);
            $user2 = UserTestHelper::createBasicUser('Benny');
            $projectactivity->owner = $user2;
            unset($user2);
            $this->assertTrue($projectactivity->owner !== null);
            $user = $projectactivity->owner;
            $this->assertEquals('benny', $user->username);
            unset($user);
        }

        /**
         * @depends testCreateAndGetProjectactivityById
         */
        public function testUpdateProjectactivityFromForm()
        {
            Yii::app()->user->userModel = User::getByUsername('super');

            $user = User::getByUsername('billy');
            $projectactivities = Projectactivity::getByName('Test Projectactivity');
            $projectactivity = $projectactivities[0];
            $this->assertEquals($projectactivity->name, 'Test Projectactivity');
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
            $projectactivity->setAttributes($postData);
            $this->assertTrue($projectactivity->save());

            $id = $projectactivity->id;
            unset($projectactivity);
            $projectactivity = Projectactivity::getById($id);
            $this->assertEquals('New Name', $projectactivity->name);
            $this->assertEquals(500.54,     $projectactivity->amount->value);
            $this->assertEquals(1, $currencies[0]->rateToBase);
        }

        public function testDeleteProjectactivity()
        {
            Yii::app()->user->userModel = User::getByUsername('super');

            $projectactivities = Projectactivity::getAll();
            $this->assertEquals(2, count($projectactivities));
            $projectactivities[0]->delete();
            $projectactivities = Projectactivity::getAll();
            $this->assertEquals(1, count($projectactivities));
            $projectactivities[0]->delete();
            $projectactivities = Projectactivity::getAll();
            $this->assertEquals(0, count($projectactivities));
            $currencies    = Currency::getAll();
            $this->assertEquals(1, $currencies[0]->rateToBase);
        }

        public function testGetAllWhenThereAreNone()
        {
            Yii::app()->user->userModel = User::getByUsername('super');
            $projectactivities = Projectactivity::getAll();
            $this->assertEquals(0, count($projectactivities));
        }

        /**
         * @depends testCreateAndGetProjectactivityById
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
            $projectactivity = new Projectactivity();
            $projectactivity->owner        = $user;
            $projectactivity->name         = '1000 Widgets';
            $projectactivity->amount       = $currencyValue;
            $projectactivity->closeDate    = '2011-01-01'; //eventually fix to make correct format
            $projectactivity->stage->value = $stageValues[1];
            $projectactivity->source->value = $sourceValues[1];
            $saved = $projectactivity->save();
            $this->assertTrue($saved);
            $this->assertTrue($projectactivity->id !== null);
            $id = $projectactivity->id;
            unset($projectactivity);
            $projectactivity = Projectactivity::getById($id);
            $this->assertEquals('Negotiating', $projectactivity->stage->value);
            $this->assertEquals('Outbound', $projectactivity->source->value);
            $this->assertEquals(1, $currencies[0]->rateToBase);
        }

        public function testGetModelClassNames()
        {
            $modelClassNames = ProjectactivitiesModule::getModelClassNames();
            $this->assertEquals(1, count($modelClassNames));
            $this->assertEquals('Projectactivity', $modelClassNames[0]);
        }
    }
?>
