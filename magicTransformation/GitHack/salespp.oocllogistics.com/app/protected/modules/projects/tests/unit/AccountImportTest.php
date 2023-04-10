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

    class ProjectImportTest extends ImportBaseTest
    {
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            SecurityTestHelper::createSuperAdmin();
            Yii::import('application.core.data.*');
            Yii::import('application.modules.projects.data.*');
            $defaultDataMaker = new ProjectsDefaultDataMaker();
            $defaultDataMaker->make();
        }

        public function testParentProjectHasCorrectAttributeImportType()
        {
            $attributeImportRules = AttributeImportRulesFactory::makeByImportRulesTypeAndAttributeIndexOrDerivedType(
                                    'Projects', 'project');
            $this->assertTrue($attributeImportRules instanceof ProjectAttributeImportRules);
        }

        public function testShortEmailIsValidEmail()
        {
            $validator = new CEmailValidator();
            $validatedEmail = $validator->validateValue('a@a.com');
            $this->assertTrue($validatedEmail);
        }

        /**
         * @depends testParentProjectHasCorrectAttributeImportType
         */
        public function testSimpleUserImportWhereAllRowsSucceed()
        {
            Yii::app()->user->userModel            = User::getByUsername('super');
            $parentProject                         = ProjectTestHelper::
                                                     createProjectByNameForOwner('parentProject',
                                                                                 Yii::app()->user->userModel);
            $parentProjectId = $parentProject->id;
            $projects                              = Project::getAll();
            $this->assertEquals(1, count($projects));
            $import                                = new Import();
            $serializedData['importRulesType']     = 'Projects';
            $serializedData['firstRowIsHeaderRow'] = true;
            $import->serializedData                = serialize($serializedData);
            $this->assertTrue($import->save());

            ImportTestHelper::
            createTempTableByFileNameAndTableName('importTest.csv', $import->getTempTableName(),
                                                  Yii::getPathOfAlias('application.modules.projects.tests.unit.files'));

            //update the ids of the project column to match the parent project.
            R::exec("update " . $import->getTempTableName() . " set column_16 = " .
                    $parentProject->id . " where id != 1 limit 4");
            $this->assertEquals(4, ImportDatabaseUtil::getCount($import->getTempTableName())); // includes header rows.

            $mappingData = array(
                'column_0'  => ImportMappingUtil::makeStringColumnMappingData      ('name'),
                'column_1'  => ImportMappingUtil::makeStringColumnMappingData      ('officePhone'),
                'column_2'  => ImportMappingUtil::makeStringColumnMappingData      ('officeFax'),
                'column_3'  => ImportMappingUtil::makeIntegerColumnMappingData     ('employees'),
                'column_4'  => ImportMappingUtil::makeUrlColumnMappingData         ('website'),
                'column_5'  => ImportMappingUtil::makeFloatColumnMappingData       ('annualRevenue'),
                'column_6'  => ImportMappingUtil::makeTextAreaColumnMappingData    ('description'),
                'column_7'  => ImportMappingUtil::makeStringColumnMappingData      ('billingAddress__city'),
                'column_8'  => ImportMappingUtil::makeStringColumnMappingData      ('billingAddress__country'),
                'column_9'  => ImportMappingUtil::makeStringColumnMappingData      ('billingAddress__postalCode'),
                'column_10' => ImportMappingUtil::makeStringColumnMappingData      ('billingAddress__state'),
                'column_11' => ImportMappingUtil::makeStringColumnMappingData      ('billingAddress__street1'),
                'column_12' => ImportMappingUtil::makeStringColumnMappingData      ('billingAddress__street2'),
                'column_13' => ImportMappingUtil::makeEmailColumnMappingData       ('primaryEmail__emailAddress'),
                'column_14' => ImportMappingUtil::makeBooleanColumnMappingData     ('primaryEmail__isInvalid'),
                'column_15' => ImportMappingUtil::makeBooleanColumnMappingData     ('primaryEmail__optOut'),
                'column_16' => ImportMappingUtil::makeHasOneColumnMappingData      ('project'),
                'column_17' => ImportMappingUtil::makeDropDownColumnMappingData    ('industry'),
                'column_18' => ImportMappingUtil::makeDropDownColumnMappingData    ('type'),
            );

            $importRules  = ImportRulesUtil::makeImportRulesByType('Projects');
            $page         = 0;
            $config       = array('pagination' => array('pageSize' => 50)); //This way all rows are processed.
            $dataProvider = new ImportDataProvider($import->getTempTableName(), true, $config);
            $dataProvider->getPagination()->setCurrentPage($page);
            $importResultsUtil = new ImportResultsUtil($import);
            $messageLogger     = new ImportMessageLogger();
            ImportUtil::importByDataProvider($dataProvider,
                                             $importRules,
                                             $mappingData,
                                             $importResultsUtil,
                                             new ExplicitReadWriteModelPermissions(),
                                             $messageLogger);
            $importResultsUtil->processStatusAndMessagesForEachRow();

            //Confirm that 3 models where created.
            $projects = Project::getAll();
            $this->assertEquals(4, count($projects));

            $projects = Project::getByName('project1');
            $this->assertEquals(1,                         count($projects[0]));
            $this->assertEquals(123456,                    $projects[0]->officePhone);
            $this->assertEquals(555,                       $projects[0]->officeFax);
            $this->assertEquals(1,                         $projects[0]->employees);
            $this->assertEquals('http://www.project1.com', $projects[0]->website);
            $this->assertEquals(100,                       $projects[0]->annualRevenue);
            $this->assertEquals('desc1',                   $projects[0]->description);
            $this->assertEquals('city1',                   $projects[0]->billingAddress->city);
            $this->assertEquals('country1',                $projects[0]->billingAddress->country);
            $this->assertEquals('postal1',                 $projects[0]->billingAddress->postalCode);
            $this->assertEquals('state1',                  $projects[0]->billingAddress->state);
            $this->assertEquals('street11',                $projects[0]->billingAddress->street1);
            $this->assertEquals('street21',                $projects[0]->billingAddress->street2);
            $this->assertEquals('a@a.com',                 $projects[0]->primaryEmail->emailAddress);
            $this->assertEquals(null,                      $projects[0]->primaryEmail->isInvalid);
            $this->assertEquals(null,                      $projects[0]->primaryEmail->optOut);
            $this->assertTrue($projects[0]->project->isSame($parentProject));
            $this->assertEquals('Automotive',              $projects[0]->industry->value);
            $this->assertEquals('Prospect',                $projects[0]->type->value);

            $projects = Project::getByName('project2');
            $this->assertEquals(1,                         count($projects[0]));
            $this->assertEquals(223456,                    $projects[0]->officePhone);
            $this->assertEquals(666,                       $projects[0]->officeFax);
            $this->assertEquals(2,                         $projects[0]->employees);
            $this->assertEquals('http://www.project2.com', $projects[0]->website);
            $this->assertEquals(200,                       $projects[0]->annualRevenue);
            $this->assertEquals('desc2',                   $projects[0]->description);
            $this->assertEquals('city2',                   $projects[0]->billingAddress->city);
            $this->assertEquals('country2',                $projects[0]->billingAddress->country);
            $this->assertEquals('postal2',                 $projects[0]->billingAddress->postalCode);
            $this->assertEquals('state2',                  $projects[0]->billingAddress->state);
            $this->assertEquals('street12',                $projects[0]->billingAddress->street1);
            $this->assertEquals('street22',                $projects[0]->billingAddress->street2);
            $this->assertEquals('b@b.com',                 $projects[0]->primaryEmail->emailAddress);
            $this->assertEquals('1',                       $projects[0]->primaryEmail->isInvalid);
            $this->assertEquals('1',                       $projects[0]->primaryEmail->optOut);
            $this->assertTrue($projects[0]->project->isSame($parentProject));
            $this->assertEquals('Banking',                 $projects[0]->industry->value);
            $this->assertEquals('Customer',                $projects[0]->type->value);

            $projects = Project::getByName('project3');
            $this->assertEquals(1,                         count($projects[0]));
            $this->assertEquals(323456,                    $projects[0]->officePhone);
            $this->assertEquals(777,                       $projects[0]->officeFax);
            $this->assertEquals(3,                         $projects[0]->employees);
            $this->assertEquals('http://www.project3.com', $projects[0]->website);
            $this->assertEquals(300,                       $projects[0]->annualRevenue);
            $this->assertEquals('desc3',                   $projects[0]->description);
            $this->assertEquals('city3',                   $projects[0]->billingAddress->city);
            $this->assertEquals('country3',                $projects[0]->billingAddress->country);
            $this->assertEquals('postal3',                 $projects[0]->billingAddress->postalCode);
            $this->assertEquals('state3',                  $projects[0]->billingAddress->state);
            $this->assertEquals('street13',                $projects[0]->billingAddress->street1);
            $this->assertEquals('street23',                $projects[0]->billingAddress->street2);
            $this->assertEquals('c@c.com',                 $projects[0]->primaryEmail->emailAddress);
            $this->assertEquals(null,                      $projects[0]->primaryEmail->isInvalid);
            $this->assertEquals(null,                      $projects[0]->primaryEmail->optOut);
            $this->assertTrue($projects[0]->project->isSame($parentProject));
            $this->assertEquals('Energy',                  $projects[0]->industry->value);
            $this->assertEquals('Vendor',                  $projects[0]->type->value);

            //Confirm 10 rows were processed as 'created'.
            $this->assertEquals(3, ImportDatabaseUtil::getCount($import->getTempTableName(), "status = "
                                                                 . ImportRowDataResultsUtil::CREATED));

            //Confirm that 0 rows were processed as 'updated'.
            $this->assertEquals(0, ImportDatabaseUtil::getCount($import->getTempTableName(),  "status = "
                                                                 . ImportRowDataResultsUtil::UPDATED));

            //Confirm 2 rows were processed as 'errors'.
            $this->assertEquals(0, ImportDatabaseUtil::getCount($import->getTempTableName(),  "status = "
                                                                 . ImportRowDataResultsUtil::ERROR));

            $beansWithErrors = ImportDatabaseUtil::getSubset($import->getTempTableName(),     "status = "
                                                                 . ImportRowDataResultsUtil::ERROR);
            $this->assertEquals(0, count($beansWithErrors));

            //test the parent project has 3 children
            $parentProject->forget();
            $parentProject = Project::getById($parentProjectId);
            $this->assertEquals(3, $parentProject->projects->count());
        }
    }
?>