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
     * Projects Module Super User Walkthrough.
     * Walkthrough for the super user of all possible controller actions.
     * Since this is a super user, he should have access to all controller actions
     * without any exceptions being thrown.
     */
    class ProjectsSuperUserWalkthroughTest extends ZurmoWalkthroughBaseTest
    {
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            SecurityTestHelper::createSuperAdmin();
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;

            //Setup test data owned by the super user.
            ProjectTestHelper::createProjectByNameForOwner('superProject', $super);
            ProjectTestHelper::createProjectByNameForOwner('superProject2', $super);
            ProjectTestHelper::createProjectByNameForOwner('superProject3', $super);
            ProjectTestHelper::createProjectByNameForOwner('superProject4', $super);
            ProjectTestHelper::createProjectByNameForOwner('superProject5', $super);
            ProjectTestHelper::createProjectByNameForOwner('superProject6', $super);
            ProjectTestHelper::createProjectByNameForOwner('superProject7', $super);
            ProjectTestHelper::createProjectByNameForOwner('superProject8', $super);
            ProjectTestHelper::createProjectByNameForOwner('superProject9', $super);
            ProjectTestHelper::createProjectByNameForOwner('superProject10', $super);
            ProjectTestHelper::createProjectByNameForOwner('superProject11', $super);
            ProjectTestHelper::createProjectByNameForOwner('superProject12', $super);
            ProjectTestHelper::createProjectByNameForOwner('superProject13', $super);
            ProjectTestHelper::createProjectByNameForOwner('superProject14', $super);
            ProjectTestHelper::createProjectByNameForOwner('superProject15', $super);
            ProjectTestHelper::createProjectByNameForOwner('superProject16', $super);
            ProjectTestHelper::createProjectByNameForOwner('superProject17', $super);
            ProjectTestHelper::createProjectByNameForOwner('superProject18', $super);
            ProjectTestHelper::createProjectByNameForOwner('superProject19', $super);
            ProjectTestHelper::createProjectByNameForOwner('superProject20', $super);
            //Setup default dashboard.
            Dashboard::getByLayoutIdAndUser(Dashboard::DEFAULT_USER_LAYOUT_ID, $super);
        }

        public function testSuperUserAllDefaultControllerActions()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Test all default controller actions that do not require any POST/GET variables to be passed.
            //This does not include portlet controller actions.
            $this->runControllerWithNoExceptionsAndGetContent('projects/default');
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/index');
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/create');

            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default/list');
            $this->assertFalse(strpos($content, 'anyMixedAttributes') === false);
            //Test the search or paging of the listview.
            Yii::app()->clientScript->reset(); //to make sure old js doesn't make it to the UI
            $this->setGetArray(array('ajax' => 'list-view'));
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default/list');
            $this->assertTrue(strpos($content, 'anyMixedAttributes') === false);
            $this->resetGetArray();

            //Default Controller actions requiring some sort of parameter via POST or GET
            //Load Model Edit Views
            $projects = Project::getAll();
            $this->assertEquals(20, count($projects));
            $superProjectId = self::getModelIdByModelNameAndName ('Project', 'superProject');
            $superProjectId2 = self::getModelIdByModelNameAndName('Project', 'superProject2');
            $superProjectId3 = self::getModelIdByModelNameAndName('Project', 'superProject3');
            $superProjectId4 = self::getModelIdByModelNameAndName('Project', 'superProject4');
            $superProjectId5 = self::getModelIdByModelNameAndName('Project', 'superProject5');
            $superProjectId6 = self::getModelIdByModelNameAndName('Project', 'superProject6');
            $superProjectId7 = self::getModelIdByModelNameAndName('Project', 'superProject7');
            $superProjectId8 = self::getModelIdByModelNameAndName('Project', 'superProject8');
            $superProjectId9 = self::getModelIdByModelNameAndName ('Project', 'superProject9');
            $superProjectId10 = self::getModelIdByModelNameAndName('Project', 'superProject10');
            $superProjectId11 = self::getModelIdByModelNameAndName('Project', 'superProject11');
            $superProjectId12 = self::getModelIdByModelNameAndName('Project', 'superProject12');
            $this->setGetArray(array('id' => $superProjectId));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/edit');
            //Save project.
            $superProject = Project::getById($superProjectId);
            $this->assertEquals(null, $superProject->officePhone);
            $this->setPostArray(array('Project' => array('officePhone' => '456765421')));
            //Make sure the redirect is to the details view and not the list view.
            $this->runControllerWithRedirectExceptionAndGetContent('projects/default/edit',
                        Yii::app()->createUrl('projects/default/details', array('id' => $superProjectId)));
            $superProject = Project::getById($superProjectId);
            $this->assertEquals('456765421', $superProject->officePhone);
            //Test having a failed validation on the project during save.
            $this->setGetArray (array('id'      => $superProjectId));
            $this->setPostArray(array('Project' => array('name' => '')));
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default/edit');
            $this->assertFalse(strpos($content, 'Name cannot be blank') === false);

            //Load Model Detail Views
            $this->setGetArray(array('id' => $superProjectId));
            $this->resetPostArray();
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/details');

            //Load Model MassEdit Views.
            //MassEdit view for single selected ids
            $this->setGetArray(array('selectedIds' => '4,5,6,7,8', 'selectAll' => ''));  // Not Coding Standard
            $this->resetPostArray();
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default/massEdit');
            $this->assertFalse(strpos($content, '<strong>5</strong>&#160;records selected for updating') === false);

            //MassEdit view for all result selected ids
            $this->setGetArray(array('selectAll' => '1'));
            $this->resetPostArray();
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default/massEdit');
            $this->assertFalse(strpos($content, '<strong>20</strong>&#160;records selected for updating') === false);

            //save Model MassEdit for selected Ids
            //Test that the 2 projects do not have the office phone number we are populating them with.
            $project1 = Project::getById($superProjectId);
            $project2 = Project::getById($superProjectId2);
            $project3 = Project::getById($superProjectId3);
            $project4 = Project::getById($superProjectId4);
            $this->assertNotEquals('7788', $project1->officePhone);
            $this->assertNotEquals('7788', $project2->officePhone);
            $this->assertNotEquals('7788', $project3->officePhone);
            $this->assertNotEquals('7788', $project4->officePhone);
            $this->setGetArray(array(
                'selectedIds' => $superProjectId . ',' . $superProjectId2, // Not Coding Standard
                'selectAll' => '',
                'Project_page' => 1));
            $this->setPostArray(array(
                'Project'  => array('officePhone' => '7788'),
                'MassEdit' => array('officePhone' => 1)
            ));
            $this->runControllerWithRedirectExceptionAndGetContent('projects/default/massEdit');
            //Test that the 2 projects have the new office phone number and the other projects do not.
            $project1 = Project::getById($superProjectId);
            $project2 = Project::getById($superProjectId2);
            $project3 = Project::getById($superProjectId3);
            $project4 = Project::getById($superProjectId4);
            $project5 = Project::getById($superProjectId5);
            $project6 = Project::getById($superProjectId6);
            $project7 = Project::getById($superProjectId7);
            $project8 = Project::getById($superProjectId8);
            $project9 = Project::getById($superProjectId9);
            $project10 = Project::getById($superProjectId10);
            $project11 = Project::getById($superProjectId11);
            $project12 = Project::getById($superProjectId12);
            $this->assertEquals   ('7788', $project1->officePhone);
            $this->assertEquals   ('7788', $project2->officePhone);
            $this->assertNotEquals('7788', $project3->officePhone);
            $this->assertNotEquals('7788', $project4->officePhone);
            $this->assertNotEquals('7788', $project5->officePhone);
            $this->assertNotEquals('7788', $project6->officePhone);
            $this->assertNotEquals('7788', $project7->officePhone);
            $this->assertNotEquals('7788', $project8->officePhone);
            $this->assertNotEquals('7788', $project9->officePhone);
            $this->assertNotEquals('7788', $project10->officePhone);
            $this->assertNotEquals('7788', $project11->officePhone);
            $this->assertNotEquals('7788', $project12->officePhone);

            //save Model MassEdit for entire search result
            $this->setGetArray(array(
                'selectAll' => '1',
                'Project_page' => 1));
            $this->setPostArray(array(
                'Project'  => array('officePhone' => '4455'),
                'MassEdit' => array('officePhone' => 1)
            ));
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massEditProgressPageSize');
            $this->assertEquals(5, $pageSize);
            Yii::app()->pagination->setForCurrentUserByType('massEditProgressPageSize', 20);
            $this->runControllerWithRedirectExceptionAndGetContent('projects/default/massEdit');
            Yii::app()->pagination->setForCurrentUserByType('massEditProgressPageSize', $pageSize);
            //Test that all projects have the new phone number.
            $project1 = Project::getById($superProjectId);
            $project2 = Project::getById($superProjectId2);
            $project3 = Project::getById($superProjectId3);
            $project4 = Project::getById($superProjectId4);
            $project5 = Project::getById($superProjectId5);
            $project6 = Project::getById($superProjectId6);
            $project7 = Project::getById($superProjectId7);
            $project8 = Project::getById($superProjectId8);
            $project9 = Project::getById($superProjectId9);
            $project10 = Project::getById($superProjectId10);
            $project11 = Project::getById($superProjectId11);
            $project12 = Project::getById($superProjectId12);
            $this->assertEquals('4455', $project1->officePhone);
            $this->assertEquals('4455', $project2->officePhone);
            $this->assertEquals('4455', $project3->officePhone);
            $this->assertEquals('4455', $project4->officePhone);
            $this->assertEquals('4455', $project5->officePhone);
            $this->assertEquals('4455', $project6->officePhone);
            $this->assertEquals('4455', $project7->officePhone);
            $this->assertEquals('4455', $project8->officePhone);
            $this->assertEquals('4455', $project9->officePhone);
            $this->assertEquals('4455', $project10->officePhone);
            $this->assertEquals('4455', $project11->officePhone);
            $this->assertEquals('4455', $project12->officePhone);

            //Run Mass Update using progress save.
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massEditProgressPageSize');
            $this->assertEquals(5, $pageSize);
            Yii::app()->pagination->setForCurrentUserByType('massEditProgressPageSize', 1);
            //The page size is smaller than the result set, so it should exit.
            $this->runControllerWithExitExceptionAndGetContent('projects/default/massEdit');
            //save Modal MassEdit using progress load for page 2, 3 and 4.
            $this->setGetArray(array('selectAll' => '1', 'Project_page' => 2));
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default/massEditProgressSave');
            $this->assertFalse(strpos($content, '"value":10') === false);
            $this->setGetArray(array('selectAll' => '1', 'Project_page' => 3));
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default/massEditProgressSave');
            $this->assertFalse(strpos($content, '"value":15') === false);
            $this->setGetArray(array('selectAll' => '1', 'Project_page' => 4));
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default/massEditProgressSave');
            $this->assertFalse(strpos($content, '"value":20') === false);
            //Set page size back to old value.
            Yii::app()->pagination->setForCurrentUserByType('massEditProgressPageSize', $pageSize);

            //Autocomplete for Project
            $this->setGetArray(array('term' => 'super'));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/autoComplete');

            //actionModalList
            $this->setGetArray(array(
                'modalTransferInformation' => array('sourceIdFieldId' => 'x', 'sourceNameFieldId' => 'y')
            ));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/modalList');

            //actionAuditEventsModalList
            $this->setGetArray(array('id' => $superProjectId));
            $this->resetPostArray();
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/auditEventsModalList');
        }

        /**
         * @depends testSuperUserAllDefaultControllerActions
         */
        public function testSuperUserDefaultPortletControllerActions()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $superProjectId2 = self::getModelIdByModelNameAndName ('Project', 'superProject2');

            //Save a layout change. Collapse all portlets in the Project Details View.
            //At this point portlets for this view should be created because we have already loaded the 'details' page in a request above.
            $portlets = Portlet::getByLayoutIdAndUserSortedByColumnIdAndPosition(
                                    'ProjectDetailsAndRelationsViewLeftBottomView', $super->id, array());
            $this->assertEquals (2, count($portlets[1])         );
            $this->assertFalse  (array_key_exists(2, $portlets) );
            $portletPostData = array();
            $portletCount = 0;
            foreach ($portlets as $column => $columnPortlets)
            {
                foreach ($columnPortlets as $position => $portlet)
                {
                    $this->assertEquals('0', $portlet->collapsed);
                    $portletPostData['ProjectDetailsAndRelationsViewLeftBottomView_' . $portlet->id] = array(
                        'collapsed' => 'true',
                        'column'    => 0,
                        'id'        => 'ProjectDetailsAndRelationsViewLeftBottomView_' . $portlet->id,
                        'position'  => $portletCount,
                    );
                    $portletCount++;
                }
            }
            //There should have been a total of 2 portlets.
            $this->assertEquals(2, $portletCount);
            $this->resetGetArray();
            $this->setPostArray(array(
                'portletLayoutConfiguration' => array(
                    'portlets' => $portletPostData,
                    'uniqueLayoutId' => 'ProjectDetailsAndRelationsViewLeftBottomView',
                )
            ));
            $this->runControllerWithNoExceptionsAndGetContent('home/defaultPortlet/saveLayout', true);
            //Now test that all the portlets are collapsed and moved to the first column.
            $portlets = Portlet::getByLayoutIdAndUserSortedByColumnIdAndPosition(
                            'ProjectDetailsAndRelationsViewLeftBottomView', $super->id, array());
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
            $this->setGetArray(array('id' => $superProjectId2));
            $this->resetPostArray();
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/details');
        }

        /**
         * @depends testSuperUserDefaultPortletControllerActions
         */
        public function testSuperUserDeleteAction()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $superProjectId4 = self::getModelIdByModelNameAndName ('Project', 'superProject4');

            //Delete an project.
            $this->setGetArray(array('id' => $superProjectId4));
            $this->resetPostArray();
            $this->runControllerWithRedirectExceptionAndGetContent('projects/default/delete');
            $projects = Project::getAll();
            $this->assertEquals(19, count($projects));
            try
            {
                Project::getById($superProjectId4);
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
            //Create a new project.
            $this->resetGetArray();
            $this->setPostArray(array('Project' => array(
                                            'name'        => 'myNewProject',
                                            'officePhone' => '456765421')));
            $redirectUrl = $this->runControllerWithRedirectExceptionAndGetUrl('projects/default/create');
            $projects = Project::getByName('myNewProject');
            $this->assertEquals(1, count($projects));
            $this->assertTrue  ($projects[0]->id > 0);
            $compareRedirectUrl = Yii::app()->createUrl('projects/default/details', array('id' => $projects[0]->id));
            $this->assertEquals($compareRedirectUrl, $redirectUrl);
            $this->assertEquals('456765421', $projects[0]->officePhone);
            $this->assertTrue  ($projects[0]->owner == $super);
            $projects = Project::getAll();
            $this->assertEquals(20, count($projects));
        }

        /**
         * @depends testSuperUserCreateAction
         */
        public function testStickySearchActions()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            StickySearchUtil::clearDataByKey('ProjectsSearchView');
            $value = StickySearchUtil::getDataByKey('ProjectsSearchView');
            $this->assertNull($value);
            $this->setGetArray(array('ProjectsSearchForm'                 =>
                                        array('anyMixedAttributes'                 => 'xyz',
                                              SearchForm::SELECTED_LIST_ATTRIBUTES => array('officePhone', 'name'))));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/');
            $data = StickySearchUtil::getDataByKey('ProjectsSearchView');
            $compareData = array('dynamicClauses'          => array(),
                                 'dynamicStructure'        => null,
                                 'anyMixedAttributes'      => 'xyz',
                                 'anyMixedAttributesScope' => null,
                                 SearchForm::SELECTED_LIST_ATTRIBUTES => array('officePhone', 'name')
            );
            $this->assertEquals($compareData, $data);
            $this->setGetArray(array('clearingSearch' => true));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default');
            $data = StickySearchUtil::getDataByKey('ProjectsSearchView');
            $compareData = array('dynamicClauses'          => array(),
                                 'dynamicStructure'        => null,
                                 'anyMixedAttributesScope' => null,
                                 SearchForm::SELECTED_LIST_ATTRIBUTES => array('name', 'type', 'owner')
            );
            $this->assertEquals($compareData, $data);
        }

        /**
         * @deletes selected projects.
         */
        public function testMassDeleteActionsForSelectedIds()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //MassDelete for selected Record Count
            $projects = Project::getAll();
            $this->assertEquals(20, count($projects));

            $superProjectId2  = self::getModelIdByModelNameAndName('Project', 'superProject2');
            $superProjectId3  = self::getModelIdByModelNameAndName('Project', 'superProject3');
            $superProjectId5  = self::getModelIdByModelNameAndName('Project', 'superProject5');
            $superProjectId6  = self::getModelIdByModelNameAndName('Project', 'superProject6');
            $superProjectId7  = self::getModelIdByModelNameAndName('Project', 'superProject7');
            $superProjectId8  = self::getModelIdByModelNameAndName('Project', 'superProject8');
            $superProjectId9  = self::getModelIdByModelNameAndName('Project', 'superProject9');
            $superProjectId10 = self::getModelIdByModelNameAndName('Project', 'superProject10');
            $superProjectId11 = self::getModelIdByModelNameAndName('Project', 'superProject11');
            $superProjectId12 = self::getModelIdByModelNameAndName('Project', 'superProject12');
            $superProjectId13 = self::getModelIdByModelNameAndName('Project', 'superProject13');
            $superProjectId14 = self::getModelIdByModelNameAndName('Project', 'superProject14');
            $superProjectId15 = self::getModelIdByModelNameAndName('Project', 'superProject15');
            $superProjectId16 = self::getModelIdByModelNameAndName('Project', 'superProject16');
            $superProjectId17 = self::getModelIdByModelNameAndName('Project', 'superProject17');
            $superProjectId18 = self::getModelIdByModelNameAndName('Project', 'superProject18');
            $superProjectId19 = self::getModelIdByModelNameAndName('Project', 'superProject19');
            $superProjectId20 = self::getModelIdByModelNameAndName('Project', 'superProject20');

            //Load Model MassDelete Views.
            //MassDelete view for single selected ids
            $this->setGetArray(array('selectedIds' => '5,6,7,8', 'selectAll' => '', ));  // Not Coding Standard
            $this->resetPostArray();
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default/massDelete');
            $this->assertFalse(strpos($content, '<strong>4</strong>&#160;Projects selected for removal') === false);

            //MassDelete view for all result selected ids
            $this->setGetArray(array('selectAll' => '1'));
            $this->resetPostArray();
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default/massDelete');
            $this->assertFalse(strpos($content, '<strong>20</strong>&#160;Projects selected for removal') === false);
            //MassDelete for selected ids
            $project2 = Project::getById($superProjectId2);
            $project3 = Project::getById($superProjectId3);
            $this->setGetArray(array(
                'selectedIds' => $superProjectId2 . ',' . $superProjectId3, // Not Coding Standard
                'selectAll' => '',
                'Project_page' => 1));
            $this->setPostArray(array('selectedRecordCount' => '5'));
            $this->runControllerWithRedirectExceptionAndGetContent('projects/default/massDelete');

            //MassDelete for selected Record Count
            $projects = Project::getAll();
            $this->assertEquals(18, count($projects));

            //MassDelete for selected ids for paged scenario
            $project13 = Project::getById($superProjectId13);
            $project14 = Project::getById($superProjectId14);
            $project15 = Project::getById($superProjectId15);
            $project16 = Project::getById($superProjectId16);
            $project17 = Project::getById($superProjectId17);
            $project18 = Project::getById($superProjectId18);
            $project19 = Project::getById($superProjectId19);
            $project20 = Project::getById($superProjectId20);

            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massDeleteProgressPageSize');
            $this->assertEquals(5, $pageSize);
            //MassDelete for selected ids for page 1
            $this->setGetArray(array(
                'selectedIds'  => $superProjectId13 . ',' . $superProjectId14 . ',' . // Not Coding Standard
                                  $superProjectId15 . ',' . $superProjectId16 . ',' . // Not Coding Standard
                                  $superProjectId17 . ',' . $superProjectId18 . ',' . // Not Coding Standard
                                  $superProjectId19 . ',' . $superProjectId20,        // Not Coding Standard
                'selectAll'    => '',
                'massDelete'   => '',
                'Project_page' => 1));
            $this->setPostArray(array('selectedRecordCount' => 8));
            $this->runControllerWithExitExceptionAndGetContent('projects/default/massDelete');

            //MassDelete for selected Record Count
            $projects = Project::getAll();
            $this->assertEquals(13, count($projects));

            //MassDelete for selected ids for page 2
            $this->setGetArray(array(
                'selectedIds' => $superProjectId13 . ',' . $superProjectId14 . ',' . // Not Coding Standard
                                 $superProjectId15 . ',' . $superProjectId16 . ',' . // Not Coding Standard
                                 $superProjectId17 . ',' . $superProjectId18 . ',' . // Not Coding Standard
                                 $superProjectId19 . ',' . $superProjectId20,        // Not Coding Standard
                'selectAll' => '',
                'Project_page' => 2));
            $this->setPostArray(array('selectedRecordCount' => 8));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/massDeleteProgress');

           //MassDelete for selected Record Count
            $projects = Project::getAll();
            $this->assertEquals(10, count($projects));
        }

         /**
         *Test Bug with mass delete and multiple pages when using select all
         */
        public function testMassDeletePagesProperlyAndRemovesAllSelected()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //MassDelete for selected Record Count
            $projects = Project::getAll();
            $this->assertEquals(10, count($projects));

            //save Model MassDelete for entire search result
            $this->setGetArray(array(
                'selectAll' => '1',           // Not Coding Standard
                'Project_page' => 1));
            $this->setPostArray(array('selectedRecordCount' => 10));
            //Run Mass Delete using progress save for page1.
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massDeleteProgressPageSize');
            $this->assertEquals(5, $pageSize);
            $this->runControllerWithExitExceptionAndGetContent('projects/default/massDelete');

            //check for previous mass delete progress
            $projects = Project::getAll();
            $this->assertEquals(5, count($projects));

            $this->setGetArray(array(
                'selectAll' => '1',           // Not Coding Standard
                'Project_page' => 2));
            $this->setPostArray(array('selectedRecordCount' => 10));
            //Run Mass Delete using progress save for page2.
            $pageSize = Yii::app()->pagination->getForCurrentUserByType('massDeleteProgressPageSize');
            $this->assertEquals(5, $pageSize);
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/massDeleteProgress');

            //calculating project's count
            $projects = Project::getAll();
            $this->assertEquals(0, count($projects));
        }
    }
?>
