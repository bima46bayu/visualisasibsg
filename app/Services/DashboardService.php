<?php

namespace App\Services;

use App\Models\SalesTarget;
use App\Models\SalesRealization;
use App\Models\Team;
use App\Models\Entity;
use App\Models\SalesMember;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData(array $filters = [])
    {
        $year = $filters['year'] ?? date('Y');
        $month = $filters['month'] ?? null;
        $teamId = $filters['team_id'] ?? null;
        $salesMemberId = $filters['sales_member_id'] ?? null;
        $entityId = $filters['entity_id'] ?? null;

        // Base Query Build
        $targetQuery = SalesTarget::query()->when($year, fn($q) => $q->where('year', $year))
            ->when($month, fn($q) => $q->where('month', $month))
            ->when($entityId, fn($q) => $q->where('entity_id', $entityId));
            
        $realizationQuery = SalesRealization::query()->when($year, fn($q) => $q->where('year', $year))
            ->when($month, fn($q) => $q->where('month', $month))
            ->when($entityId, fn($q) => $q->where('entity_id', $entityId));

        if ($teamId) {
            $salesMemberIds = SalesMember::where('team_id', $teamId)->pluck('id');
            $targetQuery->whereIn('sales_member_id', $salesMemberIds);
            $realizationQuery->whereIn('sales_member_id', $salesMemberIds);
        }

        if ($salesMemberId) {
            $targetQuery->where('sales_member_id', $salesMemberId);
            $realizationQuery->where('sales_member_id', $salesMemberId);
        }

        $totalTarget = (clone $targetQuery)->sum('target_amount');
        $totalRealization = (clone $realizationQuery)->sum('realization_amount');
        $achievementPercent = $totalTarget > 0 ? round(($totalRealization / $totalTarget) * 100, 2) : 0;

        return [
            'total_target' => $totalTarget,
            'total_realization' => $totalRealization,
            'overall_achievement_percentage' => $achievementPercent,
            'achievement_per_team' => $this->getTeamAchievement($filters),
            'achievement_per_entity' => $this->getEntityAchievement($filters),
            'monthly_trend' => $this->getMonthlyData($filters),
            'top_sales' => $this->getTopSalesMembers($filters),
            'entity_trends_monthly' => $this->getEntityMonthlyTrends($filters),
            'entity_trends_yearly' => $this->getEntityYearlyTrends($filters),
            'pivot_table' => $this->getPivotTableData($filters),
            'filter_meta' => [
                'year' => $year,
                'curr_month' => $filters['month'] ?? date('n'),
                'prev_month' => ($filters['month'] ?? date('n')) > 1 ? ($filters['month'] ?? date('n')) - 1 : null,
                'pivot_curr_month' => $filters['pivot_month'] ?? $filters['month'] ?? date('n'),
                'pivot_prev_month' => ($filters['pivot_month'] ?? $filters['month'] ?? date('n')) > 1 ? ($filters['pivot_month'] ?? $filters['month'] ?? date('n')) - 1 : null
            ]
        ];
    }

    private function getEntityMonthlyTrends(array $filters)
    {
        $year = $filters['year'] ?? date('Y');
        $entities = Entity::all();
        
        $targets = SalesTarget::where('year', $year)
            ->selectRaw('entity_id, month, sum(target_amount) as total')
            ->groupBy('entity_id', 'month')
            ->get()->groupBy('entity_id');
            
        $realizations = SalesRealization::where('year', $year)
            ->selectRaw('entity_id, month, sum(realization_amount) as total')
            ->groupBy('entity_id', 'month')
            ->get()->groupBy('entity_id');
            
        $data = [];
        foreach ($entities as $entity) {
            $eTargets = isset($targets[$entity->id]) ? $targets[$entity->id]->pluck('total', 'month')->toArray() : [];
            $eReals = isset($realizations[$entity->id]) ? $realizations[$entity->id]->pluck('total', 'month')->toArray() : [];
            
            $monthly = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthly[] = [
                    'month' => $i,
                    'target' => $eTargets[$i] ?? 0,
                    'realization' => $eReals[$i] ?? 0,
                ];
            }
            
            $data[] = [
                'entity' => $entity->name,
                'data' => $monthly
            ];
        }
        
        return $data;
    }

    private function getEntityYearlyTrends(array $filters)
    {
        $entities = \App\Models\Entity::all();
        $baseYear = $filters['year'] ?? date('Y');
        $years = range($baseYear - 2, $baseYear + 2);
        
        $targets = \App\Models\SalesTarget::whereIn('year', $years)
            ->selectRaw('entity_id, year, sum(target_amount) as total')
            ->groupBy('entity_id', 'year')
            ->get()->groupBy('entity_id');
            
        $realizations = \App\Models\SalesRealization::whereIn('year', $years)
            ->selectRaw('entity_id, year, sum(realization_amount) as total')
            ->groupBy('entity_id', 'year')
            ->get()->groupBy('entity_id');
            
        $data = [];
        foreach ($entities as $entity) {
            $eTargets = isset($targets[$entity->id]) ? $targets[$entity->id]->pluck('total', 'year')->toArray() : [];
            $eReals = isset($realizations[$entity->id]) ? $realizations[$entity->id]->pluck('total', 'year')->toArray() : [];
            
            $yearly = [];
            foreach ($years as $y) {
                $yearly[] = [
                    'label' => (string) $y,
                    'target' => $eTargets[$y] ?? 0,
                    'realization' => $eReals[$y] ?? 0,
                ];
            }
            
            $data[] = [
                'entity' => $entity->name,
                'data' => $yearly,
                'years' => $years
            ];
        }
        
        return $data;
    }

    private function getPivotTableData(array $filters)
    {
        $year = $filters['year'] ?? date('Y');
        $month = $filters['pivot_month'] ?? $filters['month'] ?? date('n');
        $prevMonth = $month > 1 ? $month - 1 : null;

        $teams = Team::all()->keyBy('id');
        $entities = Entity::all()->keyBy('id');
        $salesMembers = SalesMember::all();

        $targets = SalesTarget::where('year', $year)->get();
        $realizations = SalesRealization::where('year', $year)->get();
        
        $amPivot = [];
        foreach ($salesMembers as $sm) {
            $smTargets = $targets->where('sales_member_id', $sm->id);
            $smReals = $realizations->where('sales_member_id', $sm->id);
            
            if ($smTargets->isEmpty() && $smReals->isEmpty()) continue;

            $entityBreakdown = [];
            foreach ($entities as $entity) {
                $eT = $smTargets->where('entity_id', $entity->id);
                $eR = $smReals->where('entity_id', $entity->id);
                
                if ($eT->isEmpty() && $eR->isEmpty()) continue;
                
                $entityBreakdown[] = [
                    'name' => $entity->name,
                    'prev' => [
                        'target' => $prevMonth ? $eT->where('month', $prevMonth)->sum('target_amount') : 0,
                        'realization' => $prevMonth ? $eR->where('month', $prevMonth)->sum('realization_amount') : 0,
                    ],
                    'curr' => [
                        'target' => $eT->where('month', $month)->sum('target_amount'),
                        'realization' => $eR->where('month', $month)->sum('realization_amount'),
                    ],
                    'total' => [
                        'target' => $eT->sum('target_amount'),
                        'realization' => $eR->sum('realization_amount'),
                    ]
                ];
            }
            
            $amPivot[] = [
                'name' => $sm->name,
                'team' => isset($teams[$sm->team_id]) ? $teams[$sm->team_id]->name : '-',
                'entities' => $entityBreakdown,
                'prev' => [
                     'target' => $prevMonth ? $smTargets->where('month', $prevMonth)->sum('target_amount') : 0,
                     'realization' => $prevMonth ? $smReals->where('month', $prevMonth)->sum('realization_amount') : 0,
                ],
                'curr' => [
                     'target' => $smTargets->where('month', $month)->sum('target_amount'),
                     'realization' => $smReals->where('month', $month)->sum('realization_amount'),
                ],
                'total' => [
                     'target' => $smTargets->sum('target_amount'),
                     'realization' => $smReals->sum('realization_amount'),
                ]
            ];
        }
        
        $teamPivot = [];
        foreach ($teams as $team) {
            $memberIds = $salesMembers->where('team_id', $team->id)->pluck('id');
            $teamT = $targets->whereIn('sales_member_id', $memberIds);
            $teamR = $realizations->whereIn('sales_member_id', $memberIds);
            
            if ($teamT->isEmpty() && $teamR->isEmpty()) continue;
            
            $teamPivot[] = [
                'name' => $team->name,
                'prev' => [
                     'target' => $prevMonth ? $teamT->where('month', $prevMonth)->sum('target_amount') : 0,
                     'realization' => $prevMonth ? $teamR->where('month', $prevMonth)->sum('realization_amount') : 0,
                ],
                'curr' => [
                     'target' => $teamT->where('month', $month)->sum('target_amount'),
                     'realization' => $teamR->where('month', $month)->sum('realization_amount'),
                ],
                'total' => [
                     'target' => $teamT->sum('target_amount'),
                     'realization' => $teamR->sum('realization_amount'),
                ]
            ];
        }

        $entityPivot = [];
        foreach ($entities as $entity) {
            $eT = $targets->where('entity_id', $entity->id);
            $eR = $realizations->where('entity_id', $entity->id);
            
            if ($eT->isEmpty() && $eR->isEmpty()) continue;
            
            $entityPivot[] = [
                'name' => $entity->name,
                'prev' => [
                     'target' => $prevMonth ? $eT->where('month', $prevMonth)->sum('target_amount') : 0,
                     'realization' => $prevMonth ? $eR->where('month', $prevMonth)->sum('realization_amount') : 0,
                ],
                'curr' => [
                     'target' => $eT->where('month', $month)->sum('target_amount'),
                     'realization' => $eR->where('month', $month)->sum('realization_amount'),
                ],
                'total' => [
                     'target' => $eT->sum('target_amount'),
                     'realization' => $eR->sum('realization_amount'),
                ]
            ];
        }

        return [
            'by_am' => $amPivot,
            'by_team' => $teamPivot,
            'by_entity' => $entityPivot,
        ];
    }

    private function getMonthlyData(array $filters)
    {
        $year = $filters['year'] ?? date('Y');
        
        $targets = SalesTarget::where('year', $year)
            ->selectRaw('month, sum(target_amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month')->toArray();
            
        $realizations = SalesRealization::where('year', $year)
            ->selectRaw('month, sum(realization_amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month')->toArray();

        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = [
                'month' => $i,
                'target' => $targets[$i] ?? 0,
                'realization' => $realizations[$i] ?? 0,
            ];
        }

        return $data;
    }

    private function getTeamAchievement(array $filters)
    {
        $year = $filters['year'] ?? date('Y');
        
        $teams = Team::with(['salesMembers.salesTargets' => function ($q) use ($year) {
            $q->where('year', $year);
        }, 'salesMembers.salesRealizations' => function ($q) use ($year) {
            $q->where('year', $year);
        }])->get();

        $data = [];
        foreach ($teams as $team) {
            $target = $team->salesMembers->flatMap->salesTargets->sum('target_amount');
            $realization = $team->salesMembers->flatMap->salesRealizations->sum('realization_amount');
            
            $data[] = [
                'team' => $team->name,
                'target' => $target,
                'realization' => $realization,
            ];
        }

        return $data;
    }

    private function getEntityAchievement(array $filters)
    {
        $year = $filters['year'] ?? date('Y');
        
        $entities = Entity::with(['salesTargets' => function ($q) use ($year) {
            $q->where('year', $year);
        }, 'salesRealizations' => function ($q) use ($year) {
            $q->where('year', $year);
        }])->get();

        $data = [];
        foreach ($entities as $entity) {
            $realization = $entity->salesRealizations->sum('realization_amount');
            
            $data[] = [
                'entity' => $entity->name,
                'realization' => $realization,
            ];
        }

        return $data;
    }

    private function getTopSalesMembers(array $filters)
    {
        $year = $filters['year'] ?? date('Y');
        
        $salesMembers = SalesMember::with(['team', 'salesTargets' => function ($q) use ($year) {
            $q->where('year', $year);
        }, 'salesRealizations' => function ($q) use ($year) {
            $q->where('year', $year);
        }])->get();

        $data = [];
        foreach ($salesMembers as $member) {
            $target = $member->salesTargets->sum('target_amount');
            $realization = $member->salesRealizations->sum('realization_amount');
            $achievement = $target > 0 ? round(($realization / $target) * 100, 2) : 0;
            
            if ($realization > 0) {
                $data[] = [
                    'name' => $member->name,
                    'team' => $member->team ? $member->team->name : '-',
                    'target' => $target,
                    'realization' => $realization,
                    'achievement_percentage' => $achievement,
                ];
            }
        }

        usort($data, fn($a, $b) => $b['realization'] <=> $a['realization']);

        return array_slice($data, 0, 10);
    }
}
