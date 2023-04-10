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

    class ProjectReadPermissionsOptimizationBaseTest extends ZurmoBaseTest
    {
        protected function rebuildAndTestThatTheMungeDoesntChange()
        {
            $beforeRows = R::getAll('select munge_id, securableitem_id, count from project_read order by munge_id, securableitem_id, count');
            ReadPermissionsOptimizationUtil::rebuild();
            $afterRows  = R::getAll('select munge_id, securableitem_id, count from project_read order by munge_id, securableitem_id, count');

            if ($beforeRows != $afterRows)
            {
                echo "Before and after rebuild munge doesn't match.\n";
                echo "--------\n";
                foreach ($beforeRows as $row)
                {
                    echo join(', ', array_values($row)) . "\n";
                }
                echo "--------\n";
                foreach ($afterRows as $row)
                {
                    echo join(', ', array_values($row)) . "\n";
                }
                echo "--------\n";
            }

            $this->assertEquals(count($beforeRows), count($afterRows));
            $this->assertEquals($beforeRows, $afterRows);
        }

        protected function nukeExistingProjects()
        {
            while (true)
            {
                $projects = Project::getSubset(0, 50); // Nuke 50 at a time to
                if (count($projects) == 0)             // avoid memory issues when
                {                                      // we get to the big numbers.
                    break;
                }
                foreach ($projects as $project)
                {
                    $project->delete();
                    unset($project);
                }
                unset($projects);
            }
            $this->assertEquals(0, Project::getCount());
        }

        protected function createProjects($count, $testRebuildAfterCreateProject = false, $firstProject = 1, $step = 1)
        {
            $this->assertTrue(is_bool($testRebuildAfterCreateProject));
            $this->assertTrue($firstProject <= $count);
            $this->assertTrue($step         <  $count);
            $betty = User::getByUsername('betty');
            $benny = User::getByUsername('benny');
            $salesStaff = Group::getByName('Sales Staff');
            $countThatBennyCanRead = 0;
            $projectIdsThatBennyCanRead = array();
            $startTime = microtime(true);
            for ($i = 0; $i < $count; $i++)
            {
                $bennyCanRead = false;
                $project = self::createRandomProject($i);
                $securableItemId = $i + 1;
                if ($i % 10 == 0)
                {
                    $project->owner = $betty;
                    $bennyCanRead = true; // Because he is betty's manager.
                }
                if ($i % 8 == 0)
                {
                    $project->addPermissions($benny,      Permission::READ);
                    $bennyCanRead = true; // Just coz.
                }
                if ($i % 12 == 0)
                {
                    $project->addPermissions($salesStaff, Permission::READ);
                    $bennyCanRead = true; // Benny is in Sales Staff.
                }

                $this->assertTrue($project->save());
                if ($bennyCanRead)
                {
                    $countThatBennyCanRead++;
                    if ($countThatBennyCanRead <= 10)
                    {
                        $projectIdsThatBennyCanRead[] = $project->id;
                    }
                }

                if ($i >= $firstProject - 1                 &&
                    ($i - ($firstProject - 1)) % $step == 0 &&
                    $testRebuildAfterCreateProject)
                {
                    $startTime = microtime(true);
                    ReadPermissionsOptimizationUtil::rebuild(true);
                    $endTime = microtime(true);
                    if ($this->isDebug())
                    {
                        echo 'Rebuilt the munge in php in ' . round($endTime - $startTime, 1) . ' seconds, ' . self::getProjectMungeRowCount() . " rows.\n";
                    }
                    $phpRows = self::getProjectMungeRows();

                    // If $securityOptimized is false in debug.php the second one will just do the php again.
                    $startTime = microtime(true);
                    ReadPermissionsOptimizationUtil::rebuild();
                    $endTime = microtime(true);
                    if ($this->isDebug())
                    {
                        echo 'Rebuilt the munge ' . (SECURITY_OPTIMIZED ? 'optimized' : 'in php') . ' in ' . round($endTime - $startTime, 1) . ' seconds, ' . self::getProjectMungeRowCount() . " rows.\n";
                    }
                    $otherRows = self::getProjectMungeRows();
                    if ($phpRows != $otherRows)
                    {
                        echo 'PHP & optimized munges don\'t match after project ' . ($i + 1) . "\n";
                        echo "--------\n";
                        foreach ($phpRows as $row)
                        {
                            echo join(', ', array_values($row)) . "\n";
                        }
                        echo "--------\n";
                        foreach ($otherRows as $row)
                        {
                            echo join(', ', array_values($row)) . "\n";
                        }
                        echo "--------\n";
                    }
                    $this->assertEquals(count($phpRows), count($otherRows));
                    $this->assertEquals($phpRows, $otherRows);
                }
            }
            $endTime = microtime(true);

            return array($endTime - $startTime, $countThatBennyCanRead, $projectIdsThatBennyCanRead);
        }

        protected static function createRandomProject($i)
        {
            $project = new Project();
            $project->name                       = "Project#$i";
            $project->officePhone                = rand(10000000, 90000000);
            $project->officeFax                  = rand(10000000, 90000000);
            $project->employees                  = rand(1, 100);
            $project->website                    = "http://www.project$i.com";
            $project->annualRevenue              = rand(10000, 10000000);
            $project->description                = "An project for some company called Project#$i.";
            $project->primaryEmail->emailAddress = "info@project$i.com";
            $project->primaryEmail->optOut       = false;
            $project->primaryEmail->isInvalid    = false;
            $project->billingAddress->street1    = "$i Some St";
            $project->billingAddress->city       = 'City';
            $project->billingAddress->state      = 'State';
            $project->billingAddress->postalCode = rand(1000, 9999);
            return $project;
        }

        protected static function projectMungeDoesntChangeWhenRebuilt()
        {
            //Need to forget all since sometimes the related information is cached from
            //before something occurred during a test.
            RedBeanModelsCache::forgetAll();
            RedBeansCache::forgetAll();
            $beforeRows = self::getProjectMungeRows();
            ReadPermissionsOptimizationUtil::rebuild();
            $afterRows  = self::getProjectMungeRows();

            if ($beforeRows != $afterRows)
            {
                echo "Before and after rebuild munge doesn't match.\n";
                self::echoRows($beforeRows);
                self::echoRows($afterRows);
            }

            return $beforeRows == $afterRows;
        }

        protected static function getProjectMungeRows(SecurableItem $securableItem = null)
        {
            if ($securableItem === null)
            {
                $rows = R::getAll('select   name, munge_id, count
                                   from     project_read, ownedsecurableitem, project
                                   where    project_read.securableitem_id = ownedsecurableitem.securableitem_id and
                                            ownedsecurableitem.id         = project.ownedsecurableitem_id
                                   order by name, munge_id, project_read.securableitem_id, count');
            }
            else
            {
                $securableItemId = $securableItem->getClassId('SecurableItem');
                $rows = R::getAll("select   munge_id, count
                                   from     project_read
                                   where    securableitem_id = $securableItemId
                                   order by munge_id, count");
            }
            $rowsWithValues = array();
            foreach ($rows as $row)
            {
                $row = array_values($row);
                array_walk($row, array('self', 'stripFullStops'));
                $rowsWithValues[] = $row;
            }
            return $rowsWithValues;
        }

        protected static function stripFullStops(&$value, $index)
        {
            // The names the projects have . to pad them out
            // to the minimum length they require, which is 3.
            // This is to make them appear in the debug output
            // as they do in the munge scenarios powerpoint
            // slides.
            $value = str_replace('.', '', $value);
        }

        protected static function echoProjectMungeRows(SecurableItem $securableItem = null)
        {
            self::echoRows(self::getProjectMungeRows($securableItem));
        }

        protected static function echoRows(array $rows)
        {
            echo "--------\n";
            foreach ($rows as $row)
            {
                echo join(', ', array_values($row)) . "\n";
            }
            echo "--------\n";
        }

        protected static function getProjectMungeRowCount()
        {
            return intval(R::getCell('select count(*) from project_read'));
        }
    }
?>
