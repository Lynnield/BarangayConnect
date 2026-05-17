<?php

namespace Database\Seeders;

use App\Models\{Role, Permission, User, Resident, DocumentType, SystemSetting, AppointmentSlot};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $adminRole = Role::create(['name' => 'Administrator', 'slug' => 'admin', 'description' => 'Full system access']);
        $staffRole = Role::create(['name' => 'Barangay Staff', 'slug' => 'staff', 'description' => 'Process requests and manage appointments']);
        $residentRole = Role::create(['name' => 'Resident', 'slug' => 'resident', 'description' => 'Request documents and schedule appointments']);

        // Permissions
        $modules = [
            'users' => ['view', 'create', 'edit', 'delete'],
            'residents' => ['view', 'create', 'edit', 'delete', 'import', 'export'],
            'requests' => ['view', 'create', 'edit', 'delete', 'approve', 'reject', 'generate_pdf'],
            'appointments' => ['view', 'create', 'edit', 'delete', 'manage'],
            'reports' => ['view', 'generate', 'export'],
            'audit_logs' => ['view', 'export'],
            'settings' => ['view', 'edit'],
            'backups' => ['view', 'create', 'download'],
            'document_types' => ['view', 'create', 'edit', 'delete'],
            'roles' => ['view', 'create', 'edit', 'delete'],
        ];

        $permissions = [];
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permissions[$module][$action] = Permission::create([
                    'name' => ucfirst($action) . ' ' . ucwords(str_replace('_', ' ', $module)),
                    'slug' => $module . '.' . $action,
                    'module' => $module,
                    'description' => ucfirst($action) . ' ' . str_replace('_', ' ', $module),
                ]);
            }
        }

        // Staff permissions
        $staffPerms = [
            'residents.view', 'residents.create', 'residents.edit',
            'requests.view', 'requests.create', 'requests.edit', 'requests.approve', 'requests.reject', 'requests.generate_pdf',
            'appointments.view', 'appointments.create', 'appointments.edit', 'appointments.delete', 'appointments.manage',
            'reports.view', 'reports.generate', 'reports.export',
        ];

        foreach ($staffPerms as $perm) {
            [$module, $action] = explode('.', $perm);
            if (isset($permissions[$module][$action])) {
                $staffRole->permissions()->attach($permissions[$module][$action]->id);
            }
        }

        // Admin gets all permissions
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $adminRole->permissions()->attach($permissions[$module][$action]->id);
            }
        }

        // Demo Users
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@barangay.gov',
            'password' => Hash::make('Admin@1234'),
            'role_id' => $adminRole->id,
            'status' => 'active',
            'phone' => '09123456789',
        ]);

        $staff = User::create([
            'name' => 'Maria Santos',
            'email' => 'staff@barangay.gov',
            'password' => Hash::make('Staff@1234'),
            'role_id' => $staffRole->id,
            'status' => 'active',
            'phone' => '09234567890',
        ]);

        $residentUser = User::create([
            'name' => 'Juan Dela Cruz',
            'email' => 'resident@email.com',
            'password' => Hash::make('Resident@1234'),
            'role_id' => $residentRole->id,
            'status' => 'active',
            'phone' => '09345678901',
        ]);

        // Resident record
        $resident = Resident::create([
            'user_id' => $residentUser->id,
            'full_name' => 'Juan Dela Cruz',
            'gender' => 'male',
            'birthdate' => '1990-05-15',
            'civil_status' => 'single',
            'address' => 'Purok 5, Brgy. San Jose, Cagayan de Oro City',
            'contact_number' => '09345678901',
            'email' => 'resident@email.com',
            'occupation' => 'Teacher',
            'valid_id_type' => 'Philippine Passport',
            'valid_id_number' => 'XX1234567',
        ]);

        // Additional sample residents
        $sampleResidents = [
            ['Ana Maria Reyes', 'female', '1985-03-20', 'married'],
            ['Pedro Santos', 'male', '1978-11-10', 'married'],
            ['Rosa Gonzales', 'female', '1995-07-25', 'single'],
            ['Carlos Mendoza', 'male', '1982-01-30', 'widowed'],
        ];

        foreach ($sampleResidents as $i => [$name, $gender, $bday, $civil]) {
            Resident::create([
                'full_name' => $name,
                'gender' => $gender,
                'birthdate' => $bday,
                'civil_status' => $civil,
                'address' => 'Purok ' . ($i + 1) . ', Brgy. San Jose, Cagayan de Oro City',
                'contact_number' => '0934567890' . $i,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@email.com',
                'occupation' => ['Teacher', 'Farmer', 'Vendor', 'Driver', 'Nurse'][$i],
            ]);
        }

        // Document Types
        $documentTypes = [
            [
                'name' => 'Barangay Clearance',
                'slug' => 'barangay-clearance',
                'description' => 'Certificate of good moral character and clearance from barangay records.',
                'fee' => 50.00,
                'processing_days' => 1,
                'required_fields' => ['purpose', 'valid_id_type', 'valid_id_number'],
                'required_attachments' => ['valid_id_front', 'valid_id_back'],
            ],
            [
                'name' => 'Certificate of Residency',
                'slug' => 'certificate-of-residency',
                'description' => 'Certificate proving that the applicant resides within the barangay.',
                'fee' => 30.00,
                'processing_days' => 1,
                'required_fields' => ['purpose', 'years_of_residency'],
                'required_attachments' => ['proof_of_residency'],
            ],
            [
                'name' => 'Certificate of Indigency',
                'slug' => 'certificate-of-indigency',
                'description' => 'Certificate for qualified low-income residents.',
                'fee' => 0.00,
                'processing_days' => 2,
                'required_fields' => ['purpose', 'household_income'],
                'required_attachments' => ['valid_id'],
            ],
            [
                'name' => 'Business Permit Recommendation',
                'slug' => 'business-permit',
                'description' => 'Barangay endorsement for business permit application.',
                'fee' => 100.00,
                'processing_days' => 3,
                'required_fields' => ['business_name', 'business_address', 'business_type'],
                'required_attachments' => ['dti_registration', 'valid_id'],
            ],
            [
                'name' => 'Certificate of Good Moral Character',
                'slug' => 'certificate-of-good-moral',
                'description' => 'Certificate attesting to the good moral character of the applicant.',
                'fee' => 50.00,
                'processing_days' => 1,
                'required_fields' => ['purpose'],
                'required_attachments' => ['valid_id'],
            ],
        ];

        foreach ($documentTypes as $type) {
            DocumentType::create($type + ['is_active' => true]);
        }

        // System Settings
        $settings = [
            ['barangay_name', 'Barangay San Jose', 'general'],
            ['city', 'Cagayan de Oro City', 'general'],
            ['province', 'Misamis Oriental', 'general'],
            ['region', 'Region X (Northern Mindanao)', 'general'],
            ['captain_name', 'HON. JUAN BATONG', 'general'],
            ['secretary_name', 'MARIA L. SANTOS', 'general'],
            ['contact_email', 'brgy.sanjose@cdo.gov.ph', 'contact'],
            ['contact_phone', '(088) 123-4567', 'contact'],
            ['office_hours', 'Monday to Friday, 8:00 AM - 5:00 PM', 'schedule'],
            ['appointment_slot_duration', '30', 'schedule'],
            ['max_appointments_per_slot', '5', 'schedule'],
            ['session_timeout', '120', 'security'],
            ['max_login_attempts', '5', 'security'],
            ['maintenance_mode', '0', 'system'],
            ['backup_retention_days', '30', 'backup'],
        ];

        foreach ($settings as [$key, $value, $group]) {
            SystemSetting::create([
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_group' => $group,
            ]);
        }

        // Sample appointment slots for next 2 weeks
        $times = ['08:00', '08:30', '09:00', '09:30', '10:00', '10:30',
                  '13:00', '13:30', '14:00', '14:30', '15:00', '15:30'];

        for ($d = 1; $d <= 14; $d++) {
            $date = now()->addDays($d);
            if ($date->isWeekday()) {
                foreach ($times as $time) {
                    \App\Models\AppointmentSlot::create([
                        'slot_date' => $date->format('Y-m-d'),
                        'slot_time' => $time,
                        'max_appointments' => 5,
                        'is_available' => true,
                    ]);
                }
            }
        }

        echo "Database seeded successfully!\n";
        echo "Admin: admin@barangay.gov / Admin@1234\n";
        echo "Staff: staff@barangay.gov / Staff@1234\n";
        echo "Resident: resident@email.com / Resident@1234\n";
    }
}
