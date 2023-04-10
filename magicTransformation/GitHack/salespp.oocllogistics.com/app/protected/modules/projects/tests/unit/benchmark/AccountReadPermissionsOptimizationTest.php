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

    class ProjectReadPermissionsOptimizationTest extends ProjectReadPermissionsOptimizationBaseTest
    {
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();

            SecurityTestHelper::createSuperAdmin();
            Yii::app()->user->userModel = User::getByUsername('super');

            SecurityTestHelper::createUsers();
            SecurityTestHelper::createGroups();
            SecurityTestHelper::createProjects();
            SecurityTestHelper::createRoles();

            $everyone = Group::getByName('Everyone');
            $saved = $everyone->save();
            assert('$saved'); // Not Coding Standard

            //Add contacts to help test that the rebuild is working correctly
            $contact = ContactTestHelper::createContactByNameForOwner('jason', Yii::app()->user->userModel);
            $contact->addPermissions(User::getByUsername('betty'),         Permission::READ);
            $contact->addPermissions(Group::getByName   ('Support Staff'), Permission::READ);
            $saved = $contact->save();
            assert('$saved'); // Not Coding Standard

            ReadPermissionsOptimizationUtil::rebuild();
            assert('self::getProjectMungeRowCount() == 0'); // Not Coding Standard
        }

        public function setup1()
        {
            Yii::app()->user->userModel = User::getByUsername('super');

            $projects = Project::getAll();
            assert('count($projects) == 3'); // Not Coding Standard

            // The following is the test set up, given the
            // users, groups, and roles set up by SecurityTestHelper
            // the the additional setup below.

            // There is an project, owned by Benny, to which
            // Betty has been given explicit access, along
            // with anyone in Support Staff. The support staff
            // are Bernice and Brian. Benny is a Sales Person
            // so Bobby, the Sales Manager has access via roles.
            // Billy the admin guy has no access.

            $projects[0]->owner = User::getByUsername('benny');
            $projects[0]->addPermissions(User::getByUsername('betty'),         Permission::READ);
            $projects[0]->addPermissions(Group::getByName   ('Support Staff'), Permission::READ);
            $saved = $projects[0]->save();
            assert('count($saved)'); // Not Coding Standard
            ReadPermissionsOptimizationUtil::rebuild();
            $this->assertEquals(5, self::getProjectMungeRowCount());

            $this->rebuildAndTestThatTheMungeDoesntChange();
        }

        public function testOwnerOnlyNoMungeRowsGetAll()
        {
            Yii::app()->user->userModel = User::getByUsername('super');
            $projects = Project::getAll();
            $this->assertEquals(3, count($projects));
            $project = new Project();
            $project->name = 'A lone project';
            $this->assertTrue($project->save());
            //This will test that even though there are no munge rows, the project we just created gets returned
            //correctly.
            $projects = Project::getAll();

            $this->assertEquals(4, count($projects));
            $this->assertEquals('A lone project', $projects[3]->name);
            $projects[3]->delete();
        }

        /**
         * @depends testOwnerOnlyNoMungeRowsGetAll
         */
        public function testGetAllAsUsersWithRead()
        {
            $this->setup1();

            $allProjects = Project::getAll();
            $this->assertEquals(3, count($allProjects));
            foreach (array(
                        'benny',   // Owner
                        'betty',   // Explicit
                        'bernice', // Via group
                        'bobby',   // Via role (Benny's manager)
                     ) as $username)
            {
                Yii::app()->user->userModel = User::getByUsername($username);
                $projects = Project::getAll();
                $this->assertEquals(1, count($projects));
                $this->assertTrue($projects[0]->isSame($allProjects[0]));
                $this->assertEquals(1, Project::getCount());
            }
            $this->rebuildAndTestThatTheMungeDoesntChange();
        }

        /**
         * @depends testGetAllAsUsersWithRead
         */
        public function testGetAllUserWithNoMungeAccessButIsOwnerOnSome()
        {
            Yii::app()->user->userModel = User::getByUsername('billy');

            $projects = Project::getAll();
            $this->assertEquals(2, count($projects));
            $this->assertEquals(2, Project::getCount());

            $this->rebuildAndTestThatTheMungeDoesntChange();
        }

        public function setup2()
        {
            Yii::app()->user->userModel = User::getByUsername('super');

            // The following sets up more, continuing from setup1.

            $projects = Project::getAll();
            assert('count($projects) == 3'); // Not Coding Standard

            $user = User::getByUsername('betty');

            for ($i = 1; $i < 3; $i++)
            {
                $projects[$i]->addPermissions($user, Permission::READ);
                $saved = $projects[$i]->save();
                assert('count($saved)'); // Not Coding Standard
            }

            ReadPermissionsOptimizationUtil::rebuild();
            $this->assertEquals(11, self::getProjectMungeRowCount());
        }

        /**
         * @depends testGetAllUserWithNoMungeAccessButIsOwnerOnSome
         */
        public function testGetAllAsUsersWithReadOnMoreThanOne()
        {
            $this->setup2();

            $allProjects = Project::getAll();
            $this->assertEquals(3, count($allProjects));

            Yii::app()->user->userModel = User::getByUsername('betty');

            $projects = Project::getAll();
            $this->assertEquals(3, count($projects));
            $this->assertTrue($projects[0]->isSame($allProjects[0]));
            $this->assertTrue($projects[1]->isSame($allProjects[1]));
            $this->assertTrue($projects[2]->isSame($allProjects[2]));

            $this->rebuildAndTestThatTheMungeDoesntChange();
        }

        /**
         * @depends testGetAllAsUsersWithReadOnMoreThanOne
         */
        public function testGetSubset()
        {
            Yii::app()->user->userModel = User::getByUsername('super');

            $allProjects = Project::getAll();

            Yii::app()->user->userModel = User::getByUsername('betty');

            $projects = Project::getSubset(null, 0, 1);
            $this->assertEquals(1, count($projects));
            $this->assertTrue($projects[0]->isSame($allProjects[0]));

            $projects = Project::getSubset(null, 1, 1);
            $this->assertEquals(1, count($projects));
            $this->assertTrue($projects[0]->isSame($allProjects[1]));

            $projects = Project::getSubset(null, 2, 1);
            $this->assertEquals(1, count($projects));
            $this->assertTrue($projects[0]->isSame($allProjects[2]));

            $projects = Project::getSubset(null, 0, 2);
            $this->assertEquals(2, count($projects));
            $this->assertTrue($projects[0]->isSame($allProjects[0]));
            $this->assertTrue($projects[1]->isSame($allProjects[1]));

            $projects = Project::getSubset(null, 1, 2);
            $this->assertEquals(2, count($projects));
            $this->assertTrue($projects[0]->isSame($allProjects[1]));
            $this->assertTrue($projects[1]->isSame($allProjects[2]));

            $projects = Project::getSubset(null, 2, 2);
            $this->assertEquals(1, count($projects));
            $this->assertTrue($projects[0]->isSame($allProjects[2]));

            $projects = Project::getSubset(null, 0, 3);
            $this->assertEquals(3, count($projects));
            $this->assertTrue($projects[0]->isSame($allProjects[0]));
            $this->assertTrue($projects[1]->isSame($allProjects[1]));
            $this->assertTrue($projects[2]->isSame($allProjects[2]));

            $projects = Project::getSubset(null, 1, 3);
            $this->assertEquals(2, count($projects));
            $this->assertTrue($projects[0]->isSame($allProjects[1]));
            $this->assertTrue($projects[1]->isSame($allProjects[2]));

            $projects = Project::getSubset(null, 2, 3);
            $this->assertEquals(1, count($projects));
            $this->assertTrue($projects[0]->isSame($allProjects[2]));

            $projects = Project::getSubset(null, 3, 3);
            $this->assertEquals(0, count($projects));

            $this->assertEquals(3, Project::getCount());

            $this->rebuildAndTestThatTheMungeDoesntChange();
        }

        /**
         * @depends testGetSubset
         */
        public function testOwnedSecurableItemCreated()
        {
            $mungeTableRowsBefore = self::getProjectMungeRowCount();

            $bobby = User::getByUsername('bobby');
            $benny = User::getByUsername('benny');

            // Benny is a sales person so his manager Bobby should have access
            // to things he creates.
            Yii::app()->user->userModel = $bobby;
            $bobbyBeforeProjects = Project::getAll();

            Yii::app()->user->userModel = $benny;
            $bennyBeforeProjects = Project::getAll();

            $project = new Project();
            $project->name = 'Doop de doop';
            $this->assertTrue($project->save());

            $this->assertEquals(array(Permission::ALL, Permission::NONE), $project->getActualPermissions   ($benny));
            $this->assertEquals(array(Permission::ALL, Permission::NONE), $project->getActualPermissions   ($bobby));

            $this->assertEquals(Permission::ALL,                          $project->getEffectivePermissions($benny));
            $this->assertEquals(Permission::ALL,                          $project->getEffectivePermissions($bobby));

            //Called in OwnedSecurableItem::afterSave();
            //ReadPermissionsOptimizationUtil::ownedSecurableItemCreated($project);

            $bennyAfterProjects = Project::getAll();

            Yii::app()->user->userModel = $bobby;
            $bobbyAfterProjects = Project::getAll();

            $this->assertEquals(count($bennyBeforeProjects) + 1, count($bennyAfterProjects));
            $this->assertEquals(count($bobbyBeforeProjects) + 1, count($bobbyAfterProjects));

            $this->assertEquals($mungeTableRowsBefore + 1, self::getProjectMungeRowCount());

            $this->rebuildAndTestThatTheMungeDoesntChange();
        }

        /**
         * @depends testOwnedSecurableItemCreated
         */
        public function testMemoryUsageCreatingModels()
        {
            Yii::app()->user->userModel = User::getByUsername('super');
            // Create some projects to make sure memory is stable.
            for ($i = 0; $i < 20; $i++)
            {
                $project = self::createRandomProject($i);
                $this->assertTrue($project->save(false));
            }
            $memoryBefore = memory_get_usage(true);
            // Create more while monitoring memory.
            for ($i = 20; $i < 220; $i++)
            {
                $project = self::createRandomProject($i);
                $this->assertTrue($project->save(false));
                $memoryDuring = memory_get_usage(true);
                $this->assertWithinPercentage($memoryBefore, $memoryDuring, 10);
            }
            $memoryAfter  = memory_get_usage(true);
            $this->assertWithinPercentage($memoryBefore, $memoryAfter, 10);
        }

        /**
         * @depends testMemoryUsageCreatingModels
         */
        public function testMemoryUsageGettingModels()
        {
            Yii::app()->user->userModel = User::getByUsername('super');
            $memoryBefore = memory_get_usage(true);
            $count = Project::getCount();
            for ($i = 0; $i < $count; $i += 3)
            {
                $projects = Project::getSubset(null, $i, 3);
                $memoryDuring = memory_get_usage(true);
                $this->assertWithinPercentage($memoryBefore, $memoryDuring, 10);
                foreach ($projects as $project)
                {
                    unset($project);
                }
                $memoryDuring = memory_get_usage(true);
                $this->assertWithinPercentage($memoryBefore, $memoryDuring, 10);
            }
            $memoryAfter = memory_get_usage(true);
            $this->assertWithinPercentage($memoryBefore, $memoryAfter, 10);
        }

        /**
         * @depends testMemoryUsageGettingModels
         */
        public function testMemoryUsageNukingModels()
        {
            Yii::app()->user->userModel = User::getByUsername('super');
            $memoryBefore = memory_get_usage(true);
            $count = Project::getCount();
            for ($i = 0; $i < $count; $i += 3)
            {
                $projects = Project::getSubset(null, $i, 3);
                foreach ($projects as $project)
                {
                    $project->delete();
                    unset($project);
                }
                $memoryDuring = memory_get_usage(true);
                $this->assertWithinPercentage($memoryBefore, $memoryDuring, 10);
            }
            $memoryAfter = memory_get_usage(true);
            $this->assertWithinPercentage($memoryBefore, $memoryAfter, 10);
        }

        public function disabled_testMemcachingProjectsDirectly()
        {
            Yii::app()->user->userModel = User::getByUsername('super');
            $count = 50;
            $memcache = new Memcache();
            $memcache->connect('localhost', 11211);
            for ($i = 0; $i < $count; $i++)
            {
                $project = self::createRandomProject($i);
                $this->assertTrue($project->save()); // So that it has everything it should,
                                                     // particularly its createdByUser.
                $this->assertTrue($memcache->set("M:$i", serialize($project)));
            }
            RedBeanModelsCache::forgetAll(true);
            RedBeansCache::forgetAll();
            Yii::app()->user->userModel = User::getByUsername('super');
            $memoryBefore = memory_get_usage(true);
            for ($i = 0; $i < $count; $i++)
            {
                $data = $memcache->get("M:$i");
                $this->assertTrue($data !== false);
                $project = unserialize($data);
                $this->assertTrue  ($project instanceof Project);
                $this->assertEquals("Project#$i",               $project->name);
                $this->assertEquals("http://www.project$i.com", $project->website);
                $this->assertTrue  ($project->owner instanceof User);
                $this->assertEquals('super', $project->owner->username);
                $this->assertTrue  ($project->owner          === Yii::app()->user->userModel);
                $this->assertTrue  ($project->createdByUser  === Yii::app()->user->userModel);
                $this->assertTrue  ($project->modifiedByUser === Yii::app()->user->userModel);
                unset($project);
            }
            $memoryAfter = memory_get_usage(true);
            $this->assertWithinPercentage($memoryBefore, $memoryAfter, 10);
        }

        public function disabled_testComparePhpAndSqlifiedMungeRebuilds()
        {
            Yii::app()->user->userModel = User::getByUsername('super');

            if ($this->isDebug())
            {
                echo "\nNuking existing projects...\n";
            }
            $this->nukeExistingProjects();
            // This is checked in to do a short range in
            // order to not be too slow.
            // Manipulate the numbers to test different ranges
            // if the php and optimized versions of doing a
            // munge rebuild seem to be giving different results.
            // The parameters are...
            //    - the number of projects to create.
            //    - will it rebuild the munge after projects? - leave as true.
            //    - after which project will it start rebuilding the munge.
            //    - step.
            // eg: 150, true, 100, 3 - create projects, rebuilding the munge
            // after projects 100, 103, 106, 109, etc, until 150 projects
            // have been created.
            $this->createProjects(150, true, 100, 3);
        }

        // This is not an automated test.
        // It must be disabled out for check in.
        // The easiest way is to rename it.
        //public function testAShootLoadOfProjects() // Uncomment to run.
        public function disabled_testAShootLoadOfProjects() // Uncomment to check in.
        {
            $freezeAfterFirst20 = false;

            $super = User::getByUsername('super');

            foreach (array(20, 50, 100, 200, 500, 1000, 10000, 100000, 200000) as $shootLoad)
            {
                echo "\nNuking existing projects...\n";
                Yii::app()->user->userModel = $super;
                $this->nukeExistingProjects();

                echo "Creating $shootLoad projects...\n";
                echo " - Giving every 10th to Betty, giving Benny read\n";
                echo "   on overy 8th, and giving Sales Staff read on\n";
                echo "   every 12th.\n";
                list($time, $countThatBennyCanRead, $projectIdsThatBennyCanRead) = $this->createProjects($shootLoad);
                echo 'Created projects in ' . round($time, 1) . " seconds.\n";
                echo "Benny can read $countThatBennyCanRead of them.\n";
                echo 'The first few... ';
                for ($i = 0; $i < 10 && $i < count($projectIdsThatBennyCanRead); $i++)
                {
                    echo "{$projectIdsThatBennyCanRead[$i]}|";
                }
                echo "\n";

                $startTime = microtime(true);
                ReadPermissionsOptimizationUtil::rebuild(true);
                $endTime = microtime(true);
                if ($this->isDebug())
                {
                    echo 'Rebuilt the munge in php in ' . round($endTime - $startTime, 1) . ' seconds, ' . self::getProjectMungeRowCount() . " rows.\n";
                }
                $phpRows = R::getAll('select munge_id, securableitem_id, count from project_read order by munge_id, securableitem_id, count');

                // If $securityOptimized is false in debug.php the second one will just do the php again.
                $startTime = microtime(true);
                ReadPermissionsOptimizationUtil::rebuild();
                $endTime = microtime(true);
                if ($this->isDebug())
                {
                    echo 'Rebuilt the munge ' . (SECURITY_OPTIMIZED ? 'optimized' : 'in php') . ' in ' . round($endTime - $startTime, 1) . ' seconds, ' . self::getProjectMungeRowCount() . " rows.\n";
                }
                $otherRows = R::getAll('select munge_id, securableitem_id, count from project_read order by munge_id, securableitem_id, count');

                if (count(array_diff($phpRows, $otherRows)) > 0)
                {
                    echo "PHP & optimized munges don't match.\n";
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

                Yii::app()->user->userModel = User::getByUsername('benny');
                $count    = Project::getCount();

                $startTime = microtime(true);
                $projects = Project::getSubset(null, 0, 20);
                $endTime = microtime(true);
                echo 'As Benny retrieved 1 - ' . count($projects) . " of $count in " . round($endTime - $startTime, 2) . " seconds.\n";
                unset($projects);

                $offset = intval($count * 0.75);
                $startTime = microtime(true);
                $projects = Project::getSubset(null, $offset, 20);
                $endTime = microtime(true);
                echo "As Benny retrieved $offset - " . ($offset + count($projects)) . " of $count in " . round($endTime - $startTime, 3) . " seconds.\n";
                unset($projects);

                echo "Done.\n";
                echo "\n-------------------------------\n";

                if ($freezeAfterFirst20 && !RedBeanDatabase::isFrozen())
                {
                    echo "Freezing database...\n";
                    RedBeanDatabase::freeze();
                }
            }
        }
    }
?>
