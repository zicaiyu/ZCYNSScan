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
     * Sensitiveinfos Module Walkthrough.
     * Walkthrough for a peon user.  The peon user at first will have no granted
     * rights or permissions.  Most attempted actions will result in an ExitException
     * and a access failure view.  After this, we elevate the user with added tab rights
     * so that some of the actions will result in success and no exceptions being thrown.
     * There will still be some actions they cannot get too though because of the lack of
     * elevated permissions.  Then we will elevate permissions to allow the user to access
     * other owner's records.
     */
    class SensitiveinfosRegularUserWalkthroughTest extends ZurmoRegularUserWalkthroughBaseTest
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
            SensitiveinfoTestHelper::createSensitiveinfoStagesIfDoesNotExist     ();
            SensitiveinfoTestHelper::createSensitiveinfoWithAccountByNameForOwner('superOpp',      $super, $account);
            SensitiveinfoTestHelper::createSensitiveinfoWithAccountByNameForOwner('superOpp2',     $super, $account);
            SensitiveinfoTestHelper::createSensitiveinfoWithAccountByNameForOwner('superOpp3',     $super, $account);
            SensitiveinfoTestHelper::createSensitiveinfoWithAccountByNameForOwner('superOpp4',     $super, $account);
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
            //Create sensitiveinfo owned by user super.
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $sensitiveinfo = SensitiveinfoTestHelper::createSensitiveinfoByNameForOwner('Sensitiveinfo', $super);
            Yii::app()->user->userModel = User::getByUsername('nobody');

            //Now test all portlet controller actions
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default');
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/index');
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/list');
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/create');
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/edit');
            $this->setGetArray(array('id' => $sensitiveinfo->id));
            $this->resetPostArray();
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/details');
            $this->setGetArray(array('selectedIds' => '4,5,6,7,8', 'selectAll' => ''));  // Not Coding Standard
            $this->resetPostArray();
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/massEdit');
            $this->setGetArray(array('selectAll' => '1', 'Sensitiveinfo_page' => 2));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/massEditProgressSave');

            //Autocomplete for sensitiveinfo should fail
            $this->setGetArray(array('term' => 'super'));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/autoComplete');

            //actionModalList should fail
            $this->setGetArray(array(
                'modalTransferInformation' => array('sourceIdFieldId' => 'x', 'sourceNameFieldId' => 'y')
            ));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/modalList');

            //actionAuditEventsModalList should fail
            $this->setGetArray(array('id' => $sensitiveinfo->id));
            $this->resetPostArray();
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/auditEventsModalList');

            //actionDelete should fail.
            $this->setGetArray(array('id' => $sensitiveinfo->id));
            $this->resetPostArray();
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/delete');
        }

        /**
         * @depends testRegularUserAllControllerActionsNoElevation
         */
        public function testRegularUserControllerActionsWithElevationToAccessAndCreate()
        {
            //Now test peon with elevated rights to tabs /other available rights
            $nobody = $this->logoutCurrentUserLoginNewUserAndGetByUsername('nobody');

            //Now test peon with elevated rights to sensitiveinfos
            $nobody->setRight('SensitiveinfosModule', SensitiveinfosModule::RIGHT_ACCESS_SENSITIVEINFOS);
            $nobody->setRight('SensitiveinfosModule', SensitiveinfosModule::RIGHT_CREATE_SENSITIVEINFOS);
            $nobody->setRight('SensitiveinfosModule', SensitiveinfosModule::RIGHT_DELETE_SENSITIVEINFOS);
            $this->assertTrue($nobody->save());

            //Test nobody with elevated rights.
            Yii::app()->user->userModel = User::getByUsername('nobody');
            $content = $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/list');
            $this->assertFalse(strpos($content, 'Albert Einstein') === false);
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/create');

            //Test nobody can view an existing sensitiveinfo he owns.
            $sensitiveinfo = SensitiveinfoTestHelper::createSensitiveinfoByNameForOwner('sensitiveinfoOwnedByNobody', $nobody);

            //At this point the listview for leads should show the search/list and not the helper screen.
            $content = $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/list');
            $this->assertTrue(strpos($content, 'Albert Einstein') === false);

            $this->setGetArray(array('id' => $sensitiveinfo->id));
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/edit');

            //Test nobody can delete an existing sensitiveinfo he owns and it redirects to index.
            $this->setGetArray(array('id' => $sensitiveinfo->id));
            $this->resetPostArray();
            $this->runControllerWithRedirectExceptionAndGetContent('sensitiveinfos/default/delete',
                                                                   Yii::app()->createUrl('sensitiveinfos/default/index'));

            //Autocomplete for Sensitiveinfo should not fail.
            $this->setGetArray(array('term' => 'super'));
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/autoComplete');

            //actionModalList for Sensitiveinfo should not fail.
            $this->setGetArray(array(
                'modalTransferInformation' => array('sourceIdFieldId' => 'x', 'sourceNameFieldId' => 'y')
            ));
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/modalList');
        }

        /**
         * @depends testRegularUserControllerActionsWithElevationToAccessAndCreate
         */
        public function testRegularUserControllerActionsWithElevationToModels()
        {
            //Create sensitiveinfo owned by user super.
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $sensitiveinfo = SensitiveinfoTestHelper::createSensitiveinfoByNameForOwner('sensitiveinfoForElevationToModelTest', $super);

            //Test nobody, access to edit and details should fail.
            $nobody = $this->logoutCurrentUserLoginNewUserAndGetByUsername('nobody');
            $this->setGetArray(array('id' => $sensitiveinfo->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/edit');
            $this->setGetArray(array('id' => $sensitiveinfo->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/details');

            //give nobody access to read
            Yii::app()->user->userModel = $super;
            $sensitiveinfo->addPermissions($nobody, Permission::READ);
            $this->assertTrue($sensitiveinfo->save());

            //Now the nobody user can access the details view.
            Yii::app()->user->userModel = $nobody;
            $this->setGetArray(array('id' => $sensitiveinfo->id));
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/details');

            //Test nobody, access to edit should fail.
            $this->setGetArray(array('id' => $sensitiveinfo->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/edit');

            //give nobody access to read and write
            Yii::app()->user->userModel = $super;
            $sensitiveinfo->addPermissions($nobody, Permission::READ_WRITE_CHANGE_PERMISSIONS);
            $this->assertTrue($sensitiveinfo->save());

            //Now the nobody user should be able to access the edit view and still the details view.
            Yii::app()->user->userModel = $nobody;
            $this->setGetArray(array('id' => $sensitiveinfo->id));
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/details');
            $this->setGetArray(array('id' => $sensitiveinfo->id));
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/edit');

            //revoke nobody access to read
            Yii::app()->user->userModel = $super;
            $sensitiveinfo->addPermissions($nobody, Permission::READ_WRITE_CHANGE_PERMISSIONS, Permission::DENY);
            $this->assertTrue($sensitiveinfo->save());

            //Test nobody, access to detail should fail.
            Yii::app()->user->userModel = $nobody;
            $this->setGetArray(array('id' => $sensitiveinfo->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/details');
            $this->setGetArray(array('id' => $sensitiveinfo->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/edit');

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

            //create sensitiveinfo owned by super

            $sensitiveinfo2 = SensitiveinfoTestHelper::createSensitiveinfoByNameForOwner('testingParentRolePermission', $super);

            //Test userInParentRole, access to details and edit should fail.
            Yii::app()->user->userModel = $userInParentRole;
            $this->setGetArray(array('id' => $sensitiveinfo2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/details');
            $this->setGetArray(array('id' => $sensitiveinfo2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/edit');

            //give userInChildRole access to READ
            Yii::app()->user->userModel = $super;
            $sensitiveinfo2->addPermissions($userInChildRole, Permission::READ);
            $this->assertTrue($sensitiveinfo2->save());

            //Test userInChildRole, access to details should not fail.
            Yii::app()->user->userModel = $userInChildRole;
            $this->setGetArray(array('id' => $sensitiveinfo2->id));
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/details');

            //Test userInParentRole, access to details should not fail.
            Yii::app()->user->userModel = $userInParentRole;
            $this->setGetArray(array('id' => $sensitiveinfo2->id));
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/details');

            //give userInChildRole access to read and write
            Yii::app()->user->userModel = $super;
            $sensitiveinfo2->addPermissions($userInChildRole, Permission::READ_WRITE_CHANGE_PERMISSIONS);
            $this->assertTrue($sensitiveinfo2->save());

            //Test userInChildRole, access to edit should not fail.
            Yii::app()->user->userModel = $userInChildRole;
            $this->setGetArray(array('id' => $sensitiveinfo2->id));
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/edit');

            //Test userInParentRole, access to edit should not fail.
            $this->logoutCurrentUserLoginNewUserAndGetByUsername($userInParentRole->username);
            $this->setGetArray(array('id' => $sensitiveinfo2->id));
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/edit');

            //revoke userInChildRole access to read and write
            Yii::app()->user->userModel = $super;
            $sensitiveinfo2->addPermissions($userInChildRole, Permission::READ_WRITE_CHANGE_PERMISSIONS, Permission::DENY);
            $this->assertTrue($sensitiveinfo2->save());

            //Test userInChildRole, access to detail should fail.
            Yii::app()->user->userModel = $userInChildRole;
            $this->setGetArray(array('id' => $sensitiveinfo2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/details');
            $this->setGetArray(array('id' => $sensitiveinfo2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/edit');

            //Test userInParentRole, access to detail should fail.
            Yii::app()->user->userModel = $userInParentRole;
            $this->setGetArray(array('id' => $sensitiveinfo2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/details');
            $this->setGetArray(array('id' => $sensitiveinfo2->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/edit');

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

            //Add access for the confused user to Sensitiveinfos and creation of Sensitiveinfos.
            $userInChildGroup->setRight('SensitiveinfosModule', SensitiveinfosModule::RIGHT_ACCESS_SENSITIVEINFOS);
            $userInChildGroup->setRight('SensitiveinfosModule', SensitiveinfosModule::RIGHT_CREATE_SENSITIVEINFOS);
            $this->assertTrue($userInChildGroup->save());

            //create sensitiveinfo owned by super
            $sensitiveinfo3 = SensitiveinfoTestHelper::createSensitiveinfoByNameForOwner('testingParentGroupPermission', $super);

            //Test userInParentGroup, access to details and edit should fail.
            Yii::app()->user->userModel = $userInParentGroup;
            $this->setGetArray(array('id' => $sensitiveinfo3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/details');
            $this->setGetArray(array('id' => $sensitiveinfo3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/edit');

            //Test userInChildGroup, access to details and edit should fail.
            Yii::app()->user->userModel = $userInChildGroup;
            $this->setGetArray(array('id' => $sensitiveinfo3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/details');
            $this->setGetArray(array('id' => $sensitiveinfo3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/edit');

            //give parentGroup access to READ
            Yii::app()->user->userModel = $super;
            $sensitiveinfo3->addPermissions($parentGroup, Permission::READ);
            $this->assertTrue($sensitiveinfo3->save());

            //Test userInParentGroup, access to details should not fail.
            Yii::app()->user->userModel = $userInParentGroup;
            $this->setGetArray(array('id' => $sensitiveinfo3->id));
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/details');

            //Test userInChildGroup, access to details should not fail.
            Yii::app()->user->userModel = $userInChildGroup;
            $this->setGetArray(array('id' => $sensitiveinfo3->id));
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/details');

            //give parentGroup access to read and write
            Yii::app()->user->userModel = $super;
            $sensitiveinfo3->addPermissions($parentGroup, Permission::READ_WRITE_CHANGE_PERMISSIONS);
            $this->assertTrue($sensitiveinfo3->save());

            //Test userInParentGroup, access to edit should not fail.
            Yii::app()->user->userModel = $userInParentGroup;
            $this->setGetArray(array('id' => $sensitiveinfo3->id));
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/edit');

            //Test userInChildGroup, access to edit should not fail.
            Yii::app()->user->userModel = $userInChildGroup;
            $this->logoutCurrentUserLoginNewUserAndGetByUsername($userInChildGroup->username);
            $this->setGetArray(array('id' => $sensitiveinfo3->id));
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/edit');

            //revoke parentGroup access to read and write
            Yii::app()->user->userModel = $super;
            $sensitiveinfo3->addPermissions($parentGroup, Permission::READ_WRITE_CHANGE_PERMISSIONS, Permission::DENY);
            $this->assertTrue($sensitiveinfo3->save());

            //Test userInChildGroup, access to detail should fail.
            Yii::app()->user->userModel = $userInChildGroup;
            $this->setGetArray(array('id' => $sensitiveinfo3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/details');
            $this->setGetArray(array('id' => $sensitiveinfo3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/edit');

            //Test userInParentGroup, access to detail should fail.
            Yii::app()->user->userModel = $userInParentGroup;
            $this->setGetArray(array('id' => $sensitiveinfo3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/details');
            $this->setGetArray(array('id' => $sensitiveinfo3->id));
            $this->runControllerShouldResultInAccessFailureAndGetContent('sensitiveinfos/default/edit');

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
        public function testRegularUserViewingSensitiveinfoWithoutAccessToAccount()
        {
            $super       = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $aUser       = UserTestHelper::createBasicUser('aUser');
            $aUser->setRight('SensitiveinfosModule', SensitiveinfosModule::RIGHT_ACCESS_SENSITIVEINFOS);
            $aUser->setRight('AccountsModule',      AccountsModule::RIGHT_ACCESS_ACCOUNTS);
            $this->assertTrue($aUser->save());
            $aUser       = User::getByUsername('aUser');
            $account     = AccountTestHelper::createAccountByNameForOwner('superTestAccount', $super);
            $sensitiveinfo = SensitiveinfoTestHelper::createSensitiveinfoWithAccountByNameForOwner('sensitiveinfoOwnedByaUser', $aUser, $account);
            $account->forget();
            $id          = $sensitiveinfo->id;
            $sensitiveinfo->forget();
            unset($sensitiveinfo);
            $this->logoutCurrentUserLoginNewUserAndGetByUsername('aUser');
            $content = $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default');
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
            //Load MassDelete view for the 3 sensitiveinfos.
            $sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(9, count($sensitiveinfos));
            $sensitiveinfo1 = SensitiveinfoTestHelper::createSensitiveinfoByNameForOwner('oppotunityDelete1', $confused);
            $sensitiveinfo2 = SensitiveinfoTestHelper::createSensitiveinfoByNameForOwner('oppotunityDelete2', $confused);
            $sensitiveinfo3 = SensitiveinfoTestHelper::createSensitiveinfoByNameForOwner('oppotunityDelete3', $nobody);
            $sensitiveinfo4 = SensitiveinfoTestHelper::createSensitiveinfoByNameForOwner('oppotunityDelete4', $confused);
            $sensitiveinfo5 = SensitiveinfoTestHelper::createSensitiveinfoByNameForOwner('oppotunityDelete5', $confused);
            $sensitiveinfo6 = SensitiveinfoTestHelper::createSensitiveinfoByNameForOwner('oppotunityDelete6', $nobody);
            $selectedIds = $sensitiveinfo1->id . ',' . $sensitiveinfo2->id . ',' . $sensitiveinfo3->id ;    // Not Coding Standard
            $this->setGetArray(array('selectedIds' => $selectedIds, 'selectAll' => ''));  // Not Coding Standard
            $this->resetPostArray();
            $content = $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/massDelete');
            $this->assertFalse(strpos($content, '<strong>3</strong>&#160;Sensitiveinfos selected for removal') === false);
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massDeleteProgressPageSize');
            $this->assertEquals(5, $pageSize);
            //calculating leads after adding 6 new records
            $sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(15, count($sensitiveinfos));
            //Deleting 6 sensitiveinfos for pagination scenario
            //Run Mass Delete using progress save for page1
            $selectedIds = $sensitiveinfo1->id . ',' . $sensitiveinfo2->id . ',' . // Not Coding Standard
                           $sensitiveinfo3->id . ',' . $sensitiveinfo4->id . ',' . // Not Coding Standard
                           $sensitiveinfo5->id . ',' . $sensitiveinfo6->id;        // Not Coding Standard
            $this->setGetArray(array(
                'selectedIds' => $selectedIds, // Not Coding Standard
                'selectAll' => '',
                'Sensitiveinfo_page' => 1));
            $this->setPostArray(array('selectedRecordCount' => 6));
            $content = $this->runControllerWithExitExceptionAndGetContent('sensitiveinfos/default/massDelete');
            $sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(10, count($sensitiveinfos));

            //Run Mass Delete using progress save for page2
            $selectedIds = $sensitiveinfo1->id . ',' . $sensitiveinfo2->id . ',' . // Not Coding Standard
                           $sensitiveinfo3->id . ',' . $sensitiveinfo4->id . ',' . // Not Coding Standard
                           $sensitiveinfo5->id . ',' . $sensitiveinfo6->id;        // Not Coding Standard
            $this->setGetArray(array(
                'selectedIds' => $selectedIds, // Not Coding Standard
                'selectAll' => '',
                'Sensitiveinfo_page' => 2));
            $this->setPostArray(array('selectedRecordCount' => 6));
            $content = $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/massDeleteProgress');
            $sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(9, count($sensitiveinfos));
        }

         /**
         *Test Bug with mass delete and multiple pages when using select all
         */
        public function testRegularMassDeletePagesProperlyAndRemovesAllSelected()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $confused = User::getByUsername('confused');
            $nobody = User::getByUsername('nobody');

            //Load MassDelete view for the 8 sensitiveinfos.
            $sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(9, count($sensitiveinfos));
             //Deleting all sensitiveinfos

            //mass Delete pagination scenario
            //Run Mass Delete using progress save for page1
            $this->setGetArray(array(
                'selectAll' => '1',
                'Sensitiveinfo_page' => 1));
            $this->setPostArray(array('selectedRecordCount' => 9));
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massDeleteProgressPageSize');
            $this->assertEquals(5, $pageSize);
            $content = $this->runControllerWithExitExceptionAndGetContent('sensitiveinfos/default/massDelete');
            $sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(4, count($sensitiveinfos));

           //Run Mass Delete using progress save for page2
            $this->setGetArray(array(
                'selectAll' => '1',
                'Sensitiveinfo_page' => 2));
            $this->setPostArray(array('selectedRecordCount' => 9));
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massDeleteProgressPageSize');
            $this->assertEquals(5, $pageSize);
            $content = $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/massDeleteProgress');

            $sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(0, count($sensitiveinfos));
        }
    }
?>