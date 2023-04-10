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
    * Designer Module Walkthrough of projects.
    * Walkthrough for the super user of all possible controller actions.
    * Since this is a super user, he should have access to all controller actions
    * without any exceptions being thrown.
    * This also test the creation of the customfileds, addition of custom fields to all the layouts including the search
    * views.
    * This also test creation, search, edit and delete of the project based on the custom fields.
    */
    class ProjectsDesignerSuperUserWalkthroughTest extends ZurmoWalkthroughBaseTest
    {
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            SecurityTestHelper::createSuperAdmin();
            $super                      = User::getByUsername('super');
            Yii::app()->user->userModel = $super;
            Currency::makeBaseCurrency();
            //Create a project for testing.
            $project = ProjectTestHelper::createProjectByNameForOwner('superProject', $super);
        }

        public function testSuperUserProjectDefaultControllerActions()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            //Default Controller actions requiring some sort of parameter via POST or GET
            //Load Project Modules Menu.
            $this->resetPostArray();
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/modulesMenu');

            //Load AttributesList for Project module.
            $this->resetPostArray();
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/attributesList');

            //Load ModuleLayoutsList for Project module.
            $this->resetPostArray();
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/moduleLayoutsList');

            //Load ModuleEdit view for each applicable module.
            $this->resetPostArray();
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/moduleEdit');

            //Now validate save with failed validation.
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule'));
            $this->setPostArray(array('ajax' => 'edit-form',
                'ProjectsModuleForm' => $this->createModuleEditBadValidationPostData()));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/moduleEdit');

            //Now validate save with successful validation.
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule'));
            $this->setPostArray(array('ajax' => 'edit-form',
                'ProjectsModuleForm' => $this->createModuleEditGoodValidationPostData('acc new name')));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/moduleEdit');
            $this->assertEquals('[]', $content);

            //Now save successfully.
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule'));
            $this->setPostArray(array('save' => 'Save',
                'ProjectsModuleForm' => $this->createModuleEditGoodValidationPostData('acc new name')));
            $this->runControllerWithRedirectExceptionAndGetContent('designer/default/moduleEdit');

            //Now confirm everything did in fact save correctly.
            $this->assertEquals('Acc New Name',  ProjectsModule::getModuleLabelByTypeAndLanguage('Singular'));
            $this->assertEquals('Acc New Names', ProjectsModule::getModuleLabelByTypeAndLanguage('Plural'));
            $this->assertEquals('acc new name',  ProjectsModule::getModuleLabelByTypeAndLanguage('SingularLowerCase'));
            $this->assertEquals('acc new names', ProjectsModule::getModuleLabelByTypeAndLanguage('PluralLowerCase'));

            //Load LayoutEdit for each applicable module and applicable layout
            $this->resetPostArray();
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule',
                                     'viewClassName'   => 'ProjectEditAndDetailsView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule',
                                     'viewClassName'   => 'ProjectsListView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule',
                                     'viewClassName'   => 'ProjectsMassEditView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule',
                                     'viewClassName'   => 'ProjectsModalListView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule',
                                     'viewClassName'   => 'ProjectsModalSearchView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule',
                                     'viewClassName'   => 'ProjectsRelatedListView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule',
                                     'viewClassName'   => 'ProjectsSearchView'));
            $this->runControllerWithNoExceptionsAndGetContent('designer/default/LayoutEdit');
        }

        /**
         * @depends testSuperUserProjectDefaultControllerActions
         */
        public function testSuperUserCustomFieldsWalkthroughForProjectsModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Test create field list.
            $this->resetPostArray();
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule'));

            //View creation screen, then create custom field for each custom field type.
            $this->createCheckBoxCustomFieldByModule            ('ProjectsModule', 'checkbox');
            $this->createCurrencyValueCustomFieldByModule       ('ProjectsModule', 'currency');
            $this->createDateCustomFieldByModule                ('ProjectsModule', 'date');
            $this->createDateTimeCustomFieldByModule            ('ProjectsModule', 'datetime');
            $this->createDecimalCustomFieldByModule             ('ProjectsModule', 'decimal');
            $this->createDropDownCustomFieldByModule            ('ProjectsModule', 'picklist');
            $this->createDependentDropDownCustomFieldByModule   ('ProjectsModule', 'countrylist');
            $this->createDependentDropDownCustomFieldByModule   ('ProjectsModule', 'statelist');
            $this->createDependentDropDownCustomFieldByModule   ('ProjectsModule', 'citylist');
            $this->createMultiSelectDropDownCustomFieldByModule ('ProjectsModule', 'multiselect');
            $this->createTagCloudCustomFieldByModule            ('ProjectsModule', 'tagcloud');
            $this->createCalculatedNumberCustomFieldByModule    ('ProjectsModule', 'calcnumber');
            $this->createDropDownDependencyCustomFieldByModule  ('ProjectsModule', 'dropdowndep');
            $this->createDropDownDependencyCustomFieldByModule  ('ProjectsModule', 'dropdowndep2');
            $this->createIntegerCustomFieldByModule             ('ProjectsModule', 'integer');
            $this->createPhoneCustomFieldByModule               ('ProjectsModule', 'phone');
            $this->createRadioDropDownCustomFieldByModule       ('ProjectsModule', 'radio');
            $this->createTextCustomFieldByModule                ('ProjectsModule', 'text');
            $this->createTextAreaCustomFieldByModule            ('ProjectsModule', 'textarea');
            $this->createUrlCustomFieldByModule                 ('ProjectsModule', 'url');
        }

        /**
         * @depends testSuperUserCustomFieldsWalkthroughForProjectsModule
         */
        public function testSuperUserAddCustomFieldsToLayoutsForProjectsModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Add custom fields to ProjectEditAndDetailsView.
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule',
                                     'viewClassName'   => 'ProjectEditAndDetailsView'));
            $layout = ProjectsDesignerWalkthroughHelperUtil::getProjectEditAndDetailsViewLayoutWithAllCustomFieldsPlaced();
            $this->setPostArray(array('save'  => 'Save', 'layout' => $layout,
                                      'LayoutPanelsTypeForm' => array('type' => FormLayout::PANELS_DISPLAY_TYPE_ALL)));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/LayoutEdit');
            $this->assertFalse(strpos($content, 'Layout saved successfully') === false);

            //Add all fields to ProjectsSearchView.
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule',
                                     'viewClassName'   => 'ProjectsSearchView'));
            $layout = ProjectsDesignerWalkthroughHelperUtil::getProjectsSearchViewLayoutWithAllCustomFieldsPlaced();
            $this->setPostArray(array('save'  => 'Save', 'layout' => $layout));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/LayoutEdit');
            $this->assertFalse(strpos($content, 'Layout saved successfully') === false);

            //Add all fields to ProjectsModalSearchView.
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule',
                                     'viewClassName'   => 'ProjectsModalSearchView'));
            $layout = ProjectsDesignerWalkthroughHelperUtil::getProjectsSearchViewLayoutWithAllCustomFieldsPlaced();
            $this->setPostArray(array('save'  => 'Save', 'layout' => $layout));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/LayoutEdit');
            $this->assertFalse(strpos($content, 'Layout saved successfully') === false);

            //Add all fields to ProjectsListView.
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule',
                                     'viewClassName'   => 'ProjectsListView'));
            $layout = ProjectsDesignerWalkthroughHelperUtil::getProjectsListViewLayoutWithAllStandardAndCustomFieldsPlaced();
            $this->setPostArray(array('save'  => 'Save', 'layout' => $layout));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/LayoutEdit');
            $this->assertFalse(strpos($content, 'Layout saved successfully') === false);

            //Add all fields to ProjectsRelatedListView.
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule',
                                     'viewClassName'   => 'ProjectsRelatedListView'));
            $layout = ProjectsDesignerWalkthroughHelperUtil::getProjectsListViewLayoutWithAllStandardAndCustomFieldsPlaced();
            $this->setPostArray(array('save'  => 'Save', 'layout' => $layout));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/LayoutEdit');
            $this->assertFalse(strpos($content, 'Layout saved successfully') === false);

            //Add all fields to ProjectsModalListView.
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule',
                                     'viewClassName'   => 'ProjectsModalListView'));
            $layout = ProjectsDesignerWalkthroughHelperUtil::getProjectsListViewLayoutWithAllStandardAndCustomFieldsPlaced();
            $this->setPostArray(array('save'  => 'Save', 'layout' => $layout));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/LayoutEdit');
            $this->assertFalse(strpos($content, 'Layout saved successfully') === false);

            //Add all fields to ProjectsMassEditView.
            $this->setGetArray(array('moduleClassName' => 'ProjectsModule',
                                     'viewClassName'   => 'ProjectsMassEditView'));
            $layout = ProjectsDesignerWalkthroughHelperUtil::getProjectsMassEditViewLayoutWithAllStandardAndCustomFieldsPlaced();
            $this->setPostArray(array('save'  => 'Save', 'layout' => $layout));
            $content = $this->runControllerWithExitExceptionAndGetContent('designer/default/LayoutEdit');
            $this->assertFalse(strpos($content, 'Layout saved successfully') === false);
        }

        /**
         * @depends testSuperUserAddCustomFieldsToLayoutsForProjectsModule
         */
        public function testLayoutsLoadOkAfterCustomFieldsPlacedForProjectsModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');
            $superProjectId = self::getModelIdByModelNameAndName ('Project', 'superProject');
            //Load create, edit, and details views.
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/create');
            $this->setGetArray(array('id' => $superProjectId));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/edit');
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/details');
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/list');
            $this->setGetArray(array(
                'modalTransferInformation' => array('sourceIdFieldId' => 'x', 'sourceNameFieldId' => 'y')
            ));
            $this->resetPostArray();
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/modalList');
            $this->setGetArray(array('selectAll' => '1'));
            $this->resetPostArray();
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/massEdit');
            //todo: test related list once the related list is available in a sub view.
        }

        /**
         * @depends testLayoutsLoadOkAfterCustomFieldsPlacedForProjectsModule
         */
        public function testCreateAnProjectUserAfterTheCustomFieldsArePlacedForProjectsModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Set the date and datetime variable values here
            $date           = Yii::app()->dateFormatter->format(DateTimeUtil::getLocaleDateFormat(), time());
            $dateAssert     = date('Y-m-d');
            $datetime       = Yii::app()->dateFormatter->format(DateTimeUtil::getLocaleDateTimeFormat(), time());
            $datetimeAssert = date('Y-m-d H:i:')."00";
            $baseCurrency   = Currency::getByCode(Yii::app()->currencyHelper->getBaseCode());

            //Create a new project based on the custom fields.
            $this->resetGetArray();
            $this->setPostArray(array('Project' => array(
                                    'name'                                  => 'myNewProject',
                                    'officePhone'                           => '259-784-2169',
                                    'industry'                              => array('value' => 'Automotive'),
                                    'officeFax'                             => '299-845-7863',
                                    'employees'                             => '930',
                                    'annualRevenue'                         => '474000000',
                                    'type'                                  => array('value' => 'Prospect'),
                                    'website'                               => 'http://www.Unnamed.com',
                                    'primaryEmail'                          => array('emailAddress' => 'info@myNewProject.com',
                                                                                  'optOut' => '1',
                                                                                  'isInvalid' => '0'),
                                    'secondaryEmail'                        => array('emailAddress' => '',
                                                                                  'optOut' => '0',
                                                                                  'isInvalid' => '0'),
                                    'billingAddress'                        => array('street1' => '6466 South Madison Creek',
                                                                                  'street2' => '',
                                                                                  'city' => 'Chicago',
                                                                                  'state' => 'IL',
                                                                                  'postalCode' => '60652',
                                                                                  'country' => 'USA'),
                                    'shippingAddress'                       => array('street1' => '27054 West Michigan Lane',
                                                                                  'street2' => '',
                                                                                  'city' => 'Austin',
                                                                                  'state' => 'TX',
                                                                                  'postalCode' => '78759',
                                                                                  'country' => 'USA'),
                                    'description'                           => 'This is a Description',
                                    'explicitReadWriteModelPermissions'     => array('type' => null),
                                    'checkboxCstm'                          => '1',
                                    'currencyCstm'                          => array('value'    => 45,
                                                                                 'currency' => array('id' =>
                                                                                 $baseCurrency->id)),
                                    'dateCstm'                              => $date,
                                    'datetimeCstm'                          => $datetime,
                                    'decimalCstm'                           => '123',
                                    'picklistCstm'                          => array('value'  => 'a'),
                                    'multiselectCstm'                       => array('values' => array('ff', 'rr')),
                                    'tagcloudCstm'                          => array('values' => array('writing', 'gardening')),
                                    'countrylistCstm'                       => array('value'  => 'bbbb'),
                                    'statelistCstm'                         => array('value'  => 'bbb1'),
                                    'citylistCstm'                          => array('value'  => 'bb1'),
                                    'integerCstm'                           => '12',
                                    'phoneCstm'                             => '259-784-2169',
                                    'radioCstm'                             => array('value' => 'd'),
                                    'textCstm'                              => 'This is a test Text',
                                    'textareaCstm'                          => 'This is a test TextArea',
                                    'urlCstm'                               => 'http://wwww.abc.com')));
            $this->runControllerWithRedirectExceptionAndGetUrl('projects/default/create');

            //Check the details if they are saved properly for the custom fields.
            $project = Project::getByName('myNewProject');
            //Retrieve the permission for the project.
            $explicitReadWriteModelPermissions = ExplicitReadWriteModelPermissionsUtil::
                                                 makeBySecurableItem(Project::getById($project[0]->id));
            $readWritePermitables = $explicitReadWriteModelPermissions->getReadWritePermitables();
            $readOnlyPermitables  = $explicitReadWriteModelPermissions->getReadOnlyPermitables();
            $this->assertEquals(1, count($project));
            $this->assertEquals($project[0]->name                           , 'myNewProject');
            $this->assertEquals($project[0]->officePhone                    , '259-784-2169');
            $this->assertEquals($project[0]->industry->value                , 'Automotive');
            $this->assertEquals($project[0]->officeFax                      , '299-845-7863');
            $this->assertEquals($project[0]->employees                      , '930');
            $this->assertEquals($project[0]->annualRevenue                  , '474000000');
            $this->assertEquals($project[0]->type->value                    , 'Prospect');
            $this->assertEquals($project[0]->website                        , 'http://www.Unnamed.com');
            $this->assertEquals($project[0]->primaryEmail->emailAddress     , 'info@myNewProject.com');
            $this->assertEquals($project[0]->primaryEmail->optOut           , '1');
            $this->assertEquals($project[0]->primaryEmail->isInvalid        , '0');
            $this->assertEquals($project[0]->secondaryEmail->emailAddress   , '');
            $this->assertEquals($project[0]->secondaryEmail->optOut         , '0');
            $this->assertEquals($project[0]->secondaryEmail->isInvalid      , '0');
            $this->assertEquals($project[0]->billingAddress->street1        , '6466 South Madison Creek');
            $this->assertEquals($project[0]->billingAddress->street2        , '');
            $this->assertEquals($project[0]->billingAddress->city           , 'Chicago');
            $this->assertEquals($project[0]->billingAddress->state          , 'IL');
            $this->assertEquals($project[0]->billingAddress->postalCode     , '60652');
            $this->assertEquals($project[0]->billingAddress->country        , 'USA');
            $this->assertEquals($project[0]->shippingAddress->street1       , '27054 West Michigan Lane');
            $this->assertEquals($project[0]->shippingAddress->street2       , '');
            $this->assertEquals($project[0]->shippingAddress->city          , 'Austin');
            $this->assertEquals($project[0]->shippingAddress->state         , 'TX');
            $this->assertEquals($project[0]->shippingAddress->postalCode    , '78759');
            $this->assertEquals($project[0]->shippingAddress->country       , 'USA');
            $this->assertEquals($project[0]->description                    , 'This is a Description');
            $this->assertEquals(0                                           , count($readWritePermitables));
            $this->assertEquals(0                                           , count($readOnlyPermitables));
            $this->assertEquals($project[0]->checkboxCstm                   , '1');
            $this->assertEquals($project[0]->currencyCstm->value            , 45);
            $this->assertEquals($project[0]->currencyCstm->currency->id     , $baseCurrency->id);
            $this->assertEquals($project[0]->dateCstm                       , $dateAssert);
            $this->assertEquals($project[0]->datetimeCstm                   , $datetimeAssert);
            $this->assertEquals($project[0]->decimalCstm                    , '123');
            $this->assertEquals($project[0]->picklistCstm->value            , 'a');
            $this->assertEquals($project[0]->integerCstm                    , 12);
            $this->assertEquals($project[0]->phoneCstm                      , '259-784-2169');
            $this->assertEquals($project[0]->radioCstm->value               , 'd');
            $this->assertEquals($project[0]->textCstm                       , 'This is a test Text');
            $this->assertEquals($project[0]->textareaCstm                   , 'This is a test TextArea');
            $this->assertEquals($project[0]->urlCstm                        , 'http://wwww.abc.com');
            $this->assertEquals($project[0]->countrylistCstm->value         , 'bbbb');
            $this->assertEquals($project[0]->statelistCstm->value           , 'bbb1');
            $this->assertEquals($project[0]->citylistCstm->value            , 'bb1');
            $this->assertContains('ff'                                      , $project[0]->multiselectCstm->values);
            $this->assertContains('rr'                                      , $project[0]->multiselectCstm->values);
            $this->assertContains('writing'                                 , $project[0]->tagcloudCstm->values);
            $this->assertContains('gardening'                               , $project[0]->tagcloudCstm->values);
            $metadata            = CalculatedDerivedAttributeMetadata::
                                   getByNameAndModelClassName('calcnumber', 'Project');
            $testCalculatedValue = CalculatedNumberUtil::calculateByFormulaAndModel($metadata->getFormula(), $project[0]);
            $this->assertEquals(474000930                                   , $testCalculatedValue);
        }

        /**
         * @depends testCreateAnProjectUserAfterTheCustomFieldsArePlacedForProjectsModule
         */
        public function testWhetherSearchWorksForTheCustomFieldsPlacedForProjectsModuleAfterCreatingTheProjectUser()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Search a created project using the customfields.
            $this->resetPostArray();
            $this->setGetArray(array('ProjectsSearchForm' => array(
                                        'name'                  => 'myNewProject',
                                        'officePhone'           => '259-784-2169',
                                        'type'                  => array('value'  =>  'Prospect'),
                                        'officeFax'             => '299-845-7863',
                                        'employees'             => '930',
                                        'website'               => 'http://www.Unnamed.com',
                                        'annualRevenue'         => '474000000',
                                        'anyCity'               => 'Austin',
                                        'anyState'              => 'TX',
                                        'anyStreet'             => '27054 West Michigan Lane',
                                        'anyPostalCode'         => '78759',
                                        'anyCountry'            => 'USA',
                                        'anyEmail'              => 'info@myNewProject.com',
                                        'anyOptOutEmail'        => array('value' => '1'),
                                        'anyInvalidEmail'       => array('value' => ''),
                                        'ownedItemsOnly'        => '1',
                                        'industry'              => array('value' => 'Automotive'),
                                        'decimalCstm'           => '123',
                                        'integerCstm'           => '12',
                                        'phoneCstm'             => '259-784-2169',
                                        'textCstm'              => 'This is a test Text',
                                        'textareaCstm'          => 'This is a test TextArea',
                                        'urlCstm'               => 'http://wwww.abc.com',
                                        'checkboxCstm'          => array('value'  => '1'),
                                        'currencyCstm'          => array('value'  => 45),
                                        'picklistCstm'          => array('value'  => 'a'),
                                        'multiselectCstm'       => array('values' => array('ff', 'rr')),
                                        'tagcloudCstm'          => array('values' => array('writing', 'gardening')),
                                        'countrylistCstm'       => array('value'  => 'bbbb'),
                                        'statelistCstm'         => array('value'  => 'bbb1'),
                                        'citylistCstm'          => array('value'  => 'bb1'),
                                        'radioCstm'             => array('value'  => 'd'),
                                        'dateCstm__Date'        => array('type'   => 'Today'),
                                        'datetimeCstm__DateTime' => array('type'   => 'Today')),
                                     'ajax' =>  'list-view'));
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default');

            //Check if the project name exists after the search is performed on the basis of the
            //custom fields added to the projects module.
            //$this->assertTrue(strpos($content, "Displaying 1-1 of 1 result(s).") > 0); //removed until we show the count again in the listview.
            $this->assertTrue(strpos($content, "myNewProject") > 0);
        }

        /**
         * @depends testWhetherSearchWorksForTheCustomFieldsPlacedForProjectsModuleAfterCreatingTheProjectUser
         */
        public function testEditOfTheProjectUserForTheTagCloudFieldAfterRemovingAllTagsPlacedForProjectsModule()
        {
            $super          = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Set the date and datetime variable values here.
            $date           = Yii::app()->dateFormatter->format(DateTimeUtil::getLocaleDateFormat(), time());
            $dateAssert     = date('Y-m-d');
            $datetime       = Yii::app()->dateFormatter->format(DateTimeUtil::getLocaleDateTimeFormat(), time());
            $datetimeAssert = date('Y-m-d H:i:')."00";
            $baseCurrency   = Currency::getByCode(Yii::app()->currencyHelper->getBaseCode());
            $explicitReadWriteModelPermission = ExplicitReadWriteModelPermissionsUtil::MIXED_TYPE_EVERYONE_GROUP;

            //Get the project id from the recently created project.
            $project        = Project::getByName('myNewProject');
            $projectId      = $project[0]->id;
            $this->assertEquals(2, $project[0]->tagcloudCstm->values->count());

            //Edit and save the project.
            $this->setGetArray(array('id' => $projectId));
            $this->setPostArray(array('Project' => array(
                            'name'                              => 'myEditProject',
                            'officePhone'                       => '259-734-2169',
                            'industry'                          => array('value' => 'Energy'),
                            'officeFax'                         => '299-825-7863',
                            'employees'                         => '630',
                            'annualRevenue'                     => '472000000',
                            'type'                              => array('value' => 'Customer'),
                            'website'                           => 'http://www.UnnamedEdit.com',
                            'primaryEmail'                      => array('emailAddress' => 'info@myEditProject.com',
                                                                         'optOut' => '0',
                                                                         'isInvalid' => '0'),
                            'secondaryEmail'                    => array('emailAddress' => '',
                                                                         'optOut' => '0',
                                                                         'isInvalid' => '0'),
                            'billingAddress'                    => array('street1' => '26378 South Arlington Ave',
                                                                         'street2' => '',
                                                                         'city' => 'San Jose',
                                                                         'state' => 'CA',
                                                                         'postalCode' => '95131',
                                                                         'country' => 'USA'),
                            'shippingAddress'                   => array('street1' => '8519 East Franklin Center',
                                                                         'street2' => '',
                                                                         'city' => 'Chicago',
                                                                         'state' => 'IL',
                                                                         'postalCode' => '60652',
                                                                         'country' => 'USA'),
                            'description'                       => 'This is a Edit Description',
                            'explicitReadWriteModelPermissions' => array('type' => $explicitReadWriteModelPermission),
                            'dateCstm'                          => $date,
                            'datetimeCstm'                      => $datetime,
                            'checkboxCstm'                      => '0',
                            'currencyCstm'                      => array('value'   => 40,
                                                                          'currency' => array(
                                                                          'id' => $baseCurrency->id)),
                            'decimalCstm'                       => '12',
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
                            'urlCstm'                           => 'http://wwww.abc-edit.com'),
                            'save' => 'Save'));
            $this->runControllerWithRedirectExceptionAndGetUrl('projects/default/edit');

            //Check the details if they are saved properly for the custom fields after the edit.
            $project = Project::getByName('myEditProject');

            //Retrieve the permission of the project
            $explicitReadWriteModelPermissions = ExplicitReadWriteModelPermissionsUtil::
                                                 makeBySecurableItem(Project::getById($project[0]->id));
            $readWritePermitables = $explicitReadWriteModelPermissions->getReadWritePermitables();
            $readOnlyPermitables  = $explicitReadWriteModelPermissions->getReadOnlyPermitables();
            $this->assertEquals(1, count($project));
            $this->assertEquals($project[0]->name                           , 'myEditProject');
            $this->assertEquals($project[0]->officePhone                    , '259-734-2169');
            $this->assertEquals($project[0]->industry->value                , 'Energy');
            $this->assertEquals($project[0]->officeFax                      , '299-825-7863');
            $this->assertEquals($project[0]->employees                      , '630');
            $this->assertEquals($project[0]->annualRevenue                  , '472000000');
            $this->assertEquals($project[0]->type->value                    , 'Customer');
            $this->assertEquals($project[0]->website                        , 'http://www.UnnamedEdit.com');
            $this->assertEquals($project[0]->primaryEmail->emailAddress     , 'info@myEditProject.com');
            $this->assertEquals($project[0]->primaryEmail->optOut           , '0');
            $this->assertEquals($project[0]->primaryEmail->isInvalid        , '0');
            $this->assertEquals($project[0]->secondaryEmail->emailAddress   , '');
            $this->assertEquals($project[0]->secondaryEmail->optOut         , '0');
            $this->assertEquals($project[0]->secondaryEmail->isInvalid      , '0');
            $this->assertEquals($project[0]->billingAddress->street1        , '26378 South Arlington Ave');
            $this->assertEquals($project[0]->billingAddress->street2        , '');
            $this->assertEquals($project[0]->billingAddress->city           , 'San Jose');
            $this->assertEquals($project[0]->billingAddress->state          , 'CA');
            $this->assertEquals($project[0]->billingAddress->postalCode     , '95131');
            $this->assertEquals($project[0]->billingAddress->country        , 'USA');
            $this->assertEquals($project[0]->shippingAddress->street1       , '8519 East Franklin Center');
            $this->assertEquals($project[0]->shippingAddress->street2       , '');
            $this->assertEquals($project[0]->shippingAddress->city          , 'Chicago');
            $this->assertEquals($project[0]->shippingAddress->state         , 'IL');
            $this->assertEquals($project[0]->shippingAddress->postalCode    , '60652');
            $this->assertEquals($project[0]->shippingAddress->country       , 'USA');
            $this->assertEquals($project[0]->description                    , 'This is a Edit Description');
            $this->assertEquals(1                                           , count($readWritePermitables));
            $this->assertEquals(0                                           , count($readOnlyPermitables));
            $this->assertEquals($project[0]->checkboxCstm                   , '0');
            $this->assertEquals($project[0]->currencyCstm->value            ,  40);
            $this->assertEquals($project[0]->currencyCstm->currency->id     , $baseCurrency->id);
            $this->assertEquals($project[0]->dateCstm                       , $dateAssert);
            $this->assertEquals($project[0]->datetimeCstm                   , $datetimeAssert);
            $this->assertEquals($project[0]->decimalCstm                    , '12');
            $this->assertEquals($project[0]->picklistCstm->value            , 'b');
            $this->assertEquals($project[0]->integerCstm                    ,  11);
            $this->assertEquals($project[0]->phoneCstm                      , '259-784-2069');
            $this->assertEquals($project[0]->radioCstm->value               , 'e');
            $this->assertEquals($project[0]->textCstm                       , 'This is a test Edit Text');
            $this->assertEquals($project[0]->textareaCstm                   , 'This is a test Edit TextArea');
            $this->assertEquals($project[0]->urlCstm                        , 'http://wwww.abc-edit.com');
            $this->assertEquals($project[0]->countrylistCstm->value         , 'aaaa');
            $this->assertEquals($project[0]->statelistCstm->value           , 'aaa1');
            $this->assertEquals($project[0]->citylistCstm->value            , 'ab1');
            $this->assertContains('gg'                                      , $project[0]->multiselectCstm->values);
            $this->assertContains('hh'                                      , $project[0]->multiselectCstm->values);
            $this->assertEquals(0                                           , $project[0]->tagcloudCstm->values->count());

            $metadata            = CalculatedDerivedAttributeMetadata::
                                   getByNameAndModelClassName('calcnumber', 'Project');
            $testCalculatedValue = CalculatedNumberUtil::calculateByFormulaAndModel($metadata->getFormula(), $project[0]);
            $this->assertEquals(472000630                                   , $testCalculatedValue);
        }

        /**
         * @depends testEditOfTheProjectUserForTheTagCloudFieldAfterRemovingAllTagsPlacedForProjectsModule
         */
        public function testEditOfTheProjectUserForTheCustomFieldsPlacedForProjectsModule()
        {
            $super          = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Set the date and datetime variable values here.
            $date           = Yii::app()->dateFormatter->format(DateTimeUtil::getLocaleDateFormat(), time());
            $dateAssert     = date('Y-m-d');
            $datetime       = Yii::app()->dateFormatter->format(DateTimeUtil::getLocaleDateTimeFormat(), time());
            $datetimeAssert = date('Y-m-d H:i:')."00";
            $baseCurrency   = Currency::getByCode(Yii::app()->currencyHelper->getBaseCode());
            $explicitReadWriteModelPermission = ExplicitReadWriteModelPermissionsUtil::MIXED_TYPE_EVERYONE_GROUP;

            //Get the project id from the recently created project.
            $project        = Project::getByName('myEditProject');
            $projectId      = $project[0]->id;

            //Edit and save the project.
            $this->setGetArray(array('id' => $projectId));
            $this->setPostArray(array('Project' => array(
                            'name'                              => 'myEditProject',
                            'officePhone'                       => '259-734-2169',
                            'industry'                          => array('value' => 'Energy'),
                            'officeFax'                         => '299-825-7863',
                            'employees'                         => '630',
                            'annualRevenue'                     => '472000000',
                            'type'                              => array('value' => 'Customer'),
                            'website'                           => 'http://www.UnnamedEdit.com',
                            'primaryEmail'                      => array('emailAddress' => 'info@myEditProject.com',
                                                                         'optOut' => '0',
                                                                         'isInvalid' => '0'),
                            'secondaryEmail'                    => array('emailAddress' => '',
                                                                         'optOut' => '0',
                                                                         'isInvalid' => '0'),
                            'billingAddress'                    => array('street1' => '26378 South Arlington Ave',
                                                                         'street2' => '',
                                                                         'city' => 'San Jose',
                                                                         'state' => 'CA',
                                                                         'postalCode' => '95131',
                                                                         'country' => 'USA'),
                            'shippingAddress'                   => array('street1' => '8519 East Franklin Center',
                                                                         'street2' => '',
                                                                         'city' => 'Chicago',
                                                                         'state' => 'IL',
                                                                         'postalCode' => '60652',
                                                                         'country' => 'USA'),
                            'description'                       => 'This is a Edit Description',
                            'explicitReadWriteModelPermissions' => array('type' => $explicitReadWriteModelPermission),
                            'dateCstm'                          => $date,
                            'datetimeCstm'                      => $datetime,
                            'checkboxCstm'                      => '0',
                            'currencyCstm'                      => array('value'   => 40,
                                                                          'currency' => array(
                                                                          'id' => $baseCurrency->id)),
                            'decimalCstm'                       => '12',
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
                            'urlCstm'                           => 'http://wwww.abc-edit.com'),
                            'save' => 'Save'));
            $this->runControllerWithRedirectExceptionAndGetUrl('projects/default/edit');

            //Check the details if they are saved properly for the custom fields after the edit.
            $project = Project::getByName('myEditProject');

            //Retrieve the permission of the project
            $explicitReadWriteModelPermissions = ExplicitReadWriteModelPermissionsUtil::
                                                 makeBySecurableItem(Project::getById($project[0]->id));
            $readWritePermitables = $explicitReadWriteModelPermissions->getReadWritePermitables();
            $readOnlyPermitables  = $explicitReadWriteModelPermissions->getReadOnlyPermitables();
            $this->assertEquals(1, count($project));
            $this->assertEquals($project[0]->name                           , 'myEditProject');
            $this->assertEquals($project[0]->officePhone                    , '259-734-2169');
            $this->assertEquals($project[0]->industry->value                , 'Energy');
            $this->assertEquals($project[0]->officeFax                      , '299-825-7863');
            $this->assertEquals($project[0]->employees                      , '630');
            $this->assertEquals($project[0]->annualRevenue                  , '472000000');
            $this->assertEquals($project[0]->type->value                    , 'Customer');
            $this->assertEquals($project[0]->website                        , 'http://www.UnnamedEdit.com');
            $this->assertEquals($project[0]->primaryEmail->emailAddress     , 'info@myEditProject.com');
            $this->assertEquals($project[0]->primaryEmail->optOut           , '0');
            $this->assertEquals($project[0]->primaryEmail->isInvalid        , '0');
            $this->assertEquals($project[0]->secondaryEmail->emailAddress   , '');
            $this->assertEquals($project[0]->secondaryEmail->optOut         , '0');
            $this->assertEquals($project[0]->secondaryEmail->isInvalid      , '0');
            $this->assertEquals($project[0]->billingAddress->street1        , '26378 South Arlington Ave');
            $this->assertEquals($project[0]->billingAddress->street2        , '');
            $this->assertEquals($project[0]->billingAddress->city           , 'San Jose');
            $this->assertEquals($project[0]->billingAddress->state          , 'CA');
            $this->assertEquals($project[0]->billingAddress->postalCode     , '95131');
            $this->assertEquals($project[0]->billingAddress->country        , 'USA');
            $this->assertEquals($project[0]->shippingAddress->street1       , '8519 East Franklin Center');
            $this->assertEquals($project[0]->shippingAddress->street2       , '');
            $this->assertEquals($project[0]->shippingAddress->city          , 'Chicago');
            $this->assertEquals($project[0]->shippingAddress->state         , 'IL');
            $this->assertEquals($project[0]->shippingAddress->postalCode    , '60652');
            $this->assertEquals($project[0]->shippingAddress->country       , 'USA');
            $this->assertEquals($project[0]->description                    , 'This is a Edit Description');
            $this->assertEquals(1                                           , count($readWritePermitables));
            $this->assertEquals(0                                           , count($readOnlyPermitables));
            $this->assertEquals($project[0]->checkboxCstm                   , '0');
            $this->assertEquals($project[0]->currencyCstm->value            ,  40);
            $this->assertEquals($project[0]->currencyCstm->currency->id     , $baseCurrency->id);
            $this->assertEquals($project[0]->dateCstm                       , $dateAssert);
            $this->assertEquals($project[0]->datetimeCstm                   , $datetimeAssert);
            $this->assertEquals($project[0]->decimalCstm                    , '12');
            $this->assertEquals($project[0]->picklistCstm->value            , 'b');
            $this->assertEquals($project[0]->integerCstm                    ,  11);
            $this->assertEquals($project[0]->phoneCstm                      , '259-784-2069');
            $this->assertEquals($project[0]->radioCstm->value               , 'e');
            $this->assertEquals($project[0]->textCstm                       , 'This is a test Edit Text');
            $this->assertEquals($project[0]->textareaCstm                   , 'This is a test Edit TextArea');
            $this->assertEquals($project[0]->urlCstm                        , 'http://wwww.abc-edit.com');
            $this->assertEquals($project[0]->countrylistCstm->value         , 'aaaa');
            $this->assertEquals($project[0]->statelistCstm->value           , 'aaa1');
            $this->assertEquals($project[0]->citylistCstm->value            , 'ab1');
            $this->assertContains('gg'                                      , $project[0]->multiselectCstm->values);
            $this->assertContains('hh'                                      , $project[0]->multiselectCstm->values);
            $this->assertContains('reading'                                 , $project[0]->tagcloudCstm->values);
            $this->assertContains('surfing'                                 , $project[0]->tagcloudCstm->values);

            $metadata            = CalculatedDerivedAttributeMetadata::
                                   getByNameAndModelClassName('calcnumber', 'Project');
            $testCalculatedValue = CalculatedNumberUtil::calculateByFormulaAndModel($metadata->getFormula(), $project[0]);
            $this->assertEquals(472000630                                   , $testCalculatedValue);
        }

        /**
         * @depends testEditOfTheProjectUserForTheCustomFieldsPlacedForProjectsModule
         */
        public function testWhetherSearchWorksForTheCustomFieldsPlacedForProjectsModuleAfterEditingTheProjectUser()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Search a created project using the customfield.
            $this->resetPostArray();
            $this->setGetArray(array(
                        'ProjectsSearchForm' => ProjectsDesignerWalkthroughHelperUtil::fetchProjectsSearchFormGetData(),
                        'ajax'               => 'list-view')
            );
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default');

            //Assert that the edit project exits after the edit and is diaplayed on the search page.
            //$this->assertTrue(strpos($content, "Displaying 1-1 of 1 result(s).") > 0); //removed until we show the count again in the listview.
            $this->assertTrue(strpos($content, "myEditProject") > 0);
        }

        /**
         * @depends testWhetherSearchWorksForTheCustomFieldsPlacedForProjectsModuleAfterEditingTheProjectUser
         */
        public function testWhetherSearchWorksForTheCustomFieldsPlacedForProjectsModuleWithMultiSelectValueSetToNull()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Search a created project using the customfield.
            $this->resetPostArray();
            $this->setGetArray(array(
                        'ProjectsSearchForm' => ProjectsDesignerWalkthroughHelperUtil::fetchProjectsSearchFormGetDataWithMultiSelectValueSetToNull(),
                        'ajax'               => 'list-view')
            );
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default');

            //Assert that the edit project exits after the edit and is diaplayed on the search page.
            //$this->assertTrue(strpos($content, "Displaying 1-1 of 1 result(s).") > 0); //removed until we show the count again in the listview.
            $this->assertTrue(strpos($content, "myEditProject") > 0);
        }

        /**
         * @depends testWhetherSearchWorksForTheCustomFieldsPlacedForProjectsModuleWithMultiSelectValueSetToNull
         */
        public function testCreateSecondProjectForUserAfterTheCustomFieldsArePlacedForProjectsModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Set the date and datetime variable values here
            $date           = Yii::app()->dateFormatter->format(DateTimeUtil::getLocaleDateFormat(), time());
            $dateAssert     = date('Y-m-d');
            $datetime       = Yii::app()->dateFormatter->format(DateTimeUtil::getLocaleDateTimeFormat(), time());
            $datetimeAssert = date('Y-m-d H:i:')."00";
            $baseCurrency   = Currency::getByCode(Yii::app()->currencyHelper->getBaseCode());

            //Create a new project based on the custom fields.
            $this->resetGetArray();
            $this->setPostArray(array('Project' => array(
                                    'name'                              => 'mySecondProject',
                                    'officePhone'                       => '259-784-2169',
                                    'industry'                          => array('value' => 'Automotive'),
                                    'officeFax'                         => '299-845-7863',
                                    'employees'                         => '930',
                                    'annualRevenue'                     => '474000000',
                                    'type'                              => array('value' => 'Prospect'),
                                    'website'                           => 'http://www.Unnamed.com',
                                    'primaryEmail'                      => array('emailAddress' => 'info@myNewProject.com',
                                                                                  'optOut' => '1',
                                                                                  'isInvalid' => '0'),
                                    'secondaryEmail'                    => array('emailAddress' => '',
                                                                                  'optOut' => '0',
                                                                                  'isInvalid' => '0'),
                                    'billingAddress'                    => array('street1' => '6466 South Madison Creek',
                                                                                  'street2' => '',
                                                                                  'city' => 'Chicago',
                                                                                  'state' => 'IL',
                                                                                  'postalCode' => '60652',
                                                                                  'country' => 'USA'),
                                    'shippingAddress'                   => array('street1' => '27054 West Michigan Lane',
                                                                                  'street2' => '',
                                                                                  'city' => 'Austin',
                                                                                  'state' => 'TX',
                                                                                  'postalCode' => '78759',
                                                                                  'country' => 'USA'),
                                    'description'                       => 'This is a Description',
                                    'explicitReadWriteModelPermissions' => array('type' => null),
                                    'checkboxCstm'                      => '1',
                                    'currencyCstm'                      => array('value'    => 45,
                                                                                 'currency' => array('id' =>
                                                                                 $baseCurrency->id)),
                                    'dateCstm'                          => $date,
                                    'datetimeCstm'                      => $datetime,
                                    'decimalCstm'                       => '123',
                                    'picklistCstm'                      => array('value'  => 'a'),
                                    'multiselectCstm'                   => array('values' => array('gg', 'ff')),
                                    'tagcloudCstm'                      => array('values' => array('reading', 'writing')),
                                    'countrylistCstm'                   => array('value'  => 'bbbb'),
                                    'statelistCstm'                     => array('value'  => 'bbb1'),
                                    'citylistCstm'                      => array('value'  => 'bb1'),
                                    'integerCstm'                       => '12',
                                    'phoneCstm'                         => '259-784-2169',
                                    'radioCstm'                         => array('value' => 'd'),
                                    'textCstm'                          => 'This is a test Text',
                                    'textareaCstm'                      => 'This is a test TextArea',
                                    'urlCstm'                           => 'http://wwww.abc.com')));
            $this->runControllerWithRedirectExceptionAndGetUrl('projects/default/create');

            //Check the details if they are saved properly for the custom fields.
            $project = Project::getByName('mySecondProject');
            //Retrieve the permission for the project.
            $explicitReadWriteModelPermissions = ExplicitReadWriteModelPermissionsUtil::
                                                 makeBySecurableItem(Project::getById($project[0]->id));
            $readWritePermitables = $explicitReadWriteModelPermissions->getReadWritePermitables();
            $readOnlyPermitables  = $explicitReadWriteModelPermissions->getReadOnlyPermitables();
            $this->assertEquals(1, count($project));
            $this->assertEquals($project[0]->name                           , 'mySecondProject');
            $this->assertEquals($project[0]->officePhone                    , '259-784-2169');
            $this->assertEquals($project[0]->industry->value                , 'Automotive');
            $this->assertEquals($project[0]->officeFax                      , '299-845-7863');
            $this->assertEquals($project[0]->employees                      , '930');
            $this->assertEquals($project[0]->annualRevenue                  , '474000000');
            $this->assertEquals($project[0]->type->value                    , 'Prospect');
            $this->assertEquals($project[0]->website                        , 'http://www.Unnamed.com');
            $this->assertEquals($project[0]->primaryEmail->emailAddress     , 'info@myNewProject.com');
            $this->assertEquals($project[0]->primaryEmail->optOut           , '1');
            $this->assertEquals($project[0]->primaryEmail->isInvalid        , '0');
            $this->assertEquals($project[0]->secondaryEmail->emailAddress   , '');
            $this->assertEquals($project[0]->secondaryEmail->optOut         , '0');
            $this->assertEquals($project[0]->secondaryEmail->isInvalid      , '0');
            $this->assertEquals($project[0]->billingAddress->street1        , '6466 South Madison Creek');
            $this->assertEquals($project[0]->billingAddress->street2        , '');
            $this->assertEquals($project[0]->billingAddress->city           , 'Chicago');
            $this->assertEquals($project[0]->billingAddress->state          , 'IL');
            $this->assertEquals($project[0]->billingAddress->postalCode     , '60652');
            $this->assertEquals($project[0]->billingAddress->country        , 'USA');
            $this->assertEquals($project[0]->shippingAddress->street1       , '27054 West Michigan Lane');
            $this->assertEquals($project[0]->shippingAddress->street2       , '');
            $this->assertEquals($project[0]->shippingAddress->city          , 'Austin');
            $this->assertEquals($project[0]->shippingAddress->state         , 'TX');
            $this->assertEquals($project[0]->shippingAddress->postalCode    , '78759');
            $this->assertEquals($project[0]->shippingAddress->country       , 'USA');
            $this->assertEquals($project[0]->description                    , 'This is a Description');
            $this->assertEquals(0                                           , count($readWritePermitables));
            $this->assertEquals(0                                           , count($readOnlyPermitables));
            $this->assertEquals($project[0]->checkboxCstm                   , '1');
            $this->assertEquals($project[0]->currencyCstm->value            , 45);
            $this->assertEquals($project[0]->currencyCstm->currency->id     , $baseCurrency->id);
            $this->assertEquals($project[0]->dateCstm                       , $dateAssert);
            $this->assertEquals($project[0]->datetimeCstm                   , $datetimeAssert);
            $this->assertEquals($project[0]->decimalCstm                    , '123');
            $this->assertEquals($project[0]->picklistCstm->value            , 'a');
            $this->assertEquals($project[0]->integerCstm                    , 12);
            $this->assertEquals($project[0]->phoneCstm                      , '259-784-2169');
            $this->assertEquals($project[0]->radioCstm->value               , 'd');
            $this->assertEquals($project[0]->textCstm                       , 'This is a test Text');
            $this->assertEquals($project[0]->textareaCstm                   , 'This is a test TextArea');
            $this->assertEquals($project[0]->urlCstm                        , 'http://wwww.abc.com');
            $this->assertEquals($project[0]->countrylistCstm->value         , 'bbbb');
            $this->assertEquals($project[0]->statelistCstm->value           , 'bbb1');
            $this->assertEquals($project[0]->citylistCstm->value            , 'bb1');
            $this->assertContains('gg'                                      , $project[0]->multiselectCstm->values);
            $this->assertContains('ff'                                      , $project[0]->multiselectCstm->values);
            $this->assertContains('reading'                                 , $project[0]->tagcloudCstm->values);
            $this->assertContains('writing'                                 , $project[0]->tagcloudCstm->values);
        }

        /**
         * @depends testCreateSecondProjectForUserAfterTheCustomFieldsArePlacedForProjectsModule
         */
        public function testMultiValueCustomFieldContentAfterCreateAndEditPlacedForProjectsModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Set the date and datetime variable values here
            $date           = Yii::app()->dateFormatter->format(DateTimeUtil::getLocaleDateFormat(), time());
            $dateAssert     = date('Y-m-d');
            $datetime       = Yii::app()->dateFormatter->format(DateTimeUtil::getLocaleDateTimeFormat(), time());
            $datetimeAssert = date('Y-m-d H:i:')."00";
            $baseCurrency   = Currency::getByCode(Yii::app()->currencyHelper->getBaseCode());

            //Create a new project based on the custom fields.
            $this->resetGetArray();
            $this->setPostArray(array('Project' => array(
                            'name'                              => 'myThirdProject',
                            'officePhone'                       => '259-784-2169',
                            'industry'                          => array('value' => 'Automotive'),
                            'officeFax'                         => '299-845-7863',
                            'employees'                         => '930',
                            'annualRevenue'                     => '474000000',
                            'type'                              => array('value' => 'Prospect'),
                            'website'                           => 'http://www.Unnamed.com',
                            'primaryEmail'                      => array('emailAddress' => 'info@myNewProject.com',
                                                                          'optOut' => '1',
                                                                          'isInvalid' => '0'),
                            'secondaryEmail'                    => array('emailAddress' => '',
                                                                          'optOut' => '0',
                                                                          'isInvalid' => '0'),
                            'billingAddress'                    => array('street1' => '6466 South Madison Creek',
                                                                          'street2' => '',
                                                                          'city' => 'Chicago',
                                                                          'state' => 'IL',
                                                                          'postalCode' => '60652',
                                                                          'country' => 'USA'),
                            'shippingAddress'                   => array('street1' => '27054 West Michigan Lane',
                                                                          'street2' => '',
                                                                          'city' => 'Austin',
                                                                          'state' => 'TX',
                                                                          'postalCode' => '78759',
                                                                          'country' => 'USA'),
                            'description'                       => 'This is a Description',
                            'explicitReadWriteModelPermissions' => array('type' => null),
                            'checkboxCstm'                      => '1',
                            'currencyCstm'                      => array('value'    => 45,
                                                                         'currency' => array('id' =>
                                                                         $baseCurrency->id)),
                            'dateCstm'                          => $date,
                            'datetimeCstm'                      => $datetime,
                            'decimalCstm'                       => '123',
                            'picklistCstm'                      => array('value'  => 'a'),
                            'multiselectCstm'                   => array('values' => array('gg', 'ff')),
                            'tagcloudCstm'                      => array('values' => array('reading', 'writing')),
                            'countrylistCstm'                   => array('value'  => 'bbbb'),
                            'statelistCstm'                     => array('value'  => 'bbb1'),
                            'citylistCstm'                      => array('value'  => 'bb1'),
                            'integerCstm'                       => '12',
                            'phoneCstm'                         => '259-784-2169',
                            'radioCstm'                         => array('value' => 'd'),
                            'textCstm'                          => 'This is a test Text',
                            'textareaCstm'                      => 'This is a test TextArea',
                            'urlCstm'                           => 'http://wwww.abc.com')));
            $this->runControllerWithRedirectExceptionAndGetUrl('projects/default/create');

            //Check the details if they are saved properly for the custom fields.
            $project = Project::getByName('myThirdProject');
            //Retrieve the permission for the project.
            $explicitReadWriteModelPermissions = ExplicitReadWriteModelPermissionsUtil::
                                                 makeBySecurableItem(Project::getById($project[0]->id));
            $readWritePermitables = $explicitReadWriteModelPermissions->getReadWritePermitables();
            $readOnlyPermitables  = $explicitReadWriteModelPermissions->getReadOnlyPermitables();
            $this->assertEquals(1, count($project));
            $this->assertEquals($project[0]->name               , 'myThirdProject');
            $this->assertEquals(2                               , $project[0]->multiselectCstm->values->count());
            $this->assertEquals(2                               , $project[0]->tagcloudCstm->values->count());
            $this->assertContains('gg'                          , $project[0]->multiselectCstm->values);
            $this->assertContains('ff'                          , $project[0]->multiselectCstm->values);
            $this->assertContains('reading'                     , $project[0]->tagcloudCstm->values);
            $this->assertContains('writing'                     , $project[0]->tagcloudCstm->values);
            unset($project);

            $project        = Project::getByName('myThirdProject');
            $projectId      = $project[0]->id;
            //Edit and save the project.
            $this->setGetArray(array('id' => $projectId));
            $this->setPostArray(array('Project' => array(
                            'name'                              => 'myThirdProject',
                            'multiselectCstm'                       => array('values' =>  array('ff')),
                            'tagcloudCstm'                          => array('values' =>  array('writing')),
                            ),
                            'save' => 'Save'));
            $this->runControllerWithRedirectExceptionAndGetUrl('projects/default/edit');

            //Check the details if they are saved properly for the custom fields.
            $project = Project::getByName('myThirdProject');
            //Retrieve the permission for the project.
            $explicitReadWriteModelPermissions = ExplicitReadWriteModelPermissionsUtil::
                                                 makeBySecurableItem(Project::getById($project[0]->id));
            $readWritePermitables = $explicitReadWriteModelPermissions->getReadWritePermitables();
            $readOnlyPermitables  = $explicitReadWriteModelPermissions->getReadOnlyPermitables();
            $this->assertEquals(1, count($project));
            $this->assertEquals(1                               , $project[0]->multiselectCstm->values->count());
            $this->assertContains('ff'                          , $project[0]->multiselectCstm->values);
            $this->assertNotContains('gg'                       , $project[0]->multiselectCstm->values);
            $this->assertNotContains('hh'                       , $project[0]->multiselectCstm->values);
            $this->assertNotContains('rr'                       , $project[0]->multiselectCstm->values);

            $this->assertEquals(1                               , $project[0]->tagcloudCstm->values->count());
            $this->assertContains('writing'                     , $project[0]->tagcloudCstm->values);
        }

        /**
         * @depends testMultiValueCustomFieldContentAfterCreateAndEditPlacedForProjectsModule
         */
        public function testMassUpdateForMultiSelectFieldPlacedForProjectsModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            $project = Project::getByName('myEditProject');
            $this->assertEquals(1, count($project));
            $this->assertEquals($project[0]->name, 'myEditProject');
            $this->assertContains('gg'           , $project[0]->multiselectCstm->values);
            $this->assertContains('hh'           , $project[0]->multiselectCstm->values);
            unset($project);

            $secondProject = Project::getByName('mySecondProject');
            $this->assertEquals(1, count($secondProject));
            $this->assertEquals($secondProject[0]->name, 'mySecondProject');
            $this->assertContains('gg'           , $secondProject[0]->multiselectCstm->values);
            $this->assertContains('ff'           , $secondProject[0]->multiselectCstm->values);
            unset($secondProject);

            $this->resetPostArray();
            $this->setGetArray(array('selectAll' => '1', 'Project_page' => '1', 'selectedIds' => null, 'ajax' => null));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/massEdit');

            $this->setPostArray(array('save'     => 'Save',
                                      'MassEdit' => array('multiselectCstm' => '1'),
                                      'Project'  => array('multiselectCstm' => array('values' => array('ff', 'rr')))
                                     )
                               );
            $this->runControllerWithRedirectExceptionAndGetContent('projects/default/massEdit');

            $project = Project::getByName('myEditProject');
            $this->assertEquals(1, count($project));
            $this->assertEquals($project[0]->name, 'myEditProject');
            $this->assertContains('ff'           , $project[0]->multiselectCstm->values);
            $this->assertContains('rr'           , $project[0]->multiselectCstm->values);

            $secondProject = Project::getByName('mySecondProject');
            $this->assertEquals(1, count($secondProject));
            $this->assertEquals($secondProject[0]->name, 'mySecondProject');
            $this->assertContains('ff'           , $secondProject[0]->multiselectCstm->values);
            $this->assertContains('rr'           , $secondProject[0]->multiselectCstm->values);
        }

        /**
         * @depends testMassUpdateForMultiSelectFieldPlacedForProjectsModule
         */
        public function testMassUpdateForTagCloudFieldPlacedForProjectsModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            $project = Project::getByName('myEditProject');
            $this->assertEquals(1, count($project));
            $this->assertEquals($project[0]->name, 'myEditProject');
            $this->assertContains('reading'      , $project[0]->tagcloudCstm->values);
            $this->assertContains('surfing'      , $project[0]->tagcloudCstm->values);
            unset($project);

            $secondProject = Project::getByName('mySecondProject');
            $this->assertEquals(1, count($secondProject));
            $this->assertEquals($secondProject[0]->name, 'mySecondProject');
            $this->assertContains('reading'      , $secondProject[0]->tagcloudCstm->values);
            $this->assertContains('writing'      , $secondProject[0]->tagcloudCstm->values);
            unset($secondProject);

            $this->resetPostArray();
            $this->setGetArray(array('selectAll' => '1', 'Project_page' => '1', 'selectedIds' => null, 'ajax' => null));
            $this->runControllerWithNoExceptionsAndGetContent('projects/default/massEdit');

            $this->setPostArray(array('save'     => 'Save',
                                      'MassEdit' => array('tagcloudCstm' => '1'),
                                      'Project'  => array('tagcloudCstm' => array('values' => array('writing', 'gardening')))
                                     )
                               );
            $this->runControllerWithRedirectExceptionAndGetContent('projects/default/massEdit');

            $project = Project::getByName('myEditProject');
            $this->assertEquals(1, count($project));
            $this->assertEquals($project[0]->name, 'myEditProject');
            $this->assertContains('writing'      , $project[0]->tagcloudCstm->values);
            $this->assertContains('gardening'    , $project[0]->tagcloudCstm->values);

            $secondProject = Project::getByName('mySecondProject');
            $this->assertEquals(1, count($secondProject));
            $this->assertEquals($secondProject[0]->name, 'mySecondProject');
            $this->assertContains('writing'      , $secondProject[0]->tagcloudCstm->values);
            $this->assertContains('gardening'    , $secondProject[0]->tagcloudCstm->values);
        }

        /**
         * @depends testMassUpdateForTagCloudFieldPlacedForProjectsModule
         */
        public function testDeleteOfTheProjectUserForTheCustomFieldsPlacedForProjectsModule()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Get the project id from the recently edited project.
            $projectId = self::getModelIdByModelNameAndName('Project', 'myEditProject');

            //Set the project id so as to delete the project.
            $this->setGetArray(array('id' => $projectId));
            $this->runControllerWithRedirectExceptionAndGetUrl('projects/default/delete');

            //Check whether the project is deleted.
            $project = Project::getByName('myEditProject');
            $this->assertEquals(0, count($project));
        }

        /**
         * @depends testDeleteOfTheProjectUserForTheCustomFieldsPlacedForProjectsModule
         */
        public function testWhetherSearchWorksForTheCustomFieldsPlacedForProjectsModuleAfterDeletingTheProject()
        {
            $super = $this->logoutCurrentUserLoginNewUserAndGetByUsername('super');

            //Search a created project using the customfield.
            $this->resetGetArray();
            $this->setGetArray(array(
                        'ProjectsSearchForm' => ProjectsDesignerWalkthroughHelperUtil::fetchProjectsSearchFormGetData(),
                        'ajax'               => 'list-view')
            );
            $content = $this->runControllerWithNoExceptionsAndGetContent('projects/default');

            //Assert that the edit project does not exits after the search.
            $this->assertTrue(strpos($content, "No results found.") > 0);
            $this->assertFalse(strpos($content, "26378 South Arlington Ave") > 0);
        }

        /**
         * @depends testWhetherSearchWorksForTheCustomFieldsPlacedForProjectsModuleAfterDeletingTheProject
         */
        public function testTypeAheadWorksForTheTagCloudFieldPlacedForProjectsModule()
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
         * @depends testTypeAheadWorksForTheTagCloudFieldPlacedForProjectsModule
         */
        public function testLabelLocalizationForTheTagCloudFieldPlacedForProjectsModule()
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