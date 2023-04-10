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

    class SensitiveinfoEditAndDetailsView extends SecuredEditAndDetailsView
    {
        public static function getDefaultMetadata()
        {
            $metadata = array(
                'global' => array(
                    'toolbar' => array(
                        'elements' => array(
                            array('type' => 'CancelLink', 'renderType' => 'Edit'),
                            array('type' => 'SaveButton', 'renderType' => 'Edit'),
                            array('type' => 'EditLink', 'renderType' => 'Details'),
                            array('type' => 'AuditEventsModalListLink', 'renderType' => 'Details'),
                            array('type' => 'SensitiveinfoDeleteLink', 'renderType' => 'Details'),
                        ),
                    ),
                    'derivedAttributeTypes' => array('Files'
                    ),
                    'nonPlaceableAttributeNames' => array(
                        'owner',
                    ),
                    'panelsDisplayType' => FormLayout::PANELS_DISPLAY_TYPE_ALL,
                    'panels' => array(
                        array(
                            'title'=>'General Information',
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
                                array('cells' =>
                                    array(
                                        array(
                                            'elements' => array(
                                                array('attributeName' => 'project', 'type' => 'Project'),
                                            ),
                                        ),
                                    )
                                ),
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
                                                array('attributeName' => 'remark', 'type' => 'TextArea'),
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

        protected function getNewModelTitleLabel()
        {
            return Zurmo::t('SensitiveinfosModule', 'Create SensitiveinfosModuleSingularLabel',
                                     LabelUtil::getTranslationParamsForAllModules());
        }
    }
?>
