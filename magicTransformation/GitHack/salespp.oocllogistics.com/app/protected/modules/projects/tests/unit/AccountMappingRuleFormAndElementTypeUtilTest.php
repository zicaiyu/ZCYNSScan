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

    class ProjectMappingRuleFormAndElementTypeUtilTest extends ImportBaseTest
    {
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            SecurityTestHelper::createSuperAdmin();
        }

        public function testMakeCollectionByAttributeImportRules()
        {
            //Project
            $attributeImportRules = new ProjectAttributeImportRules(new ImportModelTestItem(), 'hasOne');
            $collection           = MappingRuleFormAndElementTypeUtil::
                                    makeCollectionByAttributeImportRules($attributeImportRules,
                                                                         'hasOne', 'importColumn');
            $this->assertEquals(2, count($collection));
            $this->assertEquals('ImportMappingRuleDefaultModelNameId', $collection[0]['elementType']);
            $this->assertEquals('DefaultModelNameIdMappingRuleForm', get_class($collection[0]['mappingRuleForm']));
            $this->assertEquals('ImportMappingRelatedModelValueTypeDropDown', $collection[1]['elementType']);
            $this->assertEquals('RelatedModelValueTypeMappingRuleForm', get_class($collection[1]['mappingRuleForm']));

            //Project Derved
            $attributeImportRules = new ProjectDerivedAttributeImportRules(new ImportModelTestItem(), 'projectDerived');
            $collection           = MappingRuleFormAndElementTypeUtil::
                                    makeCollectionByAttributeImportRules($attributeImportRules,
                                                                         'projectDerived', 'importColumn');
            $this->assertEquals(2, count($collection));
            $this->assertEquals('ImportMappingRuleDefaultModelNameId', $collection[0]['elementType']);
            $this->assertEquals('DefaultModelNameIdDerivedAttributeMappingRuleForm', get_class($collection[0]['mappingRuleForm']));
            $this->assertEquals('ImportMappingModelIdValueTypeDropDown', $collection[1]['elementType']);
            $this->assertEquals('IdValueTypeMappingRuleForm', get_class($collection[1]['mappingRuleForm']));
        }
    }
?>