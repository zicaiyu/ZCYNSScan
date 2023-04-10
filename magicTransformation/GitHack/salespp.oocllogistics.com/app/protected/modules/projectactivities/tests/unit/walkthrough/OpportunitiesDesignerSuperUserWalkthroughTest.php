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
    * Designer Module Walkthrough of Projectactivities.
    * Walkthrough for the super user of all possible controller actions.
    * Since this is a super user, he should have access to all controller actions
    * without any exceptions being thrown.
    * This also test the creation of the customfileds, addition of custom fields to all the layouts including the search
    * views.
    * This also test creation search, edit and delete of the Projectactivity based on the custom fields.
    */
    class ProjectactivitiesDesignerSuperUserWalkthroughTest extends ZurmoWalkthroughBaseTest
    {
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            SecurityTestHelper::createSuperAdmin();
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;
            Currency::makeBaseCurrency();

            //Create a account for testing.
            $account = AccountTestHelper::createAccountByNameForOwner('superAccount', $super);

            //Create a Projectactivity for testing.
            ProjectactivityTestHelper::createProjectactivityWithAccountByNameForOwner('superOpp', $super, $account);
        }

         public function testSuperUserProjectactivityDefaultControllerActions()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Default Controller actions requiring some sort of parameter via POST or GET
            //Load Projectactivity Modules Menu.
            $this->setGetArray(array('moduleClassName' => 'ProjectactivitiesModule'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/modulesMenu');

            //Load AttributesList for Projectactivity module.
            $this->setGetArray(array('moduleClassName' => 'ProjectactivitiesModule'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/attributesList');

            //Load ModuleLayoutsList for Projectactivity module.
            $this->setGetArray(array('moduleClassName' => 'ProjectactivitiesModule'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/moduleLayoutsList');

            //Load ModuleEdit view for each applicable module.
            $this->setGetArray(array('moduleClassName' => 'ProjectactivitiesModule'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/moduleEdit');

            //Now validate save with failed validation.
            $this->setGetArray(array('moduleClassName' => 'ProjectactivitiesModule'));
            $this->setPostArray(array('ajax' => 'edit-form',
                'ProjectactivitiesModuleForm' => $this->createModuleEditBadValidationPostData()));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/moduleEdit');
            $this->assertTrue(strlen($content) > 50); //approximate, but should definetely be larger than 50.

            //Now validate save with successful validation.
            $this->setGetArray(array('moduleClassName' => 'ProjectactivitiesModule'));
            $this->setPostArray(array('ajax' => 'edit-form',
                'ProjectactivitiesModuleForm' => $this->createModuleEditGoodValidationPostData('opp new name')));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/moduleEdit');
            $this->assertEquals('[]', $content);

            //Now save successfully.
            $this->setGetArray(array('moduleClassName' => 'ProjectactivitiesModule'));
            $this->setPostArray(array('save' => 'Save',
                'ProjectactivitiesModuleForm' => $this->createModuleEditGoodValidationPostData('opp new name')));
            $this->runControllerWithRedirectExceptionAndGetContent('designer/default/moduleEdit');

            //Now confirm everything did in fact save correctly.
            $this->assertEquals('Opp New Name',  ProjectactivitiesModule::getModuleLabelByTypeAndLanguage('Singular'));
            $this->assertEquals('Opp New Names', ProjectactivitiesModule::getModuleLabelByTypeAndLanguage('Plural'));
            $this->assertEquals('opp new name',  ProjectactivitiesModule::getModuleLabelByTypeAndLanguage('SingularLowerCase'));
            $this->assertEquals('opp new names', ProjectactivitiesModule::getModuleLabelByTypeAndLanguage('PluralLowerCase'));

            //Load LayoutEdit for each applicable module and applicable layout
            $this->resetPostArray();
            $this->setGetArray(array('moduleClassName' => 'ProjectactivitiesModule',
                                     'viewClassName'   => 'ProjectactivitiesListView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
            $this->setGetArray(array('moduleClassName' => 'ProjectactivitiesModule',
                                     'viewClassName'   => 'ProjectactivitiesModalListView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
            $this->setGetArray(array('moduleClassName' => 'ProjectactivitiesModule',
                                     'viewClassName'   => 'ProjectactivitiesModalSearchView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
            $this->setGetArray(array('moduleClassName' => 'ProjectactivitiesModule',
                                     'viewClassName'   => 'ProjectactivitiesMassEditView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
            $this->setGetArray(array('moduleClassName' => 'ProjectactivitiesModule',
                                     'viewClassName'   => 'ProjectactivitiesRelatedListView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
            $this->setGetArray(array('moduleClassName' => 'ProjectactivitiesModule',
                                     'viewClassName'   => 'ProjectactivitiesSearchView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
            $this->setGetArray(array('moduleClassName' => 'ProjectactivitiesModule',
                                     'viewClassName'   => 'ProjectactivityEditAndDetailsView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
        }

        /**
         * @depends testSuperUserProjectactivityDefaultControllerActions
         */
        public function testSuperUserCustomFieldsWalkthroughForProjectactivitiesModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Test create field list.
            $this->setGetArray(array('moduleClassName' => 'ProjectactivitiesModule'));

            //View creation screen, then create custom field for each custom field type.
            $this->createCheckBoxCustomFieldByModule            ('ProjectactivitiesModule', 'checkbox');
            $this->createCurrencyValueCustomFieldByModule       ('ProjectactivitiesModule', 'currency');
            $this->createDateCustomFieldByModule                ('ProjectactivitiesModule', 'date');
            $this->createDateTimeCustomFieldByModule            ('ProjectactivitiesModule', 'datetime');
            $this->createDecimalCustomFieldByModule             ('ProjectactivitiesModule', 'decimal');
            $this->createDropDownCustomFieldByModule            ('ProjectactivitiesModule', 'picklist');
            $this->createDependentDropDownCustomFieldByModule   ('ProjectactivitiesModule', 'countrylist');
            $this->createDependentDropDownCustomFieldByModule   ('ProjectactivitiesModule', 'statelist');
            $this->createDependentDropDownCustomFieldByModule   ('ProjectactivitiesModule', 'citylist');
            $this->createIntegerCustomFieldByModule             ('ProjectactivitiesModule', 'integer');
            $this->createMultiSelectDropDownCustomFieldByModule ('ProjectactivitiesModule', 'multiselect');
            $this->createTagCloudCustomFieldByModule            ('ProjectactivitiesModule', 'tagcloud');
            $this->createCalculatedNumberCustomFieldByModule    ('ProjectactivitiesModule', 'calcnumber');
            $this->createDropDownDependencyCustomFieldByModule  ('ProjectactivitiesModule', 'dropdowndep');
            $this->createPhoneCustomFieldByModule               ('ProjectactivitiesModule', 'phone');
            $this->createRadioDropDownCustomFieldByModule       ('ProjectactivitiesModule', 'radio');
            $this->createTextCustomFieldByModule                ('ProjectactivitiesModule', 'text');
            $this->createTextAreaCustomFieldByModule            ('ProjectactivitiesModule', 'textarea');
            $this->createUrlCustomFieldByModule                 ('ProjectactivitiesModule', 'url');
        }

        /**
         * @depends testSuperUserCustomFieldsWalkthroughForProjectactivitiesModule
         */
        public function testSuperUserAddCustomFieldsToLayoutsForProjectactivitiesModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Add custom fields to ProjectactivityEditAndDetailsView.
            $this->setGetArray(array('moduleClassName' => 'ProjectactivitiesModule',
                                     'viewClassName'   => 'ProjectactivityEditAndDetailsView'));
            $layout = ProjectactivitiesDesignerWalkthroughHelperUtil::getProjectactivityEditAndDetailsViewLayoutWithAllCustomFieldsPlaced();
            $this->setPostArray(array('save'  => 'Save', 'layout' => $layout,
                                      'LayoutPanelsTypeForm' => array('type' => FormLayout::PANELS_DISPLAY_TYPE_ALL)));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/LayoutEdit');
            $this->assertFalse(strpos($content, 'Layout saved successfully') === false);

            //Add all fields to ProjectactivitiesSearchView.
            $this->setGetArray(array('moduleClassName' => 'ProjectactivitiesModule',
                                     'viewClassName'   => 'ProjectactivitiesSearchView'));
            $layout = ProjectactivitiesDesignerWalkthroughHelperUtil::getProjectactivitiesSearchViewLayoutWithAllCustomFieldsPlaced();
            $this->setPostArray(array('save'  => 'Save', 'layout' => $layout));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/LayoutEdit');
            $this->assertFalse(strpos($content, 'Layout saved successfully') === false);

            //Add all fields to ProjectactivitiesListView.
            $this->setGetArray(array('moduleClassName' => 'ProjectactivitiesModule',
                                     'viewClassName'   => 'ProjectactivitiesListView'));
            $layout = ProjectactivitiesDesignerWalkthroughHelperUtil::getProjectactivitiesListViewLayoutWithAllStandardAndCustomFieldsPlaced();
            $this->setPostArray(array('save'  => 'Save', 'layout' => $layout));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/LayoutEdit');
            $this->assertFalse(strpos($content, 'Layout saved successfully') === false);

            //Add all fields to ProjectactivitiesRelatedListView.
            $this->setGetArray(array('moduleClassName' => 'ProjectactivitiesModule',
                                     'viewClassName'   => 'ProjectactivitiesRelatedListView'));
            $layout = ProjectactivitiesDesignerWalkthroughHelperUtil::getProjectactivitiesListViewLayoutWithAllStandardAndCustomFieldsPlaced();
            $this->setPostArray(array('save'  => 'Save', 'layout' => $layout));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/LayoutEdit');
            $this->assertFalse(strpos($content, 'Layout saved successfully') === false);

            //Add all fields to ProjectactivitiesMassEditView.
            $this->setGetArray(array('moduleClassName' => 'ProjectactivitiesModule',
                                     'viewClassName'   => 'ProjectactivitiesMassEditView'));
            $layout = ProjectactivitiesDesignerWalkthroughHelperUtil::getProjectactivitiesMassEditViewLayoutWithAllStandardAndCustomFieldsPlaced();
            $this->setPostArray(array('save'  => 'Save', 'layout' => $layout));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/LayoutEdit');
            $this->assertFalse(strpos($content, 'Layout saved successfully') === false);
        }

        /**
         * @depends testSuperUserAddCustomFieldsToLayoutsForProjectactivitiesModule
         */
        public function testLayoutsLoadOkAfterCustomFieldsPlacedForProjectactivitiesModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $superAccountId = self::getModelIdByModelNameAndName ('Account', 'superAccount');
            $superProjectactivityId = self::getModelIdByModelNameAndName ('Projectactivity', 'superOpp');
            //Load create, edit, and details views.
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/create');
            $this->setGetArray(array('id' => $superProjectactivityId));
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/edit');
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/details');
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/list');
            $this->setGetArray(array(
                'modalTransferInformation' => array('sourceIdFieldId' => 'x', 'sourceNameFieldId' => 'y')
            ));
            $this->resetPostArray();
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/modalList');
            $this->setGetArray(array('id' => $superAccountId));
            $this->resetPostArray();
            $this->runControllerWithNoExceptionsAndGetContent('accounts/default/details');
            $this->setGetArray(array('selectAll' => '1'));
            $this->resetPostArray();
            $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default/massEdit');
        }

        /**
         * @depends testLayoutsLoadOkAfterCustomFieldsPlacedForProjectactivitiesModule
         */
        public function testCreateAnProjectactivityAfterTheCustomFieldsArePlacedForProjectactivitiesModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Set the date and datetime variable values here.
            $date           = Yii::app()->dateFormatter->format(DateTimeUtil::getLocaleDateFormat(), time());
            $dateAssert     = date('Y-m-d');
            $datetime       = Yii::app()->dateFormatter->format(DateTimeUtil::getLocaleDateTimeFormat(), time());
            $datetimeAssert = date('Y-m-d H:i:')."00";
            $baseCurrency   = Currency::getByCode(Yii::app()->currencyHelper->getBaseCode());

            //Retrieve the account id and the super account id.
            $accountId   = self::getModelIdByModelNameAndName ('Account', 'superAccount');
            $superUserId = $super->id;

            //Create a new Projectactivity based on the custom fields.
            $this->resetGetArray();
            $this->setPostArray(array('Projectactivity' => array(
                            'name'                              => 'myNewProjectactivity',
                            'amount'                            => array('value' => 298000,
                                                                         'currency' => array('id' => $baseCurrency->id)),
                            'account'                           => array('id' => $accountId),
                            'probability'                       => '1',
                            'closeDate'                         => $date,
                            'stage'                             => array('value' => 'Prospecting'),
                            'source'                            => array('value' => 'Self-Generated'),
                            'description'                       => 'This is the Description',
                            'owner'                             => array('id' => $superUserId),
                            'explicitReadWriteModelPermissions' => array('type' => null),
                            'checkboxCstm'                      => '1',
                            'currencyCstm'                      => array('value'    => 45,
                                                                         'currency' => array('id' => $baseCurrency->id)),
                            'dateCstm'                          => $date,
                            'datetimeCstm'                      => $datetime,
                            'decimalCstm'                       => '123',
                            'picklistCstm'                      => array('value' => 'a'),
                            'multiselectCstm'                   => array('values' => array('ff', 'rr')),
                            'tagcloudCstm'                      => array('values' => array('writing', 'gardening')),
                            'countrylistCstm'                   => array('value'  => 'bbbb'),
                            'statelistCstm'                     => array('value'  => 'bbb1'),
                            'citylistCstm'                      => array('value'  => 'bb1'),
                            'integerCstm'                       => '12',
                            'phoneCstm'                         => '259-784-2169',
                            'radioCstm'                         => array('value' => 'd'),
                            'textCstm'                          => 'This is a test Text',
                            'textareaCstm'                      => 'This is a test TextArea',
                            'urlCstm'                           => 'http://wwww.abc.com')));
            $this->runControllerWithRedirectExceptionAndGetUrl('projectactivities/default/create');

            //Check the details if they are saved properly for the custom fields.
            $projectactivityId = self::getModelIdByModelNameAndName('Projectactivity', 'myNewProjectactivity');
            $projectactivity   = Projectactivity::getById($projectactivityId);

            //Retrieve the permission of the projectactivity.
            $explicitReadWriteModelPermissions = ExplicitReadWriteModelPermissionsUtil::
                                                 makeBySecurableItem($projectactivity);
            $readWritePermitables              = $explicitReadWriteModelPermissions->getReadWritePermitables();
            $readOnlyPermitables               = $explicitReadWriteModelPermissions->getReadOnlyPermitables();

            $this->assertEquals($projectactivity->name                       , 'myNewProjectactivity');
            $this->assertEquals($projectactivity->amount->value              , '298000');
            $this->assertEquals($projectactivity->amount->currency->id       , $baseCurrency->id);
            $this->assertEquals($projectactivity->account->id                , $accountId);
            $this->assertEquals($projectactivity->probability                , '1');
            $this->assertEquals($projectactivity->stage->value               , 'Prospecting');
            $this->assertEquals($projectactivity->source->value              , 'Self-Generated');
            $this->assertEquals($projectactivity->description                , 'This is the Description');
            $this->assertEquals($projectactivity->owner->id                  , $superUserId);
            $this->assertEquals(0                                        , count($readWritePermitables));
            $this->assertEquals(0                                        , count($readOnlyPermitables));
            $this->assertEquals($projectactivity->checkboxCstm               , '1');
            $this->assertEquals($projectactivity->currencyCstm->value        , 45);
            $this->assertEquals($projectactivity->currencyCstm->currency->id , $baseCurrency->id);
            $this->assertEquals($projectactivity->dateCstm                   , $dateAssert);
            $this->assertEquals($projectactivity->datetimeCstm               , $datetimeAssert);
            $this->assertEquals($projectactivity->decimalCstm                , '123');
            $this->assertEquals($projectactivity->picklistCstm->value        , 'a');
            $this->assertEquals($projectactivity->integerCstm                , 12);
            $this->assertEquals($projectactivity->phoneCstm                  , '259-784-2169');
            $this->assertEquals($projectactivity->radioCstm->value           , 'd');
            $this->assertEquals($projectactivity->textCstm                   , 'This is a test Text');
            $this->assertEquals($projectactivity->textareaCstm               , 'This is a test TextArea');
            $this->assertEquals($projectactivity->urlCstm                    , 'http://wwww.abc.com');
            $this->assertEquals($projectactivity->countrylistCstm->value     , 'bbbb');
            $this->assertEquals($projectactivity->statelistCstm->value       , 'bbb1');
            $this->assertEquals($projectactivity->citylistCstm->value        , 'bb1');
            $this->assertContains('ff'                                   , $projectactivity->multiselectCstm->values);
            $this->assertContains('rr'                                   , $projectactivity->multiselectCstm->values);
            $this->assertContains('writing'                              , $projectactivity->tagcloudCstm->values);
            $this->assertContains('gardening'                            , $projectactivity->tagcloudCstm->values);
            $metadata            = CalculatedDerivedAttributeMetadata::
                                   getByNameAndModelClassName('calcnumber', 'Projectactivity');
            $testCalculatedValue = CalculatedNumberUtil::calculateByFormulaAndModel($metadata->getFormula(), $projectactivity);
            $this->assertEquals(1476                                     , $testCalculatedValue);
        }

        /**
         * @depends testCreateAnProjectactivityAfterTheCustomFieldsArePlacedForProjectactivitiesModule
         */
        public function testWhetherSearchWorksForTheCustomFieldsPlacedForProjectactivitiesModuleAfterCreatingTheProjectactivity()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Retrieve the account id and the super user id.
            $accountId      = self::getModelIdByModelNameAndName ('Account', 'superAccount');
            $superUserId    = $super->id;
            $baseCurrency   = Currency::getByCode(Yii::app()->currencyHelper->getBaseCode());

            //Search a created projectactivity using the customfield.
            $this->resetPostArray();
            $this->setGetArray(array('ProjectactivitiesSearchForm' => array(
                                                'name'               => 'myNewProjectactivity',
                                                'owner'              => array('id' => $superUserId),
                                                'ownedItemsOnly'     => '1',
                                                'account'            => array('id' => $accountId),
                                                'amount'             => array('value'       => '298000',
                                                                              'relatedData' => true,
                                                                              'currency'    => array(
                                                                              'id' => $baseCurrency->id)),
                                                'closeDate__Date'    => array('value' => 'Today'),
                                                'stage'              => array('value' => 'Prospecting'),
                                                'source'             => array('value' => 'Self-Generated'),
                                                'probability'        => '1',
                                                'decimalCstm'        => '123',
                                                'integerCstm'        => '12',
                                                'phoneCstm'          => '259-784-2169',
                                                'textCstm'           => 'This is a test Text',
                                                'textareaCstm'       => 'This is a test TextArea',
                                                'urlCstm'            => 'http://wwww.abc.com',
                                                'checkboxCstm'       => array('value'  =>  '1'),
                                                'currencyCstm'       => array('value'  =>  45),
                                                'picklistCstm'       => array('value'  =>  'a'),
                                                'multiselectCstm'    => array('values' => array('ff', 'rr')),
                                                'tagcloudCstm'       => array('values' => array('writing', 'gardening')),
                                                'countrylistCstm'    => array('value'  => 'bbbb'),
                                                'statelistCstm'      => array('value'  => 'bbb1'),
                                                'citylistCstm'       => array('value'  => 'bb1'),
                                                'radioCstm'          => array('value'  =>  'd'),
                                                'dateCstm__Date'     => array('type'   =>  'Today'),
                                                'datetimeCstm__DateTime' => array('type'   =>  'Today')),
                                     'ajax' =>  'list-view'));
            $content = $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default');

            //Check if the projectactivity name exits after the search is performed on the basis of the
            //custom fields added to the projectactivities module.
            //$this->assertTrue(strpos($content, "Displaying 1-1 of 1 result(s).") > 0); //removed until we show the count again in the listview.
            $this->assertTrue(strpos($content, "myNewProjectactivity") > 0);
        }

        /**
         * @depends testWhetherSearchWorksForTheCustomFieldsPlacedForProjectactivitiesModuleAfterCreatingTheProjectactivity
         */
        public function testEditOfTheProjectactivityForTheTagCloudFieldAfterRemovingAllTagsPlacedForProjectactivitiesModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Set the date and datetime variable values here.
            $date           = Yii::app()->dateFormatter->format(DateTimeUtil::getLocaleDateFormat(), time());
            $dateAssert     = date('Y-m-d');
            $datetime       = Yii::app()->dateFormatter->format(DateTimeUtil::getLocaleDateTimeFormat(), time());
            $datetimeAssert = date('Y-m-d H:i:')."00";
            $baseCurrency   = Currency::getByCode(Yii::app()->currencyHelper->getBaseCode());

            //Retrieve the account id, the super user id and projectactivity Id.
            $accountId                        = self::getModelIdByModelNameAndName ('Account', 'superAccount');
            $superUserId                      = $super->id;
            $explicitReadWriteModelPermission = ExplicitReadWriteModelPermissionsUtil::MIXED_TYPE_EVERYONE_GROUP;
            $projectactivity   = Projectactivity::getByName('myNewProjectactivity');
            $projectactivityId = $projectactivity[0]->id;
            $this->assertEquals(2, $projectactivity[0]->tagcloudCstm->values->count());

            //Edit a new Projectactivity based on the custom fields.
            $this->setGetArray(array('id' => $projectactivityId));
            $this->setPostArray(array('Projectactivity' => array(
                            'name'                              => 'myEditProjectactivity',
                            'amount'                            => array('value'       => 288000,
                                                                         'currency'    => array(
                                                                             'id'      => $baseCurrency->id)),
                            'account'                           => array('id' => $accountId),
                            'probability'                       => '2',
                            'closeDate'                         => $date,
                            'stage'                             => array('value' => 'Qualification'),
                            'source'                            => array('value' => 'Inbound Call'),
                            'description'                       => 'This is the Edit Description',
                            'owner'                             => array('id' => $superUserId),
                            'explicitReadWriteModelPermissions' => array('type' => $explicitReadWriteModelPermission),
                            'checkboxCstm'                      => '0',
                            'currencyCstm'                      => array('value'       => 40,
                                                                         'currency'    => array(
                                                                             'id' => $baseCurrency->id)),
                            'decimalCstm'                       => '12',
                            'dateCstm'                          => $date,
                            'datetimeCstm'                      => $datetime,
                            'picklistCstm'                      => array('value'  => 'b'),
                            'multiselectCstm'                   => array('values' =>  array('gg', 'hh')),
                            'tagcloudCstm'                      => array('values' =>  array()),
                            'countrylistCstm'                   => array('value'  => 'aaaa'),
                            'statelistCstm'                     => array('value'  => 'aaa1'),
                            'citylistCstm'                      => array('value'  => 'ab1'),
                            'integerCstm'                       => '11',
                            'phoneCstm'                         => '259-784-2069',
                            'radioCstm'                         => array('value' => 'e'),
                            'textCstm'                          => 'This is a test Edit Text',
                            'textareaCstm'                      => 'This is a test Edit TextArea',
                            'urlCstm'                           => 'http://wwww.abc-edit.com')));
            $this->runControllerWithRedirectExceptionAndGetUrl('projectactivities/default/edit');

            //Check the details if they are saved properly for the custom fields.
            $projectactivityId = self::getModelIdByModelNameAndName('Projectactivity', 'myEditProjectactivity');
            $projectactivity   = Projectactivity::getById($projectactivityId);

            //Retrieve the permission of the projectactivity.
            $explicitReadWriteModelPermissions = ExplicitReadWriteModelPermissionsUtil::
                                                 makeBySecurableItem($projectactivity);
            $readWritePermitables              = $explicitReadWriteModelPermissions->getReadWritePermitables();
            $readOnlyPermitables               = $explicitReadWriteModelPermissions->getReadOnlyPermitables();

            $this->assertEquals($projectactivity->name                       , 'myEditProjectactivity');
            $this->assertEquals($projectactivity->amount->value              , '288000');
            $this->assertEquals($projectactivity->amount->currency->id       , $baseCurrency->id);
            $this->assertEquals($projectactivity->account->id                , $accountId);
            $this->assertEquals($projectactivity->probability                , '2');
            $this->assertEquals($projectactivity->stage->value               , 'Qualification');
            $this->assertEquals($projectactivity->source->value              , 'Inbound Call');
            $this->assertEquals($projectactivity->description                , 'This is the Edit Description');
            $this->assertEquals($projectactivity->owner->id                  , $superUserId);
            $this->assertEquals(1                                        , count($readWritePermitables));
            $this->assertEquals(0                                        , count($readOnlyPermitables));
            $this->assertEquals($projectactivity->checkboxCstm               , '0');
            $this->assertEquals($projectactivity->currencyCstm->value        , 40);
            $this->assertEquals($projectactivity->currencyCstm->currency->id , $baseCurrency->id);
            $this->assertEquals($projectactivity->dateCstm                   , $dateAssert);
            $this->assertEquals($projectactivity->datetimeCstm               , $datetimeAssert);
            $this->assertEquals($projectactivity->decimalCstm                , '12');
            $this->assertEquals($projectactivity->picklistCstm->value        , 'b');
            $this->assertEquals($projectactivity->integerCstm                , 11);
            $this->assertEquals($projectactivity->phoneCstm                  , '259-784-2069');
            $this->assertEquals($projectactivity->radioCstm->value           , 'e');
            $this->assertEquals($projectactivity->textCstm                   , 'This is a test Edit Text');
            $this->assertEquals($projectactivity->textareaCstm               , 'This is a test Edit TextArea');
            $this->assertEquals($projectactivity->urlCstm                    , 'http://wwww.abc-edit.com');
            $this->assertEquals($projectactivity->dateCstm                   , $dateAssert);
            $this->assertEquals($projectactivity->datetimeCstm               , $datetimeAssert);
            $this->assertEquals($projectactivity->countrylistCstm->value     , 'aaaa');
            $this->assertEquals($projectactivity->statelistCstm->value       , 'aaa1');
            $this->assertEquals($projectactivity->citylistCstm->value        , 'ab1');
            $this->assertContains('gg'                                   , $projectactivity->multiselectCstm->values);
            $this->assertContains('hh'                                   , $projectactivity->multiselectCstm->values);
            $this->assertEquals(0                                        , $projectactivity->tagcloudCstm->values->count());
            $metadata            = CalculatedDerivedAttributeMetadata::
                                   getByNameAndModelClassName('calcnumber', 'Projectactivity');
            $testCalculatedValue = CalculatedNumberUtil::calculateByFormulaAndModel($metadata->getFormula(), $projectactivity);
            $this->assertEquals(132                                      , $testCalculatedValue);
        }

        /**
         * @depends testEditOfTheProjectactivityForTheTagCloudFieldAfterRemovingAllTagsPlacedForProjectactivitiesModule
         */
        public function testEditOfTheProjectactivityForTheCustomFieldsPlacedForProjectactivitiesModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Set the date and datetime variable values here.
            $date           = Yii::app()->dateFormatter->format(DateTimeUtil::getLocaleDateFormat(), time());
            $dateAssert     = date('Y-m-d');
            $datetime       = Yii::app()->dateFormatter->format(DateTimeUtil::getLocaleDateTimeFormat(), time());
            $datetimeAssert = date('Y-m-d H:i:')."00";
            $baseCurrency   = Currency::getByCode(Yii::app()->currencyHelper->getBaseCode());

            //Retrieve the account id, the super user id and projectactivity Id.
            $accountId                        = self::getModelIdByModelNameAndName ('Account', 'superAccount');
            $superUserId                      = $super->id;
            $explicitReadWriteModelPermission = ExplicitReadWriteModelPermissionsUtil::MIXED_TYPE_EVERYONE_GROUP;
            $projectactivity                      = Projectactivity::getByName('myEditProjectactivity');
            $projectactivityId                    = $projectactivity[0]->id;

            //Edit a new Projectactivity based on the custom fields.
            $this->setGetArray(array('id' => $projectactivityId));
            $this->setPostArray(array('Projectactivity' => array(
                            'name'                              => 'myEditProjectactivity',
                            'amount'                            => array('value' => 288000,
                                                                         'currency' => array(
                                                                         'id' => $baseCurrency->id)),
                            'account'                           => array('id' => $accountId),
                            'probability'                       => '2',
                            'closeDate'                         => $date,
                            'stage'                             => array('value' => 'Qualification'),
                            'source'                            => array('value' => 'Inbound Call'),
                            'description'                       => 'This is the Edit Description',
                            'owner'                             => array('id' => $superUserId),
                            'explicitReadWriteModelPermissions' => array('type' => $explicitReadWriteModelPermission),
                            'checkboxCstm'                      => '0',
                            'currencyCstm'                      => array('value'   => 40,
                                                                         'currency' => array(
                                                                         'id' => $baseCurrency->id)),
                            'decimalCstm'                       => '12',
                            'dateCstm'                          => $date,
                            'datetimeCstm'                      => $datetime,
                            'picklistCstm'                      => array('value'  => 'b'),
                            'multiselectCstm'                   => array('values' =>  array('gg', 'hh')),
                            'tagcloudCstm'                      => array('values' =>  array('reading', 'surfing')),
                            'countrylistCstm'                   => array('value'  => 'aaaa'),
                            'statelistCstm'                     => array('value'  => 'aaa1'),
                            'citylistCstm'                      => array('value'  => 'ab1'),
                            'integerCstm'                       => '11',
                            'phoneCstm'                         => '259-784-2069',
                            'radioCstm'                         => array('value' => 'e'),
                            'textCstm'                          => 'This is a test Edit Text',
                            'textareaCstm'                      => 'This is a test Edit TextArea',
                            'urlCstm'                           => 'http://wwww.abc-edit.com')));
            $this->runControllerWithRedirectExceptionAndGetUrl('projectactivities/default/edit');

            //Check the details if they are saved properly for the custom fields.
            $projectactivityId = self::getModelIdByModelNameAndName('Projectactivity', 'myEditProjectactivity');
            $projectactivity   = Projectactivity::getById($projectactivityId);

            //Retrieve the permission of the projectactivity.
            $explicitReadWriteModelPermissions = ExplicitReadWriteModelPermissionsUtil::
                                                 makeBySecurableItem($projectactivity);
            $readWritePermitables              = $explicitReadWriteModelPermissions->getReadWritePermitables();
            $readOnlyPermitables               = $explicitReadWriteModelPermissions->getReadOnlyPermitables();

            $this->assertEquals($projectactivity->name                       , 'myEditProjectactivity');
            $this->assertEquals($projectactivity->amount->value              , '288000');
            $this->assertEquals($projectactivity->amount->currency->id       , $baseCurrency->id);
            $this->assertEquals($projectactivity->account->id                , $accountId);
            $this->assertEquals($projectactivity->probability                , '2');
            $this->assertEquals($projectactivity->stage->value               , 'Qualification');
            $this->assertEquals($projectactivity->source->value              , 'Inbound Call');
            $this->assertEquals($projectactivity->description                , 'This is the Edit Description');
            $this->assertEquals($projectactivity->owner->id                  , $superUserId);
            $this->assertEquals(1                                        , count($readWritePermitables));
            $this->assertEquals(0                                        , count($readOnlyPermitables));
            $this->assertEquals($projectactivity->checkboxCstm               , '0');
            $this->assertEquals($projectactivity->currencyCstm->value        , 40);
            $this->assertEquals($projectactivity->currencyCstm->currency->id , $baseCurrency->id);
            $this->assertEquals($projectactivity->dateCstm                   , $dateAssert);
            $this->assertEquals($projectactivity->datetimeCstm               , $datetimeAssert);
            $this->assertEquals($projectactivity->decimalCstm                , '12');
            $this->assertEquals($projectactivity->picklistCstm->value        , 'b');
            $this->assertEquals($projectactivity->integerCstm                , 11);
            $this->assertEquals($projectactivity->phoneCstm                  , '259-784-2069');
            $this->assertEquals($projectactivity->radioCstm->value           , 'e');
            $this->assertEquals($projectactivity->textCstm                   , 'This is a test Edit Text');
            $this->assertEquals($projectactivity->textareaCstm               , 'This is a test Edit TextArea');
            $this->assertEquals($projectactivity->urlCstm                    , 'http://wwww.abc-edit.com');
            $this->assertEquals($projectactivity->dateCstm                   , $dateAssert);
            $this->assertEquals($projectactivity->datetimeCstm               , $datetimeAssert);
            $this->assertEquals($projectactivity->countrylistCstm->value     , 'aaaa');
            $this->assertEquals($projectactivity->statelistCstm->value       , 'aaa1');
            $this->assertEquals($projectactivity->citylistCstm->value        , 'ab1');
            $this->assertContains('gg'                                   , $projectactivity->multiselectCstm->values);
            $this->assertContains('hh'                                   , $projectactivity->multiselectCstm->values);
            $this->assertContains('reading'                              , $projectactivity->tagcloudCstm->values);
            $this->assertContains('surfing'                              , $projectactivity->tagcloudCstm->values);
            $metadata            = CalculatedDerivedAttributeMetadata::
                                   getByNameAndModelClassName('calcnumber', 'Projectactivity');
            $testCalculatedValue = CalculatedNumberUtil::calculateByFormulaAndModel($metadata->getFormula(), $projectactivity);
            $this->assertEquals(132                                      , $testCalculatedValue);
        }

        /**
         * @depends testEditOfTheProjectactivityForTheCustomFieldsPlacedForProjectactivitiesModule
         */
        public function testWhetherSearchWorksForTheCustomFieldsPlacedForProjectactivitiesModuleAfterEditingTheProjectactivity()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Retrieve the account id, the super user id and projectactivity Id.
            $accountId      = self::getModelIdByModelNameAndName ('Account', 'superAccount');
            $superUserId    = $super->id;
            $baseCurrency   = Currency::getByCode(Yii::app()->currencyHelper->getBaseCode());

            //Search a created Projectactivity using the customfields.
            $this->resetPostArray();
            $this->setGetArray(array(
                        'ProjectactivitiesSearchForm' =>
                            ProjectactivitiesDesignerWalkthroughHelperUtil::fetchProjectactivitiesSearchFormGetData($accountId,
                                                                                      $superUserId, $baseCurrency->id),
                        'ajax'                    =>  'list-view')
            );
            $content = $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default');

            //Assert that the edit Projectactivity exits after the edit and is diaplayed on the search page.
            //$this->assertTrue(strpos($content, "Displaying 1-1 of 1 result(s).") > 0); //removed until we show the count again in the listview.
            $this->assertTrue(strpos($content, "myEditProjectactivity") > 0);
        }

        /**
         * @depends testWhetherSearchWorksForTheCustomFieldsPlacedForProjectactivitiesModuleAfterEditingTheProjectactivity
         */
        public function testDeleteOfTheProjectactivityUserForTheCustomFieldsPlacedForProjectactivitiesModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Get the projectactivity id from the recently edited projectactivity.
            $projectactivityId = self::getModelIdByModelNameAndName('Projectactivity', 'myEditProjectactivity');

            //Set the projectactivity id so as to delete the projectactivity.
            $this->setGetArray(array('id' => $projectactivityId));
            $this->runControllerWithRedirectExceptionAndGetUrl('projectactivities/default/delete');

            //Check wether the projectactivity is deleted.
            $projectactivity = Projectactivity::getByName('myEditProjectactivity');
            $this->assertEquals(0, count($projectactivity));
        }

        /**
         * @depends testDeleteOfTheProjectactivityUserForTheCustomFieldsPlacedForProjectactivitiesModule
         */
        public function testWhetherSearchWorksForTheCustomFieldsPlacedForProjectactivitiesModuleAfterDeletingTheProjectactivity()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Retrieve the account id, the super user id and projectactivity Id.
            $accountId      = self::getModelIdByModelNameAndName ('Account', 'superAccount');
            $superUserId    = $super->id;
            $baseCurrency   = Currency::getByCode(Yii::app()->currencyHelper->getBaseCode());

            //Search a created Projectactivity using the customfields.
            $this->resetPostArray();
            $this->setGetArray(array(
                        'ProjectactivitiesSearchForm' =>
                            ProjectactivitiesDesignerWalkthroughHelperUtil::fetchProjectactivitiesSearchFormGetData($accountId,
                                                                                      $superUserId, $baseCurrency->id),
                        'ajax'                    =>  'list-view')
            );
            $content = $this->runControllerWithNoExceptionsAndGetContent('projectactivities/default');

            //Assert that the edit Projectactivity does not exits after the search.
            $this->assertTrue(strpos($content, "No results found.") > 0);
        }

        /**
         * @depends testWhetherSearchWorksForTheCustomFieldsPlacedForProjectactivitiesModuleAfterDeletingTheProjectactivity
         */
        public function testTypeAheadWorksForTheTagCloudFieldPlacedForProjectactivitiesModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Search a list item by typing in tag cloud attribute.
            $this->resetPostArray();
            $this->setGetArray(array('name' => 'tagcloud',
                                     'term' => 'rea'));
            $content = $this->runControllerWithNoExceptionsAndGetContent('zurmo/default/autoCompleteCustomFieldData');

            //Check if the returned content contains the expected vlaue
            $this->assertTrue(strpos($content, "reading") > 0);
        }

        /**
         * @depends testTypeAheadWorksForTheTagCloudFieldPlacedForProjectactivitiesModule
         */
        public function testLabelLocalizationForTheTagCloudFieldPlacedForProjectactivitiesModule()
        {
            Yii::app()->user->userModel =  User::getByUsername('super');
            $languageHelper = new ZurmoLanguageHelper();
            $languageHelper->load();
            $this->assertEquals('en', $languageHelper->getForCurrentUser());
            Yii::app()->user->userModel->language = 'fr';
            $this->assertTrue(Yii::app()->user->userModel->save());
            $languageHelper->setActive('fr');
            $this->assertEquals('fr', Yii::app()->user->getState('language'));

            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Search a list item by typing in tag cloud attribute.
            $this->resetPostArray();
            $this->setGetArray(array('name' => 'tagcloud',
                                     'term' => 'surf'));
            $content = $this->runControllerWithNoExceptionsAndGetContent('zurmo/default/autoCompleteCustomFieldData');

            //Check if the returned content contains the expected vlaue
            $this->assertTrue(strpos($content, "surfing fr") > 0);
        }
    }
?>