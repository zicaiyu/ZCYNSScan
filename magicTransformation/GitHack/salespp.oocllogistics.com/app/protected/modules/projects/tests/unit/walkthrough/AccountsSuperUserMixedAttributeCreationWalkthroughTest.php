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
    * Testing creating mixed attributes (multi-select and tag cloud), while already having an existing project
    * and making sure things work correctly with retrieval and save.
    */
    class ProjectsSuperUserMixedAttributeCreationWalkthroughTest extends ZurmoWalkthroughBaseTest
    {
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            SecurityTestHelper::createSuperAdmin();
            $super                      = User::getByUsername('super');
            Yii::app()->user->userModel = $super;
        }

        /**
         * @see MissingBeanException (Related to why we needed this test in the first place.)  Since superProject
         * is created before the attributes, was causing a problem where some parts of the relatedModel beans are not
         * present.  Catching the MissingBeanException in the RedBeanModel solved this problem.
         */
        public function testSuperUserSavingProjectCreatedBeforeThreeoRequiredCustomAttributesAreCreated()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //First create an project before you create multiselect and tagcloud attributes
            $project = ProjectTestHelper::createProjectByNameForOwner('superProject', $super);
            $project->forget();

            //Test create field list.
            $this->resetPostArray();
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule'));

            //Create 2 custom attributes that are required.
            $this->createDropDownCustomFieldByModule            ('ProjectsModule', 'dropdown');
            $this->createMultiSelectDropDownCustomFieldByModule ('ProjectsModule', 'multiselect');
            $this->createTagCloudCustomFieldByModule            ('ProjectsModule', 'tagcloud');

            //Save the project again.  Everything is fine.
            $project       = Project::getByName('superProject');

            $project[0]->save(false);
            $project[0]->forget();

            //Retrieving the project again at this point should retrieve ok.
            $project       = Project::getByName('superProject');
            $project[0]->save(false);
            $project[0]->forget();
        }
    }
?>
