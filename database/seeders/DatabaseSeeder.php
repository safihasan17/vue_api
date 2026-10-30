<?php

namespace Database\Seeders;

use App\Models\Admission;
use App\Models\Appointment;
use App\Models\Bed;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\LabTest;
use App\Models\LabTestOrder;
use App\Models\Medicine;
use App\Models\MedicinePurchase;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionMedicine;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Role::factory()->createMany([
            ['name' => 'Super Admin'],
            ['name' => 'Doctor'],
            ['name' => 'Receptionist'],
            ['name' => 'Pharmacist'],
            ['name' => 'Lab Technician'],
            ['name' => 'Accountant'],
            ['name' => 'Nurse'],
        ]);


        User::factory(30)->create();

        Patient::factory(30)->create();

        Department::factory()->createMany([
            ['name' => 'Cardiology', 'description' => 'Diagnosis and treatment of heart-related conditions.'],
            ['name' => 'Orthopedics', 'description' => 'Care for bones, joints, and the musculoskeletal system.'],
            ['name' => 'Pediatrics', 'description' => 'Medical care for infants, children, and adolescents.'],
            ['name' => 'Neurology', 'description' => 'Diagnosis and treatment of disorders of the nervous system.'],
            ['name' => 'Gynecology', 'description' => "Women's reproductive health and related care."],
            ['name' => 'Dermatology', 'description' => 'Diagnosis and treatment of skin, hair, and nail conditions.'],
            ['name' => 'ENT', 'description' => 'Ear, nose, and throat related treatment and surgery.'],
            ['name' => 'General Medicine', 'description' => 'General diagnosis and treatment of common illnesses.'],
        ]);

        Doctor::factory(10)->create();

        Appointment::factory(30)->create();

        Prescription::factory(30)->create();
        Medicine::factory(30)->create();
        PrescriptionMedicine::factory(30)->create();


        LabTest::factory(12)->create();
        LabTestOrder::factory(20)->create();

        Ward::factory(20)->create();
        Bed::factory(50)->create();
        Admission::factory(25)->create();
        Invoice::factory(20)->create();
        InvoiceItem::factory(20)->create();

        
        Supplier::factory(30)->create();
        MedicinePurchase::factory(30)->create();





    }
}
