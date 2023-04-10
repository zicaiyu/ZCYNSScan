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
     * Walkthrough for the super user of all possible controller actions.
     * Since this is a super user, he should have access to all controller actions
     * without any exceptions being thrown.
     */
    class ProjectactivitiesSuperUserWalkthroughTest extends ZurmoWalkthroughBaseTest
    {
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            SecurityTestHelper::createSuperAdmin();
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;

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
            ProjectactivityTestHelper::createProjectactivityWithAccountByNameForOwner('superOpp5',     $super, $account);
            ProjectactivityTestHelper::createProjectactivityWithAccountByNameForOwner('superOpp6',     $super, $account);
            ProjectactivityTestHelper::createProjectactivityWithAccountByNameForOwner('superOpp7',     $super, $account);
            ProjectactivityTestHelper::createProjectactivityWithAccountByNameForOwner('superOpp8',     $super, $account);
            ProjectactivityTestHelper::createProjectactivityWithAccountByNameForOwner('superOpp9',     $super, $account);
            ProjectactivityTestHelper::createProjectactivityWithAccountByNameForOwner('superOpp10',    $super, $account);
            ProjectactivityTestHelper::createProjectactivityWithAccountByNameForOwner('superOpp11',    $super, $account);
            ProjectactivityTestHelper::createProjectactivityWithAccountByNameForOwner('superOpp12',    $super, $account);
            //Setup default dashboard.
            Dashboard::getByLayoutIdAndUser                                  (Dashboard::DEFAULT_USER_LAYOUT_ID, $super);
        }

        public function testSuperUserAllDefaultControllerActions()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Test all default controller actions that do not require any POST/GET variables to be passed.
            //This does not include portlet controller actions.
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default');
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/index');
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/create');

            $content = $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/list');
            $this->assertFalse(strpos($content, 'anyMixedAttributes') === false);
            //Test the search or paging of the listview.
            Yii::app()->clientScript->reset(); //to make sure old js doesn't make it to the UI
            $this->setGetArray(array('ajax' => 'list-view'));
            $content = $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/list');
            $this->assertTrue(strpos($content, 'anyMixedAttributes') === false);
            $this->resetGetArray();

            //Default Controller actions requiring some sort of parameter via POST or GET
            //Load Model Edit Views
            $projectactivities = Projectactivity::getAll();
            $this->assertEquals(12, count($projectactivities));
            $superProjectactivityId   = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp');
            $superProjectactivityId2  = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp2');
            $superProjectactivityId3  = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp3');
            $superProjectactivityId4  = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp4');
            $superProjectactivityId5  = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp5');
            $superProjectactivityId6  = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp6');
            $superProjectactivityId7  = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp7');
            $superProjectactivityId8  = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp8');
            $superProjectactivityId9  = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp9');
            $superProjectactivityId10 = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp10');
            $superProjectactivityId11 = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp11');
            $superProjectactivityId12 = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp12');
            $this->setGetArray(array('id' => $superProjectactivityId));
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/edit');
            //Save projectactivity.
            $superProjectactivity = Projectactivity::getById($superProjectactivityId);
            $this->assertEquals(null, $superProjectactivity->description);
            $this->setPostArray(array('Projectactivity' => array('description' => '456765421')));
            $this->runControllerWithRedirectExceptionAndGetContent('projectactivities/default/edit');
            $superProjectactivity = Projectactivity::getById($superProjectactivityId);
            $this->assertEquals('456765421', $superProjectactivity->description);
            //Test having a failed validation on the projectactivity during save.
            $this->setGetArray (array('id'      => $superProjectactivityId));
            $this->setPostArray(array('Projectactivity' => array('name' => '')));
            $content = $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/edit');
            $this->assertFalse(strpos($content, 'Name cannot be blank') === false);

            //Load Model Detail Views
            $this->setGetArray(array('id' => $superProjectactivityId));
            $this->resetPostArray();
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/details');

            //Load Model MassEdit Views.
            //MassEdit view for single selected ids
            $this->setGetArray(array('selectedIds' => '4,5,6,7,8,9', 'selectAll' => '')); // Not Coding Standard
            $this->resetPostArray();
            $content = $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/massEdit');
            $this->assertFalse(strpos($content, '<strong>6</strong>&#160;records selected for updating') === false);

            //MassEdit view for all result selected ids
            $this->setGetArray(array('selectAll' => '1'));
            $this->resetPostArray();
            $content = $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/massEdit');
            $this->assertFalse(strpos($content, '<strong>12</strong>&#160;records selected for updating') === false);

            //save Model MassEdit for selected Ids
            //Test that the 2 contacts do not have the office phone number we are populating them with.
            $projectactivity1 = Projectactivity::getById($superProjectactivityId);
            $projectactivity2 = Projectactivity::getById($superProjectactivityId2);
            $projectactivity3 = Projectactivity::getById($superProjectactivityId3);
            $projectactivity4 = Projectactivity::getById($superProjectactivityId4);
            $this->assertNotEquals('7788', $projectactivity1->description);
            $this->assertNotEquals('7788', $projectactivity2->description);
            $this->assertNotEquals('7788', $projectactivity3->description);
            $this->assertNotEquals('7788', $projectactivity4->description);
            $this->setGetArray(array(
                'selectedIds' => $superProjectactivityId . ',' . $superProjectactivityId2, // Not Coding Standard
                'selectAll' => '',
                'Projectactivity_page' => 1));
            $this->setPostArray(array(
                'Projectactivity'  => array('description' => '7788'),
                'MassEdit' => array('description' => 1)
            ));
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massEditProgressPageSize');
            $this->assertEquals(5, $pageSize);
            Yii::app()->pagination->setForCurrentUserByType('massEditProgressPageSize', 20);
            $this->runControllerWithRedirectExceptionAndGetContent('projectactivities/default/massEdit');
            Yii::app()->pagination->setForCurrentUserByType('massEditProgressPageSize', $pageSize);
            //Test that the 2 projectactivities have the new office phone number and the other contacts do not.
            $projectactivity1  = Projectactivity::getById($superProjectactivityId);
            $projectactivity2  = Projectactivity::getById($superProjectactivityId2);
            $projectactivity3  = Projectactivity::getById($superProjectactivityId3);
            $projectactivity4  = Projectactivity::getById($superProjectactivityId4);
            $projectactivity5  = Projectactivity::getById($superProjectactivityId5);
            $projectactivity6  = Projectactivity::getById($superProjectactivityId6);
            $projectactivity7  = Projectactivity::getById($superProjectactivityId7);
            $projectactivity8  = Projectactivity::getById($superProjectactivityId8);
            $projectactivity9  = Projectactivity::getById($superProjectactivityId9);
            $projectactivity10 = Projectactivity::getById($superProjectactivityId10);
            $projectactivity11 = Projectactivity::getById($superProjectactivityId11);
            $projectactivity12 = Projectactivity::getById($superProjectactivityId12);
            $this->assertEquals('7788', $projectactivity1->description);
            $this->assertEquals('7788', $projectactivity2->description);
            $this->assertNotEquals('7788', $projectactivity3->description);
            $this->assertNotEquals('7788', $projectactivity4->description);
            $this->assertNotEquals('7788', $projectactivity5->description);
            $this->assertNotEquals('7788', $projectactivity6->description);
            $this->assertNotEquals('7788', $projectactivity7->description);
            $this->assertNotEquals('7788', $projectactivity8->description);
            $this->assertNotEquals('7788', $projectactivity9->description);
            $this->assertNotEquals('7788', $projectactivity10->description);
            $this->assertNotEquals('7788', $projectactivity11->description);
            $this->assertNotEquals('7788', $projectactivity12->description);

            //save Model MassEdit for entire search result
            $this->setGetArray(array(
                'selectAll' => '1',
                'Projectactivity_page' => 1));
            $this->setPostArray(array(
                'Projectactivity'  => array('description' => '6654'),
                'MassEdit' => array('description' => 1)
            ));
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massEditProgressPageSize');
            $this->assertEquals(5, $pageSize);
            Yii::app()->pagination->setForCurrentUserByType('massEditProgressPageSize', 20);
            $this->runControllerWithRedirectExceptionAndGetContent('projectactivities/default/massEdit');
            Yii::app()->pagination->setForCurrentUserByType('massEditProgressPageSize', $pageSize);
            //Test that all projectactivities have the new description.
            $projectactivity1 = Projectactivity::getById($superProjectactivityId);
            $projectactivity2 = Projectactivity::getById($superProjectactivityId2);
            $projectactivity3 = Projectactivity::getById($superProjectactivityId3);
            $projectactivity4 = Projectactivity::getById($superProjectactivityId4);
            $projectactivity5 = Projectactivity::getById($superProjectactivityId5);
            $projectactivity6 = Projectactivity::getById($superProjectactivityId6);
            $projectactivity7 = Projectactivity::getById($superProjectactivityId7);
            $projectactivity8 = Projectactivity::getById($superProjectactivityId8);
            $projectactivity9 = Projectactivity::getById($superProjectactivityId9);
            $projectactivity10 = Projectactivity::getById($superProjectactivityId10);
            $projectactivity11 = Projectactivity::getById($superProjectactivityId11);
            $projectactivity12 = Projectactivity::getById($superProjectactivityId12);
            $this->assertEquals('6654', $projectactivity1->description);
            $this->assertEquals('6654', $projectactivity2->description);
            $this->assertEquals('6654', $projectactivity3->description);
            $this->assertEquals('6654', $projectactivity4->description);
            $this->assertEquals('6654', $projectactivity5->description);
            $this->assertEquals('6654', $projectactivity6->description);
            $this->assertEquals('6654', $projectactivity7->description);
            $this->assertEquals('6654', $projectactivity8->description);
            $this->assertEquals('6654', $projectactivity9->description);
            $this->assertEquals('6654', $projectactivity10->description);
            $this->assertEquals('6654', $projectactivity11->description);
            $this->assertEquals('6654', $projectactivity12->description);

            //Run Mass Update using progress save.
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massEditProgressPageSize');
            $this->assertEquals(5, $pageSize);
            Yii::app()->pagination->setForCurrentUserByType('massEditProgressPageSize', 1);
            //The page size is smaller than the result set, so it should exit.
            $this->runControllerWithExitExceptionAndGetContent('projectactivities/default/massEdit');
            //save Modal MassEdit using progress load for page 2, 3 and 4.
            $this->setGetArray(array('selectAll' => '1', 'Projectactivity_page' => 2));
            $content = $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/massEditProgressSave');
            $this->assertFalse(strpos($content, '"value":16') === false);
            $this->setGetArray(array('selectAll' => '1', 'Projectactivity_page' => 3));
            $content = $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/massEditProgressSave');
            $this->assertFalse(strpos($content, '"value":25') === false);
            $this->setGetArray(array('selectAll' => '1', 'Projectactivity_page' => 4));
            $content = $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/massEditProgressSave');
            $this->assertFalse(strpos($content, '"value":33') === false);
            //Set page size back to old value.
            Yii::app()->pagination->setForCurrentUserByType('massEditProgressPageSize', $pageSize);

            //save Model MassEdit for selected Ids
            //Test that the 2 contacts do not have the closed date populating them with.
            //Test that closed dates are properly updated
            $projectactivity1 = Projectactivity::getById($superProjectactivityId);
            $projectactivity2 = Projectactivity::getById($superProjectactivityId2);
            $projectactivity3 = Projectactivity::getById($superProjectactivityId3);
            $projectactivity4 = Projectactivity::getById($superProjectactivityId4);
            $this->assertNotEquals('2012-12-05', $projectactivity1->closeDate);
            $this->assertNotEquals('2012-12-05', $projectactivity2->closeDate);
            $this->assertNotEquals('2012-12-05', $projectactivity3->closeDate);
            $this->assertNotEquals('2012-12-05', $projectactivity4->closeDate);
            $this->setGetArray(array(
                'selectedIds' => $superProjectactivityId . ',' . $superProjectactivityId2, // Not Coding Standard
                'selectAll' => '',
                'Projectactivity_page' => 1));
            $this->setPostArray(array(
                'Projectactivity'  => array('closeDate' => '12/5/12'),
                'MassEdit' => array('closeDate' => 1)
            ));
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massEditProgressPageSize');
            $this->assertEquals(5, $pageSize);
            Yii::app()->pagination->setForCurrentUserByType('massEditProgressPageSize', 20);
            $content = $this->runControllerWithRedirectExceptionAndGetContent('projectactivities/default/massEdit');

            $projectactivity1 = Projectactivity::getById($superProjectactivityId);
            $projectactivity2 = Projectactivity::getById($superProjectactivityId2);
            $projectactivity3 = Projectactivity::getById($superProjectactivityId3);
            $projectactivity4 = Projectactivity::getById($superProjectactivityId4);
            $this->assertEquals('2012-12-05', $projectactivity1->closeDate);
            $this->assertEquals('2012-12-05', $projectactivity2->closeDate);
            $this->assertNotEquals('2012-12-05', $projectactivity3->closeDate);
            $this->assertNotEquals('2012-12-05', $projectactivity4->closeDate);

            //Autocomplete for Projectactivity
            $this->setGetArray(array('term' => 'super'));
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/autoComplete');

            //actionModalList
            $this->setGetArray(array(
                'modalTransferInformation' => array('sourceIdFieldId' => 'x', 'sourceNameFieldId' => 'y')
            ));
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/modalList');

            //actionAuditEventsModalList
            $this->setGetArray(array('id' => $superProjectactivityId));
            $this->resetPostArray();
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/auditEventsModalList');

            //Select a related Projectactivity for this contact. Go to the select screen.
            $superContactId     = self::getModelIdByModelNameAndName ('Contact', 'superContact superContactson');
            $projectactivity1->forget();
            $projectactivity = Projectactivity::getById($superProjectactivityId);
            $portlets = Portlet::getByLayoutIdAndUserSortedByColumnIdAndPosition(
                                    'ProjectactivityDetailsAndRelationsViewLeftBottomView', $super->id, array());
            $this->assertEquals(1, count($portlets));
            $this->assertEquals(2, count($portlets[1]));
            $contact = Contact::getById($superContactId);
            $this->assertEquals(0, $contact->projectactivities->count());
            $this->assertEquals(0, $projectactivity->contacts->count());
            $this->setGetArray(array('portletId'             => $portlets[1][1]->id, //Doesnt matter which portlet we are using
                                     'relationAttributeName' => 'projectactivities',
                                     'relationModuleId'      => 'projectactivities',
                                     'relationModelId'       => $superProjectactivityId,
                                     'uniqueLayoutId'        => 'ProjectactivityDetailsAndRelationsViewLeftBottomView_' .
                                                                $portlets[1][1]->id)
            );

            $this->resetPostArray();
            $this->runControllerWithNoExceptionsAndGetContent('contacts/default/SelectFromRelatedList');
            //Now add an projectactivity to a contact via the select from related list action.
            $this->setGetArray(array(   'portletId'             => $portlets[1][1]->id,
                                        'modelId'               => $superContactId,
                                        'relationAttributeName' => 'projectactivities',
                                        'relationModuleId'      => 'projectactivities',
                                        'relationModelId'       => $superProjectactivityId,
                                        'uniqueLayoutId'        => 'ProjectactivityDetailsAndRelationsViewLeftBottomView_' .
                                                                   $portlets[1][1]->id)
            );
            $this->resetPostArray();
            $this->runControllerWithRedirectExceptionAndGetContent('contacts/defaultPortlet/SelectFromRelatedListSave');
            //Run forget in order to refresh the contact and projectactivity showing the new relation
            $contact->forget();
            $projectactivity->forget();
            $contact     = Contact::getById($superContactId);
            $projectactivity = Projectactivity::getById($superProjectactivityId);
            $this->assertEquals(1,                $projectactivity->contacts->count());
            $this->assertEquals($contact,         $projectactivity->contacts[0]);
            $this->assertEquals(1,                $contact->projectactivities->count());
            $this->assertEquals($projectactivity->id, $contact->projectactivities[0]->id);
        }

        /**
         * @depends testSuperUserAllDefaultControllerActions
         */
        public function testSuperUserDefaultPortletControllerActions()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $superProjectactivityId2 = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp2');
            //Save a layout change. Collapse all portlets in the Projectactivity Details View.
            //At this point portlets for this view should be created because we have
            //already loaded the 'details' page in a request above.
            $portlets = Portlet::getByLayoutIdAndUserSortedByColumnIdAndPosition(
                                    'ProjectactivityDetailsAndRelationsViewLeftBottomView', $super->id, array());
            $this->assertEquals (2, count($portlets[1])         );
            $this->assertFalse  (array_key_exists(2, $portlets) );
            $portletPostData = array();
            $portletCount = 0;
            foreach ($portlets as $column => $columnPortlets)
            {
                foreach ($columnPortlets as $position => $portlet)
                {
                    $this->assertEquals('0', $portlet->collapsed);
                    $portletPostData['ProjectactivityDetailsAndRelationsViewLeftBottomView_' . $portlet->id] = array(
                        'collapsed' => 'true',
                        'column'    => 0,
                        'id'        => 'ProjectactivityDetailsAndRelationsViewLeftBottomView_' . $portlet->id,
                        'position'  => $portletCount,
                    );
                    $portletCount++;
                }
            }
            //There should have been a total of 3 portlets.
            $this->assertEquals(2, $portletCount);
            $this->resetGetArray();
            $this->setPostArray(array(
                'portletLayoutConfiguration' => array(
                    'portlets' => $portletPostData,
                    'uniqueLayoutId' => 'ProjectactivityDetailsAndRelationsViewLeftBottomView',
                )
            ));
            $this->runControllerWithNoExceptionsAndGetContent('home/defaultPortlet/saveLayout', true);
            //Now test that all the portlets are collapsed and moved to the first column.
            $portlets = Portlet::getByLayoutIdAndUserSortedByColumnIdAndPosition(
                            'ProjectactivityDetailsAndRelationsViewLeftBottomView', $super->id, array());
            $this->assertEquals (2, count($portlets[1])         );
            $this->assertFalse  (array_key_exists(2, $portlets) );
            foreach ($portlets as $column => $columns)
            {
                foreach ($columns as $position => $positionPortlets)
                {
                    $this->assertEquals('1', $positionPortlets->collapsed);
                }
            }
            //Load Details View again to make sure everything is ok after the layout change.
            $this->setGetArray(array('id' => $superProjectactivityId2));
            $this->resetPostArray();
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/details');
        }

        /**
         * @depends testSuperUserDefaultPortletControllerActions
         */
        public function testSuperUserDeleteAction()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $superProjectactivityId4 = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp4');
            //Delete an projectactivity.
            $this->setGetArray(array('id' => $superProjectactivityId4));
            $this->resetPostArray();
            $this->runControllerWithRedirectExceptionAndGetContent('projectactivities/default/delete');
            $projectactivities = Projectactivity::getAll();
            $this->assertEquals(11, count($projectactivities));
            try
            {
                Contact::getById($superProjectactivityId4);
                $this->fail();
            }
            catch (NotFoundException $e)
            {
                //success
            }
        }

        /**
         * @depends testSuperUserDeleteAction
         */
        public function testSuperUserCreateAction()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $currencies    = Currency::getAll();
            //Create a new projectactivity.
            $this->resetGetArray();
            $this->setPostArray(array('Projectactivity' => array(
                                            'name'        => 'myNewProjectactivity',
                                            'description' => '456765421',
                                            'closeDate'   => '11/1/11',
                                            'amount' => array(  'value' => '545',
                                                                'currency' => array('id' => $currencies[0]->id)),
                                            'stage'       => array('value' => 'Negotiating'))));
            $this->runControllerWithRedirectExceptionAndGetContent('projectactivities/default/create');
            $projectactivities = Projectactivity::getByName('myNewProjectactivity');
            $this->assertEquals(1, count($projectactivities));
            $this->assertTrue  ($projectactivities[0]->id > 0);
            $this->assertTrue  ($projectactivities[0]->owner == $super);
            $this->assertEquals('456765421',   $projectactivities[0]->description);
            $this->assertEquals('545',         $projectactivities[0]->amount->value);
            $this->assertEquals('2011-11-01',  $projectactivities[0]->closeDate);
            $this->assertEquals('Negotiating', $projectactivities[0]->stage->value);
            $projectactivities = Projectactivity::getAll();
            $this->assertEquals(12, count($projectactivities));

            //todo: test save with account.
        }

        /**
         * @depends testSuperUserCreateAction
         */
        public function testSuperUserCreateFromRelationAction()
        {
            $super         = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $currencies    = Currency::getAll();
            $projectactivities      = Projectactivity::getAll();
            $this->assertEquals(12, count($projectactivities));
            $account       = Account::getByName('superAccount2');
            $contact       = Contact::getByName('superContact2 superContact2son');
            $this->assertEquals(1, count($contact));

            //Create a new contact from a related account.
            $this->setGetArray(array(   'relationAttributeName' => 'account',
                                        'relationModelId'       => $account[0]->id,
                                        'relationModuleId'      => 'accounts',
                                        'redirectUrl'           => 'someRedirect'));
            $this->setPostArray(array('Projectactivity' => array(
                                        'name'        => 'myUltraNewProjectactivity',
                                        'description' => '456765421',
                                        'closeDate'   => '11/1/11',
                                        'amount' => array(  'value' => '545',
                                                            'currency' => array('id' => $currencies[0]->id)),
                                        'stage'       => array('value' => 'Negotiating'))));
            $this->runControllerWithRedirectExceptionAndGetContent('projectactivities/default/createFromRelation');
            $projectactivities = Projectactivity::getByName('myUltraNewProjectactivity');
            $this->assertEquals(1, count($projectactivities));
            $this->assertTrue($projectactivities[0]->id > 0);
            $this->assertTrue($projectactivities[0]->owner   == $super);
            $this->assertTrue($projectactivities[0]->account == $account[0]);
            $this->assertEquals('456765421',   $projectactivities[0]->description);
            $this->assertEquals('545',         $projectactivities[0]->amount->value);
            $this->assertEquals('2011-11-01',  $projectactivities[0]->closeDate);
            $this->assertEquals('Negotiating', $projectactivities[0]->stage->value);
            $projectactivities      = Projectactivity::getAll();
            $this->assertEquals(13, count($projectactivities));

            //Create a new contact from a related projectactivity
            $this->setGetArray(array(   'relationAttributeName' => 'contacts',
                                        'relationModelId'       => $contact[0]->id,
                                        'relationModuleId'      => 'contacts',
                                        'redirectUrl'           => 'someRedirect'));
            $this->setPostArray(array('Projectactivity' => array(
                                        'name'        => 'mySuperNewProjectactivity',
                                        'description' => '456765421',
                                        'closeDate'   => '11/1/11',
                                        'amount' => array(  'value' => '545',
                                                            'currency' => array('id' => $currencies[0]->id)),
                                        'stage'       => array('value' => 'Negotiating'))));
            $this->runControllerWithRedirectExceptionAndGetContent('projectactivities/default/createFromRelation');
            $projectactivities = Projectactivity::getByName('mySuperNewProjectactivity');
            $this->assertEquals(1, count($projectactivities));
            $this->assertTrue(                 $projectactivities[0]->id > 0);
            $this->assertTrue(                 $projectactivities[0]->owner   == $super);
            $this->assertEquals(1,             $projectactivities[0]->contacts->count());
            $this->assertTrue(                 $projectactivities[0]->contacts[0] == $contact[0]);
            $this->assertEquals('456765421',   $projectactivities[0]->description);
            $this->assertEquals('545',         $projectactivities[0]->amount->value);
            $this->assertEquals('2011-11-01',  $projectactivities[0]->closeDate);
            $this->assertEquals('Negotiating', $projectactivities[0]->stage->value);
            $projectactivities      = Projectactivity::getAll();
            $this->assertEquals(14, count($projectactivities));

            //todo: test save with account.
        }

        /**
         * @deletes selected leads.
         */
        public function testMassDeleteActionsForSelectedIds()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $projectactivities = Projectactivity::getAll();
            $this->assertEquals(14, count($projectactivities));
            $superProjectactivityId   = self::getModelIdByModelNameAndName('Projectactivity', 'mySuperNewProjectactivity');
            $superProjectactivityId2  = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp2');
            $superProjectactivityId3  = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp3');
            $superProjectactivityId4  = self::getModelIdByModelNameAndName('Projectactivity', 'myNewProjectactivity');
            $superProjectactivityId5  = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp5');
            $superProjectactivityId6  = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp6');
            $superProjectactivityId7  = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp7');
            $superProjectactivityId8  = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp8');
            $superProjectactivityId9  = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp9');
            $superProjectactivityId10 = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp10');
            $superProjectactivityId11 = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp11');
            $superProjectactivityId12 = self::getModelIdByModelNameAndName('Projectactivity', 'superOpp12');
            //Load Model MassDelete Views.
            //MassDelete view for single selected ids
            $this->setGetArray(array('selectedIds' => '5,6,7,8,9', 'selectAll' => '', ));  // Not Coding Standard
            $this->resetPostArray();
            $content = $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/massDelete');
            $this->assertFalse(strpos($content, '<strong>5</strong>&#160;Projectactivities selected for removal') === false);

            //MassDelete view for all result selected ids
            $this->setGetArray(array('selectAll' => '1'));
            $this->resetPostArray();
            $content = $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/massDelete');
            $this->assertFalse(strpos($content, '<strong>14</strong>&#160;Projectactivities selected for removal') === false);

            //MassDelete for selected ids for page 1
            $this->setGetArray(array(
                'selectedIds' => $superProjectactivityId  . ',' . $superProjectactivityId2 . ',' . // Not Coding Standard
                                 $superProjectactivityId3 . ',' . $superProjectactivityId4 . ',' . // Not Coding Standard
                                 $superProjectactivityId5 . ',' . $superProjectactivityId6,        // Not Coding Standard
                'selectAll'        => '',
                'massDelete'       => '',
                'Projectactivity_page' => 1));
            $this->setPostArray(array('selectedRecordCount' => 6));
            $this->runControllerWithExitExceptionAndGetContent('projectactivities/default/massDelete');

            //MassDelete for selected Record Count
            $Projectactivities = Projectactivity::getAll();
            $this->assertEquals(9, count($Projectactivities));

            //MassDelete for selected ids for page 2
            $this->setGetArray(array(
                'selectedIds' => $superProjectactivityId . ',' . $superProjectactivityId2 . ',' .  // Not Coding Standard
                                 $superProjectactivityId3 . ',' . $superProjectactivityId4 . ',' . // Not Coding Standard
                                 $superProjectactivityId5 . ',' . $superProjectactivityId6,        // Not Coding Standard
                'selectAll'        => '',
                'massDelete'       => '',
                'Projectactivity_page' => 2));
            $this->setPostArray(array('selectedRecordCount' => 6));
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/massDeleteProgress');

           //MassDelete for selected Record Count
            $Projectactivities = Projectactivity::getAll();
            $this->assertEquals(8, count($Projectactivities));
        }

         /**
         *Test Bug with mass delete and multiple pages when using select all
         */
        public function testMassDeletePagesProperlyAndRemovesAllSelected()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //MassDelete for selected Record Count
            $Projectactivities = Projectactivity::getAll();
            $this->assertEquals(8, count($Projectactivities));

            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massDeleteProgressPageSize');
            $this->assertEquals(5, $pageSize);
            //save Model MassDelete for entire search result
            $this->setGetArray(array(
                'selectAll' => '1',           // Not Coding Standard
                'Projectactivity_page' => 1));
            $this->setPostArray(array('selectedRecordCount' => 8));
            //Run Mass Delete using progress save for page1.
            $this->runControllerWithExitExceptionAndGetContent('projectactivities/default/massDelete');

            //check for previous mass delete progress
            $Projectactivities = Projectactivity::getAll();
            $this->assertEquals(3, count($Projectactivities));

            $this->setGetArray(array(
                'selectAll' => '1',           // Not Coding Standard
                'Projectactivity_page' => 2));
            $this->setPostArray(array('selectedRecordCount' => 8));
            //Run Mass Delete using progress save for page2.
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massDeleteProgressPageSize');
            $this->assertEquals(5, $pageSize);
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/massDeleteProgress');

            //calculating lead's count
            $Projectactivities = Projectactivity::getAll();
            $this->assertEquals(0, count($Projectactivities));
        }
    }
?>