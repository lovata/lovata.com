<?php

use System\Models\File as FileModel;
use Database\Tester\Models\User;
use Database\Tester\Models\SoftDeleteUser;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AttachOneModelTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        include_once base_path('modules/system/tests/fixtures/plugins/database/tester/models/User.php');

        $this->migratePlugin('Database.Tester');
    }

    public function testSetRelationValue()
    {
        Model::unguard();
        $user = User::create(['name' => 'Stevie', 'email' => 'stevie@email.tld']);
        $user2 = User::create(['name' => 'Joe', 'email' => 'joe@email.tld']);
        Model::reguard();

        // Set by model object
        $file = $user->avatar()->make();
        $file->fromFile(base_path('modules/system/tests/fixtures/plugins/database/tester/assets/images/avatar.png'));
        $user->avatar = $file;
        $user->save();

        $this->assertNotNull($user->avatar);
        $this->assertEquals('avatar.png', $user->avatar->file_name);

        // Set by Uploaded file
        $sample = $user->avatar;
        $upload = new UploadedFile(
            base_path('modules/system/tests/fixtures/plugins/database/tester/assets/images/avatar.png'),
            $sample->file_name,
            $sample->content_type,
            null,
            true
        );

        $user2->avatar = $upload;

        // The file is prepped but not yet committed, this is for validation
        $this->assertNotNull($user2->avatar);
        $this->assertEquals($upload, $user2->avatar);

        // Commit the file and it should snap to a File model
        $user2->save();

        $this->assertNotNull($user2->avatar);
        $this->assertEquals('avatar.png', $user2->avatar->file_name);
    }

    public function testDeleteFlagDestroyRelationship()
    {
        Model::unguard();
        $user = User::create(['name' => 'Stevie', 'email' => 'stevie@email.tld']);
        Model::reguard();

        $this->assertNull($user->avatar);
        $user->avatar()->createFromFile(base_path('modules/system/tests/fixtures/plugins/database/tester/assets/images/avatar.png'));
        $user->unsetRelations();
        $this->assertNotNull($user->avatar);

        $avatar = $user->avatar;
        $avatarId = $avatar->id;

        $user->avatar()->remove($avatar);
        $this->assertNull(FileModel::find($avatarId));
    }

    public function testDeleteFlagDeleteModel()
    {
        Model::unguard();
        $user = User::create(['name' => 'Stevie', 'email' => 'stevie@email.tld']);
        Model::reguard();

        $this->assertNull($user->avatar);
        $user->avatar()->createFromFile(base_path('modules/system/tests/fixtures/plugins/database/tester/assets/images/avatar.png'));
        $user->unsetRelations();
        $this->assertNotNull($user->avatar);

        $avatarId = $user->avatar->id;
        $user->delete();
        $this->assertNull(FileModel::find($avatarId));
    }

    public function testDeleteFlagSoftDeleteModel()
    {
        Model::unguard();
        $user = SoftDeleteUser::create(['name' => 'Stevie', 'email' => 'stevie@email.tld']);
        Model::reguard();

        $user->avatar()->createFromFile(base_path('modules/system/tests/fixtures/plugins/database/tester/assets/images/avatar.png'));
        $this->assertNotNull($user->avatar);

        $avatarId = $user->avatar->id;
        $user->delete();
        $this->assertNotNull(FileModel::find($avatarId));
    }
}
