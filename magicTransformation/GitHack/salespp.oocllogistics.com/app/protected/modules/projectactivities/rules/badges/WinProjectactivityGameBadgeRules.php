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
     * Class for defining the badge associated with winning projectactivities
     */
    class WinProjectactivityGameBadgeRules extends GameBadgeRules
    {
        public static $valuesIndexedByGrade = array(
            1  => 1,
            2  => 3,
            3  => 5,
            4  => 10,
            5  => 20,
            6  => 30,
            7  => 40,
            8  => 50,
            9  => 60,
            10 => 70,
            11 => 80,
            12 => 90,
            13 => 100
        );

        public static function getPassiveDisplayLabel($value)
        {
            return Zurmo::t('ProjectactivitiesModule', '{n} ProjectactivitiesModuleSingularLabel won|{n} ProjectactivitiesModulePluralLabel won',
                          array_merge(array($value), LabelUtil::getTranslationParamsForAllModules()));
        }

        public static function badgeGradeUserShouldHaveByPointsAndScores($userPointsByType, $userScoresByType)
        {
            assert('is_array($userPointsByType)');
            assert('is_array($userScoresByType)');
            if (isset($userScoresByType[ProjectactivityGamificationRules::SCORE_TYPE_WIN_PROJECTACTIVITY]))
            {
                return static::getBadgeGradeByValue((int)$userScoresByType[ProjectactivityGamificationRules::SCORE_TYPE_WIN_PROJECTACTIVITY]->value);
            }
            return 0;
        }
    }
?>