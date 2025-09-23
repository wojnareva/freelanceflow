<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Indexes for clients table - search optimization
        Schema::table('clients', function (Blueprint $table) {
            $table->index(['name'], 'clients_name_index');
            $table->index(['email'], 'clients_email_index');
            $table->index(['company'], 'clients_company_index');
            $table->index(['user_id'], 'clients_user_id_index');
        });

        // Indexes for projects table - filtering and sorting optimization
        Schema::table('projects', function (Blueprint $table) {
            $table->index(['status'], 'projects_status_index');
            $table->index(['client_id'], 'projects_client_id_index');
            $table->index(['user_id'], 'projects_user_id_index');
            $table->index(['name'], 'projects_name_index');
            $table->index(['created_at'], 'projects_created_at_index');
            $table->index(['start_date'], 'projects_start_date_index');
            $table->index(['end_date'], 'projects_end_date_index');
        });

        // Indexes for time_entries table - performance for aggregations
        Schema::table('time_entries', function (Blueprint $table) {
            $table->index(['user_id'], 'time_entries_user_id_index');
            $table->index(['project_id'], 'time_entries_project_id_index');
            $table->index(['date'], 'time_entries_date_index');
            $table->index(['billable'], 'time_entries_billable_index');
            $table->index(['invoiced'], 'time_entries_invoiced_index');
            $table->index(['created_at'], 'time_entries_created_at_index');
        });

        // Indexes for invoices table - status and date filtering
        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['user_id'], 'invoices_user_id_index');
            $table->index(['client_id'], 'invoices_client_id_index');
            $table->index(['status'], 'invoices_status_index');
            $table->index(['issue_date'], 'invoices_issue_date_index');
            $table->index(['due_date'], 'invoices_due_date_index');
            $table->index(['paid_at'], 'invoices_paid_at_index');
        });

        // Indexes for expenses table - filtering and reporting
        Schema::table('expenses', function (Blueprint $table) {
            $table->index(['user_id'], 'expenses_user_id_index');
            $table->index(['project_id'], 'expenses_project_id_index');
            $table->index(['category'], 'expenses_category_index');
            $table->index(['status'], 'expenses_status_index');
            $table->index(['expense_date'], 'expenses_expense_date_index');
            $table->index(['billable'], 'expenses_billable_index');
        });

        // Composite indexes for common query patterns
        Schema::table('time_entries', function (Blueprint $table) {
            $table->index(['user_id', 'date'], 'time_entries_user_date_index');
            $table->index(['project_id', 'date'], 'time_entries_project_date_index');
            $table->index(['billable', 'invoiced'], 'time_entries_billable_invoiced_index');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'projects_user_status_index');
            $table->index(['client_id', 'status'], 'projects_client_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes for clients table
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex('clients_name_index');
            $table->dropIndex('clients_email_index');
            $table->dropIndex('clients_company_index');
            $table->dropIndex('clients_user_id_index');
        });

        // Drop indexes for projects table
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('projects_status_index');
            $table->dropIndex('projects_client_id_index');
            $table->dropIndex('projects_user_id_index');
            $table->dropIndex('projects_name_index');
            $table->dropIndex('projects_created_at_index');
            $table->dropIndex('projects_start_date_index');
            $table->dropIndex('projects_end_date_index');
            $table->dropIndex('projects_user_status_index');
            $table->dropIndex('projects_client_status_index');
        });

        // Drop indexes for time_entries table
        Schema::table('time_entries', function (Blueprint $table) {
            $table->dropIndex('time_entries_user_id_index');
            $table->dropIndex('time_entries_project_id_index');
            $table->dropIndex('time_entries_date_index');
            $table->dropIndex('time_entries_billable_index');
            $table->dropIndex('time_entries_invoiced_index');
            $table->dropIndex('time_entries_created_at_index');
            $table->dropIndex('time_entries_user_date_index');
            $table->dropIndex('time_entries_project_date_index');
            $table->dropIndex('time_entries_billable_invoiced_index');
        });

        // Drop indexes for invoices table
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_user_id_index');
            $table->dropIndex('invoices_client_id_index');
            $table->dropIndex('invoices_status_index');
            $table->dropIndex('invoices_issue_date_index');
            $table->dropIndex('invoices_due_date_index');
            $table->dropIndex('invoices_paid_at_index');
        });

        // Drop indexes for expenses table
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('expenses_user_id_index');
            $table->dropIndex('expenses_project_id_index');
            $table->dropIndex('expenses_category_index');
            $table->dropIndex('expenses_status_index');
            $table->dropIndex('expenses_expense_date_index');
            $table->dropIndex('expenses_billable_index');
        });
    }
};
