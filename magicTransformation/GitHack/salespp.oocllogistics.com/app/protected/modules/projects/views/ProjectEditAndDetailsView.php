<?php
    /*********************************************************************************
     * Zurmo is a customer relationship management program developed by
     * Zurmo, Inc. Copyright (C) 2017 Zurmo Inc.
     *
     * Zurmo is free software; you can redistribute it and/or modify it under
     * the terms of the GNU Affero General Public License version 3 as published by the
     * Free Software Foundation with the addition of the following permission added
     * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
     * IN WHICH THE COPYRIGHT IS OWNED BY ZURMO, ZURMO DISCLAIMS THE WARRANTY
     * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
     *
     * Zurmo is distributed in the hope that it will be useful, but WITHOUT
     * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
     * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
     * details.
     *
     * You should have received a copy of the GNU Affero General Public License along with
     * this program; if not, see http://www.gnu.org/licenses or write to the Free
     * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
     * 02110-1301 USA.
     *
     * You can contact Zurmo, Inc. with a mailing address at 27 North Wacker Drive
     * Suite 370 Chicago, IL 60606. or at email address contact@zurmo.com.
     *
     * The interactive user interfaces in original and modified versions
     * of this program must display Appropriate Legal Notices, as required under
     * Section 5 of the GNU Affero General Public License version 3.
     *
     * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
     * these Appropriate Legal Notices must retain the display of the Zurmo
     * logo and Zurmo copyright notice. If the display of the logo is not reasonably
     * feasible for technical reasons, the Appropriate Legal Notices must display the words
     * "Copyright Zurmo Inc. 2017. All rights reserved".
     ********************************************************************************/
    /**
     * Project edit and details view
     */
    class ProjectEditAndDetailsView extends SecuredEditAndDetailsView
    {
        /**
         * @return array
         */
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'toolbar' => array(
                        'elements' => array(
                            array('type'  => 'CancelLink',        'renderType' => 'Edit'),
                            array('type'  => 'SaveButton',        'renderType' => 'Edit'),
                            array('type'  => 'EditLink',          'renderType' => 'Details'),
                            array('type'  => 'ProjectDeleteLink', 'renderType' => 'Details'),
                            array('type'  => 'CopyLink',          'renderType' => 'Details'),
                        ),
                    ),
                    'nonPlaceableAttributeNames' => array(
                    ),
                    'derivedAttributeTypes' => array(
                        'Files'
                    ),
                    'panelsDisplayType' => FormLayout::PANELS_DISPLAY_TYPE_ALL,
                    'panels' => array(
                        array(
                            'title' => 'Project',
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'account', 'type' => 'Account'),
                                            ),
                                        ),
                                    )
                                ),

                            /**
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName'   => 'null',
                                                      'type'            => 'MultipleAccountsForProjects',
                                                ),
                                            ),
                                        ),
                                    )
                                ),
                                */
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'name', 'type' => 'Text'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'description', 'type' => 'TextArea'),
                                            ),
                                        ),
                                    )
                                ),
                                 /**
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'project_status', 'type' => 'DropDown','addBlank' => true,),
                                            ),
                                        ),
                                    )
                                ),

                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName'   => 'null',
                                                      'type'            => 'MultipleContactsForProjects',
                                                ),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName'   => 'null',
                                                      'type'            => 'MultipleOpportunitiesForProjects',
                                                ),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'status', 'type' => 'ProjectStatusDropDown'),
                                            ),
                                        ),
                                    )
                                )
                                */

                            ),
                        ),
                        array(
                            'title' => 'Project Size',
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'teu', 'type' => 'Integer'),
                                            ),
                                        ),
                                    )

                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'ratio', 'type' => 'Integer'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'annual_in_cbm', 'type' => 'Integer'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'no_of_chb_entries', 'type' => 'Integer'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'no_of_truck_orders', 'type' => 'Integer'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'warehouse_storage', 'type' => 'Integer'),
                                            ),
                                        ),
                                    )
                                ),
                            ),
                            ),
                            array(

                            'title' => 'Project Details',
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'project_oll_potential_services', 'type' => 'MultiSelectDropDown','addBlank' => true,),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'project_detinations', 'type' => 'TagCloud','addBlank' => true,),
                                            ),
                                        ),
                                    )
                                ),
                               array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'project_involved_origins', 'type' => 'TagCloud','addBlank' => true,),
                                            ),
                                        ),
                                    )
                                ),
                               array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'estimated_revenue', 'type' => 'CurrencyValue'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'estimated_gross_contribution', 'type' => 'CurrencyValue'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'contact_duration', 'type' => 'Integer'),
                                            ),
                                        ),
                                    )
                                ),
                            ),
                            ),
                        array(
                            'title' => 'Timeline',
                            'rows' => array(
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'rfi_submission_date', 'type' => 'Date'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'tender_submission_date', 'type' => 'Date'),
                                            ),
                                        ),
                                    )
                                ),
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'proposal_submission_date', 'type' => 'Date'),
                                            ),
                                        ),
                                    )
                                ),
                            ),
                        ),
                        array(
                            'title' => 'Attachment',
                            'rows' => array(
                                array('cells' => array(
                                    array('elements' => array( array('attributeName' => 'null','type' => 'Files',), ), ),), ),
                            ),
                        ),
                    ),
                ),
            );
            return $metadata;
        }

        /**
         * @return string
         */
        protected function getNewModelTitleLabel()
        {
            return Zurmo::t('ProjectsModule', 'Create ProjectsModuleSingularLabel',
                                     LabelUtil::getTranslationParamsForAllModules());
        }

        /**
         * @param ZurmoActiveForm $form
         * @return string|void
         */
        protected function renderRightSideFormLayoutForEdit($form)
        {
            assert('$form instanceof ZurmoActiveForm');
            //$content = parent::renderRightSideFormLayoutForEdit($form);
            //return $content;
            if(Yii::app()->user->userModel->username == 'super'){
                return parent::renderRightSideFormLayoutForEdit($form);
            }
        }

        public static function getDesignerRulesType()
        {
            return 'DetailsViewOnlyForUserOwnerEditAndDetailsView';
        }
    }
?>