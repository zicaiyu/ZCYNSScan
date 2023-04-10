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
     * Build a data set showing projectactivities grouped by the source summed by the amount.
     */
    class ProjectactivitiesBySourceChartDataProvider extends ChartDataProvider
    {
        public function __construct()
        {
            $this->model = new Projectactivity(false);
        }

        public function getXAxisName()
        {
            return $this->model->getAttributeLabel('source');
        }

        public function getYAxisName()
        {
            return $this->model->getAttributeLabel('amount');
        }

        public function getChartData()
        {
            $customFieldData = CustomFieldDataModelUtil::
                               getDataByModelClassNameAndAttributeName('Projectactivity', 'source');
            $labels          = CustomFieldDataUtil::
                               getDataIndexedByDataAndTranslatedLabelsByLanguage($customFieldData, Yii::app()->language);
            $sql             = static::makeChartSqlQuery();
            $rows            = R::getAll($sql);
            $chartData       = array();
            foreach ($rows as $row)
            {
                $chartData[] = array(
                    'value'        => $utf8_text = $this->resolveCurrencyValueConversionRateForCurrentUserForDisplay($row['amount']),
                    'displayLabel' => static::resolveLabelByValueAndLabels($row['source'], $labels),
                );
            }
            return $chartData;
        }

        protected static function makeChartSqlQuery()
        {
            $quote                     = DatabaseCompatibilityUtil::getQuote();
            $where                     = null;
            $selectDistinct            = false;
            $joinTablesAdapter         = new RedBeanModelJoinTablesQueryAdapter('Projectactivity');
            Projectactivity::resolveReadPermissionsOptimizationToSqlQuery(Yii::app()->user->userModel,
                                                                      $joinTablesAdapter,
                                                                      $where,
                                                                      $selectDistinct);
            $selectQueryAdapter        = new RedBeanModelSelectQueryAdapter($selectDistinct);
            $sumPart                   = "{$quote}currencyvalue{$quote}.{$quote}value{$quote} ";
            $sumPart                  .= "* {$quote}currencyvalue{$quote}.{$quote}ratetobase{$quote}";
            $selectQueryAdapter->addClause('customfield', 'value', 'source');
            $selectQueryAdapter->addSummationClause($sumPart, 'amount');
            $joinTablesAdapter->addFromTableAndGetAliasName('customfield', 'source_customfield_id', 'projectactivity');
            $joinTablesAdapter->addFromTableAndGetAliasName('currencyvalue', 'amount_currencyvalue_id', 'projectactivity');
            $groupBy                   = "{$quote}customfield{$quote}.{$quote}value{$quote}";
            $sql                       = SQLQueryUtil::makeQuery('projectactivity', $selectQueryAdapter,
                                                                 $joinTablesAdapter, null, null, $where, null, $groupBy);
            return $sql;
        }
    }
?>