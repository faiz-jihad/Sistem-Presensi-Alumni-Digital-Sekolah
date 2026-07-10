<?php

namespace Database\Seeders;
use App\Models\Student;
use App\Models\User;
use App\Models\School;
use App\Models\StudentClass;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class SeederTest extends Seeder
{
    public function run(): void
    {
$school = School::first();

$classes = StudentClass::take(10)->get();

$students = [
    ['nis' => '20260001', 'nisn' => '1000000001', 'name' => 'Ahmad Fauzi', 'gender' => 'male'],
    ['nis' => '20260002', 'nisn' => '1000000002', 'name' => 'Budi Santoso', 'gender' => 'male'],
    ['nis' => '20260003', 'nisn' => '1000000003', 'name' => 'Citra Lestari', 'gender' => 'female'],
    ['nis' => '20260004', 'nisn' => '1000000004', 'name' => 'Dewi Anggraini', 'gender' => 'female'],
    ['nis' => '20260005', 'nisn' => '1000000005', 'name' => 'Eko Pratama', 'gender' => 'male'],
    ['nis' => '20260006', 'nisn' => '1000000006', 'name' => 'Fitri Ramadhani', 'gender' => 'female'],
    ['nis' => '20260007', 'nisn' => '1000000007', 'name' => 'Galih Saputra', 'gender' => 'male'],
    ['nis' => '20260008', 'nisn' => '1000000008', 'name' => 'Hana Putri', 'gender' => 'female'],
    ['nis' => '20260009', 'nisn' => '1000000009', 'name' => 'Indra Wijaya', 'gender' => 'male'],
    ['nis' => '20260010', 'nisn' => '1000000010', 'name' => 'Joko Hidayat', 'gender' => 'male'],
];

foreach ($students as $index => $student) {

    // Membuat akun orang tua
    $parent = User::firstOrCreate(
        [
            'email' => 'parent' . ($index + 1) . '@demo.com',
        ],
        [
            'name' => 'Orang Tua ' . $student['name'],
            'phone' => '0812345678' . ($index + 1),
            'password' => Hash::make('password'),
            'role' => 'parent',
            'school_id' => $school->id,
            'status' => 'active',
        ]
    );

    Student::firstOrCreate(
        [
            'nis' => $student['nis'],
        ],
        [
            'school_id' => $school->id,
            'class_id' => $classes[$index % $classes->count()]->id,
            'parent_user_id' => $parent->id,
            'parent_phone' => $parent->phone,
            'nisn' => $student['nisn'],
            'name' => $student['name'],
            'gender' => $student['gender'],
            'birth_date' => '2008-01-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
            'status' => 'active',
        ]
    );
}
    }
}