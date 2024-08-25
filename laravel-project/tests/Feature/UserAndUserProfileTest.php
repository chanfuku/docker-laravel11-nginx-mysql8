<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UserAndUserProfileTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        DB::enableQueryLog();
        self::insertInitialData();
    }

    #[Test]
    public function user_profilesとjoinしてfindするとidが上書きされてしまうケース()
    {
        // user.id=1のUserをUserProfileとjoinして取得
        $target_user_id = 1;
        $user = User::join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->whereNotNull('user_profiles.address')
            ->findOrfail($target_user_id);

        // UserProfileを取得する
        $user_profile = $user->userProfile;

        dump(DB::getQueryLog());
        self::outputUserAndUserProfile($user, $user_profile);

        // user_idが一致しないことを検証
        $this::assertNotEquals($target_user_id, $user_profile->user_id);
    }

    #[Test]
    public function user_profilesとjoinしてfindする時にselect指定すればOK()
    {
        // user.id=1のUserをUserProfileとjoinして取得
        $target_user_id = 1;
        $user = User::select(['users.id', 'users.name', 'users.email'])->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->whereNotNull('user_profiles.address')
            ->findOrFail($target_user_id);

        // UserProfileを取得する
        $user_profile = $user->userProfile;

        dump(DB::getQueryLog());
        self::outputUserAndUserProfile($user, $user_profile);

        // user_idが一致することを検証
        $this::assertEquals($target_user_id, $user_profile->user_id);
    }

    #[Test]
    public function user_profilesをwithで指定すればOK()
    {
        // user.id=1のUserをUserProfileとjoinして取得
        $target_user_id = 1;
        $user = User::with([
            'userProfile' => function ($query) {
                $query->whereNotNull('address');
            }
        ])->findOrFail($target_user_id);

        // UserProfileを取得する
        $user_profile = $user->userProfile;

        dump(DB::getQueryLog());
        self::outputUserAndUserProfile($user, $user_profile);

        // user_idが一致することを検証
        $this::assertEquals($target_user_id, $user_profile->user_id);
    }

    // user_profiles.idを連番ではなく歯抜けで登録する
    private function insertInitialData()
    {
        // 1人目のUser・UserProfileを作成
        $user1 = User::create([
            'id' => 1,
            'name' => 'name1',
            'email' => '111@111.com',
            'password' => 'password1',
        ]);
        UserProfile::create([
            // 連番だとid=1になるが、id=1はスキップされた(歯抜けが発生した)と仮定し、意図的にid=2を指定
            'id' => 2,
            'user_id' => $user1->id,
            'address' => 'address1',
        ]);

        // 2人目のUser・UserProfileを作成
        $user2 = User::create([
            'id' => 2,
            'name' => 'name2',
            'email' => '222@222.com',
            'password' => 'password2',
        ]);
        UserProfile::create([
            // 2人目だけど、id=2は既に使われているので、id=3を指定
            'id' => 3,
            'user_id' => $user2->id,
            'address' => 'address2',
        ]);
    }

    private function outputUserAndUserProfile(User $user, UserProfile $user_profile)
    {
        dump('###User###');
        dump('id:'. $user->id);
        dump('name:'. $user->name);
        dump('email:'. $user->email);

        dump('###UserProfile###');
        dump('id:'. $user_profile->id);
        dump('user_id:'. $user_profile->user_id);
        dump('address:'. $user_profile->address);
    }
}
