<?php

namespace App\Tests\Workspace;

use App\Workspace\InMemoryWorkspaceRepository;
use App\Workspace\MissionFilter;
use App\Workspace\MissionStatus;
use PHPUnit\Framework\TestCase;

final class MissionFilterTest extends TestCase
{
    public function testQueryIsTrimmedAndBounded(): void
    {
        $filter = new MissionFilter('  '.str_repeat('a', 150).'  ');

        self::assertSame(MissionFilter::QUERY_MAX_LENGTH, mb_strlen($filter->query));
    }

    public function testMatchesTitleOrCategoryCaseInsensitively(): void
    {
        $repository = new InMemoryWorkspaceRepository();

        self::assertCount(2, $repository->findMissions(new MissionFilter('structure')));
        self::assertCount(1, $repository->findMissions(new MissionFilter('RÉMUNÉRATION')));
    }

    public function testCombinesQueryAndStatus(): void
    {
        $missions = (new InMemoryWorkspaceRepository())->findMissions(new MissionFilter('structure', MissionStatus::InProgress));

        self::assertSame([2, 5], array_map(static fn ($mission): int => $mission->id, $missions));
    }

    public function testQueryOnlyKeepsNonEmptyParameters(): void
    {
        self::assertSame([], (new MissionFilter())->toQuery());
        self::assertSame(['q' => 'audit', 'status' => 'done'], (new MissionFilter('audit', MissionStatus::Done))->toQuery());
    }
}
