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
     * Export module walkthrough tests.
     */
    class ProjectsRegularUserExportWalkthroughTest extends ZurmoRegularUserWalkthroughBaseTest
    {
        protected static $asynchronusThreshold;

        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            //Setup test data owned by the super user.
            $project = ProjectTestHelper::createProjectByNameForOwner('superProject', Yii::app()->user->userModel);

            self::$asynchronusThreshold = ExportModule::$asynchronusThreshold;
            ExportModule::$asynchronusThreshold = 3;
            ReadPermissionsOptimizationUtil::rebuild();
        }

        public static function tearDownAfterClass()
        {
            ExportModule::$asynchronusThreshold = self::$asynchronusThreshold;
            parent::tearDownAfterClass();
        }

        /**
         * Walkthrough test for synchronous download
         */
        public function testDownloadDefaultControllerActions()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            $projects = array();
            for ($i = 0; $i < 2; $i++)
            {
                $projects[] = ProjectTestHelper::createProjectByNameForOwner('superProject' . $i, $super);
            }

            // Check if access is denied if user doesn't have access privileges at all to export actions
            Yii::app()->user->userModel = User::getByUsername('nobody');
            $nobody = $this->logoutCurrentUserLoginNewUserAndGetByUsername('nobody');

            // Provide no ids and without selectALl options.
            // This should be result with error and redirect to module page.
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/list');
            $this->setGetArray(array(
                'Project_page' => '1',
                'export' => '',
                'ajax' => '',
                'selectAll' => '',
                'selectedIds' => '')
            );
            $this->runControllerShouldResultInAccessFailureAndGetContent('projects/default/export');

            // Check if user have access to module action, but not to export action
            //Now test peon with elevated rights to projects
            $nobody->setRight('ProjectsModule', ProjectsModule::RIGHT_ACCESS_PROJECTS);
            $nobody->setRight('ProjectsModule', ProjectsModule::RIGHT_CREATE_PROJECTS);
            $nobody->setRight('ProjectsModule', ProjectsModule::RIGHT_DELETE_PROJECTS);
            $nobody->setRight('ExportModule', ExportModule::RIGHT_ACCESS_EXPORT);
            $this->assertTrue($nobody->save());

            // Check if access is denied if user doesn't have access privileges at all to export actions
            $nobody = $this->logoutCurrentUserLoginNewUserAndGetByUsername('nobody');
            Yii::app()->user->userModel = User::getByUsername('nobody');

            // Provide no ids and without selectALl options.
            // This should be result with error and redirect to module page.
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/list');
            $this->setGetArray(array(
                'Project_page' => '1',
                'export' => '',
                'ajax' => '',
                'selectAll' => '',
                'selectedIds' => '')
            );
            $response = $this->runControllerWithRedirectExceptionAndGetUrl('projects/default/export');
            $this->assertTrue(strstr($response, 'projects/default/index') !== false);

            $this->setGetArray(array(
                'ProjectsSearchForm' => array(
                    'anyMixedAttributesScope' => array(0 => 'All'),
                    'anyMixedAttributes'      => '',
                    'name'                    => 'superProject',
                    'officePhone'             => ''
                ),
                'multiselect_ProjectsSearchForm_anyMixedAttributesScope' => 'All',
                'selectAll' => '1',
                'selectedIds' => '',
                'Project_page'   => '1',
                'export'         => '',
                'ajax'           => '')
            );
            $response = $this->runControllerWithRedirectExceptionAndGetUrl('projects/default/export');
            $this->assertTrue(strstr($response, 'projects/default/index') !== false);

            $this->setGetArray(array(
                'ProjectsSearchForm' => array(
                    'anyMixedAttributesScope' => array(0 => 'All'),
                    'anyMixedAttributes'      => '',
                    'name'                    => '',
                    'officePhone'             => ''
                ),
                'multiselect_ProjectsSearchForm_anyMixedAttributesScope' => 'All',
                'selectAll' => '',
                'selectedIds' => "{$projects[0]->id}, {$projects[1]->id}", // Not Coding Standard
                'Project_page'   => '1',
                'export'         => '',
                'ajax'           => '')
            );
            $response = $this->runControllerWithRedirectExceptionAndGetUrl('projects/default/export');
            $this->assertTrue(strstr($response, 'projects/default/index') !== false);
            $this->assertContains('There is no data to export.',
                Yii::app()->user->getFlash('notification'));

            //give nobody access to read and write
            Yii::app()->user->userModel = $super;
            foreach ($projects as $project)
            {
                $project->addPermissions($nobody, Permission::READ_WRITE_CHANGE_PERMISSIONS);
                ReadPermissionsOptimizationUtil::securableItemGivenPermissionsForUser($project, $nobody);
                $this->assertTrue($project->save());
            }

            //Now the nobody user should be able to access the edit view and still the details view.
            Yii::app()->user->userModel = $nobody;
            $this->setGetArray(array(
                'ProjectsSearchForm' => array(
                    'anyMixedAttributesScope' => array(0 => 'All'),
                    'anyMixedAttributes'      => '',
                    'name'                    => '',
                    'officePhone'             => ''
                ),
                'multiselect_ProjectsSearchForm_anyMixedAttributesScope' => 'All',
                'selectAll' => '1',
                'selectedIds' => '',
                'Project_page'   => '1',
                'export'         => '',
                'ajax'           => '')
            );
            $response = $this->runControllerWithExitExceptionAndGetContent('projects/default/export');
            $this->assertEquals('Testing download.', $response);

            $this->setGetArray(array(
                'ProjectsSearchForm' => array(
                    'anyMixedAttributesScope' => array(0 => 'All'),
                    'anyMixedAttributes'      => '',
                    'name'                    => '',
                    'officePhone'             => ''
                ),
                'multiselect_ProjectsSearchForm_anyMixedAttributesScope' => 'All',
                'selectAll' => '',
                'selectedIds' => "{$projects[0]->id}, {$projects[1]->id}",
                'Project_page'   => '1',
                'export'         => '',
                'ajax'           => '')
            );
            $response = $this->runControllerWithExitExceptionAndGetContent('projects/default/export');
            $this->assertEquals('Testing download.', $response);

            // No matches
            $this->setGetArray(array(
                'ProjectsSearchForm' => array(
                    'anyMixedAttributesScope' => array(0 => 'All'),
                    'anyMixedAttributes'      => '',
                    'name'                    => 'missingName',
                    'officePhone'             => ''
                ),
                'multiselect_ProjectsSearchForm_anyMixedAttributesScope' => 'All',
                'Project_page' => '1',
                'selectAll' => '1',
                'selectedIds' => '',
                'export'       => '',
                'ajax'         => '')
            );
            $response = $this->runControllerWithRedirectExceptionAndGetUrl('projects/default/export');
            $this->assertTrue(strstr($response, 'projects/default/index') !== false);
        }
    }
?>