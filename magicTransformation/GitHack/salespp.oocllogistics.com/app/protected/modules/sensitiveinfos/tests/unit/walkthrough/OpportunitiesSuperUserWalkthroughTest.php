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
     * Walkthrough for the super user of all possible controller actions.
     * Since this is a super user, he should have access to all controller actions
     * without any exceptions being thrown.
     */
    class SensitiveinfosSuperUserWalkthroughTest extends ZurmoWalkthroughBaseTest
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
            SensitiveinfoTestHelper::createSensitiveinfoStagesIfDoesNotExist     ();
            SensitiveinfoTestHelper::createSensitiveinfoWithAccountByNameForOwner('superOpp',      $super, $account);
            SensitiveinfoTestHelper::createSensitiveinfoWithAccountByNameForOwner('superOpp2',     $super, $account);
            SensitiveinfoTestHelper::createSensitiveinfoWithAccountByNameForOwner('superOpp3',     $super, $account);
            SensitiveinfoTestHelper::createSensitiveinfoWithAccountByNameForOwner('superOpp4',     $super, $account);
            SensitiveinfoTestHelper::createSensitiveinfoWithAccountByNameForOwner('superOpp5',     $super, $account);
            SensitiveinfoTestHelper::createSensitiveinfoWithAccountByNameForOwner('superOpp6',     $super, $account);
            SensitiveinfoTestHelper::createSensitiveinfoWithAccountByNameForOwner('superOpp7',     $super, $account);
            SensitiveinfoTestHelper::createSensitiveinfoWithAccountByNameForOwner('superOpp8',     $super, $account);
            SensitiveinfoTestHelper::createSensitiveinfoWithAccountByNameForOwner('superOpp9',     $super, $account);
            SensitiveinfoTestHelper::createSensitiveinfoWithAccountByNameForOwner('superOpp10',    $super, $account);
            SensitiveinfoTestHelper::createSensitiveinfoWithAccountByNameForOwner('superOpp11',    $super, $account);
            SensitiveinfoTestHelper::createSensitiveinfoWithAccountByNameForOwner('superOpp12',    $super, $account);
            //Setup default dashboard.
            Dashboard::getByLayoutIdAndUser                                  (Dashboard::DEFAULT_USER_LAYOUT_ID, $super);
        }

        public function testSuperUserAllDefaultControllerActions()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Test all default controller actions that do not require any POST/GET variables to be passed.
            //This does not include portlet controller actions.
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default');
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/index');
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/create');

            $content = $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/list');
            $this->assertFalse(strpos($content, 'anyMixedAttributes') === false);
            //Test the search or paging of the listview.
            Yii::app()->clientScript->reset(); //to make sure old js doesn't make it to the UI
            $this->setGetArray(array('ajax' => 'list-view'));
            $content = $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/list');
            $this->assertTrue(strpos($content, 'anyMixedAttributes') === false);
            $this->resetGetArray();

            //Default Controller actions requiring some sort of parameter via POST or GET
            //Load Model Edit Views
            $sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(12, count($sensitiveinfos));
            $superSensitiveinfoId   = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp');
            $superSensitiveinfoId2  = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp2');
            $superSensitiveinfoId3  = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp3');
            $superSensitiveinfoId4  = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp4');
            $superSensitiveinfoId5  = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp5');
            $superSensitiveinfoId6  = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp6');
            $superSensitiveinfoId7  = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp7');
            $superSensitiveinfoId8  = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp8');
            $superSensitiveinfoId9  = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp9');
            $superSensitiveinfoId10 = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp10');
            $superSensitiveinfoId11 = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp11');
            $superSensitiveinfoId12 = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp12');
            $this->setGetArray(array('id' => $superSensitiveinfoId));
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/edit');
            //Save sensitiveinfo.
            $superSensitiveinfo = Sensitiveinfo::getById($superSensitiveinfoId);
            $this->assertEquals(null, $superSensitiveinfo->description);
            $this->setPostArray(array('Sensitiveinfo' => array('description' => '456765421')));
            $this->runControllerWithRedirectExceptionAndGetContent('sensitiveinfos/default/edit');
            $superSensitiveinfo = Sensitiveinfo::getById($superSensitiveinfoId);
            $this->assertEquals('456765421', $superSensitiveinfo->description);
            //Test having a failed validation on the sensitiveinfo during save.
            $this->setGetArray (array('id'      => $superSensitiveinfoId));
            $this->setPostArray(array('Sensitiveinfo' => array('name' => '')));
            $content = $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/edit');
            $this->assertFalse(strpos($content, 'Name cannot be blank') === false);

            //Load Model Detail Views
            $this->setGetArray(array('id' => $superSensitiveinfoId));
            $this->resetPostArray();
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/details');

            //Load Model MassEdit Views.
            //MassEdit view for single selected ids
            $this->setGetArray(array('selectedIds' => '4,5,6,7,8,9', 'selectAll' => '')); // Not Coding Standard
            $this->resetPostArray();
            $content = $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/massEdit');
            $this->assertFalse(strpos($content, '<strong>6</strong>&#160;records selected for updating') === false);

            //MassEdit view for all result selected ids
            $this->setGetArray(array('selectAll' => '1'));
            $this->resetPostArray();
            $content = $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/massEdit');
            $this->assertFalse(strpos($content, '<strong>12</strong>&#160;records selected for updating') === false);

            //save Model MassEdit for selected Ids
            //Test that the 2 contacts do not have the office phone number we are populating them with.
            $sensitiveinfo1 = Sensitiveinfo::getById($superSensitiveinfoId);
            $sensitiveinfo2 = Sensitiveinfo::getById($superSensitiveinfoId2);
            $sensitiveinfo3 = Sensitiveinfo::getById($superSensitiveinfoId3);
            $sensitiveinfo4 = Sensitiveinfo::getById($superSensitiveinfoId4);
            $this->assertNotEquals('7788', $sensitiveinfo1->description);
            $this->assertNotEquals('7788', $sensitiveinfo2->description);
            $this->assertNotEquals('7788', $sensitiveinfo3->description);
            $this->assertNotEquals('7788', $sensitiveinfo4->description);
            $this->setGetArray(array(
                'selectedIds' => $superSensitiveinfoId . ',' . $superSensitiveinfoId2, // Not Coding Standard
                'selectAll' => '',
                'Sensitiveinfo_page' => 1));
            $this->setPostArray(array(
                'Sensitiveinfo'  => array('description' => '7788'),
                'MassEdit' => array('description' => 1)
            ));
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massEditProgressPageSize');
            $this->assertEquals(5, $pageSize);
            Yii::app()->pagination->setForCurrentUserByType('massEditProgressPageSize', 20);
            $this->runControllerWithRedirectExceptionAndGetContent('sensitiveinfos/default/massEdit');
            Yii::app()->pagination->setForCurrentUserByType('massEditProgressPageSize', $pageSize);
            //Test that the 2 sensitiveinfos have the new office phone number and the other contacts do not.
            $sensitiveinfo1  = Sensitiveinfo::getById($superSensitiveinfoId);
            $sensitiveinfo2  = Sensitiveinfo::getById($superSensitiveinfoId2);
            $sensitiveinfo3  = Sensitiveinfo::getById($superSensitiveinfoId3);
            $sensitiveinfo4  = Sensitiveinfo::getById($superSensitiveinfoId4);
            $sensitiveinfo5  = Sensitiveinfo::getById($superSensitiveinfoId5);
            $sensitiveinfo6  = Sensitiveinfo::getById($superSensitiveinfoId6);
            $sensitiveinfo7  = Sensitiveinfo::getById($superSensitiveinfoId7);
            $sensitiveinfo8  = Sensitiveinfo::getById($superSensitiveinfoId8);
            $sensitiveinfo9  = Sensitiveinfo::getById($superSensitiveinfoId9);
            $sensitiveinfo10 = Sensitiveinfo::getById($superSensitiveinfoId10);
            $sensitiveinfo11 = Sensitiveinfo::getById($superSensitiveinfoId11);
            $sensitiveinfo12 = Sensitiveinfo::getById($superSensitiveinfoId12);
            $this->assertEquals('7788', $sensitiveinfo1->description);
            $this->assertEquals('7788', $sensitiveinfo2->description);
            $this->assertNotEquals('7788', $sensitiveinfo3->description);
            $this->assertNotEquals('7788', $sensitiveinfo4->description);
            $this->assertNotEquals('7788', $sensitiveinfo5->description);
            $this->assertNotEquals('7788', $sensitiveinfo6->description);
            $this->assertNotEquals('7788', $sensitiveinfo7->description);
            $this->assertNotEquals('7788', $sensitiveinfo8->description);
            $this->assertNotEquals('7788', $sensitiveinfo9->description);
            $this->assertNotEquals('7788', $sensitiveinfo10->description);
            $this->assertNotEquals('7788', $sensitiveinfo11->description);
            $this->assertNotEquals('7788', $sensitiveinfo12->description);

            //save Model MassEdit for entire search result
            $this->setGetArray(array(
                'selectAll' => '1',
                'Sensitiveinfo_page' => 1));
            $this->setPostArray(array(
                'Sensitiveinfo'  => array('description' => '6654'),
                'MassEdit' => array('description' => 1)
            ));
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massEditProgressPageSize');
            $this->assertEquals(5, $pageSize);
            Yii::app()->pagination->setForCurrentUserByType('massEditProgressPageSize', 20);
            $this->runControllerWithRedirectExceptionAndGetContent('sensitiveinfos/default/massEdit');
            Yii::app()->pagination->setForCurrentUserByType('massEditProgressPageSize', $pageSize);
            //Test that all sensitiveinfos have the new description.
            $sensitiveinfo1 = Sensitiveinfo::getById($superSensitiveinfoId);
            $sensitiveinfo2 = Sensitiveinfo::getById($superSensitiveinfoId2);
            $sensitiveinfo3 = Sensitiveinfo::getById($superSensitiveinfoId3);
            $sensitiveinfo4 = Sensitiveinfo::getById($superSensitiveinfoId4);
            $sensitiveinfo5 = Sensitiveinfo::getById($superSensitiveinfoId5);
            $sensitiveinfo6 = Sensitiveinfo::getById($superSensitiveinfoId6);
            $sensitiveinfo7 = Sensitiveinfo::getById($superSensitiveinfoId7);
            $sensitiveinfo8 = Sensitiveinfo::getById($superSensitiveinfoId8);
            $sensitiveinfo9 = Sensitiveinfo::getById($superSensitiveinfoId9);
            $sensitiveinfo10 = Sensitiveinfo::getById($superSensitiveinfoId10);
            $sensitiveinfo11 = Sensitiveinfo::getById($superSensitiveinfoId11);
            $sensitiveinfo12 = Sensitiveinfo::getById($superSensitiveinfoId12);
            $this->assertEquals('6654', $sensitiveinfo1->description);
            $this->assertEquals('6654', $sensitiveinfo2->description);
            $this->assertEquals('6654', $sensitiveinfo3->description);
            $this->assertEquals('6654', $sensitiveinfo4->description);
            $this->assertEquals('6654', $sensitiveinfo5->description);
            $this->assertEquals('6654', $sensitiveinfo6->description);
            $this->assertEquals('6654', $sensitiveinfo7->description);
            $this->assertEquals('6654', $sensitiveinfo8->description);
            $this->assertEquals('6654', $sensitiveinfo9->description);
            $this->assertEquals('6654', $sensitiveinfo10->description);
            $this->assertEquals('6654', $sensitiveinfo11->description);
            $this->assertEquals('6654', $sensitiveinfo12->description);

            //Run Mass Update using progress save.
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massEditProgressPageSize');
            $this->assertEquals(5, $pageSize);
            Yii::app()->pagination->setForCurrentUserByType('massEditProgressPageSize', 1);
            //The page size is smaller than the result set, so it should exit.
            $this->runControllerWithExitExceptionAndGetContent('sensitiveinfos/default/massEdit');
            //save Modal MassEdit using progress load for page 2, 3 and 4.
            $this->setGetArray(array('selectAll' => '1', 'Sensitiveinfo_page' => 2));
            $content = $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/massEditProgressSave');
            $this->assertFalse(strpos($content, '"value":16') === false);
            $this->setGetArray(array('selectAll' => '1', 'Sensitiveinfo_page' => 3));
            $content = $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/massEditProgressSave');
            $this->assertFalse(strpos($content, '"value":25') === false);
            $this->setGetArray(array('selectAll' => '1', 'Sensitiveinfo_page' => 4));
            $content = $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/massEditProgressSave');
            $this->assertFalse(strpos($content, '"value":33') === false);
            //Set page size back to old value.
            Yii::app()->pagination->setForCurrentUserByType('massEditProgressPageSize', $pageSize);

            //save Model MassEdit for selected Ids
            //Test that the 2 contacts do not have the closed date populating them with.
            //Test that closed dates are properly updated
            $sensitiveinfo1 = Sensitiveinfo::getById($superSensitiveinfoId);
            $sensitiveinfo2 = Sensitiveinfo::getById($superSensitiveinfoId2);
            $sensitiveinfo3 = Sensitiveinfo::getById($superSensitiveinfoId3);
            $sensitiveinfo4 = Sensitiveinfo::getById($superSensitiveinfoId4);
            $this->assertNotEquals('2012-12-05', $sensitiveinfo1->closeDate);
            $this->assertNotEquals('2012-12-05', $sensitiveinfo2->closeDate);
            $this->assertNotEquals('2012-12-05', $sensitiveinfo3->closeDate);
            $this->assertNotEquals('2012-12-05', $sensitiveinfo4->closeDate);
            $this->setGetArray(array(
                'selectedIds' => $superSensitiveinfoId . ',' . $superSensitiveinfoId2, // Not Coding Standard
                'selectAll' => '',
                'Sensitiveinfo_page' => 1));
            $this->setPostArray(array(
                'Sensitiveinfo'  => array('closeDate' => '12/5/12'),
                'MassEdit' => array('closeDate' => 1)
            ));
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massEditProgressPageSize');
            $this->assertEquals(5, $pageSize);
            Yii::app()->pagination->setForCurrentUserByType('massEditProgressPageSize', 20);
            $content = $this->runControllerWithRedirectExceptionAndGetContent('sensitiveinfos/default/massEdit');

            $sensitiveinfo1 = Sensitiveinfo::getById($superSensitiveinfoId);
            $sensitiveinfo2 = Sensitiveinfo::getById($superSensitiveinfoId2);
            $sensitiveinfo3 = Sensitiveinfo::getById($superSensitiveinfoId3);
            $sensitiveinfo4 = Sensitiveinfo::getById($superSensitiveinfoId4);
            $this->assertEquals('2012-12-05', $sensitiveinfo1->closeDate);
            $this->assertEquals('2012-12-05', $sensitiveinfo2->closeDate);
            $this->assertNotEquals('2012-12-05', $sensitiveinfo3->closeDate);
            $this->assertNotEquals('2012-12-05', $sensitiveinfo4->closeDate);

            //Autocomplete for Sensitiveinfo
            $this->setGetArray(array('term' => 'super'));
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/autoComplete');

            //actionModalList
            $this->setGetArray(array(
                'modalTransferInformation' => array('sourceIdFieldId' => 'x', 'sourceNameFieldId' => 'y')
            ));
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/modalList');

            //actionAuditEventsModalList
            $this->setGetArray(array('id' => $superSensitiveinfoId));
            $this->resetPostArray();
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/auditEventsModalList');

            //Select a related Sensitiveinfo for this contact. Go to the select screen.
            $superContactId     = self::getModelIdByModelNameAndName ('Contact', 'superContact superContactson');
            $sensitiveinfo1->forget();
            $sensitiveinfo = Sensitiveinfo::getById($superSensitiveinfoId);
            $portlets = Portlet::getByLayoutIdAndUserSortedByColumnIdAndPosition(
                                    'SensitiveinfoDetailsAndRelationsViewLeftBottomView', $super->id, array());
            $this->assertEquals(1, count($portlets));
            $this->assertEquals(2, count($portlets[1]));
            $contact = Contact::getById($superContactId);
            $this->assertEquals(0, $contact->sensitiveinfos->count());
            $this->assertEquals(0, $sensitiveinfo->contacts->count());
            $this->setGetArray(array('portletId'             => $portlets[1][1]->id, //Doesnt matter which portlet we are using
                                     'relationAttributeName' => 'sensitiveinfos',
                                     'relationModuleId'      => 'sensitiveinfos',
                                     'relationModelId'       => $superSensitiveinfoId,
                                     'uniqueLayoutId'        => 'SensitiveinfoDetailsAndRelationsViewLeftBottomView_' .
                                                                $portlets[1][1]->id)
            );

            $this->resetPostArray();
            $this->runControllerWithNoExceptionsAndGetContent('contacts/default/SelectFromRelatedList');
            //Now add an sensitiveinfo to a contact via the select from related list action.
            $this->setGetArray(array(   'portletId'             => $portlets[1][1]->id,
                                        'modelId'               => $superContactId,
                                        'relationAttributeName' => 'sensitiveinfos',
                                        'relationModuleId'      => 'sensitiveinfos',
                                        'relationModelId'       => $superSensitiveinfoId,
                                        'uniqueLayoutId'        => 'SensitiveinfoDetailsAndRelationsViewLeftBottomView_' .
                                                                   $portlets[1][1]->id)
            );
            $this->resetPostArray();
            $this->runControllerWithRedirectExceptionAndGetContent('contacts/defaultPortlet/SelectFromRelatedListSave');
            //Run forget in order to refresh the contact and sensitiveinfo showing the new relation
            $contact->forget();
            $sensitiveinfo->forget();
            $contact     = Contact::getById($superContactId);
            $sensitiveinfo = Sensitiveinfo::getById($superSensitiveinfoId);
            $this->assertEquals(1,                $sensitiveinfo->contacts->count());
            $this->assertEquals($contact,         $sensitiveinfo->contacts[0]);
            $this->assertEquals(1,                $contact->sensitiveinfos->count());
            $this->assertEquals($sensitiveinfo->id, $contact->sensitiveinfos[0]->id);
        }

        /**
         * @depends testSuperUserAllDefaultControllerActions
         */
        public function testSuperUserDefaultPortletControllerActions()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $superSensitiveinfoId2 = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp2');
            //Save a layout change. Collapse all portlets in the Sensitiveinfo Details View.
            //At this point portlets for this view should be created because we have
            //already loaded the 'details' page in a request above.
            $portlets = Portlet::getByLayoutIdAndUserSortedByColumnIdAndPosition(
                                    'SensitiveinfoDetailsAndRelationsViewLeftBottomView', $super->id, array());
            $this->assertEquals (2, count($portlets[1])         );
            $this->assertFalse  (array_key_exists(2, $portlets) );
            $portletPostData = array();
            $portletCount = 0;
            foreach ($portlets as $column => $columnPortlets)
            {
                foreach ($columnPortlets as $position => $portlet)
                {
                    $this->assertEquals('0', $portlet->collapsed);
                    $portletPostData['SensitiveinfoDetailsAndRelationsViewLeftBottomView_' . $portlet->id] = array(
                        'collapsed' => 'true',
                        'column'    => 0,
                        'id'        => 'SensitiveinfoDetailsAndRelationsViewLeftBottomView_' . $portlet->id,
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
                    'uniqueLayoutId' => 'SensitiveinfoDetailsAndRelationsViewLeftBottomView',
                )
            ));
            $this->runControllerWithNoExceptionsAndGetContent('home/defaultPortlet/saveLayout', true);
            //Now test that all the portlets are collapsed and moved to the first column.
            $portlets = Portlet::getByLayoutIdAndUserSortedByColumnIdAndPosition(
                            'SensitiveinfoDetailsAndRelationsViewLeftBottomView', $super->id, array());
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
            $this->setGetArray(array('id' => $superSensitiveinfoId2));
            $this->resetPostArray();
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/details');
        }

        /**
         * @depends testSuperUserDefaultPortletControllerActions
         */
        public function testSuperUserDeleteAction()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $superSensitiveinfoId4 = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp4');
            //Delete an sensitiveinfo.
            $this->setGetArray(array('id' => $superSensitiveinfoId4));
            $this->resetPostArray();
            $this->runControllerWithRedirectExceptionAndGetContent('sensitiveinfos/default/delete');
            $sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(11, count($sensitiveinfos));
            try
            {
                Contact::getById($superSensitiveinfoId4);
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
            //Create a new sensitiveinfo.
            $this->resetGetArray();
            $this->setPostArray(array('Sensitiveinfo' => array(
                                            'name'        => 'myNewSensitiveinfo',
                                            'description' => '456765421',
                                            'closeDate'   => '11/1/11',
                                            'amount' => array(  'value' => '545',
                                                                'currency' => array('id' => $currencies[0]->id)),
                                            'stage'       => array('value' => 'Negotiating'))));
            $this->runControllerWithRedirectExceptionAndGetContent('sensitiveinfos/default/create');
            $sensitiveinfos = Sensitiveinfo::getByName('myNewSensitiveinfo');
            $this->assertEquals(1, count($sensitiveinfos));
            $this->assertTrue  ($sensitiveinfos[0]->id > 0);
            $this->assertTrue  ($sensitiveinfos[0]->owner == $super);
            $this->assertEquals('456765421',   $sensitiveinfos[0]->description);
            $this->assertEquals('545',         $sensitiveinfos[0]->amount->value);
            $this->assertEquals('2011-11-01',  $sensitiveinfos[0]->closeDate);
            $this->assertEquals('Negotiating', $sensitiveinfos[0]->stage->value);
            $sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(12, count($sensitiveinfos));

            //todo: test save with account.
        }

        /**
         * @depends testSuperUserCreateAction
         */
        public function testSuperUserCreateFromRelationAction()
        {
            $super         = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $currencies    = Currency::getAll();
            $sensitiveinfos      = Sensitiveinfo::getAll();
            $this->assertEquals(12, count($sensitiveinfos));
            $account       = Account::getByName('superAccount2');
            $contact       = Contact::getByName('superContact2 superContact2son');
            $this->assertEquals(1, count($contact));

            //Create a new contact from a related account.
            $this->setGetArray(array(   'relationAttributeName' => 'account',
                                        'relationModelId'       => $account[0]->id,
                                        'relationModuleId'      => 'accounts',
                                        'redirectUrl'           => 'someRedirect'));
            $this->setPostArray(array('Sensitiveinfo' => array(
                                        'name'        => 'myUltraNewSensitiveinfo',
                                        'description' => '456765421',
                                        'closeDate'   => '11/1/11',
                                        'amount' => array(  'value' => '545',
                                                            'currency' => array('id' => $currencies[0]->id)),
                                        'stage'       => array('value' => 'Negotiating'))));
            $this->runControllerWithRedirectExceptionAndGetContent('sensitiveinfos/default/createFromRelation');
            $sensitiveinfos = Sensitiveinfo::getByName('myUltraNewSensitiveinfo');
            $this->assertEquals(1, count($sensitiveinfos));
            $this->assertTrue($sensitiveinfos[0]->id > 0);
            $this->assertTrue($sensitiveinfos[0]->owner   == $super);
            $this->assertTrue($sensitiveinfos[0]->account == $account[0]);
            $this->assertEquals('456765421',   $sensitiveinfos[0]->description);
            $this->assertEquals('545',         $sensitiveinfos[0]->amount->value);
            $this->assertEquals('2011-11-01',  $sensitiveinfos[0]->closeDate);
            $this->assertEquals('Negotiating', $sensitiveinfos[0]->stage->value);
            $sensitiveinfos      = Sensitiveinfo::getAll();
            $this->assertEquals(13, count($sensitiveinfos));

            //Create a new contact from a related sensitiveinfo
            $this->setGetArray(array(   'relationAttributeName' => 'contacts',
                                        'relationModelId'       => $contact[0]->id,
                                        'relationModuleId'      => 'contacts',
                                        'redirectUrl'           => 'someRedirect'));
            $this->setPostArray(array('Sensitiveinfo' => array(
                                        'name'        => 'mySuperNewSensitiveinfo',
                                        'description' => '456765421',
                                        'closeDate'   => '11/1/11',
                                        'amount' => array(  'value' => '545',
                                                            'currency' => array('id' => $currencies[0]->id)),
                                        'stage'       => array('value' => 'Negotiating'))));
            $this->runControllerWithRedirectExceptionAndGetContent('sensitiveinfos/default/createFromRelation');
            $sensitiveinfos = Sensitiveinfo::getByName('mySuperNewSensitiveinfo');
            $this->assertEquals(1, count($sensitiveinfos));
            $this->assertTrue(                 $sensitiveinfos[0]->id > 0);
            $this->assertTrue(                 $sensitiveinfos[0]->owner   == $super);
            $this->assertEquals(1,             $sensitiveinfos[0]->contacts->count());
            $this->assertTrue(                 $sensitiveinfos[0]->contacts[0] == $contact[0]);
            $this->assertEquals('456765421',   $sensitiveinfos[0]->description);
            $this->assertEquals('545',         $sensitiveinfos[0]->amount->value);
            $this->assertEquals('2011-11-01',  $sensitiveinfos[0]->closeDate);
            $this->assertEquals('Negotiating', $sensitiveinfos[0]->stage->value);
            $sensitiveinfos      = Sensitiveinfo::getAll();
            $this->assertEquals(14, count($sensitiveinfos));

            //todo: test save with account.
        }

        /**
         * @deletes selected leads.
         */
        public function testMassDeleteActionsForSelectedIds()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(14, count($sensitiveinfos));
            $superSensitiveinfoId   = self::getModelIdByModelNameAndName('Sensitiveinfo', 'mySuperNewSensitiveinfo');
            $superSensitiveinfoId2  = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp2');
            $superSensitiveinfoId3  = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp3');
            $superSensitiveinfoId4  = self::getModelIdByModelNameAndName('Sensitiveinfo', 'myNewSensitiveinfo');
            $superSensitiveinfoId5  = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp5');
            $superSensitiveinfoId6  = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp6');
            $superSensitiveinfoId7  = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp7');
            $superSensitiveinfoId8  = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp8');
            $superSensitiveinfoId9  = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp9');
            $superSensitiveinfoId10 = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp10');
            $superSensitiveinfoId11 = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp11');
            $superSensitiveinfoId12 = self::getModelIdByModelNameAndName('Sensitiveinfo', 'superOpp12');
            //Load Model MassDelete Views.
            //MassDelete view for single selected ids
            $this->setGetArray(array('selectedIds' => '5,6,7,8,9', 'selectAll' => '', ));  // Not Coding Standard
            $this->resetPostArray();
            $content = $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/massDelete');
            $this->assertFalse(strpos($content, '<strong>5</strong>&#160;Sensitiveinfos selected for removal') === false);

            //MassDelete view for all result selected ids
            $this->setGetArray(array('selectAll' => '1'));
            $this->resetPostArray();
            $content = $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/massDelete');
            $this->assertFalse(strpos($content, '<strong>14</strong>&#160;Sensitiveinfos selected for removal') === false);

            //MassDelete for selected ids for page 1
            $this->setGetArray(array(
                'selectedIds' => $superSensitiveinfoId  . ',' . $superSensitiveinfoId2 . ',' . // Not Coding Standard
                                 $superSensitiveinfoId3 . ',' . $superSensitiveinfoId4 . ',' . // Not Coding Standard
                                 $superSensitiveinfoId5 . ',' . $superSensitiveinfoId6,        // Not Coding Standard
                'selectAll'        => '',
                'massDelete'       => '',
                'Sensitiveinfo_page' => 1));
            $this->setPostArray(array('selectedRecordCount' => 6));
            $this->runControllerWithExitExceptionAndGetContent('sensitiveinfos/default/massDelete');

            //MassDelete for selected Record Count
            $Sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(9, count($Sensitiveinfos));

            //MassDelete for selected ids for page 2
            $this->setGetArray(array(
                'selectedIds' => $superSensitiveinfoId . ',' . $superSensitiveinfoId2 . ',' .  // Not Coding Standard
                                 $superSensitiveinfoId3 . ',' . $superSensitiveinfoId4 . ',' . // Not Coding Standard
                                 $superSensitiveinfoId5 . ',' . $superSensitiveinfoId6,        // Not Coding Standard
                'selectAll'        => '',
                'massDelete'       => '',
                'Sensitiveinfo_page' => 2));
            $this->setPostArray(array('selectedRecordCount' => 6));
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/massDeleteProgress');

           //MassDelete for selected Record Count
            $Sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(8, count($Sensitiveinfos));
        }

         /**
         *Test Bug with mass delete and multiple pages when using select all
         */
        public function testMassDeletePagesProperlyAndRemovesAllSelected()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //MassDelete for selected Record Count
            $Sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(8, count($Sensitiveinfos));

            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massDeleteProgressPageSize');
            $this->assertEquals(5, $pageSize);
            //save Model MassDelete for entire search result
            $this->setGetArray(array(
                'selectAll' => '1',           // Not Coding Standard
                'Sensitiveinfo_page' => 1));
            $this->setPostArray(array('selectedRecordCount' => 8));
            //Run Mass Delete using progress save for page1.
            $this->runControllerWithExitExceptionAndGetContent('sensitiveinfos/default/massDelete');

            //check for previous mass delete progress
            $Sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(3, count($Sensitiveinfos));

            $this->setGetArray(array(
                'selectAll' => '1',           // Not Coding Standard
                'Sensitiveinfo_page' => 2));
            $this->setPostArray(array('selectedRecordCount' => 8));
            //Run Mass Delete using progress save for page2.
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massDeleteProgressPageSize');
            $this->assertEquals(5, $pageSize);
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/massDeleteProgress');

            //calculating lead's count
            $Sensitiveinfos = Sensitiveinfo::getAll();
            $this->assertEquals(0, count($Sensitiveinfos));
        }
    }
?>