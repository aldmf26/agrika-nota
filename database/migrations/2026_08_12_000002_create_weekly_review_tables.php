<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_reviews', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedTinyInteger('week');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->json('nota_ids')->nullable();
            $table->unsignedInteger('nota_count')->default(0);
            $table->unsignedBigInteger('total_nominal')->default(0);
            $table->timestamps();
            $table->unique(['year', 'month', 'week']);
        });

        Schema::create('weekly_review_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('nota_ids');
            $table->unsignedInteger('nota_count');
            $table->unsignedBigInteger('total_nominal');
            $table->timestamp('reviewed_at');
            $table->timestamps();
        });

        Schema::create('nota_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nota_id')->constrained('notas')->cascadeOnDelete();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note');
            $table->timestamp('reported_at');
            $table->foreignId('replacement_nota_id')->nullable()->constrained('notas')->nullOnDelete();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['nota_id', 'resolved_at']);
        });

        foreach (['weekly-review.view', 'weekly-review.close', 'weekly-review.report-issue'] as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        if ($approver = Role::where('name', 'approver')->first()) {
            $approver->syncPermissions(['nota.view-all', 'weekly-review.view', 'weekly-review.close', 'weekly-review.report-issue']);
        }

        if ($superAdmin = Role::where('name', 'super_admin')->first()) {
            $superAdmin->givePermissionTo(Permission::all());
        }

        app('cache')->forget('spatie.permission.cache');
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_issues');
        Schema::dropIfExists('weekly_review_snapshots');
        Schema::dropIfExists('weekly_reviews');
    }
};
