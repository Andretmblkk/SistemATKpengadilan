<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TestRoleAccess extends Command
{
    protected $signature = 'test:role-access {email?}';
    protected $description = 'Test batasan akses berdasarkan role';

    public function handle()
    {
        $email = $this->argument('email');
        
        if (!$email) {
            $this->info('Menggunakan user default: fatma@pengadilan-agama.go.id');
            $email = 'fatma@pengadilan-agama.go.id';
        }

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User dengan email {$email} tidak ditemukan!");
            return 1;
        }

        $this->info("Testing akses untuk user: {$user->name} ({$user->email})");
        $this->info("Role: " . implode(', ', $user->roles->pluck('name')->toArray()));
        $this->line('');

        // Test akses ke setiap resource
        $this->testResourceAccess('ItemResource', $user);
        $this->testResourceAccess('RequestResource', $user);
        $this->testResourceAccess('PurchaseRequestResource', $user);
        $this->testResourceAccess('ReportResource', $user);
        $this->testResourceAccess('UserResource', $user);
        $this->testResourceAccess('CategoryResource', $user);
        $this->testResourceAccess('RoleResource', $user);
        $this->testResourceAccess('PermissionResource', $user);

        $this->info('Testing selesai!');
        return 0;
    }

    private function testResourceAccess($resourceClass, $user)
    {
        $this->line("Testing {$resourceClass}:");
        
        // Simulasi login
        auth()->login($user);
        
        $fullClassName = "App\\Filament\\Resources\\{$resourceClass}";
        
        if (!class_exists($fullClassName)) {
            $this->error("  ❌ Class {$fullClassName} tidak ditemukan");
            return;
        }

        try {
            // Test canAccess
            $canAccess = $fullClassName::canAccess();
            $this->line("  canAccess: " . ($canAccess ? '✅ Ya' : '❌ Tidak'));

            // Test canCreate
            $canCreate = $fullClassName::canCreate();
            $this->line("  canCreate: " . ($canCreate ? '✅ Ya' : '❌ Tidak'));

            // Test shouldRegisterNavigation
            $canNavigate = $fullClassName::shouldRegisterNavigation();
            $this->line("  shouldRegisterNavigation: " . ($canNavigate ? '✅ Ya' : '❌ Tidak'));

        } catch (\Exception $e) {
            $this->error("  ❌ Error: " . $e->getMessage());
        }

        $this->line('');
    }
} 