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
     * Walkthrough for a peon user.  The peon user at first will have no granted
     * rights or permissions.  Most attempted actions will result in an ExitException
     * and a access failure view.  After this, we elevate the user with added tab rights
     * so that some of the actions will result in success and no exceptions being thrown.
     * There will still be some actions they cannot get too though because of the lack of
     * elevated permissions.  Then we will elevate permissions to allow the user to access
     * other owner's records.
     */
    class ProjectsRegularUserWalkthroughTest extends ZurmoRegularUserWalkthroughBaseTest
    {
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            //Setup test data owned by the super user.
            ProjectTestHelper::createProjectByNameForOwner('superProject',  Yii::app()->user->userModel);
            ProjectTestHelper::createProjectByNameForOwner('superProject2', Yii::app()->user->userModel);
            ProjectTestHelper::createProjectByNameForOwner('superProject3', Yii::app()->user->userModel);
            ProjectTestHelper::createProjectByNameForOwner('superProject4', Yii::app()->user->userModel);
            //Setup default dashboard.
            Dashboard::getByLayoutIdAndUser(Dashboard::DEFAULT_USER_LAYOUT_ID, Yii::app()->user->userModel);
            ReadPermissionsOptimizationUtil::rebuild();
        }

        public function testRegularUserAllControllerActionsNoElevation()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $superProjectId = self::getModelIdByModelNameAndName ('Project', 'superProject');
            Yii::app()->user->userModel = User::getByUsername('nobody');

            //Now test all portlet controller actions
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default');
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/index');
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/list');
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/create');
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/edit');
            $this->setGetArray(array('id' => $superProjectId));
            $this->resetPostArray();
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/details');
            $this->setGetArray(array('selectedIds' => '4,5,6,7,8', 'selectAll' => ''));  // Not Coding Standard
            $this->resetPostArray();
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/massEdit');
            $this->setGetArray(array('selectAll' => '1', 'Project_page' => 2));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/massEditProgressSave');

            //Autocomplete for Project should fail
            $this->setGetArray(array('term' => 'super'));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/autoComplete');

            //actionModalList should fail
            $this->setGetArray(array(
                'modalTransferInformation' => array('sourceIdFieldId' => 'x', 'sourceNameFieldId' => 'y')
            ));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/modalList');

            //actionAuditEventsModalList should fail
            $this->setGetArray(array('id' => $superProjectId));
            $this->resetPostArray();
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/auditEventsModalList');

            //actionDelete should fail.
            $this->setGetArray(array('id' => $superProjectId));
            $this->resetPostArray();
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');
        }

        /**
         * @depends testRegularUserAllControllerActionsNoElevation
         */
        public function testRegularUserControllerActionsWithElevationToAccessAndCreate()
        {
            $nobody = $this->logoutCurrentUserLoginNewUserAndGetByUsername('nobody');

            //Now test peon with elevated rights to projects
            $nobody->setRight('ProjectsModule', ProjectsModule::RIGHT_ACCESS_PROJECTS);
            $nobody->setRight('ProjectsModule', ProjectsModule::RIGHT_CREATE_PROJECTS);
            $nobody->setRight('ProjectsModule', ProjectsModule::RIGHT_DELETE_PROJECTS);
            $this->assertTrue($nobody->save());

            //Test nobody with elevated rights.
            Yii::app()->user->userModel = User::getByUsername('nobody');
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default/list');
            $this->assertFalse(strpos($content, 'Benjamin Franklin') === false);
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/create');

            //Test nobody can view an existing project he owns.
            $project = ProjectTestHelper::createProjectByNameForOwner('projectOwnedByNobody', $nobody);

            //At this point the listview for projects should show the search/list and not the helper screen.
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default/list');
            $this->assertTrue(strpos($content, 'Benjamin Franklin') === false);

            //Go to the a ccount editview.
            $this->setGetArray(array('id' => $project->id));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/edit');

            //Test nobody can delete an existing project he owns and it redirects to index.
            $this->setGetArray(array('id' => $project->id));
            $this->resetPostArray();
            $this->runControllerWithRedirectExceptionAndGetContent('projects/default/delete',
                        Yii::app()->createUrl('projects/default/index'));

            //Autocomplete for Project should not fail.
            $this->setGetArray(array('term' => 'super'));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/autoComplete');

            //actionModalList for Project should not fail.
            $this->setGetArray(array(
                'modalTransferInformation' => array('sourceIdFieldId' => 'x', 'sourceNameFieldId' => 'y')
            ));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/modalList');
        }

        /**
         * @depends testRegularUserControllerActionsWithElevationToAccessAndCreate
         */
        public function testRegularUserControllerActionsWithElevationToModels()
        {
            //Create project owned by user super.
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $project = ProjectTestHelper::createProjectByNameForOwner('testingProjectsForElevationToModelTest', $super);

            //Test nobody, access to edit, details and delete should fail.
            $nobody = $this->logoutCurrentUserLoginNewUserAndGetByUsername('nobody');
            $this->setGetArray(array('id' => $project->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/edit');
            $this->setGetArray(array('id' => $project->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/details');
            $this->setGetArray(array('id' => $project->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');

            //give nobody access to read
            Yii::app()->user->userModel = $super;
            $project->addPermissions($nobody, Permission::READ);
            $this->assertTrue($project->save());

            //Now the nobody user can access the details view.
            Yii::app()->user->userModel = $nobody;
            $this->setGetArray(array('id' => $project->id));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/details');

            //Test nobody, access to edit and delete should fail.
            $this->setGetArray(array('id' => $project->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/edit');
            $this->setGetArray(array('id' => $project->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');

            //give nobody access to read and write
            Yii::app()->user->userModel = $super;
            $project->addPermissions($nobody, Permission::READ_WRITE_CHANGE_PERMISSIONS);
            $this->assertTrue($project->save());

            //Now the nobody user should be able to access the edit view and still the details view.
            Yii::app()->user->userModel = $nobody;
            $this->setGetArray(array('id' => $project->id));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/details');
            $this->setGetArray(array('id' => $project->id));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/edit');

            //Test nobody, access to delete should fail.
            $this->setGetArray(array('id' => $project->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');

            //revoke nobody access to read
            Yii::app()->user->userModel = $super;
            $project->removePermissions($nobody, Permission::READ_WRITE_CHANGE_PERMISSIONS);
            $this->assertTrue($project->save());

            //Test nobody, access to detail, edit and delete should fail.
            Yii::app()->user->userModel = $nobody;
            $this->setGetArray(array('id' => $project->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/details');
            $this->setGetArray(array('id' => $project->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/edit');
            $this->setGetArray(array('id' => $project->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');

            //give nobody access to read, write and delete
            Yii::app()->user->userModel = $super;
            $project->addPermissions($nobody, Permission::READ_WRITE_DELETE);
            $this->assertTrue($project->save());

            //Test nobody, access to delete should not fail.
            Yii::app()->user->userModel = $nobody;
            $this->setGetArray(array('id' => $project->id));
            $this->resetPostArray();
            $this->runControllerWithRedirectExceptionAndGetContent('projects/default/delete',
                       Yii::app()->createUrl('projects/default/index'));

            //create some roles
            Yii::app()->user->userModel = $super;
            $parentRole = new Role();
            $parentRole->name = 'AAA';
            $this->assertTrue($parentRole->save());

            $childRole = new Role();
            $childRole->name = 'BBB';
            $this->assertTrue($childRole->save());

            $userInParentRole = User::getByUsername('confused');
            $userInChildRole = User::getByUsername('nobody');

            $childRole->users->add($userInChildRole);
            $this->assertTrue($childRole->save());
            $parentRole->users->add($userInParentRole);
            $parentRole->roles->add($childRole);
            $this->assertTrue($parentRole->save());

            //create project owned by super
            $project2 = ProjectTestHelper::createProjectByNameForOwner('testingProjectsParentRolePermission', $super);

            //Test userInChildRole, access to details, edit and delete should fail.
            Yii::app()->user->userModel = $userInChildRole;
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/details');
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/edit');
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');

            //Test userInParentRole, access to details, edit and delete should fail.
            Yii::app()->user->userModel = $userInParentRole;
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/details');
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/edit');
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');

            //give userInChildRole access to READ
            Yii::app()->user->userModel = $super;
            $project2->addPermissions($userInChildRole, Permission::READ);
            $this->assertTrue($project2->save());

            //Test userInChildRole, access to details should not fail.
            Yii::app()->user->userModel = $userInChildRole;
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/details');

            //Test userInChildRole, access to edit and delete should fail.
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/edit');
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');

            //Test userInParentRole, access to details should not fail.
            Yii::app()->user->userModel = $userInParentRole;
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/details');

            //Test userInParentRole, access to edit and delete should fail.
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/edit');
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');

            //give userInChildRole access to read and write
            Yii::app()->user->userModel = $super;
            $project2->addPermissions($userInChildRole, Permission::READ_WRITE_CHANGE_PERMISSIONS);
            $this->assertTrue($project2->save());

            //Test userInChildRole, access to edit should not fail.
            Yii::app()->user->userModel = $userInChildRole;
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/edit');

            //Test userInChildRole, access to delete should fail.
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');

            //Test userInParentRole, access to edit should not fail.
            $this->logoutCurrentUserLoginNewUserAndGetByUsername($userInParentRole->username);
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/edit');

            //Test userInParentRole, access to delete should fail.
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');

            //revoke userInChildRole access to read and write
            Yii::app()->user->userModel = $super;
            $project2->removePermissions($userInChildRole, Permission::READ_WRITE_CHANGE_PERMISSIONS);
            $this->assertTrue($project2->save());

            //Test userInChildRole, access to detail, edit and delete should fail.
            Yii::app()->user->userModel = $userInChildRole;
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/details');
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/edit');
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');

            //Test userInParentRole, access to detail, edit and delete should fail.
            Yii::app()->user->userModel = $userInParentRole;
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/details');
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/edit');
            $this->setGetArray(array('id' => $project2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');

            //give userInChildRole access to read, write and delete
            Yii::app()->user->userModel = $super;
            $project2->addPermissions($userInChildRole, Permission::READ_WRITE_DELETE);
            $this->assertTrue($project2->save());

            //Test userInParentRole, access to delete should not fail.
            Yii::app()->user->userModel = $userInParentRole;
            $this->setGetArray(array('id' => $project2->id));
            $this->resetPostArray();
            $this->runControllerWithRedirectExceptionAndGetContent('projects/default/delete',
                        Yii::app()->createUrl('projects/default/index'));

            //clear up the role relationships between users so not to effect next assertions
            $parentRole->users->remove($userInParentRole);
            $parentRole->roles->remove($childRole);
            $this->assertTrue($parentRole->save());
            $childRole->users->remove($userInChildRole);
            $this->assertTrue($childRole->save());

            //create some groups and assign users to groups
            Yii::app()->user->userModel = $super;
            $parentGroup = new Group();
            $parentGroup->name = 'AAA';
            $this->assertTrue($parentGroup->save());

            $childGroup = new Group();
            $childGroup->name = 'BBB';
            $this->assertTrue($childGroup->save());

            $userInChildGroup = User::getByUsername('confused');
            $userInParentGroup = User::getByUsername('nobody');

            $childGroup->users->add($userInChildGroup);
            $this->assertTrue($childGroup->save());
            $parentGroup->users->add($userInParentGroup);
            $parentGroup->groups->add($childGroup);
            $this->assertTrue($parentGroup->save());
            $parentGroup->forget();
            $childGroup->forget();
            $parentGroup = Group::getByName('AAA');
            $childGroup = Group::getByName('BBB');

            //Add access for the confused user to projects and creation of projects.
            $userInChildGroup->setRight('ProjectsModule', ProjectsModule::RIGHT_ACCESS_PROJECTS);
            $userInChildGroup->setRight('ProjectsModule', ProjectsModule::RIGHT_CREATE_PROJECTS);
            $userInChildGroup->setRight('ProjectsModule', ProjectsModule::RIGHT_DELETE_PROJECTS);
            $this->assertTrue($userInChildGroup->save());

            //create project owned by super
            $project3 = ProjectTestHelper::createProjectByNameForOwner('testingProjectsParentGroupPermission', $super);

            //Test userInParentGroup, access to details, edit and delete should fail.
            Yii::app()->user->userModel = $userInParentGroup;
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/details');
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/edit');
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');

            //Test userInChildGroup, access to details, edit and delete should fail.
            Yii::app()->user->userModel = $userInChildGroup;
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/details');
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/edit');
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');

            //give parentGroup access to READ
            Yii::app()->user->userModel = $super;
            $project3->addPermissions($parentGroup, Permission::READ);
            $this->assertTrue($project3->save());

            //Test userInParentGroup, access to details should not fail.
            Yii::app()->user->userModel = $userInParentGroup;
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/details');

            //Test userInParentGroup, access to edit and delete should fail.
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/edit');
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');

            //Test userInChildGroup, access to details should not fail.
            Yii::app()->user->userModel = $userInChildGroup;
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/details');

            //Test userInChildGroup, access to edit and delete should fail.
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/edit');
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');

            //give parentGroup access to read and write
            Yii::app()->user->userModel = $super;
            $project3->addPermissions($parentGroup, Permission::READ_WRITE_CHANGE_PERMISSIONS);
            $this->assertTrue($project3->save());

            //Test userInParentGroup, access to edit should not fail.
            Yii::app()->user->userModel = $userInParentGroup;
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/edit');

            //Test userInParentGroup, access to delete should fail.
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');

            //Test userInChildGroup, access to edit should not fail.
            Yii::app()->user->userModel = $userInChildGroup;
            $this->logoutCurrentUserLoginNewUserAndGetByUsername($userInChildGroup->username);
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/edit');

            //Test userInChildGroup, access to delete should fail.
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');

            //revoke parentGroup access to read and write
            Yii::app()->user->userModel = $super;
            $project3->removePermissions($parentGroup, Permission::READ_WRITE_CHANGE_PERMISSIONS);
            $this->assertTrue($project3->save());

            //Test userInChildGroup, access to detail, edit and delete should fail.
            Yii::app()->user->userModel = $userInChildGroup;
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/details');
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/edit');
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');

            //Test userInParentGroup, access to detail, edit and delete should fail.
            Yii::app()->user->userModel = $userInParentGroup;
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/details');
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/edit');
            $this->setGetArray(array('id' => $project3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/delete');

            //give parentGroup access to read, write and delete
            Yii::app()->user->userModel = $super;
            $project3->addPermissions($parentGroup, Permission::READ_WRITE_DELETE);
            $this->assertTrue($project3->save());

            //Test userInChildGroup, access to delete should not fail.
            Yii::app()->user->userModel = $userInChildGroup;
            $this->setGetArray(array('id' => $project3->id));
            $this->resetPostArray();
            $this->runControllerWithRedirectExceptionAndGetContent('projects/default/delete',
                        Yii::app()->createUrl('projects/default/index'));

            //clear up the role relationships between users so not to effect next assertions
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $userInParentGroup->forget();
            $userInChildGroup->forget();
            $childGroup->forget();
            $parentGroup->forget();
            $userInParentGroup          = User::getByUsername('nobody');
            $userInChildGroup           = User::getByUsername('confused');
            $childGroup                 = Group::getByName('BBB');
            $parentGroup                = Group::getByName('AAA');

            $parentGroup->users->remove($userInParentGroup);
            $parentGroup->groups->remove($childGroup);
            $this->assertTrue($parentGroup->save());
            $childGroup->users->remove($userInChildGroup);
            $this->assertTrue($childGroup->save());
        }

        /**
         * @depends testRegularUserControllerActionsWithElevationToModels
         */
        public function testRegularUserSwitchingOwnershipLosesAccessToProject()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $confused = User::getByUsername('confused');
            //$this->assertEquals(Right::DENY, $confused->getEffectiveRight('ProjectsModule', ProjectsModule::RIGHT_ACCESS_PROJECTS));
            //$this->assertEquals(Right::DENY, $confused->getEffectiveRight('ProjectsModule', ProjectsModule::RIGHT_CREATE_PROJECTS));
            //$confused->setRight('ProjectsModule', ProjectsModule::RIGHT_ACCESS_PROJECTS);
            //$confused->setRight('ProjectsModule', ProjectsModule::RIGHT_CREATE_PROJECTS);
            //$this->assertTrue($confused->save());

            Yii::app()->user->userModel = $confused;
            $project = ProjectTestHelper::createProjectByNameForOwner('Switcheroo', $confused);
            //User can get to edit ok.
            $this->setGetArray(array('id' => $project->id));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/edit');

            //Save project, just name.
            $this->setPostArray(array('Project' => array('name' => 'Switcheroo Inc.')));
            //Make sure the redirect is to the details view and not the list view.
            $this->runControllerWithRedirectExceptionAndGetContent('projects/default/edit',
                        Yii::app()->createUrl('projects/default/details', array('id' => $project->id)));

            //Now save project changing the owner, the redirect should go to the list view and provide a flash message.
            $this->setPostArray(array('Project' => array('owner' => array('id' => $super->id))));
            //Make sure the redirect is to the details view and not the list view.
            $this->runControllerWithRedirectExceptionAndGetContent('projects/default/edit',
                        Yii::app()->createUrl('projects/default/index'));
            ///Confirm flash message is set.
            $this->assertContains('You no longer have permissions to access Switcheroo Inc',
                                  Yii::app()->user->getFlash('notification'));
        }

        /**
         * @depends testRegularUserSwitchingOwnershipLosesAccessToProject
         */
        public function testRegularUserBullkWriteWhereSomeItemsTheyDontHavePrivledgesToDoIt()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $confused = User::getByUsername('confused');
            $billy = User::getByUsername('billy');
            $this->assertEquals(Right::DENY, $confused->getEffectiveRight('ZurmoModule', ZurmoModule::RIGHT_BULK_WRITE));
            $confused->setRight('ZurmoModule', ZurmoModule::RIGHT_BULK_WRITE);
            $this->assertTrue($confused->save());
            $project1 = ProjectTestHelper::createProjectByNameForOwner('canUpdate', $confused);
            $project2 = ProjectTestHelper::createProjectByNameForOwner('canUpdate2', $confused);
            $project3 = ProjectTestHelper::createProjectByNameForOwner('cannotUpdate', $billy);
            $this->assertEquals($confused,  $project1->owner);
            $this->assertEquals($confused,  $project2->owner);
            $this->assertEquals($billy, $project3->owner);

            //Give confused user read access to $project3
            $this->assertNotEquals($project3->owner->id, $confused->id);
            $this->assertEquals(Permission::NONE, $project3->getEffectivePermissions      ($confused));
            $project3->addPermissions($confused, Permission::READ);
            $this->assertTrue($project3->save());
            $this->assertEquals(Permission::READ, $project3->getEffectivePermissions      ($confused));

            //Make confused user the current user.
            Yii::app()->user->userModel = $confused;

            //Load MassEdit view for the 3 projects.
            $selectedIds = $project1->id . ',' . $project2->id . ',' . $project3->id ;    // Not Coding Standard
            $this->setGetArray(array('selectedIds' => $selectedIds, 'selectAll' => ''));  // Not Coding Standard
            $this->resetPostArray();
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default/massEdit');
            $this->assertFalse(strpos($content, '<strong>3</strong>&#160;records selected for updating') === false);

            //Test trying to change the owner to super and trying to change name which is required, but leaving it blank.
            //This will result in a validation error, but since since the owner has been selected as super, we want
            //to make sure there are no exceptions and the validation appears in the user interface correctly.
            $this->setGetArray(array(
                'selectedIds' => $selectedIds, // Not Coding Standard
                'selectAll' => '',
                'Project_page' => 1));
            $this->setPostArray(array(
                'Project'  => array('name' => '', 'owner' => array('id' => $super->id)),
                'MassEdit' => array('name' => 1, 'owner' => 1)
            ));
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default/massEdit');
            $this->assertFalse(strpos($content, '<strong>3</strong>&#160;records selected for updating') === false);

            //Now set office phone to a real value, keep owner set at super, and try again. This time the mass update
            //should be successful except for project3 which the confused user does not have write access too.
            $this->setGetArray(array(
                'selectedIds' => $selectedIds, // Not Coding Standard
                'selectAll' => '',
                'Project_page' => 1));
            $this->setPostArray(array(
                'Project'  => array('name' => '7799', 'owner' => array('id' => $super->id)),
                'MassEdit' => array('name' => 1, 'owner' => 1)
            ));
            $content = $this->runControllerWithRedirectExceptionAndGetContent('projects/default/massEdit');
            //Confirm the flash message shows the correct information that 1 failed.
            $this->assertContains('Successfully updated 2 records. 1 project skipped because you do not have sufficient permissions.',
                                  Yii::app()->user->getFlash('notification'));

            //Confirm updates are correct
            Yii::app()->user->userModel = $super;
            $project1 = Project::getById($project1->id);
            $project2 = Project::getById($project2->id);
            $project3 = Project::getById($project3->id);

            $this->assertEquals ('7799',         $project1->name);
            $this->assertEquals ('7799',         $project2->name);
            $this->assertEquals ('cannotUpdate', $project3->name);
            $this->assertEquals ($super->getFullName(), $project2->owner->getFullName());
            $this->assertEquals ($super->getFullName(), $project2->owner->getFullName());
            $this->assertEquals ($billy->getFullName(), $project3->owner->getFullName());
        }

         /**
         * @deletes selected projects.
         */

        public function testMassDeleteActionsForSelectedIds()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $confused = User::getByUsername('confused');
            $billy = User::getByUsername('billy');
            $this->assertEquals(Right::DENY, $confused->getEffectiveRight('ZurmoModule', ZurmoModule::RIGHT_BULK_DELETE));
            $confused->setRight('ZurmoModule', ZurmoModule::RIGHT_BULK_DELETE);
            //Load MassDelete view for the 3 projects.
            $projects = Project::getAll();
            $this->assertEquals(8, count($projects));

            $project1 = ProjectTestHelper::createProjectByNameForOwner('canDelete1', $confused);
            $project2 = ProjectTestHelper::createProjectByNameForOwner('canDelete2', $confused);
            $project3 = ProjectTestHelper::createProjectByNameForOwner('canDelete3', $billy);
            $project4 = ProjectTestHelper::createProjectByNameForOwner('canDelete4', $confused);
            $project5 = ProjectTestHelper::createProjectByNameForOwner('canDelete5', $confused);
            $project6 = ProjectTestHelper::createProjectByNameForOwner('canDelete6', $billy);

            $selectedIds = $project1->id . ',' . $project2->id . ',' . $project3->id ;    // Not Coding Standard
            $this->setGetArray(array('selectedIds' => $selectedIds,'selectAll' => ''));  // Not Coding Standard
            $this->resetPostArray();
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default/massDelete');
            $this->assertFalse(strpos($content, '<strong>3</strong>&#160;Projects selected for removal') === false);
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massDeleteProgressPageSize');
            $this->assertEquals(5, $pageSize);
            //calculating projects after adding 6 new records
            $projects = Project::getAll();
            $this->assertEquals(14, count($projects));

            //Deleting 6 projects for pagination scenario
            //Run Mass Delete using progress save for page1
            $selectedIds = $project1->id . ',' . $project2->id . ',' . // Not Coding Standard
                           $project3->id . ',' . $project4->id . ',' . // Not Coding Standard
                           $project5->id . ',' . $project6->id;        // Not Coding Standard
            $this->setGetArray(array(
                'selectedIds' => $selectedIds, // Not Coding Standard
                'selectAll' => '',
                'Project_page' => 1));
            $this->setPostArray(array('selectedRecordCount' => 6));
            $content = $this->runControllerWithExitExceptionAndGetContent('projects/default/massDelete');
            $projects = Project::getAll();
            $this->assertEquals(9, count($projects));

            //Run Mass Delete using progress save for page2
            $selectedIds = $project1->id . ',' . $project2->id . ',' . // Not Coding Standard
                           $project3->id . ',' . $project4->id . ',' . // Not Coding Standard
                           $project5->id . ',' . $project6->id;        // Not Coding Standard
            $this->setGetArray(array(
                'selectedIds' => $selectedIds, // Not Coding Standard
                'selectAll' => '',
                'Project_page' => 2));
            $this->setPostArray(array('selectedRecordCount' => 6));
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default/massDeleteProgress');
            $projects = Project::getAll();
            $this->assertEquals(8, count($projects));
        }

         /**
         *Test Bug with mass delete and multiple pages when using select all
         */
        public function testMassDeletePagesProperlyAndRemovesAllSelected()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $confused = User::getByUsername('confused');
            $billy = User::getByUsername('billy');

            //Load MassDelete view for the 8 projects.
            $projects = Project::getAll();
            $this->assertEquals(8, count($projects));
             //Deleting all projects

            //mass Delete pagination scenario
            //Run Mass Delete using progress save for page1
            $this->setGetArray(array(
                'selectAll' => '1',
                'Project_page' => 1));
            $this->setPostArray(array('selectedRecordCount' => 8));
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massDeleteProgressPageSize');
            $this->assertEquals(5, $pageSize);
            $content = $this->runControllerWithExitExceptionAndGetContent('projects/default/massDelete');
            $projects = Project::getAll();
            $this->assertEquals(3, count($projects));

           //Run Mass Delete using progress save for page2
            $this->setGetArray(array(
                'selectAll' => '1',
                'Project_page' => 2));
            $this->setPostArray(array('selectedRecordCount' => 8));
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massDeleteProgressPageSize');
            $this->assertEquals(5, $pageSize);
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default/massDeleteProgress');
            $projects = Project::getAll();
            $this->assertEquals(0, count($projects));
        }
    }
?>
