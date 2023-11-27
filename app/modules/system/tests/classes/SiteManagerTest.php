<?php

use System\Models\SiteDefinition;

class SiteManagerTest extends TestCase
{
    /**
     * testHostNameExactMatch
     */
    public function testHostNameExactMatch()
    {
        $sites = $this->listMockSites()->filter(function($site) {
            return $site->matchesHostname('en.octobercms.test');
        });

        $this->assertEquals(1, $sites->count());
        $this->assertEquals('english', $sites->first()->code);
    }

    /**
     * listMockSites
     */
    protected function listMockSites()
    {
        Model::unguard();
        $sites = collect([
            new SiteDefinition([
                'id' => 1,
                'name' => 'Primary Site',
                'code' => 'primary',
                'is_primary' => true,
                'is_enabled' => true,
                'is_enabled_edit' => true,
                'is_host_restricted' => true,
                'allow_hosts' => [
                    ['hostname' => 'octobercms.test']
                ]
            ]),
            new SiteDefinition([
                'id' => 2,
                'name' => 'English Site',
                'code' => 'english',
                'is_primary' => true,
                'is_enabled' => true,
                'is_enabled_edit' => true,
                'is_host_restricted' => true,
                'allow_hosts' => [
                    ['hostname' => 'en.octobercms.test'],
                    ['hostname' => '*.en.octobercms.test'],
                ]
            ])
        ]);
        Model::reguard();
        return $sites;
    }

}
