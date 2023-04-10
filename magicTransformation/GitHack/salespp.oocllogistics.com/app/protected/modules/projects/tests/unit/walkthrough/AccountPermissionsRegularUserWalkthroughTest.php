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
     * Project Permissions Regular User Walkthrough.
     * Walkthrough for the regular user of all possible permissions scenarios. Primarily focuses on changing the
     * DerivedExplicitReadWriteModelPermissions element values.
     */
    class ProjectPermissionsRegularUserWalkthroughTest extends ZurmoRegularUserWalkthroughBaseTest
    {
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            SecurityTestHelper::createSuperAdmin();
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;
            ReadPermissionsOptimizationUtil::rebuild();

            //Add the nobody user to an project, but only read only.
            $nobody = User::getByUsername('nobody');
            $project = ProjectTestHelper::createProjectByNameForOwner('superProjectReadableByNobody',  Yii::app()->user->userModel);
            $project->addPermissions($nobody, Permission::READ, Permission::ALLOW);
            assert($project->save()); // Not Coding Standard
            ReadPermissionsOptimizationUtil::securableItemGivenPermissionsForUser($project, $nobody);

            //Give the nobody user rights to the projects module.
            $nobody->setRight('ProjectsModule', ProjectsModule::RIGHT_ACCESS_PROJECTS);
            $nobody->setRight('ProjectsModule', ProjectsModule::RIGHT_CREATE_PROJECTS);
            assert($nobody->save()); // Not Coding Standard

            $everyoneGroup = Group::getByName(Group::EVERYONE_GROUP_NAME);
            assert($everyoneGroup->save()); // Not Coding Standard

            $group1        = new Group();
            $group1->name  = 'Group1';
            assert($group1->save()); // Not Coding Standard
        }

        public function testRegularUserCanViewOrNotViewDerivedExplicitReadWriteModelPermissionsElement()
        {
            //Set the current user as the nobody user.
            $nobody          = $this->logoutCurrentUserLoginNewUserAndGetByUsername('nobody');

            //Create an project by the nobody user.
            $project = ProjectTestHelper::
                       createProjectByNameForOwner('nobodyProject',  $nobody);

            //Confirm the nobody user can view the details of that project and can see the
            //DerivedExplicitReadWriteModelPermissions element.
            $this->setGetArray(array('id' => $project->id));
            $this->resetPostArray();
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default/details');
            //Confirm content does have the security element
            $this->assertFalse(strpos($content, 'Who can read and write') === false);

            //Now go to an project details with nobody where nobody can read, but not write.
            //In this scenario the DerivedExplicitReadWriteModelPermissions element is still shown
            //at the bottom of the details area.
            $projects = Project::getByName('superProjectReadableByNobody');
            $this->assertEquals(1, count($projects));
            $projectId = $projects[0]->id;
            $this->setGetArray(array('id' => $projectId));
            $this->resetPostArray();
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default/details');
            //Confirm content does not have security element
            $this->assertFalse(strpos($content, 'Who can read and write') === false);
        }

        /**
         * @depends testRegularUserCanViewOrNotViewDerivedExplicitReadWriteModelPermissionsElement
         */
        public function testRegularUserEditExistingProjectAndChangeExplicitPermissions()
        {
            $nobody         = $this->logoutCurrentUserLoginNewUserAndGetByUsername('nobody');
            $projectId      = self::getModelIdByModelNameAndName ('Project', 'nobodyProject');
            $group1         = Group::getByName('Group1');
            $everyoneGroup  = Group::getByName(Group::EVERYONE_GROUP_NAME);

            //Edit nobody's project and add an explicit permissions.
            //Save project and add a non-everyone group permission.
            //Permissions is the only thing changing on the project.
            $this->setGetArray(array('id' => $projectId));
            $postData = array('type' => ExplicitReadWriteModelPermissionsUtil::MIXED_TYPE_NONEVERYONE_GROUP,
                    'nonEveryoneGroup' => $group1->id);
            $this->setPostArray(array('Project' =>
                    array('explicitReadWriteModelPermissions' => $postData)));
            //Make sure the redirect is to the details view and not the list view.
            $this->runControllerWithRedirectExceptionAndGetContent('projects/default/edit'); // Not Coding Standard
            //Confirm the permissions are set right based on how the project was saved.
            $explicitReadWriteModelPermissions = ExplicitReadWriteModelPermissionsUtil::
                                                 makeBySecurableItem(Project::getById($projectId));
            $readWritePermitables = $explicitReadWriteModelPermissions->getReadWritePermitables();
            $readOnlyPermitables  = $explicitReadWriteModelPermissions->getReadOnlyPermitables();
            $this->assertEquals(1, count($readWritePermitables));
            $this->assertEquals(0, count($readOnlyPermitables));
            $this->assertEquals($group1, $readWritePermitables[$group1->id]);

            //Edit nobody's project and change the explicit permissions.
            $this->setGetArray(array('id' => $projectId));
            $postData = array('type' => ExplicitReadWriteModelPermissionsUtil::MIXED_TYPE_EVERYONE_GROUP);
            $this->setPostArray(array('Project' =>
                array('explicitReadWriteModelPermissions' => $postData)));
            //Make sure the redirect is to the details view and not the list view.
            $this->runControllerWithRedirectExceptionAndGetContent('projects/default/edit'); // Not Coding Standard
            //Confirm the permissions are set right based on how the project was saved.
            $explicitReadWriteModelPermissions = ExplicitReadWriteModelPermissionsUtil::
                                                 makeBySecurableItem(Project::getById($projectId));
            $readWritePermitables = $explicitReadWriteModelPermissions->getReadWritePermitables();
            $readOnlyPermitables  = $explicitReadWriteModelPermissions->getReadOnlyPermitables();
            $this->assertEquals(1, count($readWritePermitables));
            $this->assertEquals(0, count($readOnlyPermitables));
            $this->assertEquals($everyoneGroup, $readWritePermitables[$everyoneGroup->id]);

            //Edit nobody's project and remove the explicit permissions.
            $this->setGetArray(array('id' => $projectId));
            $postData = array('type' => null);
            $this->setPostArray(array('Project' =>
                array('explicitReadWriteModelPermissions' => $postData)));
            //Make sure the redirect is to the details view and not the list view.
            $this->runControllerWithRedirectExceptionAndGetContent('projects/default/edit'); // Not Coding Standard
            //Confirm the permissions are set right based on how the project was saved.
            $explicitReadWriteModelPermissions = ExplicitReadWriteModelPermissionsUtil::
                                                 makeBySecurableItem(Project::getById($projectId));
            $readWritePermitables = $explicitReadWriteModelPermissions->getReadWritePermitables();
            $readOnlyPermitables  = $explicitReadWriteModelPermissions->getReadOnlyPermitables();
            $this->assertEquals(0, count($readWritePermitables));
            $this->assertEquals(0, count($readOnlyPermitables));
        }

        /**
         * @depends testRegularUserEditExistingProjectAndChangeExplicitPermissions
         */
        public function testRegularUserCreateProjectAndChangeExplicitPermissions()
        {
            $nobody         = $this->logoutCurrentUserLoginNewUserAndGetByUsername('nobody');
            $group1         = Group::getByName('Group1');
            $everyoneGroup  = Group::getByName(Group::EVERYONE_GROUP_NAME);

            //Create an project for nobody with no explicit permissions.
            $this->resetGetArray();
            $postData = array('type' => null);
            $this->setPostArray(array('Project' => array(
                                            'name'        => 'myNewProject',
                                            'officePhone' => '456765421',
                                            'explicitReadWriteModelPermissions' => $postData)));
            //Make sure the redirect is to the details view and not the list view.
            $this->runControllerWithRedirectExceptionAndGetContent('projects/default/create'); // Not Coding Standard
            //Confirm the permissions are set right based on how the project was saved.
            $projects = Project::getByName('myNewProject');
            $this->assertEquals(1, count($projects));
            $projectId = $projects[0]->id;
            $explicitReadWriteModelPermissions = ExplicitReadWriteModelPermissionsUtil::
                                                 makeBySecurableItem(Project::getById($projectId));
            $readWritePermitables = $explicitReadWriteModelPermissions->getReadWritePermitables();
            $readOnlyPermitables  = $explicitReadWriteModelPermissions->getReadOnlyPermitables();
            $this->assertEquals(0, count($readWritePermitables));
            $this->assertEquals(0, count($readOnlyPermitables));

            //Create an project for nobody and add explicit permissions for the everyone group.
            $this->resetGetArray();
            $postData = array('type' => ExplicitReadWriteModelPermissionsUtil::MIXED_TYPE_EVERYONE_GROUP);
            $this->setPostArray(array('Project' => array(
                                            'name'        => 'myNewProject2',
                                            'officePhone' => '456765421',
                                            'explicitReadWriteModelPermissions' => $postData)));
            //Make sure the redirect is to the details view and not the list view.
            $this->runControllerWithRedirectExceptionAndGetContent('projects/default/create'); // Not Coding Standard
            //Confirm the permissions are set right based on how the project was saved.
                        $projects = Project::getByName('myNewProject2');
            $this->assertEquals(1, count($projects));
            $projectId = $projects[0]->id;
            $explicitReadWriteModelPermissions = ExplicitReadWriteModelPermissionsUtil::
                                                 makeBySecurableItem(Project::getById($projectId));
            $readWritePermitables = $explicitReadWriteModelPermissions->getReadWritePermitables();
            $readOnlyPermitables  = $explicitReadWriteModelPermissions->getReadOnlyPermitables();
            $this->assertEquals(1, count($readWritePermitables));
            $this->assertEquals(0, count($readOnlyPermitables));
            $this->assertEquals($everyoneGroup, $readWritePermitables[$everyoneGroup->id]);

            //Create an project for nobody and add explicit permissions for a non-everyone group.
            $this->resetGetArray();
            $postData = array('type' => ExplicitReadWriteModelPermissionsUtil::MIXED_TYPE_NONEVERYONE_GROUP,
                  'nonEveryoneGroup' => $group1->id);
            $this->setPostArray(array('Project' => array(
                                            'name'        => 'myNewProject3',
                                            'officePhone' => '456765421',
                                            'explicitReadWriteModelPermissions' => $postData)));
            //Make sure the redirect is to the details view and not the list view.
            $this->runControllerWithRedirectExceptionAndGetContent('projects/default/create'); // Not Coding Standard
            //Confirm the permissions are set right based on how the project was saved.
                        $projects = Project::getByName('myNewProject3');
            $this->assertEquals(1, count($projects));
            $projectId = $projects[0]->id;
            $explicitReadWriteModelPermissions = ExplicitReadWriteModelPermissionsUtil::
                                                 makeBySecurableItem(Project::getById($projectId));
            $readWritePermitables = $explicitReadWriteModelPermissions->getReadWritePermitables();
            $readOnlyPermitables  = $explicitReadWriteModelPermissions->getReadOnlyPermitables();
            $this->assertEquals(1, count($readWritePermitables));
            $this->assertEquals(0, count($readOnlyPermitables));
            $this->assertEquals($group1, $readWritePermitables[$group1->id]);
        }
    }
?>