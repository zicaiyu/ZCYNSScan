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
     * Project Latest Activities Super User Walkthrough.
     * Walkthrough for the super user of all possible latest activity controller actions.
     * Since this is a super user, he should have access to all controller actions
     * without any exceptions being thrown.
     */
    class ProjectLatestActivitiesSuperUserWalkthroughTest extends ZurmoWalkthroughBaseTest
    {
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            SecurityTestHelper::createSuperAdmin();
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;

            //Setup test data owned by the super user.
            ProjectTestHelper::createProjectByNameForOwner('superProject', $super);
        }

        public function testSuperUserAllDefaultControllerActions()
        {
            // key used to test persistance of user settings
            $configKey = 'rollup';

            //Set the current user as the super user.
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            $projects = Project::getAll();
            $this->assertEquals(1, count($projects));
            $superProjectId = self::getModelIdByModelNameAndName ('Project', 'superProject');

            //Load Details view to generate the portlets.
            $this->setGetArray(array('id' => $superProjectId));
            $this->resetPostArray();
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/details');

            //Find the LatestActivity portlet.
            $portletToUse = null;
            $portlets     = Portlet::getAll();
            foreach ($portlets as $portlet)
            {
                if ($portlet->viewType == 'ProjectLatestActivtiesForPortlet')
                {
                    $portletToUse = $portlet;
                    break;
                }
            }
            $this->assertNotNull($portletToUse);
            $this->assertEquals('ProjectLatestActivtiesForPortletView', get_class($portletToUse->getView()));

            //Load the portlet details for latest activity
            $getData = array('id' => $superProjectId,
                             'portletId' => $portletToUse->id,
                             'uniqueLayoutId' => 'ProjectDetailsAndRelationsView_' . $portletToUse->id,
                             'LatestActivitiesConfigurationForm' => array(
                                'filteredByModelName' => 'all',
                                'rollup' => ''
                             ));
            $this->setGetArray($getData);
            $this->resetPostArray();
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/defaultPortlet/details');
            $this->assertTrue(LatestActivitiesUtil::getPersistentConfigForCurrentUserByPortletIdAndKey(
                $getData['portletId'], $configKey) === '');

            //Now add roll up
            $getData['LatestActivitiesConfigurationForm']['rollup'] = '1';
            $this->setGetArray($getData);
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/defaultPortlet/details');
            $this->assertTrue(LatestActivitiesUtil::getPersistentConfigForCurrentUserByPortletIdAndKey(
                $getData['portletId'], $configKey) === '1');
            //Now filter by meeting, task, and note
            $getData['LatestActivitiesConfigurationForm']['filteredByModelName'] = 'Meeting';
            $this->setGetArray($getData);
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/defaultPortlet/details');
            $getData['LatestActivitiesConfigurationForm']['filteredByModelName'] = 'Note';
            $this->setGetArray($getData);
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/defaultPortlet/details');
            $getData['LatestActivitiesConfigurationForm']['filteredByModelName'] = 'Task';
            $this->setGetArray($getData);
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/defaultPortlet/details');
            //Now do the same thing with filtering but turn off rollup.
            $getData['LatestActivitiesConfigurationForm']['rollup'] = '';
            $getData['LatestActivitiesConfigurationForm']['filteredByModelName'] = 'Meeting';
            $this->setGetArray($getData);
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/defaultPortlet/details');
            $this->assertTrue(LatestActivitiesUtil::getPersistentConfigForCurrentUserByPortletIdAndKey(
                $getData['portletId'], $configKey) === '');
            $getData['LatestActivitiesConfigurationForm']['filteredByModelName'] = 'Note';
            $this->setGetArray($getData);
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/defaultPortlet/details');
            $getData['LatestActivitiesConfigurationForm']['filteredByModelName'] = 'Task';
            $this->setGetArray($getData);
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/defaultPortlet/details');
        }
    }
?>