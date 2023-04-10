<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 class SensitiveinfosForProjectRelatedListView extends SensitiveinfosRelatedListView
    {
        protected function getRelationAttributeName()
        {
            return 'project';
        }

        public static function getDisplayDescription()
        {
            return Zurmo::t('SensitiveinfosModule', 'SensitiveinfosModulePluralLabel For ProjectsModuleSingularLabel',
                        LabelUtil::getTranslationParamsForAllModules());
        }
    }
?>
