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

    class ProjectTest extends ZurmoBaseTest
    {
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            SecurityTestHelper::createSuperAdmin();
            SecurityTestHelper::createUsers();
        }

        public function setUp()
        {
            parent::setUp();
            Yii::app()->user->userModel = User::getByUsername('super');
        }

        public function testConfirmProjectNameIdElementStillImplementsCorrectInterfaceFromParent()
        {
            $classToEvaluate        = new ReflectionClass('ProjectNameIdElement');
            $this->assertTrue($classToEvaluate->implementsInterface('DerivedElementInterface'));
        }

        public function testCreateAndGetProjectById()
        {
            $user = UserTestHelper::createBasicUser('Steven');
            $project = new Project();
            $project->owner       = $user;
            $project->name        = 'Test Project';
            $project->officePhone = '1234567890';
            $this->assertTrue($project->save());
            $id = $project->id;
            unset($project);
            $project = Project::getById($id);
            $this->assertEquals('Test Project', $project->name);
            $this->assertEquals('1234567890',   $project->officePhone);
        }

        /**
         * This test can be used by any frozen running test to test out boolean values in the database, that they
         * save and change correctly.
         * @depends testCreateAndGetProjectById
         */
        public function testEmailBooleanValues()
        {
            $projects = Project::getByName('Test Project');
            $this->assertEquals(1, count($projects));
            $project = $projects[0];

            $email = new Email();
            $email->optOut = 1;
            $email->emailAddress = 'a@a.com';
            $project->primaryEmail = $email;
            $email2 = new Email();
            $email2->optOut = 0;
            $email2->emailAddress = 'a@b.com';
            $project->secondaryEmail = $email2;
            $this->assertTrue($project->save());
            $id = $project->id;
            unset($project);

            $project = Project::getById($id);
            $this->assertEquals   (1,     $project->primaryEmail->optOut);
            $this->assertNotSame(true,  $project->primaryEmail->optOut);
            $this->assertEquals   (0,     $project->secondaryEmail->optOut);
            $this->assertNotSame(false, $project->secondaryEmail->optOut);

            $project->primaryEmail->optOut = 0;
            $this->assertTrue($project->save());
            unset($email);

            $project = Project::getById($id);
            $this->assertEquals   (0,     $project->primaryEmail->optOut);
            $this->assertNotSame(false, $project->primaryEmail->optOut);

            $project->primaryEmail->optOut = 3;
            $this->assertFalse($project->save());
            //forget project so optOut value 3 doesnt get cached.
            $project->forget();
        }

        /**
         * @depends testCreateAndGetProjectById
         */
        public function testGetProjectsByName()
        {
            $projects = Project::getByName('Test Project');
            $this->assertEquals(1, count($projects));
            $this->assertEquals('Test Project', $projects[0]->name);
        }

        /**
         * @depends testCreateAndGetProjectById
         */
        public function testGetLabel()
        {
            $projects = Project::getByName('Test Project');
            $this->assertEquals(1, count($projects));
            $this->assertEquals('Project',  $projects[0]::getModelLabelByTypeAndLanguage('Singular'));
            $this->assertEquals('Projects', $projects[0]::getModelLabelByTypeAndLanguage('Plural'));
        }

        /**
         * @depends testGetProjectsByName
         */
        public function testGetProjectsByNameForNonExistentName()
        {
            $projects = Project::getByName('Test Project 69');
            $this->assertEquals(0, count($projects));
        }

        /**
         * @depends testCreateAndGetProjectById
         */
        public function testGetAll()
        {
            $user = User::getByUsername('steven');

            $project = new Project();
            $project->owner = $user;
            $project->name  = 'Test Project 2';
            $this->assertTrue($project->save());
            $projects = Project::getAll();
            $this->assertEquals(2, count($projects));
            $this->assertTrue('Test Project'   == $projects[0]->name &&
                              'Test Project 2' == $projects[1]->name ||
                              'Test Project 2' == $projects[0]->name &&
                              'Test Project'   == $projects[1]->name);
        }

        /**
         * @depends testCreateAndGetProjectById
         */
        public function testSetAndGetOwner()
        {
            $user = UserTestHelper::createBasicUser('Dicky');

            $projects = Project::getByName('Test Project');
            $this->assertEquals(1, count($projects));
            $project = $projects[0];
            $project->owner = $user;
            $saved = $project->save();
            $this->assertTrue($saved);
            unset($user);
            $this->assertTrue($project->owner->id > 0);
            $user = $project->owner;
            $project->owner = null;
            $this->assertNotNull($project->owner);
            $this->assertFalse($project->validate());
            $project->forget();
        }

        /**
         * @depends testSetAndGetOwner
         */
        public function testReplaceOwner()
        {
            $projects = Project::getByName('Test Project');
            $this->assertEquals(1, count($projects));
            $project = $projects[0];
            $user = User::getByUsername('dicky');
            $this->assertEquals($user->id, $project->owner->id);
            unset($user);
            $project->owner = User::getByUsername('benny');
            $this->assertTrue($project->owner !== null);
            $user = $project->owner;
            $this->assertEquals('benny', $user->username);
            unset($user);
        }

        /**
         * @depends testCreateAndGetProjectById
         */
        public function testUpdateProjectFromForm()
        {
            $projects = Project::getByName('Test Project');
            $project = $projects[0];
            $this->assertEquals($project->name, 'Test Project');
            $postData = array('name' => 'New Name');
            $project->setAttributes($postData);
            $this->assertTrue($project->save());

            $id = $project->id;
            unset($project);
            $project = Project::getById($id);
            $this->assertEquals('New Name', $project->name);
        }

        /**
         * @depends testCreateAndGetProjectById
         */
        public function testEmailAndAddresses()
        {
            $projects = Project::getAll();
            $this->assertTrue(count($projects) > 0);
            $project = $projects[0];
            $project->primaryEmail->emailAddress   = 'thejman@zurmoinc.com';
            $project->primaryEmail->optOut         = 0;
            $project->primaryEmail->isInvalid      = 0;
            $project->secondaryEmail->emailAddress = 'digi@magic.net';
            $project->secondaryEmail->optOut       = 1;
            $project->secondaryEmail->isInvalid    = 1;
            $project->billingAddress->street1      = '129 Noodle Boulevard';
            $project->billingAddress->street2      = 'Apartment 6000A';
            $project->billingAddress->city         = 'Noodleville';
            $project->billingAddress->postalCode   = '23453';
            $project->billingAddress->country      = 'The Good Old US of A';
            $project->shippingAddress->street1     = '25 de Agosto 2543';
            $project->shippingAddress->street2     = 'Local 3';
            $project->shippingAddress->city        = 'Ciudad de Los Fideos';
            $project->shippingAddress->postalCode  = '5123-4';
            $project->shippingAddress->country     = 'Latinoland';
            $this->assertTrue($project->save(false));
            $id = $project->id;
            unset($project);
            $project = Project::getById($id);
            $this->assertEquals('thejman@zurmoinc.com', $project->primaryEmail->emailAddress);
            $this->assertEquals(0,                          $project->primaryEmail->optOut);
            $this->assertEquals(0,                          $project->primaryEmail->isInvalid);
            $this->assertEquals('digi@magic.net',           $project->secondaryEmail->emailAddress);
            $this->assertEquals(1,                          $project->secondaryEmail->optOut);
            $this->assertEquals(1,                          $project->secondaryEmail->isInvalid);
            $this->assertEquals('129 Noodle Boulevard',     $project->billingAddress->street1);
            $this->assertEquals('Apartment 6000A',          $project->billingAddress->street2);
            $this->assertEquals('Noodleville',              $project->billingAddress->city);
            $this->assertEquals('23453',                    $project->billingAddress->postalCode);
            $this->assertEquals('The Good Old US of A',     $project->billingAddress->country);
            $this->assertEquals('25 de Agosto 2543',        $project->shippingAddress->street1);
            $this->assertEquals('Local 3',                  $project->shippingAddress->street2);
            $this->assertEquals('Ciudad de Los Fideos',     $project->shippingAddress->city);
            $this->assertEquals('5123-4',                   $project->shippingAddress->postalCode);
            $this->assertEquals('Latinoland',               $project->shippingAddress->country);
        }

        /**
         * @depends testEmailAndAddresses
         */
        public function testDeleteProject()
        {
            $projects = Project::getAll();
            $this->assertEquals(2, count($projects));
            $projects[0]->delete();
            $projects = Project::getAll();
            $this->assertEquals(1, count($projects));
            $projects[0]->delete();
            $projects = Project::getAll();
            $this->assertEquals(0, count($projects));
        }

        /**
         * @depends testEmailAndAddresses
         */
        public function testGetAllWhenThereAreNone()
        {
            $projects = Project::getAll();
            $this->assertEquals(0, count($projects));
        }

        /**
         * @depends testCreateAndGetProjectById
         */
        public function testSetIndustryAndRetrieveDisplayName()
        {
            $user = User::getByUsername('steven');

            $values = array(
                'Automotive',
                'Adult Entertainment',
                'Financial Services',
                'Mercenaries & Armaments',
            );
            $industryFieldData = CustomFieldData::getByName('Industries');
            $industryFieldData->serializedData = serialize($values);
            $this->assertTrue($industryFieldData->save());

            $project = new Project();
            $project->owner = $user;
            $project->name = 'Jim\'s Software Company';
            $project->industry->value = $values[1];
            //$project->data            = $industryFieldData;
            $this->assertTrue($project->save());
            $this->assertTrue($project->id !== null);
            $id = $project->id;
            unset($project);
            $project = Project::getById($id);
            $this->assertEquals('Adult Entertainment', $project->industry->value);

            //Confirm a new project with no defaults set, will not show a default industry value.
            $project = new Project(false);
            $this->assertNull($project->industry->value);
        }

        public function testIndustryWithDefaultValueDoesntDefaultWhenProjectSetDefaultsFalse()
        {
            $values = array(
                'Automotive',
                'Adult Entertainment',
                'Financial Services',
                'Mercenaries & Armaments',
            );
            $industryFieldData = CustomFieldData::getByName('Industries');
            $industryFieldData->serializedData = serialize($values);
            $industryFieldData->defaultValue = $values[2];
            $this->assertTrue($industryFieldData->save());
            $project = new Project();
            $this->assertEquals($values[2], $project->industry->value);
            //Set first parameter to false, and confirm the value is null for industry
            $project = new Project(false);
            $this->assertNull($project->industry->value);
        }

        /**
         * @depends testReplaceOwner
         */
        public function testSetAttributeWithEmptyValue()
        {
            $user = User::getByUsername('benny');
            $this->assertEquals('benny', $user->username);
            $project = new Project();
            $project->name = 'abc';
            $project->owner = $user;
            $this->assertTrue($project->save());
            $project = Project::getById($project->id);
            $this->assertEquals('abc', $project->name);
            $fakePostData = array(
                'name' => '',
            );
            $project->setAttributes($fakePostData);
            $this->assertEquals('', $project->name);
        }

        public function testOwnerNotPopulatedWhenNoDefaults()
        {
            $project = new Project();
            $this->assertEquals('super', $project->owner->username);
            $project->validate();
            $project = new Project(false);
            $this->assertEquals('', $project->owner->username);
            $project->validate();
        }

        /**
         * @depends testCreateAndGetProjectById
         */
        public function testValidatesWithoutOwnerWhenSpecifyingAttributesToValidate()
        {
            $user = User::getByUsername('steven');
            $this->assertTrue($user->id > 0);
            $project = new Project(false);
            $_POST['MassEdit'] = array(
                'employees' => '1',
            );
            $_POST['fake'] = array(
                'employees' => 4,
            );
            PostUtil::sanitizePostForSavingMassEdit('fake');
            $project->setAttributes($_POST['fake']);
            $project->validate(array_keys($_POST['MassEdit']));
            $this->assertEquals(array(), $project->getErrors());
            $project->forget();
            $project = new Project(false);
            $_POST['MassEdit'] = array(
                'owner' => '1',
            );
            $_POST['fake']  = array(
                'owner'     => array(
                    'id'    => '',
                )
            );
            PostUtil::sanitizePostForSavingMassEdit('fake');
            $project->setAttributes($_POST['fake']);
            $project->validate(array_keys($_POST['MassEdit']));
            //there should be an owner error since it is specified but blank
            $this->assertNotEquals(array(), $project->getErrors());
            $project->forget();
            $project = new Project(false);
            $_POST['MassEdit'] = array(
                'employees' => '1',
                'owner'     => '2',
            );
            $_POST['fake'] = array(
                'employees' => 4,
                'owner'     => array(
                     'id' => $user->id,
                )
            );
            PostUtil::sanitizePostForSavingMassEdit('fake');
            $project->setAttributes($_POST['fake']);
            $project->validate(array_keys($_POST['MassEdit']));
            $this->assertEquals(array(), $project->getErrors());
        }

        public function testSettingDefaultValueForType()
        {
            $values = array(
                'Prospect',
                'Customer',
                'Vendor',
            );
            $typeFieldData = CustomFieldData::getByName('ProjectTypes');
            $typeFieldData->serializedData = serialize($values);
            $this->assertTrue($typeFieldData->save());

            //Add default value to type attribute for project.
            $attributeForm = new DropDownAttributeForm();
            $attributeForm->attributeName       = 'type';
            $attributeForm->attributeLabels  = array(
                'de' => 'Type',
                'en' => 'Type',
                'es' => 'Type',
                'fr' => 'Type',
                'it' => 'Type',
            );
            $attributeForm->isAudited           = true;
            $attributeForm->isRequired          = true;
            $attributeForm->defaultValueOrder   = 2;
            $attributeForm->customFieldDataData = $values;
            $attributeForm->customFieldDataName = 'ProjectTypes';

            $modelAttributesAdapterClassName = $attributeForm::getModelAttributeAdapterNameForSavingAttributeFormData();
            $adapter = new $modelAttributesAdapterClassName(new Project());
            try
            {
                $adapter->setAttributeMetadataFromForm($attributeForm);
            }
            catch (FailedDatabaseSchemaChangeException $e)
            {
                echo $e->getMessage();
                $this->fail();
            }

            $model = new Project();
            $this->assertEquals($values[2], $model->type->value);

            $user = User::getByUsername('billy');

            $_FAKEPOST = array(
                'name' => 'aTestProject',
                'type' => array(
                    'value' => $values[1],
                ),
                'owner'     => array(
                     'id' => $user->id,
                )
            );

            $model->setAttributes($_FAKEPOST);
            $this->assertEquals($values[1], $model->type->value);
            $this->assertTrue($model->save());
        }

        public function testMemberOfMembersRelation()
        {
            $user = User::getByUsername('billy');
            $hq      = ProjectTestHelper::createProjectByNameForOwner('Headquarters', $user);
            $branch1 = ProjectTestHelper::createProjectByNameForOwner('Branch 1',     $user);
            $branch2 = ProjectTestHelper::createProjectByNameForOwner('Branch 2',     $user);
            $branch3 = ProjectTestHelper::createProjectByNameForOwner('Branch 3',     $user);
            $branch4 = ProjectTestHelper::createProjectByNameForOwner('Branch 4',     $user);
            //Connect branches to headquarters.
            $hq->projects->add($branch1);
            $hq->projects->add($branch2);
            $hq->projects->add($branch3);
            $hq->projects->add($branch4);
            $this->assertTrue($hq->save());
            //Now add 2 sub branches for branch2. 2a and 2b
            $branch2a = ProjectTestHelper::createProjectByNameForOwner('Branch 2a', $user);
            $branch2b = ProjectTestHelper::createProjectByNameForOwner('Branch 2b', $user);
            $branch2->projects->add($branch2a);
            $branch2->projects->add($branch2b);
            $this->assertTrue($branch2->save());
            //Make sure hq shows branches 1 - 4 as projects.
            $this->assertEquals(4, $hq->projects->count());
            for ($i = 0; $i < 4; $i++)
            {
                $branchNumber = $i + 1;
                $this->assertEquals("Branch $branchNumber", $hq->projects[$i]->name);
                $this->assertEquals("Headquarters",         $hq->projects[$i]->project->name);
                $this->assertTrue  ($hq->isSame(            $hq->projects[$i]->project));
                $this->assertTrue  ($hq ===                 $hq->projects[$i]->project);
            }
            //Demonstrate that an project connected via project shows correctly from the other side after it is saved.
            $this->assertEquals(2, $branch2->projects->count());
            $project           = new Project();
            $project->project = $branch2;
            $project->owner    = $user;
            $project->name     = 'aNewProject';
            $this->assertTrue($project->save());

            $branch2Id = $branch2->id;
            $branch2->forget();
            unset($branch2);

            $branch2 = Project::getById($branch2Id);
            $this->assertEquals(3, $branch2->projects->count());
            $this->assertTrue($branch2->projects->contains($project));
        }

        /**
         * This test was originally here because we did not want the value of the phoneOffice when null to be coming
         * back as 0. But this is how redBean works to stay consistent with all dbs.
         * @see http://groups.google.com/group/redbeanorm/browse_thread/thread/e6a0a9d29838d973/90d12a0146544a0b
         * So for now, we will adjust the phone element in the user interface.
         */
        public function testOfficePhoneSetsToZeroWhenClearingAndForgetting()
        {
            $user = User::getByUsername('steven');
            $project = new Project();
            $project->owner       = $user;
            $project->name        = 'Test Project2';
            $project->officePhone = '1234567890';
            $this->assertTrue($project->save());
            $id = $project->id;
            unset($project);
            $project = Project::getById($id);
            $this->assertEquals('Test Project2', $project->name);
            $this->assertEquals('1234567890',   $project->officePhone);

            $project->setAttributes(array('officePhone' => ''));
            $this->assertTrue($project->save());
            $project->forget();
            $project = Project::getById($id);
            $this->assertEquals(null, $project->officePhone);
        }

        public function testGetModelClassNames()
        {
            $modelClassNames = ProjectsModule::getModelClassNames();
            $this->assertEquals(2, count($modelClassNames));
            $this->assertEquals('Project', $modelClassNames[0]);
            $this->assertEquals('ProjectSearch', $modelClassNames[1]);
        }

        public function testCreatingACustomDropDownAfterAnProjectExists()
        {
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;
            $project = ProjectTestHelper::createProjectByNameForOwner('intermediate', $super);
            $projectId = $project->id;
            $project->forget();

            //Create custom dropdown.
            $values = array(
                '747',
                'A380',
                'Seaplane',
                'Dive Bomber',
            );
            $labels = array('fr' => array('747 fr', 'A380 fr', 'Seaplane fr', 'Dive Bomber fr'),
                            'de' => array('747 de', 'A380 de', 'Seaplane de', 'Dive Bomber de'),
            );
            $airplanesFieldData = CustomFieldData::getByName('Airplanes');
            $airplanesFieldData->serializedData = serialize($values);
            $this->assertTrue($airplanesFieldData->save());

            $attributeForm = new DropDownAttributeForm();
            $attributeForm->attributeName       = 'testAirPlane';
            $attributeForm->attributeLabels  = array(
                'de' => 'Test Airplane 2 de',
                'en' => 'Test Airplane 2 en',
                'es' => 'Test Airplane 2 es',
                'fr' => 'Test Airplane 2 fr',
                'it' => 'Test Airplane 2 it',
            );
            $attributeForm->isAudited             = true;
            $attributeForm->isRequired            = true;
            $attributeForm->defaultValueOrder     = 1;
            $attributeForm->customFieldDataData   = $values;
            $attributeForm->customFieldDataName   = 'Airplanes';
            $attributeForm->customFieldDataLabels = $labels;

            $modelAttributesAdapterClassName = $attributeForm::getModelAttributeAdapterNameForSavingAttributeFormData();
            $adapter = new $modelAttributesAdapterClassName(new Project());
            $adapter->setAttributeMetadataFromForm($attributeForm);

            $compareData = array(
                '747',
                'A380',
                'Seaplane',
                'Dive Bomber',
            );
            //A new project will show the values fine.
            $projectNew = new Project();
            $this->assertEquals($compareData, unserialize($projectNew->testAirPlaneCstm->data->serializedData));

            //Now retrieve project again and make sure you can access the values in the dropdown.
            $project     = Project::getById($projectId);
            $this->assertEquals($compareData, unserialize($project->testAirPlaneCstm->data->serializedData));
        }
    }
?>
