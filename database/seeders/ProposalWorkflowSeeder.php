<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Proposal\Models\ProposalWorkflow;
use Modules\Proposal\Models\ProposalWorkflowStep;

class ProposalWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo workflow
        $workflow = ProposalWorkflow::create([
            'name' => 'Workflow mặc định',
            'is_active' => true,
        ]);

        // Step 1: Manager
        ProposalWorkflowStep::create([
            'workflow_id' => $workflow->id,
            'step_order' => 1,
            'role_name' => 'manager',
        ]);

        // Step 2: Director
        ProposalWorkflowStep::create([
            'workflow_id' => $workflow->id,
            'step_order' => 2,
            'role_name' => 'director',
        ]);
    }
}
