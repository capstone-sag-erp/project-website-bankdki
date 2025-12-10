<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel unit_terkait
        Schema::create('unit_terkait', function (Blueprint $table) {
            $table->id('id_unit');
            $table->string('nama_unit', 100);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Tabel nasabah
        Schema::create('nasabah', function (Blueprint $table) {
            $table->id('id_nasabah');
            $table->string('nama', 100);
            $table->string('no_ktp', 20)->unique();
            $table->text('alamat')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->date('tanggal_daftar');
            $table->timestamps();
        });

        // Tabel customer_service (agent)
        Schema::create('customer_service', function (Blueprint $table) {
            $table->id('id_agent');
            $table->string('nama_agent', 100);
            $table->string('email_agent', 100)->unique();
            $table->string('divisi', 50)->nullable();
            $table->timestamps();
        });

        // Tabel crm_lead
        Schema::create('crm_lead', function (Blueprint $table) {
            $table->id('id_lead');
            $table->foreignId('id_nasabah')->constrained('nasabah', 'id_nasabah')->onDelete('cascade');
            $table->string('produk_minat', 100)->nullable();
            $table->string('sumber_lead', 100)->nullable();
            $table->string('status_lead', 20)->default('new'); // new, contacted, qualified, lost
            $table->datetime('tanggal_input');
            $table->timestamps();
        });

        // Tabel tiket
        Schema::create('tiket', function (Blueprint $table) {
            $table->id('id_tiket');
            $table->foreignId('id_nasabah')->constrained('nasabah', 'id_nasabah')->onDelete('cascade');
            $table->foreignId('id_agent')->nullable()->constrained('customer_service', 'id_agent')->onDelete('set null');
            $table->string('kategori', 50)->nullable();
            $table->text('deskripsi')->nullable();
            $table->datetime('tanggal_buat');
            $table->string('status_tiket', 20)->default('open'); // open, in_progress, resolved, closed
            $table->string('prioritas', 20)->default('medium'); // low, medium, high, urgent
            $table->timestamps();
        });

        // Tabel aktivitas
        Schema::create('aktivitas', function (Blueprint $table) {
            $table->id('id_aktivitas');
            $table->foreignId('id_tiket')->constrained('tiket', 'id_tiket')->onDelete('cascade');
            $table->foreignId('id_agent')->nullable()->constrained('customer_service', 'id_agent')->onDelete('set null');
            $table->string('jenis_aktivitas', 50); // call, email, meeting, note
            $table->datetime('tanggal_aktivitas');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // Tabel penugasan
        Schema::create('penugasan', function (Blueprint $table) {
            $table->id('id_penugasan');
            $table->foreignId('id_tiket')->constrained('tiket', 'id_tiket')->onDelete('cascade');
            $table->foreignId('id_unit')->constrained('unit_terkait', 'id_unit')->onDelete('cascade');
            $table->datetime('tanggal_assign');
            $table->datetime('tanggal_selesai')->nullable();
            $table->string('status_penugasan', 20)->default('pending'); // pending, in_progress, completed
            $table->timestamps();
        });

        // Tabel opportunity
        Schema::create('opportunity', function (Blueprint $table) {
            $table->id('id_opportunity');
            $table->foreignId('id_lead')->constrained('crm_lead', 'id_lead')->onDelete('cascade');
            $table->decimal('nilai_estimasi', 15, 2)->nullable();
            $table->string('tahap', 50)->default('prospecting'); // prospecting, qualification, proposal, negotiation, closed_won, closed_lost
            $table->datetime('tanggal_update')->nullable();
            $table->timestamps();
        });

        // Tabel product_types untuk KPI
        Schema::create('product_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Tabel transactions untuk KPI
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nasabah_id')->constrained('nasabah', 'id_nasabah')->onDelete('cascade');
            $table->foreignId('product_type_id')->constrained('product_types')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->date('transaction_date');
            $table->string('status', 20)->default('completed'); // completed, pending, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Tabel kpi_sales_monthly untuk agregasi KPI
        Schema::create('kpi_sales_monthly', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->integer('month');
            $table->foreignId('product_type_id')->constrained('product_types')->onDelete('cascade');
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->integer('total_transactions')->default(0);
            $table->decimal('target_revenue', 15, 2)->nullable();
            $table->timestamps();
            
            $table->unique(['year', 'month', 'product_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_sales_monthly');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('product_types');
        Schema::dropIfExists('opportunity');
        Schema::dropIfExists('penugasan');
        Schema::dropIfExists('aktivitas');
        Schema::dropIfExists('tiket');
        Schema::dropIfExists('crm_lead');
        Schema::dropIfExists('customer_service');
        Schema::dropIfExists('nasabah');
        Schema::dropIfExists('unit_terkait');
    }
};
