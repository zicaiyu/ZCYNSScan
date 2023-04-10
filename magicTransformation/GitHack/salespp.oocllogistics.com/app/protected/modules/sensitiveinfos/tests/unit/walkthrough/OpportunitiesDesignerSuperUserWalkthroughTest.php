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
    * Designer Module Walkthrough of Sensitiveinfos.
    * Walkthrough for the super user of all possible controller actions.
    * Since this is a super user, he should have access to all controller actions
    * without any exceptions being thrown.
    * This also test the creation of the customfileds, addition of custom fields to all the layouts including the search
    * views.
    * This also test creation search, edit and delete of the Sensitiveinfo based on the custom fields.
    */
    class SensitiveinfosDesignerSuperUserWalkthroughTest extends ZurmoWalkthroughBaseTest
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

            //Create a Sensitiveinfo for testing.
            SensitiveinfoTestHelper::createSensitiveinfoWithAccountByNameForOwner('superOpp', $super, $account);
        }

         public function testSuperUserSensitiveinfoDefaultControllerActions()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Default Controller actions requiring some sort of parameter via POST or GET
            //Load Sensitiveinfo Modules Menu.
            $this->setGetArray(array('moduleClassName' => 'SensitiveinfosModule'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/modulesMenu');

            //Load AttributesList for Sensitiveinfo module.
            $this->setGetArray(array('moduleClassName' => 'SensitiveinfosModule'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/attributesList');

            //Load ModuleLayoutsList for Sensitiveinfo module.
            $this->setGetArray(array('moduleClassName' => 'SensitiveinfosModule'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/moduleLayoutsList');

            //Load ModuleEdit view for each applicable module.
            $this->setGetArray(array('moduleClassName' => 'SensitiveinfosModule'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/moduleEdit');

            //Now validate save with failed validation.
            $this->setGetArray(array('moduleClassName' => 'SensitiveinfosModule'));
            $this->setPostArray(array('ajax' => 'edit-form',
                'SensitiveinfosModuleForm' => $this->createModuleEditBadValidationPostData()));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/moduleEdit');
            $this->assertTrue(strlen($content) > 50); //approximate, but should definetely be larger than 50.

            //Now validate save with successful validation.
            $this->setGetArray(array('moduleClassName' => 'SensitiveinfosModule'));
            $this->setPostArray(array('ajax' => 'edit-form',
                'SensitiveinfosModuleForm' => $this->createModuleEditGoodValidationPostData('opp new name')));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/moduleEdit');
            $this->assertEquals('[]', $content);

            //Now save successfully.
            $this->setGetArray(array('moduleClassName' => 'SensitiveinfosModule'));
            $this->setPostArray(array('save' => 'Save',
                'SensitiveinfosModuleForm' => $this->createModuleEditGoodValidationPostData('opp new name')));
            $this->runControllerWithRedirectExceptionAndGetContent('designer/default/moduleEdit');

            //Now confirm everything did in fact save correctly.
            $this->assertEquals('Opp New Name',  SensitiveinfosModule::getModuleLabelByTypeAndLanguage('Singular'));
            $this->assertEquals('Opp New Names', SensitiveinfosModule::getModuleLabelByTypeAndLanguage('Plural'));
            $this->assertEquals('opp new name',  SensitiveinfosModule::getModuleLabelByTypeAndLanguage('SingularLowerCase'));
            $this->assertEquals('opp new names', SensitiveinfosModule::getModuleLabelByTypeAndLanguage('PluralLowerCase'));

            //Load LayoutEdit for each applicable module and applicable layout
            $this->resetPostArray();
            $this->setGetArray(array('moduleClassName' => 'SensitiveinfosModule',
                                     'viewClassName'   => 'SensitiveinfosListView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
            $this->setGetArray(array('moduleClassName' => 'SensitiveinfosModule',
                                     'viewClassName'   => 'SensitiveinfosModalListView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
            $this->setGetArray(array('moduleClassName' => 'SensitiveinfosModule',
                                     'viewClassName'   => 'SensitiveinfosModalSearchView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
            $this->setGetArray(array('moduleClassName' => 'SensitiveinfosModule',
                                     'viewClassName'   => 'SensitiveinfosMassEditView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
            $this->setGetArray(array('moduleClassName' => 'SensitiveinfosModule',
                                     'viewClassName'   => 'SensitiveinfosRelatedListView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
            $this->setGetArray(array('moduleClassName' => 'SensitiveinfosModule',
                                     'viewClassName'   => 'SensitiveinfosSearchView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
            $this->setGetArray(array('moduleClassName' => 'SensitiveinfosModule',
                                     'viewClassName'   => 'SensitiveinfoEditAndDetailsView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
        }

        /**
         * @depends testSuperUserSensitiveinfoDefaultControllerActions
         */
        public function testSuperUserCustomFieldsWalkthroughForSensitiveinfosModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Test create field list.
            $this->setGetArray(array('moduleClassName' => 'SensitiveinfosModule'));

            //View creation screen, then create custom field for each custom field type.
            $this->createCheckBoxCustomFieldByModule            ('SensitiveinfosModule', 'checkbox');
            $this->createCurrencyValueCustomFieldByModule       ('SensitiveinfosModule', 'currency');
            $this->createDateCustomFieldByModule                ('SensitiveinfosModule', 'date');
            $this->createDateTimeCustomFieldByModule            ('SensitiveinfosModule', 'datetime');
            $this->createDecimalCustomFieldByModule             ('SensitiveinfosModule', 'decimal');
            $this->createDropDownCustomFieldByModule            ('SensitiveinfosModule', 'picklist');
            $this->createDependentDropDownCustomFieldByModule   ('SensitiveinfosModule', 'countrylist');
            $this->createDependentDropDownCustomFieldByModule   ('SensitiveinfosModule', 'statelist');
            $this->createDependentDropDownCustomFieldByModule   ('SensitiveinfosModule', 'citylist');
            $this->createIntegerCustomFieldByModule             ('SensitiveinfosModule', 'integer');
            $this->createMultiSelectDropDownCustomFieldByModule ('SensitiveinfosModule', 'multiselect');
            $this->createTagCloudCustomFieldByModule            ('SensitiveinfosModule', 'tagcloud');
            $this->createCalculatedNumberCustomFieldByModule    ('SensitiveinfosModule', 'calcnumber');
            $this->createDropDownDependencyCustomFieldByModule  ('SensitiveinfosModule', 'dropdowndep');
            $this->createPhoneCustomFieldByModule               ('SensitiveinfosModule', 'phone');
            $this->createRadioDropDownCustomFieldByModule       ('SensitiveinfosModule', 'radio');
            $this->createTextCustomFieldByModule                ('SensitiveinfosModule', 'text');
            $this->createTextAreaCustomFieldByModule            ('SensitiveinfosModule', 'textarea');
            $this->createUrlCustomFieldByModule                 ('SensitiveinfosModule', 'url');
        }

        /**
         * @depends testSuperUserCustomFieldsWalkthroughForSensitiveinfosModule
         */
        public function testSuperUserAddCustomFieldsToLayoutsForSensitiveinfosModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Add custom fields to SensitiveinfoEditAndDetailsView.
            $this->setGetArray(array('moduleClassName' => 'SensitiveinfosModule',
                                     'viewClassName'   => 'SensitiveinfoEditAndDetailsView'));
            $layout = SensitiveinfosDesignerWalkthroughHelperUtil::getSensitiveinfoEditAndDetailsViewLayoutWithAllCustomFieldsPlaced();
            $this->setPostArray(array('save'  => 'Save', 'layout' => $layout,
                                      'LayoutPanelsTypeForm' => array('type' => FormLayout::PANELS_DISPLAY_TYPE_ALL)));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/LayoutEdit');
            $this->assertFalse(strpos($content, 'Layout saved successfully') === false);

            //Add all fields to SensitiveinfosSearchView.
            $this->setGetArray(array('moduleClassName' => 'SensitiveinfosModule',
                                     'viewClassName'   => 'SensitiveinfosSearchView'));
            $layout = SensitiveinfosDesignerWalkthroughHelperUtil::getSensitiveinfosSearchViewLayoutWithAllCustomFieldsPlaced();
            $this->setPostArray(array('save'  => 'Save', 'layout' => $layout));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/LayoutEdit');
            $this->assertFalse(strpos($content, 'Layout saved successfully') === false);

            //Add all fields to SensitiveinfosListView.
            $this->setGetArray(array('moduleClassName' => 'SensitiveinfosModule',
                                     'viewClassName'   => 'SensitiveinfosListView'));
            $layout = SensitiveinfosDesignerWalkthroughHelperUtil::getSensitiveinfosListViewLayoutWithAllStandardAndCustomFieldsPlaced();
            $this->setPostArray(array('save'  => 'Save', 'layout' => $layout));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/LayoutEdit');
            $this->assertFalse(strpos($content, 'Layout saved successfully') === false);

            //Add all fields to SensitiveinfosRelatedListView.
            $this->setGetArray(array('moduleClassName' => 'SensitiveinfosModule',
                                     'viewClassName'   => 'SensitiveinfosRelatedListView'));
            $layout = SensitiveinfosDesignerWalkthroughHelperUtil::getSensitiveinfosListViewLayoutWithAllStandardAndCustomFieldsPlaced();
            $this->setPostArray(array('save'  => 'Save', 'layout' => $layout));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/LayoutEdit');
            $this->assertFalse(strpos($content, 'Layout saved successfully') === false);

            //Add all fields to SensitiveinfosMassEditView.
            $this->setGetArray(array('moduleClassName' => 'SensitiveinfosModule',
                                     'viewClassName'   => 'SensitiveinfosMassEditView'));
            $layout = SensitiveinfosDesignerWalkthroughHelperUtil::getSensitiveinfosMassEditViewLayoutWithAllStandardAndCustomFieldsPlaced();
            $this->setPostArray(array('save'  => 'Save', 'layout' => $layout));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/LayoutEdit');
            $this->assertFalse(strpos($content, 'Layout saved successfully') === false);
        }

        /**
         * @depends testSuperUserAddCustomFieldsToLayoutsForSensitiveinfosModule
         */
        public function testLayoutsLoadOkAfterCustomFieldsPlacedForSensitiveinfosModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $superAccountId = self::getModelIdByModelNameAndName ('Account', 'superAccount');
            $superSensitiveinfoId = self::getModelIdByModelNameAndName ('Sensitiveinfo', 'superOpp');
            //Load create, edit, and details views.
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/create');
            $this->setGetArray(array('id' => $superSensitiveinfoId));
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/edit');
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/details');
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/list');
            $this->setGetArray(array(
                'modalTransferInformation' => array('sourceIdFieldId' => 'x', 'sourceNameFieldId' => 'y')
            ));
            $this->resetPostArray();
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/modalList');
            $this->setGetArray(array('id' => $superAccountId));
            $this->resetPostArray();
            $this->runControllerWithNoExceptionsAndGetContent('accounts/default/details');
            $this->setGetArray(array('selectAll' => '1'));
            $this->resetPostArray();
            $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default/massEdit');
        }

        /**
         * @depends testLayoutsLoadOkAfterCustomFieldsPlacedForSensitiveinfosModule
         */
        public function testCreateAnSensitiveinfoAfterTheCustomFieldsArePlacedForSensitiveinfosModule()
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

            //Create a new Sensitiveinfo based on the custom fields.
            $this->resetGetArray();
            $this->setPostArray(array('Sensitiveinfo' => array(
                            'name'                              => 'myNewSensitiveinfo',
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
            $this->runControllerWithRedirectExceptionAndGetUrl('sensitiveinfos/default/create');

            //Check the details if they are saved properly for the custom fields.
            $sensitiveinfoId = self::getModelIdByModelNameAndName('Sensitiveinfo', 'myNewSensitiveinfo');
            $sensitiveinfo   = Sensitiveinfo::getById($sensitiveinfoId);

            //Retrieve the permission of the sensitiveinfo.
            $explicitReadWriteModelPermissions = ExplicitReadWriteModelPermissionsUtil::
                                                 makeBySecurableItem($sensitiveinfo);
            $readWritePermitables              = $explicitReadWriteModelPermissions->getReadWritePermitables();
            $readOnlyPermitables               = $explicitReadWriteModelPermissions->getReadOnlyPermitables();

            $this->assertEquals($sensitiveinfo->name                       , 'myNewSensitiveinfo');
            $this->assertEquals($sensitiveinfo->amount->value              , '298000');
            $this->assertEquals($sensitiveinfo->amount->currency->id       , $baseCurrency->id);
            $this->assertEquals($sensitiveinfo->account->id                , $accountId);
            $this->assertEquals($sensitiveinfo->probability                , '1');
            $this->assertEquals($sensitiveinfo->stage->value               , 'Prospecting');
            $this->assertEquals($sensitiveinfo->source->value              , 'Self-Generated');
            $this->assertEquals($sensitiveinfo->description                , 'This is the Description');
            $this->assertEquals($sensitiveinfo->owner->id                  , $superUserId);
            $this->assertEquals(0                                        , count($readWritePermitables));
            $this->assertEquals(0                                        , count($readOnlyPermitables));
            $this->assertEquals($sensitiveinfo->checkboxCstm               , '1');
            $this->assertEquals($sensitiveinfo->currencyCstm->value        , 45);
            $this->assertEquals($sensitiveinfo->currencyCstm->currency->id , $baseCurrency->id);
            $this->assertEquals($sensitiveinfo->dateCstm                   , $dateAssert);
            $this->assertEquals($sensitiveinfo->datetimeCstm               , $datetimeAssert);
            $this->assertEquals($sensitiveinfo->decimalCstm                , '123');
            $this->assertEquals($sensitiveinfo->picklistCstm->value        , 'a');
            $this->assertEquals($sensitiveinfo->integerCstm                , 12);
            $this->assertEquals($sensitiveinfo->phoneCstm                  , '259-784-2169');
            $this->assertEquals($sensitiveinfo->radioCstm->value           , 'd');
            $this->assertEquals($sensitiveinfo->textCstm                   , 'This is a test Text');
            $this->assertEquals($sensitiveinfo->textareaCstm               , 'This is a test TextArea');
            $this->assertEquals($sensitiveinfo->urlCstm                    , 'http://wwww.abc.com');
            $this->assertEquals($sensitiveinfo->countrylistCstm->value     , 'bbbb');
            $this->assertEquals($sensitiveinfo->statelistCstm->value       , 'bbb1');
            $this->assertEquals($sensitiveinfo->citylistCstm->value        , 'bb1');
            $this->assertContains('ff'                                   , $sensitiveinfo->multiselectCstm->values);
            $this->assertContains('rr'                                   , $sensitiveinfo->multiselectCstm->values);
            $this->assertContains('writing'                              , $sensitiveinfo->tagcloudCstm->values);
            $this->assertContains('gardening'                            , $sensitiveinfo->tagcloudCstm->values);
            $metadata            = CalculatedDerivedAttributeMetadata::
                                   getByNameAndModelClassName('calcnumber', 'Sensitiveinfo');
            $testCalculatedValue = CalculatedNumberUtil::calculateByFormulaAndModel($metadata->getFormula(), $sensitiveinfo);
            $this->assertEquals(1476                                     , $testCalculatedValue);
        }

        /**
         * @depends testCreateAnSensitiveinfoAfterTheCustomFieldsArePlacedForSensitiveinfosModule
         */
        public function testWhetherSearchWorksForTheCustomFieldsPlacedForSensitiveinfosModuleAfterCreatingTheSensitiveinfo()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Retrieve the account id and the super user id.
            $accountId      = self::getModelIdByModelNameAndName ('Account', 'superAccount');
            $superUserId    = $super->id;
            $baseCurrency   = Currency::getByCode(Yii::app()->currencyHelper->getBaseCode());

            //Search a created sensitiveinfo using the customfield.
            $this->resetPostArray();
            $this->setGetArray(array('SensitiveinfosSearchForm' => array(
                                                'name'               => 'myNewSensitiveinfo',
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
            $content = $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default');

            //Check if the sensitiveinfo name exits after the search is performed on the basis of the
            //custom fields added to the sensitiveinfos module.
            //$this->assertTrue(strpos($content, "Displaying 1-1 of 1 result(s).") > 0); //removed until we show the count again in the listview.
            $this->assertTrue(strpos($content, "myNewSensitiveinfo") > 0);
        }

        /**
         * @depends testWhetherSearchWorksForTheCustomFieldsPlacedForSensitiveinfosModuleAfterCreatingTheSensitiveinfo
         */
        public function testEditOfTheSensitiveinfoForTheTagCloudFieldAfterRemovingAllTagsPlacedForSensitiveinfosModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Set the date and datetime variable values here.
            $date           = Yii::app()->dateFormatter->format(DateTimeUtil::getLocaleDateFormat(), time());
            $dateAssert     = date('Y-m-d');
            $datetime       = Yii::app()->dateFormatter->format(DateTimeUtil::getLocaleDateTimeFormat(), time());
            $datetimeAssert = date('Y-m-d H:i:')."00";
            $baseCurrency   = Currency::getByCode(Yii::app()->currencyHelper->getBaseCode());

            //Retrieve the account id, the super user id and sensitiveinfo Id.
            $accountId                        = self::getModelIdByModelNameAndName ('Account', 'superAccount');
            $superUserId                      = $super->id;
            $explicitReadWriteModelPermission = ExplicitReadWriteModelPermissionsUtil::MIXED_TYPE_EVERYONE_GROUP;
            $sensitiveinfo   = Sensitiveinfo::getByName('myNewSensitiveinfo');
            $sensitiveinfoId = $sensitiveinfo[0]->id;
            $this->assertEquals(2, $sensitiveinfo[0]->tagcloudCstm->values->count());

            //Edit a new Sensitiveinfo based on the custom fields.
            $this->setGetArray(array('id' => $sensitiveinfoId));
            $this->setPostArray(array('Sensitiveinfo' => array(
                            'name'                              => 'myEditSensitiveinfo',
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
            $this->runControllerWithRedirectExceptionAndGetUrl('sensitiveinfos/default/edit');

            //Check the details if they are saved properly for the custom fields.
            $sensitiveinfoId = self::getModelIdByModelNameAndName('Sensitiveinfo', 'myEditSensitiveinfo');
            $sensitiveinfo   = Sensitiveinfo::getById($sensitiveinfoId);

            //Retrieve the permission of the sensitiveinfo.
            $explicitReadWriteModelPermissions = ExplicitReadWriteModelPermissionsUtil::
                                                 makeBySecurableItem($sensitiveinfo);
            $readWritePermitables              = $explicitReadWriteModelPermissions->getReadWritePermitables();
            $readOnlyPermitables               = $explicitReadWriteModelPermissions->getReadOnlyPermitables();

            $this->assertEquals($sensitiveinfo->name                       , 'myEditSensitiveinfo');
            $this->assertEquals($sensitiveinfo->amount->value              , '288000');
            $this->assertEquals($sensitiveinfo->amount->currency->id       , $baseCurrency->id);
            $this->assertEquals($sensitiveinfo->account->id                , $accountId);
            $this->assertEquals($sensitiveinfo->probability                , '2');
            $this->assertEquals($sensitiveinfo->stage->value               , 'Qualification');
            $this->assertEquals($sensitiveinfo->source->value              , 'Inbound Call');
            $this->assertEquals($sensitiveinfo->description                , 'This is the Edit Description');
            $this->assertEquals($sensitiveinfo->owner->id                  , $superUserId);
            $this->assertEquals(1                                        , count($readWritePermitables));
            $this->assertEquals(0                                        , count($readOnlyPermitables));
            $this->assertEquals($sensitiveinfo->checkboxCstm               , '0');
            $this->assertEquals($sensitiveinfo->currencyCstm->value        , 40);
            $this->assertEquals($sensitiveinfo->currencyCstm->currency->id , $baseCurrency->id);
            $this->assertEquals($sensitiveinfo->dateCstm                   , $dateAssert);
            $this->assertEquals($sensitiveinfo->datetimeCstm               , $datetimeAssert);
            $this->assertEquals($sensitiveinfo->decimalCstm                , '12');
            $this->assertEquals($sensitiveinfo->picklistCstm->value        , 'b');
            $this->assertEquals($sensitiveinfo->integerCstm                , 11);
            $this->assertEquals($sensitiveinfo->phoneCstm                  , '259-784-2069');
            $this->assertEquals($sensitiveinfo->radioCstm->value           , 'e');
            $this->assertEquals($sensitiveinfo->textCstm                   , 'This is a test Edit Text');
            $this->assertEquals($sensitiveinfo->textareaCstm               , 'This is a test Edit TextArea');
            $this->assertEquals($sensitiveinfo->urlCstm                    , 'http://wwww.abc-edit.com');
            $this->assertEquals($sensitiveinfo->dateCstm                   , $dateAssert);
            $this->assertEquals($sensitiveinfo->datetimeCstm               , $datetimeAssert);
            $this->assertEquals($sensitiveinfo->countrylistCstm->value     , 'aaaa');
            $this->assertEquals($sensitiveinfo->statelistCstm->value       , 'aaa1');
            $this->assertEquals($sensitiveinfo->citylistCstm->value        , 'ab1');
            $this->assertContains('gg'                                   , $sensitiveinfo->multiselectCstm->values);
            $this->assertContains('hh'                                   , $sensitiveinfo->multiselectCstm->values);
            $this->assertEquals(0                                        , $sensitiveinfo->tagcloudCstm->values->count());
            $metadata            = CalculatedDerivedAttributeMetadata::
                                   getByNameAndModelClassName('calcnumber', 'Sensitiveinfo');
            $testCalculatedValue = CalculatedNumberUtil::calculateByFormulaAndModel($metadata->getFormula(), $sensitiveinfo);
            $this->assertEquals(132                                      , $testCalculatedValue);
        }

        /**
         * @depends testEditOfTheSensitiveinfoForTheTagCloudFieldAfterRemovingAllTagsPlacedForSensitiveinfosModule
         */
        public function testEditOfTheSensitiveinfoForTheCustomFieldsPlacedForSensitiveinfosModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Set the date and datetime variable values here.
            $date           = Yii::app()->dateFormatter->format(DateTimeUtil::getLocaleDateFormat(), time());
            $dateAssert     = date('Y-m-d');
            $datetime       = Yii::app()->dateFormatter->format(DateTimeUtil::getLocaleDateTimeFormat(), time());
            $datetimeAssert = date('Y-m-d H:i:')."00";
            $baseCurrency   = Currency::getByCode(Yii::app()->currencyHelper->getBaseCode());

            //Retrieve the account id, the super user id and sensitiveinfo Id.
            $accountId                        = self::getModelIdByModelNameAndName ('Account', 'superAccount');
            $superUserId                      = $super->id;
            $explicitReadWriteModelPermission = ExplicitReadWriteModelPermissionsUtil::MIXED_TYPE_EVERYONE_GROUP;
            $sensitiveinfo                      = Sensitiveinfo::getByName('myEditSensitiveinfo');
            $sensitiveinfoId                    = $sensitiveinfo[0]->id;

            //Edit a new Sensitiveinfo based on the custom fields.
            $this->setGetArray(array('id' => $sensitiveinfoId));
            $this->setPostArray(array('Sensitiveinfo' => array(
                            'name'                              => 'myEditSensitiveinfo',
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
            $this->runControllerWithRedirectExceptionAndGetUrl('sensitiveinfos/default/edit');

            //Check the details if they are saved properly for the custom fields.
            $sensitiveinfoId = self::getModelIdByModelNameAndName('Sensitiveinfo', 'myEditSensitiveinfo');
            $sensitiveinfo   = Sensitiveinfo::getById($sensitiveinfoId);

            //Retrieve the permission of the sensitiveinfo.
            $explicitReadWriteModelPermissions = ExplicitReadWriteModelPermissionsUtil::
                                                 makeBySecurableItem($sensitiveinfo);
            $readWritePermitables              = $explicitReadWriteModelPermissions->getReadWritePermitables();
            $readOnlyPermitables               = $explicitReadWriteModelPermissions->getReadOnlyPermitables();

            $this->assertEquals($sensitiveinfo->name                       , 'myEditSensitiveinfo');
            $this->assertEquals($sensitiveinfo->amount->value              , '288000');
            $this->assertEquals($sensitiveinfo->amount->currency->id       , $baseCurrency->id);
            $this->assertEquals($sensitiveinfo->account->id                , $accountId);
            $this->assertEquals($sensitiveinfo->probability                , '2');
            $this->assertEquals($sensitiveinfo->stage->value               , 'Qualification');
            $this->assertEquals($sensitiveinfo->source->value              , 'Inbound Call');
            $this->assertEquals($sensitiveinfo->description                , 'This is the Edit Description');
            $this->assertEquals($sensitiveinfo->owner->id                  , $superUserId);
            $this->assertEquals(1                                        , count($readWritePermitables));
            $this->assertEquals(0                                        , count($readOnlyPermitables));
            $this->assertEquals($sensitiveinfo->checkboxCstm               , '0');
            $this->assertEquals($sensitiveinfo->currencyCstm->value        , 40);
            $this->assertEquals($sensitiveinfo->currencyCstm->currency->id , $baseCurrency->id);
            $this->assertEquals($sensitiveinfo->dateCstm                   , $dateAssert);
            $this->assertEquals($sensitiveinfo->datetimeCstm               , $datetimeAssert);
            $this->assertEquals($sensitiveinfo->decimalCstm                , '12');
            $this->assertEquals($sensitiveinfo->picklistCstm->value        , 'b');
            $this->assertEquals($sensitiveinfo->integerCstm                , 11);
            $this->assertEquals($sensitiveinfo->phoneCstm                  , '259-784-2069');
            $this->assertEquals($sensitiveinfo->radioCstm->value           , 'e');
            $this->assertEquals($sensitiveinfo->textCstm                   , 'This is a test Edit Text');
            $this->assertEquals($sensitiveinfo->textareaCstm               , 'This is a test Edit TextArea');
            $this->assertEquals($sensitiveinfo->urlCstm                    , 'http://wwww.abc-edit.com');
            $this->assertEquals($sensitiveinfo->dateCstm                   , $dateAssert);
            $this->assertEquals($sensitiveinfo->datetimeCstm               , $datetimeAssert);
            $this->assertEquals($sensitiveinfo->countrylistCstm->value     , 'aaaa');
            $this->assertEquals($sensitiveinfo->statelistCstm->value       , 'aaa1');
            $this->assertEquals($sensitiveinfo->citylistCstm->value        , 'ab1');
            $this->assertContains('gg'                                   , $sensitiveinfo->multiselectCstm->values);
            $this->assertContains('hh'                                   , $sensitiveinfo->multiselectCstm->values);
            $this->assertContains('reading'                              , $sensitiveinfo->tagcloudCstm->values);
            $this->assertContains('surfing'                              , $sensitiveinfo->tagcloudCstm->values);
            $metadata            = CalculatedDerivedAttributeMetadata::
                                   getByNameAndModelClassName('calcnumber', 'Sensitiveinfo');
            $testCalculatedValue = CalculatedNumberUtil::calculateByFormulaAndModel($metadata->getFormula(), $sensitiveinfo);
            $this->assertEquals(132                                      , $testCalculatedValue);
        }

        /**
         * @depends testEditOfTheSensitiveinfoForTheCustomFieldsPlacedForSensitiveinfosModule
         */
        public function testWhetherSearchWorksForTheCustomFieldsPlacedForSensitiveinfosModuleAfterEditingTheSensitiveinfo()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Retrieve the account id, the super user id and sensitiveinfo Id.
            $accountId      = self::getModelIdByModelNameAndName ('Account', 'superAccount');
            $superUserId    = $super->id;
            $baseCurrency   = Currency::getByCode(Yii::app()->currencyHelper->getBaseCode());

            //Search a created Sensitiveinfo using the customfields.
            $this->resetPostArray();
            $this->setGetArray(array(
                        'SensitiveinfosSearchForm' =>
                            SensitiveinfosDesignerWalkthroughHelperUtil::fetchSensitiveinfosSearchFormGetData($accountId,
                                                                                      $superUserId, $baseCurrency->id),
                        'ajax'                    =>  'list-view')
            );
            $content = $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default');

            //Assert that the edit Sensitiveinfo exits after the edit and is diaplayed on the search page.
            //$this->assertTrue(strpos($content, "Displaying 1-1 of 1 result(s).") > 0); //removed until we show the count again in the listview.
            $this->assertTrue(strpos($content, "myEditSensitiveinfo") > 0);
        }

        /**
         * @depends testWhetherSearchWorksForTheCustomFieldsPlacedForSensitiveinfosModuleAfterEditingTheSensitiveinfo
         */
        public function testDeleteOfTheSensitiveinfoUserForTheCustomFieldsPlacedForSensitiveinfosModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Get the sensitiveinfo id from the recently edited sensitiveinfo.
            $sensitiveinfoId = self::getModelIdByModelNameAndName('Sensitiveinfo', 'myEditSensitiveinfo');

            //Set the sensitiveinfo id so as to delete the sensitiveinfo.
            $this->setGetArray(array('id' => $sensitiveinfoId));
            $this->runControllerWithRedirectExceptionAndGetUrl('sensitiveinfos/default/delete');

            //Check wether the sensitiveinfo is deleted.
            $sensitiveinfo = Sensitiveinfo::getByName('myEditSensitiveinfo');
            $this->assertEquals(0, count($sensitiveinfo));
        }

        /**
         * @depends testDeleteOfTheSensitiveinfoUserForTheCustomFieldsPlacedForSensitiveinfosModule
         */
        public function testWhetherSearchWorksForTheCustomFieldsPlacedForSensitiveinfosModuleAfterDeletingTheSensitiveinfo()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Retrieve the account id, the super user id and sensitiveinfo Id.
            $accountId      = self::getModelIdByModelNameAndName ('Account', 'superAccount');
            $superUserId    = $super->id;
            $baseCurrency   = Currency::getByCode(Yii::app()->currencyHelper->getBaseCode());

            //Search a created Sensitiveinfo using the customfields.
            $this->resetPostArray();
            $this->setGetArray(array(
                        'SensitiveinfosSearchForm' =>
                            SensitiveinfosDesignerWalkthroughHelperUtil::fetchSensitiveinfosSearchFormGetData($accountId,
                                                                                      $superUserId, $baseCurrency->id),
                        'ajax'                    =>  'list-view')
            );
            $content = $this->runControllerWithNoExceptionsAndGetContent('sensitiveinfos/default');

            //Assert that the edit Sensitiveinfo does not exits after the search.
            $this->assertTrue(strpos($content, "No results found.") > 0);
        }

        /**
         * @depends testWhetherSearchWorksForTheCustomFieldsPlacedForSensitiveinfosModuleAfterDeletingTheSensitiveinfo
         */
        public function testTypeAheadWorksForTheTagCloudFieldPlacedForSensitiveinfosModule()
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
         * @depends testTypeAheadWorksForTheTagCloudFieldPlacedForSensitiveinfosModule
         */
        public function testLabelLocalizationForTheTagCloudFieldPlacedForSensitiveinfosModule()
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