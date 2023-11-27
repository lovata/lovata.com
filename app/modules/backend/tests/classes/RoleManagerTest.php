<?php

use Backend\Classes\RoleManager;
use October\Rain\Exception\SystemException;

/**
 * RoleManagerTest
 */
class RoleManagerTest extends TestCase
{
    public function setUp(): void
    {
        $this->createApplication();

        $this->instance = RoleManager::instance();
        $this->instance->registerPermissions('October.TestCase', [
            'test.permission_one' => [
                'label' => 'Test Permission 1',
                'tab' => 'Test',
                'order' => 200
            ],
            'test.permission_two' => [
                'label' => 'Test Permission 2',
                'tab' => 'Test',
                'order' => 300
            ]
        ]);
    }

    public function tearDown(): void
    {
        RoleManager::forgetInstance();
    }

    public function testListPermissions()
    {
        $permissions = $this->instance->listPermissions();
        $permissionCodes = collect($permissions)->pluck('code')->toArray();
        $this->assertContains('test.permission_one', $permissionCodes);
        $this->assertContains('test.permission_two', $permissionCodes);
    }

    public function testRegisterPermissions()
    {
        $this->instance->registerPermissions('October.TestCase', [
            'test.permission_three' => [
                'label' => 'Test Permission 3',
                'tab' => 'Test',
                'order' => 100
            ]
        ]);

        $permissions = $this->instance->listPermissions();
        $permissionCodes = collect($permissions)->pluck('code')->toArray();

        $this->assertContains('test.permission_one', $permissionCodes);
        $this->assertContains('test.permission_two', $permissionCodes);
        $this->assertContains('test.permission_three', $permissionCodes);
    }

    public function testRegisterPermissionsThroughCallbacks()
    {
        // Callback one
        $this->instance->registerCallback(function ($manager) {
            $manager->registerPermissions('October.TestCase', [
                'test.permission_three' => [
                    'label' => 'Test Permission 3',
                    'tab' => 'Test',
                    'order' => 100
                ]
            ]);
        });

        // Callback two
        $this->instance->registerCallback(function ($manager) {
            $manager->registerPermissions('October.TestCase', [
                'test.permission_four' => [
                    'label' => 'Test Permission 4',
                    'tab' => 'Test',
                    'order' => 400
                ]
            ]);
        });

        $permissions = $this->instance->listPermissions();
        $permissionCodes = collect($permissions)->pluck('code')->toArray();

        $this->assertContains('test.permission_one', $permissionCodes);
        $this->assertContains('test.permission_two', $permissionCodes);
        $this->assertContains('test.permission_three', $permissionCodes);
        $this->assertContains('test.permission_four', $permissionCodes);
    }

    public function testRegisterAdditionalTab()
    {
        $this->instance->registerPermissions('October.TestCase', [
            'test.permission_three' => [
                'label' => 'Test Permission 3',
                'tab' => 'Test 2',
                'order' => 100
            ]
        ]);

        $this->instance->registerCallback(function ($manager) {
            $manager->registerPermissions('October.TestCase', [
                'test.permission_four' => [
                    'label' => 'Test Permission 4',
                    'tab' => 'Test 2',
                    'order' => 400
                ]
            ]);
        });

        $tabs = $this->listTabbedPermissions($this->instance->listPermissions());
        $this->assertArrayHasKey('Test', $tabs);
        $this->assertArrayHasKey('Test 2', $tabs);

        $tabs1 = collect($tabs['Test'])->pluck('code')->toArray();
        $this->assertContains('test.permission_one', $tabs1);
        $this->assertContains('test.permission_two', $tabs1);

        $tabs2 = collect($tabs['Test 2'])->pluck('code')->toArray();
        $this->assertContains('test.permission_three', $tabs2);
        $this->assertContains('test.permission_four', $tabs2);
    }

    public function testRemovePermission()
    {
        $this->instance->removePermission('October.TestCase', 'test.permission_one');

        $permissions = $this->instance->listPermissions();
        $permissionCodes = collect($permissions)->pluck('code')->toArray();
        $this->assertNotContains('test.permission_one', $permissionCodes);
    }

    public function testCannotRemovePermissionsBeforeLoaded()
    {
        $this->expectException(SystemException::class);
        $this->expectExceptionMessage('Unable to remove permissions before they are loaded.');

        RoleManager::forgetInstance();
        $this->instance = RoleManager::instance();
        $this->instance->removePermission('October.TestCase', 'test.permission_one');
    }

    protected function listTabbedPermissions($permissions)
    {
        $tabs = [];

        foreach ($permissions as $permission) {
            $tab = $permission->tab ?? 'backend::lang.form.undefined_tab';

            if (!array_key_exists($tab, $tabs)) {
                $tabs[$tab] = [];
            }

            $tabs[$tab][] = $permission;
        }

        return $tabs;
    }
}
