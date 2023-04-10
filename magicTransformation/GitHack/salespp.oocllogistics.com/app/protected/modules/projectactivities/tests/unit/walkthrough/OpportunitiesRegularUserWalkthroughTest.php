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
     * Projectactivities Module Walkthrough.
     * Walkthrough for a peon user.  The peon user at first will have no granted
     * rights or permissions.  Most attempted actions will result in an ExitException
     * and a access failure view.  After this, we elevate the user with added tab rights
     * so that some of the actions will result in success and no exceptions being thrown.
     * There will still be some actions they cannot get too though because of the lack of
     * elevated permissions.  Then we will elevate permissions to allow the user to access
     * other owner's records.
     */
    class ProjectactivitiesRegularUserWalkthroughTest extends ZurmoRegularUserWalkthroughBaseTest
    {
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            $super = Yii::app()->user->userModel;

            //Setup test data owned by the super user.
            $account = AccountTestHelper::createAccountByNameForOwner        ('superAccount',  $super);
            AccountTestHelper::createAccountByNameForOwner                   ('superAccount2', $super);
            ContactTestHelper::createContactWithAccountByNameForOwner        ('superContact',  $super, $account);
            ContactTestHelper::createContactWithAccountByNameForOwner        ('superContact2', $super, $account);
            ProjectactivityTestHelper::createProjectactivityStagesIfDoesNotExist     ();
            ProjectactivityTestHelper::createProjectactivityWithAccountByNameForOwner('superOpp',      $super, $account);
            ProjectactivityTestHelper::createProjectactivityWithAccountByNameForOwner('superOpp2',     $super, $account);
            ProjectactivityTestHelper::createProjectactivityWithAccountByNameForOwner('superOpp3',     $super, $account);
            ProjectactivityTestHelper::createProjectactivityWithAccountByNameForOwner('superOpp4',     $super, $account);
            //Setup default dashboard.
            Dashboard::getByLayoutIdAndUser                                  (Dashboard::DEFAULT_USER_LAYOUT_ID, $super);
            ReadPermissionsOptimizationUtil::rebuild();
        }

        public function testRegularUserAllControllerActions()
        {
            //Now test all portlet controller actions

            //Now test peon with elevated rights to tabs /other available rights
            //such as convert lead

            //Now test peon with elevated permissions to models.

            //Test peon create/select from sublist actions with none and elevated permissions
        }

        public function testRegularUserAllControllerActionsNoElevation()
        {
            //Create projectactivity owned by user super.
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $projectactivity = ProjectactivityTestHelper::createProjectactivityByNameForOwner('Projectactivity', $super);
            Yii::app()->user->userModel = User::getByUsername('nobody');

            //Now test all portlet controller actions
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default');
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/index');
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/list');
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/create');
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/edit');
            $this->setGetArray(array('id' => $projectactivity->id));
            $this->resetPostArray();
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/details');
            $this->setGetArray(array('selectedIds' => '4,5,6,7,8', 'selectAll' => ''));  // Not Coding Standard
            $this->resetPostArray();
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/massEdit');
            $this->setGetArray(array('selectAll' => '1', 'Projectactivity_page' => 2));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/massEditProgressSave');

            //Autocomplete for projectactivity should fail
            $this->setGetArray(array('term' => 'super'));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/autoComplete');

            //actionModalList should fail
            $this->setGetArray(array(
                'modalTransferInformation' => array('sourceIdFieldId' => 'x', 'sourceNameFieldId' => 'y')
            ));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/modalList');

            //actionAuditEventsModalList should fail
            $this->setGetArray(array('id' => $projectactivity->id));
            $this->resetPostArray();
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/auditEventsModalList');

            //actionDelete should fail.
            $this->setGetArray(array('id' => $projectactivity->id));
            $this->resetPostArray();
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/delete');
        }

        /**
         * @depends testRegularUserAllControllerActionsNoElevation
         */
        public function testRegularUserControllerActionsWithElevationToAccessAndCreate()
        {
            //Now test peon with elevated rights to tabs /other available rights
            $nobody = $this->logoutCurrentUserLoginNewUserAndGetByUsername('nobody');

            //Now test peon with elevated rights to projectactivities
            $nobody->setRight('ProjectactivitiesModule', ProjectactivitiesModule::RIGHT_ACCESS_PROJECTACTIVITIES);
            $nobody->setRight('ProjectactivitiesModule', ProjectactivitiesModule::RIGHT_CREATE_PROJECTACTIVITIES);
            $nobody->setRight('ProjectactivitiesModule', ProjectactivitiesModule::RIGHT_DELETE_PROJECTACTIVITIES);
            $this->assertTrue($nobody->save());

            //Test nobody with elevated rights.
            Yii::app()->user->userModel = User::getByUsername('nobody');
            $content = $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/list');
            $this->assertFalse(strpos($content, 'Albert Einstein') === false);
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/create');

            //Test nobody can view an existing projectactivity he owns.
            $projectactivity = ProjectactivityTestHelper::createProjectactivityByNameForOwner('projectactivityOwnedByNobody', $nobody);

            //At this point the listview for leads should show the search/list and not the helper screen.
            $content = $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/list');
            $this->assertTrue(strpos($content, 'Albert Einstein') === false);

            $this->setGetArray(array('id' => $projectactivity->id));
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/edit');

            //Test nobody can delete an existing projectactivity he owns and it redirects to index.
            $this->setGetArray(array('id' => $projectactivity->id));
            $this->resetPostArray();
            $this->runControllerWithRedirectExceptionAndGetContent('projectactivities/default/delete',
                                                                   Yii::app()->createUrl('projectactivities/default/index'));

            //Autocomplete for Projectactivity should not fail.
            $this->setGetArray(array('term' => 'super'));
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/autoComplete');

            //actionModalList for Projectactivity should not fail.
            $this->setGetArray(array(
                'modalTransferInformation' => array('sourceIdFieldId' => 'x', 'sourceNameFieldId' => 'y')
            ));
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/modalList');
        }

        /**
         * @depends testRegularUserControllerActionsWithElevationToAccessAndCreate
         */
        public function testRegularUserControllerActionsWithElevationToModels()
        {
            //Create projectactivity owned by user super.
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $projectactivity = ProjectactivityTestHelper::createProjectactivityByNameForOwner('projectactivityForElevationToModelTest', $super);

            //Test nobody, access to edit and details should fail.
            $nobody = $this->logoutCurrentUserLoginNewUserAndGetByUsername('nobody');
            $this->setGetArray(array('id' => $projectactivity->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/edit');
            $this->setGetArray(array('id' => $projectactivity->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/details');

            //give nobody access to read
            Yii::app()->user->userModel = $super;
            $projectactivity->addPermissions($nobody, Permission::READ);
            $this->assertTrue($projectactivity->save());

            //Now the nobody user can access the details view.
            Yii::app()->user->userModel = $nobody;
            $this->setGetArray(array('id' => $projectactivity->id));
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/details');

            //Test nobody, access to edit should fail.
            $this->setGetArray(array('id' => $projectactivity->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/edit');

            //give nobody access to read and write
            Yii::app()->user->userModel = $super;
            $projectactivity->addPermissions($nobody, Permission::READ_WRITE_CHANGE_PERMISSIONS);
            $this->assertTrue($projectactivity->save());

            //Now the nobody user should be able to access the edit view and still the details view.
            Yii::app()->user->userModel = $nobody;
            $this->setGetArray(array('id' => $projectactivity->id));
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/details');
            $this->setGetArray(array('id' => $projectactivity->id));
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/edit');

            //revoke nobody access to read
            Yii::app()->user->userModel = $super;
            $projectactivity->addPermissions($nobody, Permission::READ_WRITE_CHANGE_PERMISSIONS, Permission::DENY);
            $this->assertTrue($projectactivity->save());

            //Test nobody, access to detail should fail.
            Yii::app()->user->userModel = $nobody;
            $this->setGetArray(array('id' => $projectactivity->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/details');
            $this->setGetArray(array('id' => $projectactivity->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/edit');

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

            //create projectactivity owned by super

            $projectactivity2 = ProjectactivityTestHelper::createProjectactivityByNameForOwner('testingParentRolePermission', $super);

            //Test userInParentRole, access to details and edit should fail.
            Yii::app()->user->userModel = $userInParentRole;
            $this->setGetArray(array('id' => $projectactivity2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/details');
            $this->setGetArray(array('id' => $projectactivity2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/edit');

            //give userInChildRole access to READ
            Yii::app()->user->userModel = $super;
            $projectactivity2->addPermissions($userInChildRole, Permission::READ);
            $this->assertTrue($projectactivity2->save());

            //Test userInChildRole, access to details should not fail.
            Yii::app()->user->userModel = $userInChildRole;
            $this->setGetArray(array('id' => $projectactivity2->id));
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/details');

            //Test userInParentRole, access to details should not fail.
            Yii::app()->user->userModel = $userInParentRole;
            $this->setGetArray(array('id' => $projectactivity2->id));
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/details');

            //give userInChildRole access to read and write
            Yii::app()->user->userModel = $super;
            $projectactivity2->addPermissions($userInChildRole, Permission::READ_WRITE_CHANGE_PERMISSIONS);
            $this->assertTrue($projectactivity2->save());

            //Test userInChildRole, access to edit should not fail.
            Yii::app()->user->userModel = $userInChildRole;
            $this->setGetArray(array('id' => $projectactivity2->id));
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/edit');

            //Test userInParentRole, access to edit should not fail.
            $this->logoutCurrentUserLoginNewUserAndGetByUsername($userInParentRole->username);
            $this->setGetArray(array('id' => $projectactivity2->id));
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/edit');

            //revoke userInChildRole access to read and write
            Yii::app()->user->userModel = $super;
            $projectactivity2->addPermissions($userInChildRole, Permission::READ_WRITE_CHANGE_PERMISSIONS, Permission::DENY);
            $this->assertTrue($projectactivity2->save());

            //Test userInChildRole, access to detail should fail.
            Yii::app()->user->userModel = $userInChildRole;
            $this->setGetArray(array('id' => $projectactivity2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/details');
            $this->setGetArray(array('id' => $projectactivity2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/edit');

            //Test userInParentRole, access to detail should fail.
            Yii::app()->user->userModel = $userInParentRole;
            $this->setGetArray(array('id' => $projectactivity2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/details');
            $this->setGetArray(array('id' => $projectactivity2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/edit');

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

            //Add access for the confused user to Projectactivities and creation of Projectactivities.
            $userInChildGroup->setRight('ProjectactivitiesModule', ProjectactivitiesModule::RIGHT_ACCESS_PROJECTACTIVITIES);
            $userInChildGroup->setRight('ProjectactivitiesModule', ProjectactivitiesModule::RIGHT_CREATE_PROJECTACTIVITIES);
            $this->assertTrue($userInChildGroup->save());

            //create projectactivity owned by super
            $projectactivity3 = ProjectactivityTestHelper::createProjectactivityByNameForOwner('testingParentGroupPermission', $super);

            //Test userInParentGroup, access to details and edit should fail.
            Yii::app()->user->userModel = $userInParentGroup;
            $this->setGetArray(array('id' => $projectactivity3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/details');
            $this->setGetArray(array('id' => $projectactivity3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/edit');

            //Test userInChildGroup, access to details and edit should fail.
            Yii::app()->user->userModel = $userInChildGroup;
            $this->setGetArray(array('id' => $projectactivity3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/details');
            $this->setGetArray(array('id' => $projectactivity3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/edit');

            //give parentGroup access to READ
            Yii::app()->user->userModel = $super;
            $projectactivity3->addPermissions($parentGroup, Permission::READ);
            $this->assertTrue($projectactivity3->save());

            //Test userInParentGroup, access to details should not fail.
            Yii::app()->user->userModel = $userInParentGroup;
            $this->setGetArray(array('id' => $projectactivity3->id));
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/details');

            //Test userInChildGroup, access to details should not fail.
            Yii::app()->user->userModel = $userInChildGroup;
            $this->setGetArray(array('id' => $projectactivity3->id));
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/details');

            //give parentGroup access to read and write
            Yii::app()->user->userModel = $super;
            $projectactivity3->addPermissions($parentGroup, Permission::READ_WRITE_CHANGE_PERMISSIONS);
            $this->assertTrue($projectactivity3->save());

            //Test userInParentGroup, access to edit should not fail.
            Yii::app()->user->userModel = $userInParentGroup;
            $this->setGetArray(array('id' => $projectactivity3->id));
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/edit');

            //Test userInChildGroup, access to edit should not fail.
            Yii::app()->user->userModel = $userInChildGroup;
            $this->logoutCurrentUserLoginNewUserAndGetByUsername($userInChildGroup->username);
            $this->setGetArray(array('id' => $projectactivity3->id));
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/edit');

            //revoke parentGroup access to read and write
            Yii::app()->user->userModel = $super;
            $projectactivity3->addPermissions($parentGroup, Permission::READ_WRITE_CHANGE_PERMISSIONS, Permission::DENY);
            $this->assertTrue($projectactivity3->save());

            //Test userInChildGroup, access to detail should fail.
            Yii::app()->user->userModel = $userInChildGroup;
            $this->setGetArray(array('id' => $projectactivity3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/details');
            $this->setGetArray(array('id' => $projectactivity3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/edit');

            //Test userInParentGroup, access to detail should fail.
            Yii::app()->user->userModel = $userInParentGroup;
            $this->setGetArray(array('id' => $projectactivity3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/details');
            $this->setGetArray(array('id' => $projectactivity3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('projectactivities/default/edit');

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

            //clear up the role relationships between users so not to effect next assertions
            $parentGroup->users->remove($userInParentGroup);
            $parentGroup->groups->remove($childGroup);
            $this->assertTrue($parentGroup->save());
            $childGroup->users->remove($userInChildGroup);
            $this->assertTrue($childGroup->save());
        }

        /**
         * @depends testRegularUserControllerActionsWithElevationToModels
         */
        public function testRegularUserViewingProjectactivityWithoutAccessToAccount()
        {
            $super       = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $aUser       = UserTestHelper::createBasicUser('aUser');
            $aUser->setRight('ProjectactivitiesModule', ProjectactivitiesModule::RIGHT_ACCESS_PROJECTACTIVITIES);
            $aUser->setRight('AccountsModule',      AccountsModule::RIGHT_ACCESS_ACCOUNTS);
            $this->assertTrue($aUser->save());
            $aUser       = User::getByUsername('aUser');
            $account     = AccountTestHelper::createAccountByNameForOwner('superTestAccount', $super);
            $projectactivity = ProjectactivityTestHelper::createProjectactivityWithAccountByNameForOwner('projectactivityOwnedByaUser', $aUser, $account);
            $account->forget();
            $id          = $projectactivity->id;
            $projectactivity->forget();
            unset($projectactivity);
            $this->logoutCurrentUserLoginNewUserAndGetByUsername('aUser');
            $content = $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default');
            $this->assertFalse(strpos($content, 'Fatal error: Method Account::__toString() must not throw an exception') > 0);
        }

         /**
         * @deletes selected leads.
         */
        public function testRegularMassDeleteActionsForSelectedIds()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $confused = User::getByUsername('confused');
            $nobody = User::getByUsername('nobody');
            $this->assertEquals(Right::DENY, $confused->getEffectiveRight('ZurmoModule', ZurmoModule::RIGHT_BULK_DELETE));
            $confused->setRight('ZurmoModule', ZurmoModule::RIGHT_BULK_DELETE);
            //Load MassDelete view for the 3 projectactivities.
            $projectactivities = Projectactivity::getAll();
            $this->assertEquals(9, count($projectactivities));
            $projectactivity1 = ProjectactivityTestHelper::createProjectactivityByNameForOwner('oppotunityDelete1', $confused);
            $projectactivity2 = ProjectactivityTestHelper::createProjectactivityByNameForOwner('oppotunityDelete2', $confused);
            $projectactivity3 = ProjectactivityTestHelper::createProjectactivityByNameForOwner('oppotunityDelete3', $nobody);
            $projectactivity4 = ProjectactivityTestHelper::createProjectactivityByNameForOwner('oppotunityDelete4', $confused);
            $projectactivity5 = ProjectactivityTestHelper::createProjectactivityByNameForOwner('oppotunityDelete5', $confused);
            $projectactivity6 = ProjectactivityTestHelper::createProjectactivityByNameForOwner('oppotunityDelete6', $nobody);
            $selectedIds = $projectactivity1->id . ',' . $projectactivity2->id . ',' . $projectactivity3->id ;    // Not Coding Standard
            $this->setGetArray(array('selectedIds' => $selectedIds, 'selectAll' => ''));  // Not Coding Standard
            $this->resetPostArray();
            $content = $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/massDelete');
            $this->assertFalse(strpos($content, '<strong>3</strong>&#160;Projectactivities selected for removal') === false);
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massDeleteProgressPageSize');
            $this->assertEquals(5, $pageSize);
            //calculating leads after adding 6 new records
            $projectactivities = Projectactivity::getAll();
            $this->assertEquals(15, count($projectactivities));
            //Deleting 6 projectactivities for pagination scenario
            //Run Mass Delete using progress save for page1
            $selectedIds = $projectactivity1->id . ',' . $projectactivity2->id . ',' . // Not Coding Standard
                           $projectactivity3->id . ',' . $projectactivity4->id . ',' . // Not Coding Standard
                           $projectactivity5->id . ',' . $projectactivity6->id;        // Not Coding Standard
            $this->setGetArray(array(
                'selectedIds' => $selectedIds, // Not Coding Standard
                'selectAll' => '',
                'Projectactivity_page' => 1));
            $this->setPostArray(array('selectedRecordCount' => 6));
            $content = $this->runControllerWithExitExceptionAndGetContent('projectactivities/default/massDelete');
            $projectactivities = Projectactivity::getAll();
            $this->assertEquals(10, count($projectactivities));

            //Run Mass Delete using progress save for page2
            $selectedIds = $projectactivity1->id . ',' . $projectactivity2->id . ',' . // Not Coding Standard
                           $projectactivity3->id . ',' . $projectactivity4->id . ',' . // Not Coding Standard
                           $projectactivity5->id . ',' . $projectactivity6->id;        // Not Coding Standard
            $this->setGetArray(array(
                'selectedIds' => $selectedIds, // Not Coding Standard
                'selectAll' => '',
                'Projectactivity_page' => 2));
            $this->setPostArray(array('selectedRecordCount' => 6));
            $content = $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/massDeleteProgress');
            $projectactivities = Projectactivity::getAll();
            $this->assertEquals(9, count($projectactivities));
        }

         /**
         *Test Bug with mass delete and multiple pages when using select all
         */
        public function testRegularMassDeletePagesProperlyAndRemovesAllSelected()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $confused = User::getByUsername('confused');
            $nobody = User::getByUsername('nobody');

            //Load MassDelete view for the 8 projectactivities.
            $projectactivities = Projectactivity::getAll();
            $this->assertEquals(9, count($projectactivities));
             //Deleting all projectactivities

            //mass Delete pagination scenario
            //Run Mass Delete using progress save for page1
            $this->setGetArray(array(
                'selectAll' => '1',
                'Projectactivity_page' => 1));
            $this->setPostArray(array('selectedRecordCount' => 9));
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massDeleteProgressPageSize');
            $this->assertEquals(5, $pageSize);
            $content = $this->runControllerWithExitExceptionAndGetContent('projectactivities/default/massDelete');
            $projectactivities = Projectactivity::getAll();
            $this->assertEquals(4, count($projectactivities));

           //Run Mass Delete using progress save for page2
            $this->setGetArray(array(
                'selectAll' => '1',
                'Projectactivity_page' => 2));
            $this->setPostArray(array('selectedRecordCount' => 9));
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massDeleteProgressPageSize');
            $this->assertEquals(5, $pageSize);
            $content = $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/massDeleteProgress');

            $projectactivities = Projectactivity::getAll();
            $this->assertEquals(0, count($projectactivities));
        }
    }
?>