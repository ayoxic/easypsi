<?php

namespace Tests\Unit;

use App\Models\User;
use App\Support\LevelAudience;
use Tests\TestCase;

class LevelAudienceTest extends TestCase
{
    public function test_shared_course_belongs_to_each_selected_track(): void
    {
        $shared = '2bac::science-math|science-physique';
        $this->assertTrue(LevelAudience::matches($shared, '2bac::science-math'));
        $this->assertTrue(LevelAudience::matches($shared, '2bac::science-physique'));
        $this->assertFalse(LevelAudience::matches($shared, '2bac::svt'));
        $this->assertFalse(LevelAudience::matches($shared, '1bac::science-math'));
        $this->assertCount(2, LevelAudience::choices($shared, '2bac / Maths + Physique'));
    }

    public function test_premium_requires_active_subscription_and_matching_audience(): void
    {
        $user = new User([
            'subscription_tier' => 'premium',
            'premium_expires_at' => new \DateTimeImmutable('+1 day'),
            'premium_level_key' => '2bac::science-physique',
        ]);
        $this->assertTrue($user->hasActivePremiumForLevel('2bac::science-math|science-physique'));
        $this->assertFalse($user->hasActivePremiumForLevel('2bac::science-math'));
        $this->assertFalse($user->hasActivePremiumForLevel('1bac::science-physique'));
        $user->premium_expires_at = new \DateTimeImmutable('-1 day');
        $this->assertFalse($user->hasActivePremiumForLevel('2bac::science-math|science-physique'));
    }
}
