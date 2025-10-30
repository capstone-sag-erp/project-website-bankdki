<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Roles
        DB::table('roles')->upsert([
            ['id'=>1,'name'=>'manager','description'=>'Manager/Admin','created_at'=>$now,'updated_at'=>$now],
            ['id'=>2,'name'=>'staff','description'=>'Staff Biasa','created_at'=>$now,'updated_at'=>$now],
        ], ['id'], ['name','description','updated_at']);

        // Permissions (silakan tambah sesuai kebutuhan)
        $perms = [
            ['id'=>1,'key_name'=>'workflow.view','label'=>'Lihat halaman workflow','created_at'=>$now,'updated_at'=>$now],
            ['id'=>2,'key_name'=>'workflow.approve','label'=>'Approve/Reject dokumen','created_at'=>$now,'updated_at'=>$now],
            ['id'=>3,'key_name'=>'documents.upload','label'=>'Upload dokumen','created_at'=>$now,'updated_at'=>$now],
            ['id'=>4,'key_name'=>'documents.manage','label'=>'Kelola dokumen','created_at'=>$now,'updated_at'=>$now],
            ['id'=>5,'key_name'=>'kpi.view','label'=>'Lihat KPI','created_at'=>$now,'updated_at'=>$now],
        ];
        DB::table('permissions')->upsert($perms, ['id'], ['key_name','label','updated_at']);

        // role_permission
        $rows = [];
        $allow = fn($role,$perm)=>[
            'role_id'=>$role,'permission_id'=>$perm,'allowed'=>true,'created_at'=>$now,'updated_at'=>$now
        ];

        // Manager: semua
        foreach ([1,2,3,4,5] as $pid) $rows[] = $allow(1,$pid);

        // Staff: subset
        foreach ([1,3,5] as $pid) $rows[] = $allow(2,$pid);

        DB::table('role_permission')->upsert($rows, ['role_id','permission_id'], ['allowed','updated_at']);
    }
}
