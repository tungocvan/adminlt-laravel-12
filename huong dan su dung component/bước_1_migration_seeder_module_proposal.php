<?php
// MODULE: Proposal
// STEP 1: MIGRATIONS + SEEDERS
// Laravel 12 | Livewire 3.1 | Spatie Permission

/* ==============================
 | MIGRATION: proposals
 ============================== */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->date('expected_time')->nullable();
            $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH'])->default('MEDIUM');
            $table->enum('status', ['PENDING'])->default('PENDING');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};

/* ==============================
 | MIGRATION: proposal_workflows
 ============================== */

return new class extends Migration {
    public function up(): void
    {
        Schema::create('proposal_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_workflows');
    }
};

/* ==============================
 | MIGRATION: proposal_workflow_steps
 ============================== */

return new class extends Migration {
    public function up(): void
    {
        Schema::create('proposal_workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('proposal_workflows')->cascadeOnDelete();
            $table->integer('step_order');
            $table->string('role_name'); // spatie role
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_workflow_steps');
    }
};

/* ==============================
 | MIGRATION: proposal_approvals
 ============================== */

return new class extends Migration {
    public function up(): void
    {
        Schema::create('proposal_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposals')->cascadeOnDelete();
            $table->integer('step_order');
            $table->foreignId('approver_id')->nullable()->constrained('users');
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->timestamp('acted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_approvals');
    }
};

/* ==============================
 | MIGRATION: proposal_comments
 ============================== */

return new class extends Migration {
    public function up(): void
    {
        Schema::create('proposal_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposals')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->text('comment');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_comments');
    }
};

/* ==============================
 | MIGRATION: proposal_files
 ============================== */

return new class extends Migration {
    public function up(): void
    {
        Schema::create('proposal_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposals')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_files');
    }
};

/* ==============================
 | SEEDER: Roles & Permissions
 ============================== */

namespace Modules\Proposal\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ProposalPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'proposal.create',
            'proposal.view.own',
            'proposal.view.all',
            'proposal.approve',
            'proposal.reject',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $employee = Role::firstOrCreate(['name' => 'employee']);
        $manager  = Role::firstOrCreate(['name' => 'manager']);
        $director = Role::firstOrCreate(['name' => 'director']);

        $employee->givePermissionTo(['proposal.create', 'proposal.view.own']);
        $manager->givePermissionTo(['proposal.view.all', 'proposal.approve', 'proposal.reject']);
        $director->givePermissionTo(['proposal.view.all', 'proposal.approve', 'proposal.reject']);
    }
}
