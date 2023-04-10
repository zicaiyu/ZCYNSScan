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
     * Tests on projects specific search permutations.
     */
    class ProjectsSearchWithDataProviderTest extends ZurmoDataProviderBaseTest
    {
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            SecurityTestHelper::createSuperAdmin();
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;
        }

        public function testSearchByCustomField()
        {
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;

            //todo: add projects. 2 of them.
            ProjectTestHelper::createProjectByNameForOwner('aFirstProject', $super);
            ProjectTestHelper::createProjectByNameForOwner('aSecondProject', $super);

            //Searching with a custom field that is not blank should not produce any errors.
            //The data returned should be no projects.
            $fakePostData        = array('name'         => null,
                                         'officePhone'  => null,
                                         'industry'     => array('value' => 'Banking'),
                                         'officeFax'    => null);
            $project             = new Project(false);
            $searchForm          = new ProjectsSearchForm($project);
            $metadataAdapter     = new SearchDataProviderMetadataAdapter($searchForm, $super->id, $fakePostData);
            $searchAttributeData = $metadataAdapter->getAdaptedMetadata();

            //Run search and make sure the data returned matches how many total projects are available.
            $dataProvider        = new RedBeanModelDataProvider('Project', null, false, $searchAttributeData);
            $data                = $dataProvider->getData();
            $this->assertEquals(0, count($data));
        }

        /**
         * @depends testSearchByCustomField
         */
        public function testSearchMemberOfAndMembers()
        {
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;

            //Test member of search.
            $_FAKEPOST['Project'] = array();
            $_FAKEPOST['Project']['project']['id'] = '4';
            $metadataAdapter = new SearchDataProviderMetadataAdapter(
                new Project(false),
                1,
                $_FAKEPOST['Project']
            );
            $searchAttributeData = $metadataAdapter->getAdaptedMetadata();
            $joinTablesAdapter   = new RedBeanModelJoinTablesQueryAdapter('Project');
            $quote        = DatabaseCompatibilityUtil::getQuote();
            $where        = RedBeanModelDataProvider::makeWhere('Project', $searchAttributeData, $joinTablesAdapter);
            $compareWhere = "({$quote}project{$quote}.{$quote}project_id{$quote} = 4)";
            $this->assertEquals($compareWhere, $where);

            //Now test that the joinTablesAdapter has correct information.
            $this->assertEquals(0, $joinTablesAdapter->getFromTableJoinCount());
            $this->assertEquals(0, $joinTablesAdapter->getLeftTableJoinCount());
            $leftTables = $joinTablesAdapter->getLeftTablesAndAliases();

            //Make sure the sql runs properly.
            $dataProvider = new RedBeanModelDataProvider('Project', null, false, $searchAttributeData);
            $data = $dataProvider->getData();

            //Test projects search.
            $_FAKEPOST['Project'] = array();
            $_FAKEPOST['Project']['projects']['id'] = '5';
            $metadataAdapter     = new SearchDataProviderMetadataAdapter(new Project(false), $super->id, $_FAKEPOST['Project']);
            $searchAttributeData = $metadataAdapter->getAdaptedMetadata();
            $joinTablesAdapter   = new RedBeanModelJoinTablesQueryAdapter('Project');
            $quote        = DatabaseCompatibilityUtil::getQuote();
            $where        = RedBeanModelDataProvider::makeWhere('Project', $searchAttributeData, $joinTablesAdapter);
            $compareWhere = "({$quote}project1{$quote}.{$quote}id{$quote} = 5)";
            $this->assertEquals($compareWhere, $where);
            //Now test that the joinTablesAdapter has correct information.
            $this->assertEquals(0, $joinTablesAdapter->getFromTableJoinCount());
            $this->assertEquals(1, $joinTablesAdapter->getLeftTableJoinCount());
            $leftTables = $joinTablesAdapter->getLeftTablesAndAliases();
            $this->assertEquals('project', $leftTables[0]['tableName']);

            //Make sure the sql runs properly.
            $dataProvider = new RedBeanModelDataProvider('Project', null, false, $searchAttributeData);
            $data = $dataProvider->getData();
        }

        /**
         * @depends testSearchMemberOfAndMembers
         */
        public function testSearchByCustomFieldWithEscapedContent()
        {
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;

            //Searching with a custom field that is not blank should not produce any errors.
            //The data returned should be no projects.
            $fakePostData        = array('name'         => null,
                                         'officePhone'  => null,
                                         'industry'     => array('value' => "Ban'king"),
                                         'officeFax'    => null);
            $project             = new Project(false);
            $searchForm          = new ProjectsSearchForm($project);
            $metadataAdapter     = new SearchDataProviderMetadataAdapter($searchForm, $super->id, $fakePostData);
            $searchAttributeData = $metadataAdapter->getAdaptedMetadata();

            //Run search and make sure the data returned matches how many total projects are available.
            $dataProvider        = new RedBeanModelDataProvider('Project', null, false, $searchAttributeData);
            $data                = $dataProvider->getData();
            $this->assertEquals(0, count($data));
        }
    }
?>